<?php
// +----------------------------------------------------------------------
// | Copyright (C) 2008-2012 OSDU.Net    www.osdu.net    admin@osdu.net
// +----------------------------------------------------------------------
// | Licensed: ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author:   左手边的回忆 QQ:858908467 E-mail:858908467@qq.com
// +----------------------------------------------------------------------

//error_reporting(0);
//set_time_limit(0);

header('Content-Type:text/html; charset=utf-8');

//文件载入认证
define('WebFTP', 'www.ihotte.com');

//应用主目录
define('APP_ROOT', dirname(__FILE__).'/');

//系统核心模块目录
define('INC_ROOT',  APP_ROOT.'Inc/');

//系统模板目录
define('TPL_ROOT',  APP_ROOT.'Tpl/');

//系统数据目录
define('DATA_PATH', APP_ROOT.'Data/');



//加载系统函数库
require(INC_ROOT.'Functions.php');

//加载系统核心模块
require(INC_ROOT.'Auth.class.php');
require(INC_ROOT.'Cookie.class.php');
require(INC_ROOT.'File.class.php');
require(INC_ROOT.'Session.class.php');
/*按需加载模块
require(INC_ROOT.'Thumb.class.php');
require(INC_ROOT.'PclZip.class.php');
require(INC_ROOT.'Chmod.conf.php');
*/



//初始化配置参数
C(include(APP_ROOT.'Config.php'));


set_error_handler('error_handler_fun');
set_time_limit((int)C('MAX_TIME_LIMIT'));
if(function_exists('date_default_timezone_set')){
	date_default_timezone_set('PRC');
}

//开启SESSION
Session::start();
?>