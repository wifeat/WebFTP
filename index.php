<?php
// +----------------------------------------------------------------------
// | Copyright (C) 2008-2012 OSDU.Net    www.osdu.net    admin@osdu.net
// +----------------------------------------------------------------------
// | Licensed: ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author:   左手边的回忆 QQ:858908467 E-mail:858908467@qq.com
// +----------------------------------------------------------------------

//全局入口文件
require('./Init.php');

/*
//Auth::add_user('admin', '123456', array('*'));
//Auth::del_user('demo');
//Auth::update_user_password('demo','newpass');
///?m=login&a=api_login&key=&username=admin&tokey=kjhfsksahfkshfkjh
*/

if(isset($_GET['m']) && 'login' === $_GET['m']){
	if('out' === $_GET['a']){
		Auth::login_out();
		redirect('./?m=login&a=in');
	}elseif('local_login_check' === $_GET['a']){
		Auth::local_login_check();
	}elseif('api_login' === $_GET['a']){
		Auth::api_login_check();
	}elseif('resetpasswd' === $_GET['a']){
		Auth::update_user_password();
	}else{
		include(TPL_ROOT.'login.tpl.php');die();
	}
}

if(Auth::is_login()){
	include(TPL_ROOT.'index.tpl.php');
}else{
	redirect('./?m=login&a=in');
}
?>
