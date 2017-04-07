<?php
namespace untils;

class curl {

    protected static $handle = null;

	public static function init($options)
	{
	    $headerArr = [];
	    $referer = arrayRemove($options, 'REFERER');
	    foreach($options as $n => $v ) {
	        $headerArr[] = $n .':' . $v;
	    }

	    self::$handle = curl_init();
	    curl_setopt(self::$handle, CURLOPT_HTTPHEADER , $headerArr); //构造ip
	    curl_setopt(self::$handle, CURLOPT_REFERER, $referer);
        curl_setopt(self::$handle, CURLOPT_HEADER, false);
        curl_setopt(self::$handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(self::$handle, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/44.0.2403.89 Safari/537.36");
        // curl_setopt(self::$handle, CURLOPT_TIMEOUT, $timeout);
        // curl_setopt_array(self::$handle, $options);

	}
	
	public static function get($url, $options = [])
	{
		static::init($options);

        curl_setopt(self::$handle, CURLOPT_URL, $url);
        curl_setopt(self::$handle, CURLOPT_CUSTOMREQUEST, 'GET');

        $result = curl_exec(self::$handle);

        static::close();

        return $result;
    }

	public static function post($url, $data, $options = [])
	{
		static::init($options);

        curl_setopt(self::$handle, CURLOPT_URL, $url);
        curl_setopt(self::$handle, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt(self::$handle, CURLOPT_POSTFIELDS, http_build_query($data));

        $result = curl_exec(self::$handle);

        static::close();

        return $result;
	}

    public static function put($url, $data, $options = [])
    {
        static::init($options);

        curl_setopt(self::$handle, CURLOPT_URL, $url);
        curl_setopt(self::$handle, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt(self::$handle, CURLOPT_POSTFIELDS, http_build_query($data));

        $result = curl_exec(self::$handle);

        static::close();

        return $result;
    }

	public static function close()
	{
        curl_close(self::$handle);
	}

}