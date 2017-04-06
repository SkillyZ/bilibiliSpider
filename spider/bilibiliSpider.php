<?php

class bilibiliSpider {

	public function run()
	{
		$this->getUser(1);
	}

	public function getUser($uid = 1)
	{
	    $headers['CLIENT-IP'] = '172.18.3.200';  
	    $headers['X-FORWARDED-FOR'] = '172.18.3.200'; 
	    $headerArr = array();
	    
	    foreach( $headers as $n => $v ) {
	        $headerArr[] = $n .':' . $v;   
	    }

	    ob_start();
	    $ch = curl_init();
	    curl_setopt ($ch, CURLOPT_URL, "http://space.bilibili.com/ajax/member/GetInfo");
	    curl_setopt ($ch, CURLOPT_HTTPHEADER , $headerArr);  //构造IP
	    // for ($uid = 1; $uid < $max ; $uid++) { 
	    //     $post_string = [
	    //         'mid' => $uid
	    //     ];
	    // }
	    $post_string['mid'] = $uid;
	    curl_setopt ($ch, CURLOPT_REFERER, "http://space.bilibili.com/$uid/");
	    curl_setopt ($ch, CURLOPT_HEADER, false);
	    curl_setopt ($ch, CURLOPT_POSTFIELDS, http_build_query($post_string));
	    curl_setopt ($ch, CURLOPT_POST, 1);  
	    curl_exec ($ch);
	    curl_close ($ch);
	    $out = ob_get_contents();
	    ob_clean();
	    print_r($out);
	}
}