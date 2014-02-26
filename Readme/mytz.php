<?php
header('content-Type: text/html; charset=utf-8');
error_reporting(E_ERROR | E_WARNING | E_PARSE);
session_start();
//设置默认时区
if(function_exists('date_default_timezone_set')){
	date_default_timezone_set('PRC');
}

define('TZ_ROOT', dirname(__FILE__).'/Tz/');
define('SYS_INFO', false);

?>
<?php
//require(TZ_ROOT.'functions.php');
// 计时
function microtime_float() {
	$mtime = microtime();
	$mtime = explode(' ', $mtime);
	return $mtime[1] + $mtime[0];

}
function memory_usage() {

	$memory	 = ( ! function_exists('memory_get_usage')) ? '0' : round(memory_get_usage()/1024/1024, 2).'MB';

	return $memory;

}
/*=============================================================
函數庫
=============================================================*/
/*-------------------------------------------------------------------------------------------------------------
檢測函數支援
--------------------------------------------------------------------------------------------------------------*/
function isfun($funName)
{
	return (false !== function_exists($funName))?YES:NO;
}
/*-------------------------------------------------------------------------------------------------------------
檢測PHP設置參數
--------------------------------------------------------------------------------------------------------------*/
function getcon($varName)
{
	//switch($res = get_cfg_var($varName))
	switch($res = ini_get($varName))
	{
		case 0:
			return NO;
			break;
		case 1:
			return YES;
			break;
		default:
			return $res;
			break;
	}

}
/*-------------------------------------------------------------------------------------------------------------
整數運算能力測試
--------------------------------------------------------------------------------------------------------------*/
function test_int()
{
	$timeStart = gettimeofday();
	for($i = 0; $i < 3000000; $i++);
	{
		$t = 1+1;
	}
	$timeEnd = gettimeofday();
	$time = ($timeEnd["usec"]-$timeStart["usec"])/1000000+$timeEnd["sec"]-$timeStart["sec"];
	$time = round($time, 3)."秒";
	return $time;
}
/*-------------------------------------------------------------------------------------------------------------
浮點運算能力測試
--------------------------------------------------------------------------------------------------------------*/
function test_float()
{
	$t = pi();
	$timeStart = gettimeofday();
	for($i = 0; $i < 3000000; $i++);
	{
		sqrt($t);
	}
	$timeEnd = gettimeofday();
	$time = ($timeEnd["usec"]-$timeStart["usec"])/1000000+$timeEnd["sec"]-$timeStart["sec"];
	$time = round($time, 3)."秒";
	return $time;
}
/*-------------------------------------------------------------------------------------------------------------
資料IO能力測試
--------------------------------------------------------------------------------------------------------------*/
function test_io()
{
	$fp = fopen(PHPSELF, "r");
	$timeStart = gettimeofday();
	for($i = 0; $i < 10000; $i++)
	{
		fread($fp, 10240);
		rewind($fp);
	}
	$timeEnd = gettimeofday();
	fclose($fp);
	$time = ($timeEnd["usec"]-$timeStart["usec"])/1000000+$timeEnd["sec"]-$timeStart["sec"];
	$time = round($time, 3)."秒";
	return($time);
}

//比例條
function bar($percent){
	return '<div class="barli" style="width:'.$percent.'%">&nbsp;</div>';
}

//linux系统探测
function sys_linux(){

	// CPU
	if (false === ($str = @file("/proc/cpuinfo"))) return false;
	$str = implode("", $str);
	@preg_match_all("/model\s+name\s{0,}\:+\s{0,}([\w\s\)\(\@.-]+)([\r\n]+)/s", $str, $model);
	@preg_match_all("/cpu\s+MHz\s{0,}\:+\s{0,}([\d\.]+)[\r\n]+/", $str, $mhz);
	@preg_match_all("/cache\s+size\s{0,}\:+\s{0,}([\d\.]+\s{0,}[A-Z]+[\r\n]+)/", $str, $cache);
	@preg_match_all("/bogomips\s{0,}\:+\s{0,}([\d\.]+)[\r\n]+/", $str, $bogomips);
	if (false !== is_array($model[1])){
		$res['cpu']['num'] = sizeof($model[1]);
		for($i = 0; $i < $res['cpu']['num']; $i++){
			$res['cpu']['model'][] = $model[1][$i];
			$res['cpu']['mhz'][] = $mhz[1][$i];
			$res['cpu']['cache'][] = $cache[1][$i];
			$res['cpu']['bogomips'][] = $bogomips[1][$i];
		}
		if (false !== is_array($res['cpu']['model'])) $res['cpu']['model'] = implode("<br />", $res['cpu']['model']);
		if (false !== is_array($res['cpu']['mhz'])) $res['cpu']['mhz'] = implode("<br />", $res['cpu']['mhz']);
		if (false !== is_array($res['cpu']['cache'])) $res['cpu']['cache'] = implode("<br />", $res['cpu']['cache']);
		if (false !== is_array($res['cpu']['bogomips'])) $res['cpu']['bogomips'] = implode("<br />", $res['cpu']['bogomips']);
	}
	// NETWORK
	// UPTIME
	if (false === ($str = @file("/proc/uptime"))) return false;
	$str = explode(" ", implode("", $str));
	$str = trim($str[0]);
	$min = $str / 60;
	$hours = $min / 60;
	$days = floor($hours / 24);
	$hours = floor($hours - ($days * 24));
	$min = floor($min - ($days * 60 * 24) - ($hours * 60));
	if ($days !== 0) $res['uptime'] = $days."天";
	if ($hours !== 0) $res['uptime'] .= $hours."小时";
	$res['uptime'] .= $min."分钟";
	
	// MEMORY
	if (false === ($str = @file("/proc/meminfo"))) return false;
	$str = implode("", $str);
	preg_match_all("/MemTotal\s{0,}\:+\s{0,}([\d\.]+).+?MemFree\s{0,}\:+\s{0,}([\d\.]+).+?Cached\s{0,}\:+\s{0,}([\d\.]+).+?SwapTotal\s{0,}\:+\s{0,}([\d\.]+).+?SwapFree\s{0,}\:+\s{0,}([\d\.]+)/s", $str, $buf);
	$res['memTotal'] = round($buf[1][0]/1024, 2);
	$res['memFree'] = round($buf[2][0]/1024, 2);
	$res['memCached'] = round($buf[3][0]/1024, 2);
	$res['memUsed'] = ($res['memTotal']-$res['memFree']);
	$res['memPercent'] = (floatval($res['memTotal'])!=0)?round($res['memUsed']/$res['memTotal']*100,2):0;
	$res['memRealUsed'] = ($res['memTotal'] - $res['memFree'] - $res['memCached']);
	$res['memRealPercent'] = (floatval($res['memTotal'])!=0)?round($res['memRealUsed']/$res['memTotal']*100,2):0;
	$res['swapTotal'] = round($buf[4][0]/1024, 2);
	$res['swapFree'] = round($buf[5][0]/1024, 2);
	$res['swapUsed'] = ($res['swapTotal']-$res['swapFree']);
	$res['swapPercent'] = (floatval($res['swapTotal'])!=0)?round($res['swapUsed']/$res['swapTotal']*100,2):0;
	// LOAD AVG
	if (false === ($str = @file("/proc/loadavg"))) return false;
	$str = explode(" ", implode("", $str));
	$str = array_chunk($str, 4);
	$res['loadAvg'] = implode(" ", $str[0]);
	return $res;
}

//FreeBSD系统探测
function sys_freebsd() {
	//CPU
	if (false === ($res['cpu']['num'] = get_key("hw.ncpu"))) return false;
	$res['cpu']['model'] = get_key("hw.model");
	//LOAD AVG
	if (false === ($res['loadAvg'] = get_key("vm.loadavg"))) return false;
	//UPTIME
	if (false === ($buf = get_key("kern.boottime"))) return false;
	$buf = explode(' ', $buf);
	$sys_ticks = time() - intval($buf[3]);
	$min = $sys_ticks / 60;
	$hours = $min / 60;
	$days = floor($hours / 24);
	$hours = floor($hours - ($days * 24));
	$min = floor($min - ($days * 60 * 24) - ($hours * 60));
	if ($days !== 0) $res['uptime'] = $days."天";
	if ($hours !== 0) $res['uptime'] .= $hours."小时";
	$res['uptime'] .= $min."分钟";
	//MEMORY
	if (false === ($buf = get_key("hw.physmem"))) return false;
	$res['memTotal'] = round($buf/1024/1024, 2);
	$str = get_key("vm.vmtotal");
	preg_match_all("/\nVirtual Memory[\:\s]*\(Total[\:\s]*([\d]+)K[\,\s]*Active[\:\s]*([\d]+)K\)\n/i", $str, $buff, PREG_SET_ORDER);
	preg_match_all("/\nReal Memory[\:\s]*\(Total[\:\s]*([\d]+)K[\,\s]*Active[\:\s]*([\d]+)K\)\n/i", $str, $buf, PREG_SET_ORDER);
	$res['memRealUsed'] = round($buf[0][2]/1024, 2);
	$res['memCached'] = round($buff[0][2]/1024, 2);
	$res['memUsed'] = round($buf[0][1]/1024, 2) + $res['memCached'];
	$res['memFree'] = $res['memTotal'] - $res['memUsed'];
	$res['memPercent'] = (floatval($res['memTotal'])!=0)?round($res['memUsed']/$res['memTotal']*100,2):0;
	$res['memRealPercent'] = (floatval($res['memTotal'])!=0)?round($res['memRealUsed']/$res['memTotal']*100,2):0;
	return $res;
}

//取得参数值 FreeBSD
function get_key($keyName) {
	return do_command('sysctl', "-n $keyName");
}

//确定执行文件位置 FreeBSD
function find_command($commandName) {
	$path = array('/bin', '/sbin', '/usr/bin', '/usr/sbin', '/usr/local/bin', '/usr/local/sbin');
	foreach($path as $p) {
		if (@is_executable("$p/$commandName")) return "$p/$commandName";
	}
	return false;
}

//执行系统命令 FreeBSD
function do_command($commandName, $args) {
	$buffer = "";
	if (false === ($command = find_command($commandName))) return false;
	if ($fp = @popen("$command $args", 'r')) {
		while (!@feof($fp)){
			$buffer .= @fgets($fp, 4096);
		}
		return trim($buffer);
	}
	return false;
}

//windows系统探测
function sys_windows() {
	if (PHP_VERSION >= 5) {
		$objLocator = new COM("WbemScripting.SWbemLocator");
		$wmi = $objLocator->ConnectServer();
		$prop = $wmi->get("Win32_PnPEntity");
	} else {
		return false;
	}
	//CPU
	$cpuinfo = GetWMI($wmi,"Win32_Processor", array("Name","L2CacheSize","NumberOfCores"));
	$res['cpu']['num'] = $cpuinfo[0]['NumberOfCores'];
	if (null == $res['cpu']['num']) {
		$res['cpu']['num'] = 1;
	}
	$res['cpu']['model'] = $res['cpu']['cache'] = '';
	for ($i=0;$i<$res['cpu']['num'];$i++){
		$res['cpu']['model'] .= $cpuinfo[0]['Name']."<br />";
		$res['cpu']['cache'] .= $cpuinfo[0]['L2CacheSize']."<br />";
	}
	// SYSINFO
	$sysinfo = GetWMI($wmi,"Win32_OperatingSystem", array('LastBootUpTime','TotalVisibleMemorySize','FreePhysicalMemory','Caption','CSDVersion','SerialNumber','InstallDate'));
	$sysinfo[0]['Caption']=iconv('GBK', 'UTF-8',$sysinfo[0]['Caption']);
	$sysinfo[0]['CSDVersion']=iconv('GBK', 'UTF-8',$sysinfo[0]['CSDVersion']);
	$res['win_n'] = $sysinfo[0]['Caption']." ".$sysinfo[0]['CSDVersion']." 序列号:{$sysinfo[0]['SerialNumber']} 于".date('Y年m月d日H:i:s',strtotime(substr($sysinfo[0]['InstallDate'],0,14)))."安装";
	//UPTIME
	$res['uptime'] = $sysinfo[0]['LastBootUpTime'];
	$sys_ticks = 3600*8 + time() - strtotime(substr($res['uptime'],0,14));
	$min = $sys_ticks / 60;
	$hours = $min / 60;
	$days = floor($hours / 24);
	$hours = floor($hours - ($days * 24));
	$min = floor($min - ($days * 60 * 24) - ($hours * 60));
	if ($days !== 0) $res['uptime'] = $days."天";
	if ($hours !== 0) $res['uptime'] .= $hours."小时";
	$res['uptime'] .= $min."分钟";
	//MEMORY
	$res['memTotal'] = $sysinfo[0]['TotalVisibleMemorySize'];
	$res['memFree'] = $sysinfo[0]['FreePhysicalMemory'];
	$res['memUsed'] = $res['memTotal'] - $res['memFree'];
	$res['memPercent'] = round($res['memUsed'] / $res['memTotal']*100,2);
	$swapinfo = GetWMI($wmi,"Win32_PageFileUsage", array('AllocatedBaseSize','CurrentUsage'));
	// LoadPercentage
	$loadinfo = GetWMI($wmi,"Win32_Processor", array("LoadPercentage"));
	$res['loadAvg'] = $loadinfo[0]['LoadPercentage'];
	return $res;
}

function GetWMI($wmi,$strClass, $strValue = array()) {
	$arrData = array();
	$objWEBM = $wmi->Get($strClass);
	$arrProp = $objWEBM->Properties_;
	$arrWEBMCol = $objWEBM->Instances_();
	foreach($arrWEBMCol as $objItem) {
		@reset($arrProp);
		$arrInstance = array();
		foreach($arrProp as $propItem) {
			eval("\$value = \$objItem->" . $propItem->Name . ";");
			if (empty($strValue)) {
				$arrInstance[$propItem->Name] = trim($value);
			} else {
				if (in_array($propItem->Name, $strValue)) {
					$arrInstance[$propItem->Name] = trim($value);
				}
			}
		}
		$arrData[] = $arrInstance;
	}
	return $arrData;
}
?>
<?php
//require(TZ_ROOT.'define.php');
ob_start();
$mytz = array(
	'name'	  => 'PHP探针 - Powered by OSDU.Net',
	'version' => 'V1.0.0',
	'url_1'   => 'OSDU.Net',
	'url_2'   => 'http://www.osdu.net/'
);

define('YES', '<span class="resYes">YES</span>');
define('NO',  '<span class="resNo">NO</span>');
define('ICON','<span class="icon">2</span>&nbsp;');

$phpSelf = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
define('PHPSELF', preg_replace('/(.{0,}?\/+)/', '', $phpSelf));
$PHPSELF = PHPSELF;
$time_start = microtime_float();

$valInt   = (false == empty($_POST['pInt']))?$_POST['pInt']:'未测试';
$valFloat = (false == empty($_POST['pFloat']))?$_POST['pFloat']:'未测试';
$valIo    = (false == empty($_POST['pIo']))?$_POST['pIo']:'未测试';
$mysqlReShow = $mailReShow = $funReShow = $opReShow = $sysReShow = "none";

$ioncube = extension_loaded('ionCube Loader');
$ffmpeg  = extension_loaded("ffmpeg");
$imagick = extension_loaded("imagick");

if (isset($_GET['act']) && $_GET['act'] == 'phpinfo'){
	phpinfo();
	exit();
}elseif(isset($_POST['act']) && $_POST['act'] == 'TEST_1'){
	$valInt = test_int();
}elseif(isset($_POST['act']) && $_POST['act'] == 'TEST_2'){
	$valFloat = test_float();
}elseif(isset($_POST['act']) && $_POST['act'] == 'TEST_3'){
	$valIo = test_io();
}elseif(isset($_POST['act']) && $_POST['act'] == '连接MySQL'){
	$mysqlReShow = 'show';
	$mysqlRe = 'MYSQL连接结果：';
	$mysqlRe .= (false !== @mysql_connect($_POST['mysqlHost'], $_POST['mysqlUser'], $_POST['mysqlPassword']))?'MYSQL服务器<font color="green">连接正常</font>, ':
	'MYSQL服务器<font color="red">连接失败</font>, ';
	$mysqlRe .= '数据库 <b>'.$_POST['mysqlDb'].'</b> ';
	$mysqlRe .= (false != @mysql_select_db($_POST['mysqlDb']))?'<font color="red">连接正常</font>':'<font color="red">连接失败</font>';
}elseif(isset($_POST['act']) && $_POST['act'] == '发送邮件'){
	$mailReShow = 'show';
	$mailRe = 'MAIL邮件发送测试結果：';
	$mailRe .= (false !== @mail($_POST['mailReceiver'], 'MAIL SERVER TEST', "This email is sent by ihotte.com.\r\n\r\iHotte.Com\r\nhttp://www.ihotte.com"))?'<font color="green">发送成功</font>':'<font color="red">发送失败</font>';
}elseif(isset($_POST['act']) && $_POST['act'] == '函数检测'){
	$funReShow = 'show';
	$funRe = '函数 <b>'.$_POST['funName'].'</b> 检测结果：'.isfun($_POST['funName']);
}elseif(isset($_POST['act']) && $_POST['act'] == '配置检测'){
	$opReShow = 'show';
	$opRe = '配置参数 <b>'.$_POST['opName'].'</b> 检测结果：'.getcon($_POST['opName']);
}


// 系統參數
if(SYS_INFO){
	switch(PHP_OS) {
		case "Linux":
			$sysReShow = (false !== ($sysInfo = sys_linux()))?"show":"none";
			break;
		case "FreeBSD":
			$sysReShow = (false !== ($sysInfo = sys_freebsd()))?"show":"none";
			break;
		case "WINNT":
			$sysReShow = (false !== ($sysInfo = sys_windows()))?"show":"none";
			break;
		default:
			break;
	}
}
?>
<?php
//require(TZ_ROOT.'header.php');
print <<<EOT
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>PHP探针 - Powered by ihotte.com</title>
<meta name="keywords" content="PHP探针" />
<style type="text/css">
<!--
body, div, p, ul, form, h1 { margin:0px; padding:0px; }
body { background:#252724; }
div, a, input { font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12px; color:#7D795E; }
div { margin-left:auto; margin-right:auto; width:960px; }
input { border: 1px solid #414340; background:#353734; }
a, #t3 a.arrow, #f1 a.arrow { text-decoration:none; color:#978F78; }
a:hover { text-decoration:underline; }
a.arrow { font-family:Webdings, sans-serif; color:#343525; font-size:10px; }
a.arrow:hover { color:#ff0000; text-decoration:none; }
.resYes { font-size: 9px; font-weight: bold; color: #33CC00; }
.resNo { font-size: 9px; font-weight: bold; color: #CC3300; }
.myButton { font-size:10px; font-weight:bold; background:#3D3F3E; border:1px solid #4A4C49; border-right-color:#2D2F2C; border-bottom-color:#2D2F2C; color:#978F78; }

table { clear:both; background:#2D2F2C; border:3px solid #41433E; margin-bottom:10px; }
td, th { padding:4px; background:#363835; }
th { background:#7E7860; color:#343525; text-align:left; }
th span { font-family:Webdings, sans-serif; font-weight:normal; padding-right:4px; }
th p { float:right; line-height:10px; text-align:right; }
th a { color:#343525; }
h1 { color:#009900; font-size:35px; width:300px; float:left; }
h1 b { color:#cc3300; font-size:50px; font-family: Webdings, sans-serif; font-weight:normal; }
h1 span { font-size:10px; padding-left:10px; color:#7D795E; }
#t12 { float:right; text-align:right; padding:15px 0px 30px 0px; }
#t12 a { line-height:18px; color:#7D795E; }
#t3 td { line-height:30px; height:30px; text-align:center; background:#3D3F3E; border:1px solid #4A4C49; border-right:none; border-bottom:none; }
#t3 th, #t3 th a { font-weight:normal; }
#m4 td { text-align:center; }
.th2 th, .th3 { background:#232522; text-align:center; color:#7D795E; font-weight:normal; }
.th3 { font-weight:bold; text-align:left; }
#footer table { clear:none; }
#footer td { text-align:center; padding:1px 3px; font-size:9px; }
#footer a { font-size:9px; }
#f1 { text-align:right; padding:15px; }
#f2 { float:left; border:1px solid #dddddd; }
#f2 td { background:#FF6600; }
#f2 a { color:#ffffff; }
#f3 { border: 1px solid #888888; float:right; }
#f3 a { color:#222222; }
#f31 { background:#2359B1; color:#FFFFFF; }
#f32 { background:#dddddd; }
#footer { padding: 15px 0; text-align: center; font-size: 11px; font-family: Tahoma, Verdana; }
.bar { border:1px solid #999999; background:#FFFFFF; height:5px; font-size:2px; width:600px; margin:2px 0 5px 0; padding:1px; overflow:hidden;}
.barli_red { background:#ff6600; height:5px; margin:0px; padding:0; }
.barli_blue { background:#0099FF; height:5px; margin:0px; padding:0; }
.barli_green { background:#36b52a; height:5px; margin:0px; padding:0; }
.barli { background:#36b52a; height:5px; margin:0px; padding:0; }
-->
</style>
</head>
<body>
<form method="post" action="{$PHPSELF}#bottom">
  <div>
    <input type="hidden" name="pInt" value="{$valInt}" />
    <input type="hidden" name="pFloat" value="{$valFloat}" />
    <input type="hidden" name="pIo" value="{$valIo}" />
    <!-- =============================================================
頁頭
============================================================= -->
    <h1><i><b>i</b>PhpTZ</i><span>V1.0</span></h1>
    <a name="top"></a>
    <p id="t12"><br />■ <a href="http://www.ihotte.com/" target="_blank">点击下载 iPhpTZ 探针, 或查看最新版本 </a> ■<br /></p>
    <table width="100%" border="0" cellspacing="1" cellpadding="0" id="t3">
      <tr>
        <td><a href="#sec1">服务器特性</a></td>
        <td><a href="#sec2">PHP基本特性</a></td>
        <td><a href="#sec3">PHP组件支持</a></td>
        <td><a href="#sec4">服务器性能测试</a></td>
        <td><a href="#sec5">自定义测试</a></td>
        <td><a href="{$PHPSELF}" class="t211">刷新</a></td>
        <td><a href="#bottom" class="arrow">66</a></td>
      </tr>
      <tr>
        <th colspan="7">
			<b>〖应用〗</b>
				<a href="./">WebAdmin</a>&nbsp;&nbsp;
				<a href="./MyFTP/" target="_blank">MyFTP</a>&nbsp;&nbsp;
				<a href="./MySQL/" target="_blank">MySQL</a>&nbsp;&nbsp;			
			<b>〖项目网站〗</b>
				<a href="http://www.ihotte.com/" target="_blank">Www.iHotte.Com</a>&nbsp;&nbsp;
				<a href="http://bbs.ihotte.com/" target="_blank">Bbs.iHotte.Com</a>&nbsp;&nbsp;
				<a href="http://meinv.ihotte.com/" target="_blank">Meinv.iHotte.Com</a>&nbsp;&nbsp;
		</th>
      </tr>
    </table>
EOT;
?>
<?php
//require(TZ_ROOT.'sys_tx.php');
echo '
<!-- ======================= 服务器参数 ==================== -->

<table width="100%" border="0" cellspacing="1" cellpadding="0">
  <tr>
    <th colspan="4"><p> <a href="#top" class="arrow">5</a> <br />
        <a href="#bottom" class="arrow">6</a> </p>
      <span>8</span>服务器特性 <a name="sec1" id="sec1"></a> </th>
  </tr>
  <tr>
    <td>服务器标识</td>
    <td colspan="3">',empty($sysInfo['win_n'])?@php_uname():$sysInfo['win_n'],'</td>
  </tr>
  <tr>
    <td>服务器时间</td>
    <td>',date('Y年n月j日 H:i:s'),'</td>
    <td>北京时间</td>
    <td>',gmdate('Y年n月j日 H:i:s',time()+8*3600),'</td>
  </tr>
  <tr>
    <td>服务器域名</td>
    <td>',$_SERVER['SERVER_NAME'],'</td>
    <td>服务器IP位址</td>
    <td>',isset($_SERVER['SERVER_ADDR'])?$_SERVER['SERVER_ADDR']:$_SERVER['SERVER_NAME'],'</td>
  </tr>
  <tr>
    <td>服务器操作系统';
    $os = explode(" ", php_uname());
echo '
    <td>',$os[0],'</td>
    <td>系统內核版本</td>
    <td>',$os[1],'</td>
  </tr>
  <tr>
    <td>服务器名称</td>
    <td>',$os[2],'</td>
    <td>服务器语言</td>
    <td>',getenv("HTTP_ACCEPT_LANGUAGE"),'</td>
  </tr>
  <tr>
    <td>服务器解析引擎</td>
    <td>',$_SERVER['SERVER_SOFTWARE'],'</td>
    <td>Web服务器端口</td>
    <td>',$_SERVER['SERVER_PORT'],'</td>
  </tr>
  <tr> </tr>
  <tr>
    <td>文档路径</td>
    <td>',dirname(__FILE__),'</td>
    <td>磁盘剩余空间</td>
    <td>',round((@disk_free_space(".")/(1024*1024)),2),'M</td>
  </tr>
  <tr>
    <td>服务器管理員</td>
    <td><a href="mailto:',$_SERVER['SERVER_ADMIN'],'"> ',$_SERVER['SERVER_ADMIN'],'</a></td>
    <td>系统平均负载</td>
    <td class="w_number"><span id="loadAvg">',(isset($sysInfo['loadAvg'])?$sysInfo['loadAvg']:'-1'),'</span></td>
  </tr>
</table>';
?>
<?php
function get_ea_info($name) { $ea_info = eaccelerator_info(); return $ea_info[$name]; }
function get_gd_info($name) { $gd_info = gd_info(); return $gd_info[$name]; }

echo '
<!-- =============================================================
PHP高级组件
============================================================== -->

<table width="100%" cellpadding="0" cellspacing="1" border="0">
  <tr>
    <th colspan="3"><p><a href="#top" class="arrow">5</a> <br />
        <a href="#bottom" class="arrow">6</a> </p>
      <span>8</span>PHP高级组件 <a name="sec3" id="sec3"></a> </th>
  </tr>
  <tr>
    <td width="180">Zend Optimizer</td>
    <td>',defined('OPTIMIZER_VERSION')?isfun('printf'):isfun('notfun'),'</td>
    <td>',defined('OPTIMIZER_VERSION')?OPTIMIZER_VERSION:'no','</td>
  </tr> 
  <tr>
    <td>eAccelerator</td>
    <td>',isfun('eaccelerator_info'),'</td>
    <td>',function_exists('eaccelerator_info')?get_ea_info('version'):'no','</td>
  </tr>
  <!--tr>
    <td>Memcache</td>
    <td>',isfun("dba_close"),'</td>
    <td>',isfun("dba_close"),'</td>
  </tr-->
  <tr>
    <td>MySQL</td>
    <td>',isfun('mysql_close'),'</td>
    <td>',function_exists('mysql_get_client_info')?mysql_get_client_info():'no','</td>
  </tr>
  <tr>
    <td>GD library</td>
    <td>',isfun('gd_info'),'</td>
    <td>',function_exists('get_gd_info')?get_gd_info('GD Version'):'no','</td>
  </tr>
</table>';
?>
<?php
if(SYS_INFO){
//require(TZ_ROOT.'sys_xn.php');
	echo '
	<!-- ======================= 服务器CPU及内存相关运行参数 ==================== -->
		<table width="100%" border="0" cellspacing="1" cellpadding="0">
		<tr>
			<th colspan="4"><p> <a href="#top" class="arrow">5</a> <br />
				<a href="#bottom" class="arrow">6</a> </p>
			<span>8</span>服务器CPU及内存相关运行参数 <a name="sec1" id="sec1"></a> </th>
		</tr>
		<tr>
			<td width="20%">服务器标识</td>
			<td colspan="5">',$sysInfo['win_n'],'</td>
		</tr>
		<tr>
			<td width="20%">CPU个数</td>
			<td width="30%">',$sysInfo['cpu']['num'],'</td>
			<td width="20%">服务器运行时间</td>
			<td width="30%">',$sysInfo['uptime'],'</td>
		</tr>
		<tr>
			<td>CPU型号</td>
			<td>',$sysInfo['cpu']['model'],'</td>
			<td>CPU二级缓存</td>
			<td>',$sysInfo['cpu']['cache'],'</td>
			<!--<td>系统Bogomips</td>
			<td>',$sysInfo['cpu']['bogomips'],'</td>-->
		</tr>
		<tr>
		<td width="20%">内存使用状况</td>
		<td colspan="5">';
    $temp = array(
		'memTotal', 'memUsed', 'memFree', 'memPercent',
		'memCached', 'memRealPercent',
		'swapTotal', 'swapUsed', 'swapFree', 'swapPercent'
    );
    foreach ($temp as $v) {
      	$sysInfo[$v] = isset($sysInfo[$v]) ? $sysInfo[$v] : 0;
    }
	 // var_dump($sysInfo);
	echo '
        物理内存：共 <font color="#CC0000">',$sysInfo['memTotal'],' M</font> ,
		已用 <font color="#CC0000"><span id="UsedMemory">',$sysInfo['memUsed'],' M</span></font> ,
		空闲 <font color="#CC0000"><span id="FreeMemory">',$sysInfo['memFree'],' M</span></font> ,
		使用率 <span id="memPercent">',$sysInfo['memPercent'],'%</span>
        <div class="bar" width="600px;"><div id="barmemPercent" class="barli_green" style="width:',(int)6*$sysInfo['memPercent'],'px" ></div></div>
        Cache化内存为 <span id="CachedMemory">',$sysInfo['memCached'],' M</span> ,
		真实内存使用率为 <span id="memRealPercent">',$sysInfo['memRealPercent'],'</span> %
        <div class="bar"  width="600px;"><div id="barmemRealPercent" class="barli_blue" style="width:',(int)6*$sysInfo['memRealPercent'],'px" >&nbsp;</div></div>

        SWAP区：共 ',$sysInfo['swapTotal'],' M ,
		已使用 <span id="swapUsed">',$sysInfo['swapUsed'],' </span> M,
		空闲 <span id="swapFree">',$sysInfo['swapFree'],' </span> M,
		使用率 <span id="swapPercent">',$sysInfo['swapPercent'],'</span> %
        <div class="bar" width="600px;"><div id="barswapPercent" class="barli_red" style="width:',(int)6*$sysInfo['swapPercent'],'px" >&nbsp;</div></div></td>
    </tr>
    </table>';
}
?>
<?php
//require(TZ_ROOT.'php_tx.php');
echo '
	<!-- =============================================================
	PHP基本特性
	============================================================== -->
	<table width="100%" cellpadding="0" cellspacing="1" border="0">
		<tr>
			<th colspan="4"><p> <a href="#top" class="arrow">5</a> <br /> <a href="#bottom" class="arrow">6</a> </p><span>8</span>PHP基本特性 <a name="sec2" id="sec2"></a> </th>
		</tr>
		<tr>
			<td width="35%">PHP信息（PHPINFO）</td>
			<td width="15%">',((false!==eregi("phpinfo",get_cfg_var("disable_functions")))?NO:"<a href='$phpSelf?act=phpinfo' target='_blank' class='static'>PHPINFO</a>"),'</td>
			<td width="30%">PHP版本</td>
			<td width="20%">',PHP_VERSION,'</td>
		</tr>
		<tr>
			<td>运行于安全模式</td>
			<td>',getcon("safe_mode"),'</td>
			<td>"&lt;?...?&gt;"短标签（short_open_tag）</td>
			<td>',getcon("short_open_tag"),'</td>
		</tr>
		<tr>
			<td>ZEND编译运行</td>
			<td>',((get_cfg_var("zend_optimizer.optimization_level")||get_cfg_var("zend_extension_manager.optimizer_ts")||get_cfg_var("zend_extension_ts")) ?YES:NO),'</td>
			<td>ioncube编译运行</td>
			<td>',($ioncube?"<span class='resYes'>".YES."</span>":"<span class='resNo'>".NO."</span>"),'</td>
		</tr>
		<tr>
			<td>Eaccelerator加速</td>
			<td>',((get_cfg_var("eaccelerator.allowed_admin_path")||get_cfg_var("eaccelerator.enable")||get_cfg_var("eaccelerator.optimizer")) ?YES:NO),'</td>
			<td>FFmpeg组件</td>
			<td>',$ffmpeg?"<span class='resYes'>".YES."</span>":"<span class='resNo'>".NO."</span>",'</td>
		</tr>
		<tr>
			<td>Imagick组件</td>
			<td>',$imagick?"<span class='resYes'>".YES."</span>":"<span class='resNo'>".NO."</span>",'</td>
			<td>打开远程文件（allow_url_fopen）</td>
			<td>',getcon("allow_url_fopen"),'</td>
		</tr>
		<tr>
			<td>显示错误信息（display_errors）</td>
			<td>',getcon("display_errors"),'</td>
			<td>自定义全局变量（register_globals）</td>
			<td>',getcon("register_globals"),'</td>
		</tr>
		<tr>
			<td>字符串自动转义（magic_quotes_runtime）</td>
			<td>',((1===get_magic_quotes_runtime())?YES:NO),'</td>
			<td>数据反斜杠转义（magic_quotes_gpc）</td>
			<td>',((1===get_magic_quotes_gpc())?YES:NO),'</td>
		</tr>
		<tr>
			<td>POST提交最大限制（post_max_size）</td>
			<td>',getcon("post_max_size"),'</td>
			<td>上传文件最大限制（upload_max_filesize）</td>
			<td>',getcon("upload_max_filesize"),'</td>
		</tr>
		<tr>
			<td>脚本超时时间（max_execution_time）</td>
			<td>',getcon("max_execution_time"),' 秒</td>
			<td>脚本占用最大内存（memory_limit）</td>
			<td>',getcon("memory_limit"),'</td>
		</tr>
		<tr>
			<td>PHP运行方式</td>
			<td>',strtoupper(php_sapi_name()),'</td>
			<td>被禁用的函数（disable_functions）</td>
			<td>',((""==($disFuns=get_cfg_var("disable_functions")))?"无":str_replace(",","<br />",$disFuns)),'</td>
		</tr>
	</table>';
?>
<?php
//require(TZ_ROOT.'php_zj.php');
echo '
<!-- =============================================================
PHP組件支援
============================================================== -->

<table width="100%" cellpadding="0" cellspacing="1" border="0">
  <tr>
    <th colspan="4"><p> <a href="#top" class="arrow">5</a> <br />
        <a href="#bottom" class="arrow">6</a> </p>
      <span>8</span>PHP组件支持 <a name="sec3" id="sec3"></a> </th>
  </tr>
  <tr>
    <td>Session支持</td>
    <td>',isfun("session_start"),'</td>
    <td>Socket支持</td>
    <td>',isfun("socket_accept"),'</td>
  </tr>
  <tr>
    <td width="38%">高精度数学运算 BCMath</td>
    <td width="12%">',isfun("bcadd"),'</td>
    <td>历法运算 Calendar</td>
    <td>',isfun("cal_days_in_month"),'</td>
  </tr>
  <tr>
    <td>IMAP电子邮件</td>
    <td>',isfun("imap_close"),'</td>
    <td>VMailMgr邮件处理</td>
    <td>',isfun("vm_adduser"),'</td>
  </tr>
  <tr>
    <td>FDF表单资料格式</td>
    <td>',isfun("fdf_get_ap"),'</td>
    <td>图形处理 GD Library</td>
    <td>',isfun("gd_info"),'</td>
  </tr>
  <tr>
    <td>LDAP目录协议</td>
    <td>',isfun("ldap_close"),'</td>
    <td>MCrypt加密处理</td>
    <td>',isfun("mcrypt_cbc"),'</td>
  </tr>
  <tr>
    <td>哈稀计算 MHash</td>
    <td>',isfun("mhash_count"),'</td>
    <td>FTP</td>
    <td>',isfun("ftp_login"),'</td>
  </tr>
  <tr>
    <td>PREL相容语法 PCRE</td>
    <td>',isfun("preg_match"),'</td>
    <td>PDF文档支持</td>
    <td>',isfun("pdf_close"),'</td>
  </tr>
  <tr>
    <td>SNMP网络管理</td>
    <td>',isfun("snmpget"),'</td>
    <td>WDDX支持</td>
    <td>',isfun("wddx_add_vars"),'</td>
  </tr>
  <tr>
    <td>压缩文档(Zlib)</td>
    <td>',isfun("gzclose"),'</td>
    <td>XML解析</td>
    <td>',isfun("xml_set_object"),'</td>
  </tr>
  <tr>
    <td>mSQL数据库</td>
    <td>',isfun("msql_close"),'</td>
    <td>ODBC数据库</td>
    <td>',isfun("odbc_close"),'</td>
  </tr>
  <tr>
  <tr>
    <td>Oracle数据库</td>
    <td>',isfun("ora_close"),'</td>
    <td>Oracle 8 数据库</td>
    <td>',isfun("OCILogOff"),'</td>
  </tr>
  <td>Postgre SQL数据库</td>
    <td>',isfun("pg_close"),'</td>
    <td>SyBase数据库</td>
    <td>',isfun("sybase_close"),'</td>
  </tr>
  <tr>
    <td>SQL Server数据库</td>
    <td>',isfun("mssql_close"),'</td>
    <td>MySQL数据库</td>
    <td>',isfun("mysql_close"),'</td>
  </tr>
  <tr>
    <td>dBase数据库</td>
    <td>',isfun("dbase_close"),'</td>
    <td>DBM数据库</td>
    <td>',isfun("dbmclose"),'</td>
  </tr>
  <tr>
    <td>Hyperwave数据库</td>
    <td>',isfun("hw_close"),'</td>
    <td>FilePro数据库</td>
    <td>',isfun("filepro_fieldcount"),'</td>
  </tr>
  <tr>
    <td>DBA数据库</td>
    <td>',isfun("dba_close"),'</td>
    <td>Informix数据库</td>
    <td>',isfun("ifx_close"),'</td>
  </tr>
</table>';
?>
<?php
//require(TZ_ROOT.'sys_cs.php');
print <<<EOT
<!-- =============================================================
服务器性能檢測
============================================================== -->

<table width="100%" cellpadding="0" cellspacing="1" border="0" id="m4">
  <tr>
    <th colspan="5"><p> <a href="#top" class="arrow">5</a> <br />
        <a href="#bottom" class="arrow">6</a> </p>
      <span>8</span>服务器性能检测<a name="sec4" id="sec4"></a> </th>
  </tr>
  <tr class="th2"  align="center">
    <td width="19%">参照对象</td>
    <td width="17%">整数运算能力检测<br>
      (1+1运算300万次)</td>
    <td width="17%">浮点运算能力检测<br>
      (圆周率开平方300万次)</td>
    <td width="17%">数据I/O能力检测<br>
      (读取10K文件1万次)</td>
    <td width="30%">CPU信息</td>
  </tr>
    <tr align="center">
    <td align="left"><a href="http://www.hostsir.com/" class="black" target="_blank">美国 IXwebhosting.com</a></td>
    <td>0.535秒</td>
    <td>1.607秒</td>
    <td>0.058秒</td>
    <td align="left">4 x Xeon E5530 @ 2.40GHz</td>
  </tr>
    <tr align="center">
    <td align="left"><a href="http://www.hostsir.com/" class="black" target="_blank">埃及 CitynetHost.com</a></td>
    <td>0.343秒</td>
    <td>0.761秒</td>
    <td>0.023秒</td>
    <td align="left">2 x Core2Duo E4600 @ 2.40GHz</td>
  </tr>
  <tr align="center">
    <td align="left"><a href="http://www.hostsir.com/" class="black" target="_blank">美国 PhotonVPS.com</a></td>
    <td>0.431秒</td>
    <td>1.024秒</td>
    <td>0.034秒</td>
    <td align="left">8 x Xeon E5520 @ 2.27GHz</td>
  </tr>
  <tr align="center">
    <td align="left"><a href="http://www.hostsir.com/" class="black" target="_blank">德国 SpaceRich.com</a></td>
    <td>0.421秒</td>
    <td>1.003秒</td>
    <td>0.038秒</td>
    <td align="left">4 x Core i7 920 @ 2.67GHz</td>
  </tr>
  <tr align="center">
    <td align="left"><a href="http://www.hostsir.com/" class="black" target="_blank">美国 RiZie.com</a></td>
    <td>0.521秒</td>
    <td>1.559秒</td>
    <td>0.054秒</td>
    <td align="left">2 x Pentium4 3.00GHz</td>
  </tr>

  <tr align="center">
    <td>本台服务器</td>
    <td><b> {$valInt} </b><br /><input type="submit" value="TEST_1" class="myButton"  name="act" /></td>
    <td><b> {$valFloat} </b><br /><input type="submit" value="TEST_2" class="myButton"  name="act" /></td>
    <td><b> {$valIo} </b><br /><input type="submit" value="TEST_3" class="myButton"  name="act" /></td>
	<td></td>
  </tr>
</table>
EOT;
?>
<?php
//require(TZ_ROOT.'php_diy.php');
$isMysql = (false !== function_exists("mysql_query"))?"":" disabled";
$isMail  = (false !== function_exists("mail"))?"":" disabled";
print <<<EOT
<!-- =============================================================
自定義檢測
============================================================== -->
<table width="100%" border="0" cellspacing="1" cellpadding="0">
  <tr>
    <th colspan="4"><p> <a href="#top" class="arrow">5</a> <br />
        <a href="#bottom" class="arrow">6</a> </p>
      <span>8</span>自定义检测 <a name="sec5" id="sec5"></a> </th>
  </tr>
  <tr>
    <th colspan="4" class="th3">MYSQL连接测试</th>
  </tr>
  <tr>
    <td>MYSQL服务器</td>
    <td><input type="text" name="mysqlHost" value="localhost" {$isMysql} /></td>
    <td> MYSQL用戶名 </td>
    <td><input type="text" name="mysqlUser" {$isMysql} /></td>
  </tr>
  <tr>
    <td> MYSQL用戶密码 </td>
    <td><input type="text" name="mysqlPassword" {$isMysql} /></td>
    <td> MYSQL数据库 </td>
    <td><input type="text" name="mysqlDb" />&nbsp;<input type="submit" class="myButton" value="连接MySQL" {$isMysql}  name="act" /></td>
  </tr>
EOT;
  if('show' == $mysqlReShow){ echo '<tr><td colspan="4">'.$mysqlRe.'</td></tr>';}
print <<<EOT
  <tr>
    <th colspan="4" class="th3">MAIL邮件发送测试</th>
  </tr>
  <tr>
    <td>收信地址</td>
    <td colspan="3">
		<input type="text" name="mailReceiver" size="50" {$isMail} />&nbsp;
		<input type="submit" class="myButton" value="发送邮件" {$isMail}  name="act" />&nbsp;
	</td>
  </tr>
EOT;
  if("show"==$mailReShow){ echo '<tr><td colspan="4">'.$mailRe.'</td></tr>';}
print <<<EOT
  <tr>
    <th colspan="4" class="th3">PHP函数支持</th>
  </tr>
  <tr>
    <td>函数名称</td>
    <td colspan="3"><input type="text" name="funName" size="50" />&nbsp;<input type="submit" class="myButton" value="函数检测" name="act" /></td>
  </tr>
EOT;
  if('show' == $funReShow){ echo '<tr><td colspan="4">'.$funRe.'</td></tr>';}
print <<<EOT
  <tr>
    <th colspan="4" class="th3">PHP配置參數</th>
  </tr>
  <tr>
    <td>参数名称</td>
    <td colspan="3"><input type="text" name="opName" size="40" />&nbsp;<input type="submit" class="myButton" value="配置检测" name="act" /></td>
  </tr>
EOT;
  if("show"==$opReShow){ echo '<tr><td colspan="4">'.$opRe.'</td></tr>';}
print <<<EOT
</table>
EOT;
?>
<?php
//require(TZ_ROOT.'footer.php');
echo '
<table  border="0" cellspacing="1" cellpadding="0" id="f3">
  <tr>
    <td id="f31">Powered by</td>
    <td id="f32"><a href="',$mytz['url_2'],'" target="_blank"><b>',$mytz['url_1'],'</b></a> </td></tr>
</table>
<a id="bottom"></a>
<div id="footer">
	&copy; ',date('Y',time()),' PHP探针 <a href="',$mytz['url_2'],'" target="_blank">',$mytz['url_1'],'</a> ',$mytz['version'],' All Rights Reserved.<br />
	Processed in ',sprintf('%0.4f', microtime_float() - $time_start),' seconds.  ',memory_usage(),' memory usage.
</div>
</div>
</form>
</body>
</html>';


?>
