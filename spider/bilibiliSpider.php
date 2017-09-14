<?php
namespace spider;

class bilibiliSpider {

	public $url = 'http://api.bilibili.com/x/web-interface/archive/stat'; //?callback=jQuery17205247983639302185_1505354971465&aid=13741512&jsonp=jsonp&_=1505354971900

	public function run()
	{
		$max = 61; //总抓取数目
		$maxSize = 20; //一次并发请求
		$cycle = ceil ($max / $maxSize);
		$date = \cache\Loger::record();
		$queues = [];
		$users = [];

		$options = [
			'CLIENT-IP' => '180.175.168.14',
			'X-FORWARDED-FOR' => '180.175.168.14',
			// 'REFERER' => "http://www.bilibili.com/video/av{$aid}/",
		];

		// for ($i = 1; $i <= $max; $i++)
  //       { 
		// 	$id['mid'] = $i;
		// 	$queues[] = $id;
  //       }

    //     foreach ($queues as $key =>  $user) {
    //     	$users[] = array_shift($queues);
    //     	if (count($users) >= $max) {
				// $results = $this->curlMulti($users);
				// foreach ($results as $result) {
				// 	$this->insert($result);
				// }
				// $users = [];
    //     	}
    //         usleep(1000);
    //     } 

		for ($i = 1; $i <= $max; $i++)
        {
			$time = time();
        	$aid = $i;
			$url = $this->url . "?callback=jQuery17205247983639302185_{$time}465&aid={$aid}&jsonp=jsonp&_={$time}900";
        	$queues[] = $url;
        }
        
		for ($i = 0; $i < $cycle; $i++)
        {
			$queue = array_slice($queues, $maxSize * ($i), $maxSize);
			$results = $this->curlMulti($queue);
			foreach ($results as $result) {
	        	// $options['REFERER'] = "http://www.bilibili.com/video/av{$aid}";

		        $result = strstr($result, '(');
		        $result = str_replace(['(', ')'], '', $result);
		        $result = json_decode($result, true);
				$result = $this->insert_video($result);
			}
            usleep(1000);

        }

        echo 'chengg';



		// $results = $this->curlMulti($users);
		// foreach ($results as $result) {
		// 	$this->insert($result);
		// }

		
		// for ($i = 1; $i <= $max; $i++)
  //       { 
		// 	$time = time();
  //       	$aid = '12';
  //       	$options['REFERER'] = "http://www.bilibili.com/video/av{$aid}";
		// 	$url = $this->url . "?callback=jQuery17205247983639302185_{$time}465&aid={$aid}&jsonp=jsonp&_={$time}900";
		// 	$result = $this->curl($url, $options);

	 //        $result = strstr($result, '(');
	 //        $result = str_replace(['(', ')'], '', $result);
	 //        $result = json_decode($result, true);
		// 	$result = $this->insert_video($result);
		// 	print_r($result);die;
  //       }

		\cache\Loger::record($date);
	}

	public function curlMulti($urls, $options = [])
	{
		// $this->url = 'http://space.bilibili.com/ajax/member/GetInfo';
		return \untils\curlMulti::post($urls, '', $options);
	}

	public function curl($url, $options = [])
	{
		return \untils\curl::get($url, $options);
	}

	public function insert_video($result) {
		if (isset($result['data']) && $result['data']) {

			$data = $result['data'];
			$insert = [
				'aid' => $data['aid'],
				'coin' => $data['coin'],
				'danmaku' => $data['danmaku'],
				'favorite' => $data['favorite'],
				'reply' => $data['reply'],
				'share' => $data['share'],
				'view' => $data['view'],
				'created' => time(),
				// 'his_rank' => $data['his_rank'],
				// 'no_reprint' => $data['no_reprint'],
				// 'now_rank' => $data['now_rank'],
			];
			$result = \cache\DB::insert('bilibili_video', $insert);
		}
	}

	public function insert_user($result)
	{
		if (isset($result['status']) && $result['status']) {

			$data = $result['data'];
			$city = '';
			$province = '';
			$regtime = '';
			if (isset($data['place']) && $data['place']) {
				$place = explode(' ', $data['place']);
				if (count($place) == 1) {
					$province = $data['place'];
				} else {
					$province = current($place);
					$city = next($place);
				}
			}
			if (isset($data['regtime'])) {
				$regtime = date('Y-m-d H:i:s', $data['regtime']);
			}
			$insert = [
				'id' => $data['mid'],
				'name' => $data['name'],
				'sex' => $data['sex'],
				'regtime' => $regtime,
				'fans' => $data['fans'],
				'attention' => $data['attention'],
				'play_num' => $data['playNum'],
				'province' => $province,
				'city' => $city,
			];
			$result = \cache\DB::insert('user', $insert);
		}
	}

	public function test() {
		$result = \cache\DB::query('select * from user');
		$data = [
			'id' => 3,
			'attention' => 33,
			'fans' => 22,
		];
		$result = \cache\DB::insert('user', $data);

		p($result);
	}
}