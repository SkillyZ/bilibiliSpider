<?php
namespace spider;

class bilibiliSpider {

	public function run()
	{
		$result = $this->curl(1);
		$result = $this->insert($result);
		
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