<?php
// +----------------------------------------------------------------------
// | Copyright (C) 2008-2012 OSDU.Net    www.osdu.net    admin@osdu.net
// +----------------------------------------------------------------------
// | Licensed: ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author:   左手边的回忆 QQ:858908467 E-mail:858908467@qq.com
// +----------------------------------------------------------------------

if(!defined('WebFTP')){die('Forbidden Access');}

class Session
{
 //setcookie(session_name(), session_id(), time() + $lifeTime, "/");
    //启动Session
    static function start()
    {
		if(isset($_POST[self::name()]) && !empty($_POST[self::name()])){
			self::id(trim($_POST[self::name()]));
		}
		if(isset($_POST[C('COOKIE_PREFIX').'username']) && !empty($_POST[C('COOKIE_PREFIX').'username'])){
			$_COOKIE[C('COOKIE_PREFIX').'username'] = trim($_POST[C('COOKIE_PREFIX').'username']);
		}
		if(isset($_POST[C('COOKIE_PREFIX').'tokey']) && !empty($_POST[C('COOKIE_PREFIX').'tokey'])){
			$_COOKIE[C('COOKIE_PREFIX').'tokey'] = trim($_POST[C('COOKIE_PREFIX').'tokey']);
		}
        session_start();
        Session::setExpire(C('COOKIE_EXPIRE'));
    }

	//设置Session 过期时间
    static function setExpire($time)
    {
		setcookie(Session::name(), Session::id(), time() + $time, '/');
    }

    //设置或者获取当前Session name
    static function name($name = null)
    {
        return is_null($name) ? session_name() : session_name($name) ;
    }

    //设置或者获取当前SessionID
    static function id($id = null)
    {
        return is_null($id) ? session_id() : session_id($id);
    }

	 //检查Session 值是否已经设置
    static function is_set($name)
    {
        $name = explode('.', $name);
        if(isset($name[2])) {
            return isset($_SESSION[$name[0]][$name[1]][$name[2]]);
        }elseif(isset($name[1])){
            return isset($_SESSION[$name[0]][$name[1]]);
        }elseif(isset($name[0])){
			return isset($_SESSION[$name[0]]);
		}else{
			return false;
		}
    }

    //取得Session 值
    static function get($name)
    {
		$name = explode('.', $name);
        if(isset($name[2]) && isset($_SESSION[$name[0]][$name[1]][$name[2]])) {
            return $_SESSION[$name[0]][$name[1]][$name[2]];
        }elseif(isset($name[1]) && isset($_SESSION[$name[0]][$name[1]])){
            return $_SESSION[$name[0]][$name[1]];
        }elseif(isset($name[0]) && isset($_SESSION[$name[0]])){
			return $_SESSION[$name[0]];
		}else{
			return NULL;
		}
    }

    //设置Session 值
    static function set($name, $value=NULL)
    {
		$name = explode('.', $name);
        if(isset($name[2])) {
            $_SESSION[$name[0]][$name[1]][$name[2]] = $value;
			if(is_null($value)){unset($_SESSION[$name[0]][$name[1]][$name[2]]);}
        }elseif(isset($name[1])){
            $_SESSION[$name[0]][$name[1]] = $value;
			if(is_null($value)){unset($_SESSION[$name[0]][$name[1]]);}
        }elseif(isset($name[0])){
			$_SESSION[$name[0]] = $value;
			if(is_null($value)){unset($_SESSION[$name[0]]);}
		}
		return;
    }

	//清空Session
    static function clear()
	{
        $_SESSION = array();
    }

    //销毁Session
    static function destroy()
    {
        unset($_SESSION);
        session_destroy();
    }
}
?>