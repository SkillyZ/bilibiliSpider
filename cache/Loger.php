<?php
namespace cache;

class Loger {
	
	public static function log($msg)
	{
		// echo iconv('UTF-8', 'GB2312', $msg).PHP_EOL;
		echo $msg . PHP_EOL;
	}

	public static function record($date = null)
	{
		if ($date) {
			$time = strtotime($date);
			$timediff = floor((time() - $time) / 60);
			echo 'consum timed: ' . $timediff . ' minute' . PHP_EOL;
		}
		$time = date('Y-m-d H:i:s', time());
		echo $time . PHP_EOL;
		return $time;
	}
}