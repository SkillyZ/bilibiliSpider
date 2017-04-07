<?php
namespace untils;

class curlMulti {


	public static function init($url, $options, $data, $state = 'POST')
	{
	    $headerArr = [];
	    $referer = arrayRemove($options, 'REFERER');
	    foreach($options as $n => $v ) {
	        $headerArr[] = $n .':' . $v;
	    }

	    $handle = curl_init();
	    curl_setopt($handle, CURLOPT_HTTPHEADER , $headerArr); //构造ip
	    curl_setopt($handle, CURLOPT_REFERER, $referer);
        curl_setopt($handle, CURLOPT_HEADER, false);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/44.0.2403.89 Safari/537.36");
        
        curl_setopt($handle, CURLOPT_URL, $url);
        curl_setopt($handle, CURLOPT_CUSTOMREQUEST, $state);
        curl_setopt($handle, CURLOPT_POSTFIELDS, http_build_query($data));
        
        return $handle;
	}

    public static function post($url, $data, $options = [])
    {
        $mch = curl_multi_init();
        foreach ($data as $key => $val) {
            $chList[] = static::init($url, $options, $val);
        }

        foreach ($chList as $ch){
            curl_multi_add_handle($mch, $ch);
        }

        $list = [];
        // 轮询
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

                $list[] = json_decode($output, true);
                // //保证同时有$max_size个请求在处理
                // if ($i < sizeof($user_list) && isset($user_list[$i]) && $i < count($user_list))
                // {
                //     $ch = curl_init();
                //     curl_setopt($ch, CURLOPT_HEADER, 0);
                //     curl_setopt($ch, CURLOPT_URL, 'http://www.zhihu.com/people/' . $user_list[$i] . '/about');
                //     curl_setopt($ch, CURLOPT_COOKIE, self::$user_cookie);
                //     curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/44.0.2403.130 Safari/537.36');
                //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
                //     curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                //     $requestMap[$i] = $ch;
                //     curl_multi_add_handle($mh, $ch);

                //     $i++;
                // }

                curl_multi_remove_handle($mch, $done['handle']);
            }

            // 当没有数据的时候进行堵塞，把 CPU 使用权交出来，避免上面 do 死循环空跑数据导致 CPU 100%
            if ($active) {
                $rel = curl_multi_select($mch, 1);
                // if($rel == -1){
                //     usleep(1000);
                // }
            }

        } while ($active);

        curl_multi_close($mch);
        echo "所有请求下载完成!";

        return $list;
    }

}