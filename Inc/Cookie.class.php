<?php
// +----------------------------------------------------------------------
// | Copyright (C) 2008-2012 OSDU.Net    www.osdu.net    admin@osdu.net
// +----------------------------------------------------------------------
// | Licensed: ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author:   左手边的回忆 QQ:858908467 E-mail:858908467@qq.com
// +----------------------------------------------------------------------

if(!defined('WebFTP')){die('Forbidden Access');}

class Cookie
{
    // 判断Cookie是否存在
    static function is_set($name)
	{
        return isset($_COOKIE[C('COOKIE_PREFIX').$name]);
    }

    // 获取某个Cookie值
    static function get($name, $encode=false)
	{
        $value   = Cookie::is_set($name) ? $_COOKIE[C('COOKIE_PREFIX').$name] : NULL;
        $value   = $encode ? unserialize(base64_decode($value)) : $value;
        return $value;
    }

    // 设置某个Cookie值
    static function set($name, $value, $encode=false, $expire='',$path='',$domain='')
	{
        $expire = empty($expire) ? C('COOKIE_EXPIRE') : $expire;
		$path   = empty($path) ? C('COOKIE_PATH') : $path;
        $domain = empty($domain) ? C('COOKIE_DOMAIN') : $domain;

        $expire = empty($expire) ? 0 : time()+$expire;
        $value  =  $encode ? base64_encode(serialize($value)) : $value;
        setcookie(C('COOKIE_PREFIX').$name, $value, $expire, $path, $domain);
        $_COOKIE[C('COOKIE_PREFIX').$name]  =   $value;
    }

    // 删除某个Cookie值
    static function del($name)
	{
        Cookie::set($name, '', false, -3600*8);
        unset($_COOKIE[C('COOKIE_PREFIX').$name]);
    }

    // 清空Cookie值
    static function clear()
	{
        unset($_COOKIE);
    }
}
?>