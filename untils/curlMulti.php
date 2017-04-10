<?php
namespace untils;

class curlMulti {

    protected static $user_cookie;

	public static function init($url, $options, $data, $state = 'POST')
	{
	    $headerArr = [];
        $referer = arrayRemove($options, 'REFERER');
	    $timeout = arrayRemove($options, 'TIMEOUT', 120);
	    foreach($options as $n => $v ) {
	        $headerArr[] = $n .':' . $v;
	    }

	    $handle = curl_init();
	    curl_setopt($handle, CURLOPT_HTTPHEADER , $headerArr); //构造ip
	    curl_setopt($handle, CURLOPT_REFERER, $referer);
        curl_setopt($handle, CURLOPT_HEADER, false);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true); //作为变量储存
        curl_setopt($handle, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/44.0.2403.89 Safari/537.36");
        curl_setopt($handle, CURLOPT_URL, $url);
        curl_setopt($handle, CURLOPT_CUSTOMREQUEST, $state);
        curl_setopt($handle, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, $timeout);

        // curl_setopt($handle, CURLOPT_PROXYAUTH, CURLAUTH_BASIC); //代理认证模式
        // curl_setopt($handle, CURLOPT_PROXY, "119.176.38.36"); //代理服务器地址
        // curl_setopt($handle, CURLOPT_PROXYPORT, 8118); //代理服务器端口
        // curl_setopt($handle, CURLOPT_PROXYTYPE, CURLPROXY_HTTP); //使用http代理模式
        // curl_setopt($handle, CURLOPT_PROXYUSERPWD, ":"); //http代理认证帐号，username:password的格式
        // curl_setopt($handle, CURLOPT_FOLLOWLOCATION, 1); //使用自动跳转
        // curl_setopt($handle, CURLOPT_COOKIE, self::$user_cookie);
        
        return $handle;
	}

    public static function post($url, $userList, $options = [])
    {
        $mch = curl_multi_init();
        foreach ($userList as $key => $val) {
            $chList[] = static::init($url, $options, $val);
        }

        foreach ($chList as $ch) {
            curl_multi_add_handle($mch, $ch);
        }

        do {
            while (($execrun = curl_multi_exec($mch, $active)) == CURLM_CALL_MULTI_PERFORM) ;
            if ($execrun != CURLM_OK) {
                break;
            }

            // 一旦有一个请求完成，找出来，处理,因为curl底层是select，所以最大受限于1024
            while ($done = curl_multi_info_read($mch))
            {
                // 从请求中获取信息、内容、错误
                $info = curl_getinfo($done['handle']);
                $output = curl_multi_getcontent($done['handle']);
                $error = curl_error($done['handle']);
                
                // p($info);
                if ($info['http_code'] != 403) { //403
                    $result[] = json_decode($output, true);
                } else {
                    // usleep(1000 * 60 * 10);
                    p($info, $error);
                }
                //保证同时有$max_size个请求在处理
                // if ($index < count($userList))
                // {
                //     $user = array_shift($userQueue);
                //     $ch = static::init($url, $options, $user);
                //     curl_multi_add_handle($mch, $ch);

                //     $index++;
                // }

                curl_multi_remove_handle($mch, $done['handle']);
            }

            // 当没有数据的时候进行堵塞，把 CPU 使用权交出来，避免上面 do 死循环空跑数据导致 CPU 100%
            if ($active) {
                $rel = curl_multi_select($mch, 1000);
                if ($rel == -1) {
                    usleep(1000);
                }
            }

        } while ($active);

        curl_multi_close($mch);
        return $result;
    }

}