<?php

require_once './untils/function.php';
require_once './Autoloader.php';
// require_once './spider/bilibiliSpider.php';

$spider = new \spider\bilibiliSpider();
$spider->run();