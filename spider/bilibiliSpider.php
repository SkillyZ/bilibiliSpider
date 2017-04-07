<?php
namespace spider;

class bilibiliSpider {

	public function run()
	{
		$max = 100;
		$date = \cache\Loger::record();
		$ids = [];

		for ($i = 1; $i <= $max; $i++)
        { 
			$id['mid'] = $i;
			$ids[] = $id;
        }

		$results = $this->curlMulti($ids);
		foreach ($results as $result) {
			$this->insert($result);
		}

		
		// for ($i = 1; $i <= $max; $i++)
  //       { 
		// 	$result = $this->curl($i);
		// 	$result = $this->insert($result);
  //       }

		\cache\Loger::record($date);
	}

	public function curlMulti($post)
	{
		$url = 'http://space.bilibili.com/ajax/member/GetInfo';
		$options = [
			'CLIENT-IP' => '172.18.3.200',
			'X-FORWARDED-FOR' => '172.18.3.200',
			'REFERER' => 'http://space.bilibili.com'
		];
		return \untils\curlMulti::post($url, $post, $options);
	}


	public function curl($uid)
	{
		$url = 'http://space.bilibili.com/ajax/member/GetInfo';
		$options = [
			'CLIENT-IP' => '172.18.3.200',
			'X-FORWARDED-FOR' => '172.18.3.200',
			'REFERER' => 'http://space.bilibili.com'
		];
	    $post['mid'] = $uid;
		return json_decode(\untils\curl::post($url, $post, $options), true);
	}

	public function insert($result)
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