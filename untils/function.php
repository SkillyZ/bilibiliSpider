<?php

function p($args, $die = true)
{
    // header('Content-type:text/html;charset=utf-8');
    // iconv('UTF-8', 'GB2312', $val)
	foreach (func_get_args() as $key => $val) {
		echo '<pre>'. print_r($val, true) .'</pre>';
	}
	if ($die) die;
}

/**
 * 弹出数组指定元素
 * @param  array &$array  
 * @param  string $key     
 * @param  string $default 
 * @return string          
 */
function arrayRemove(&$array, $key, $default = null)
{
    if (is_array($array) && (isset($array[$key]) || array_key_exists($key, $array))) {
        $value = $array[$key];
        unset($array[$key]);

        return $value;
    }

    return $default;
}

