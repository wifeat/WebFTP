<?php
// +----------------------------------------------------------------------
// | Copyright (C) 2008-2012 OSDU.Net    www.osdu.net    admin@osdu.net
// +----------------------------------------------------------------------
// | Licensed: ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author:   左手边的回忆 QQ:858908467 E-mail:858908467@qq.com
// +----------------------------------------------------------------------


function redirect($url){
		exit("<script type='text/javascript'>document.location.href = '{$url}';</script>");
}
function return_json($statusCode, $message='', $result=array()){
	$data = array(
		'statusCode' => $statusCode,
		'message'    => $message,
		'result'     => $result,
		'request'    => array('get' => $_GET, 'post' => $_POST, 'request' => $_REQUEST, 'cookie' => $_COOKIE)
	);
	exit(json_encode($data));
}
function u2g($str){
	return iconv('UTF-8', 'GB2312//IGNORE', $str);
}
function g2u($str){
	return iconv('GB2312', 'UTF-8//IGNORE', $str);
}


//去除UTF-8 BOM 文件头
function stripBOM($string){
	$string = trim($string);
	if(chr(239).chr(187).chr(191) == substr($string,0,3)){
		$string = substr($string,3);
	}
	return $string;
}


//文件大小格式化
function dealsize($size){
	$dna = array( 'Byte','KB','MB','GB', 'TB', 'PB');
	$did = 0;
	while($size >= 900){
		$size = round($size*100/1024)/100;
		$did++;
	}
	return $size.' '.$dna[$did];
}

//获取扩展名
function get_ext($filename){
	$ext = 'unknown';
	$arr = explode('.',basename($filename));
	if(isset($arr[count($arr)-1])){$ext = $arr[count($arr)-1];}
	return strtolower($ext);
}

//获取文件编码('UTF-8 BOM', 'UTF-8','GB2312','ASCII')
function get_encode($file){
	$string = file_get_contents($file);
	if($string){
		if(chr(239).chr(187).chr(191) == substr($string, 0, 3)){ return 'UTF-8 BOM';}
		if($string === iconv('UTF-8', 'UTF-8',  iconv('UTF-8', 'UTF-8', $string))){   return 'UTF-8';}
		if($string === iconv('UTF-8', 'ASCII',  iconv('ASCII', 'UTF-8', $string))){   return 'ASCII';}
		if($string === iconv('UTF-8', 'GB2312', iconv('GB2312', 'UTF-8', $string))){  return 'GB2312';}
	}
	return 'unknown';
}

function is_utf8($string){
		return preg_match('/^([\x09\x0A\x0D\x20-\x7E])+/xs', trim($string));
}

// 循环创建目录
function mk_dir($dir, $mode = 0777) {
    if(is_dir($dir) || mkdir($dir, $mode)) return true;
    if(!mk_dir(dirname($dir), $mode)) return false;
    return mkdir($dir, $mode);
}

// 获取配置值
function C($name=null, $value=null) {
    static $_config = array();
    // 无参数时获取所有
    if(empty($name)){
        return $_config;
	}

    // 优先执行设置获取或赋值
    if(is_string($name)){
		$name = strtolower($name);
        if(false === strpos($name, '.')) {
            if(is_null($value)){
                return isset($_config[$name]) ? $_config[$name] : null;
			}else{
				return $_config[$name] = $value;
			}
        }

        // 二、三维数组设置和获取支持
        $name = explode('.', $name);
		if(false === isset($name[2])){
			if(is_null($value)){
				return isset($_config[$name[0]][$name[1]]) ? $_config[$name[0]][$name[1]] : null;
			}else{
				return $_config[$name[0]][$name[1]] = $value;
			}
		}else{
			if(is_null($value)){
				return isset($_config[$name[0]][$name[1]][$name[2]]) ? $_config[$name[0]][$name[1]][$name[2]] : null;
			}else{
				return $_config[$name[0]][$name[1]][$name[2]] = $value;
			}
		}
    }
    //批量设置
    if(is_array($name)){
        return $_config = array_merge($_config, array_change_key_case($name, CASE_LOWER));
	}
	//避免非法参数
    return null;
}

// 记录和统计时间（微秒）
function G($start,$end='',$dec=3) {
    static $_info = array();
    if(!empty($end)) {
		//统计时间
        if(!isset($_info[$end])) {
            $_info[$end]   =  microtime(TRUE);
        }
        return number_format(($_info[$end]-$_info[$start]), $dec);
    }else{
        $_info[$start]  =  microtime(TRUE);//记录时间
    }
}

// 浏览器友好的变量输出
function dump($var, $echo=true, $label=null, $strict=true) {
    $label = ($label === null) ? '' : rtrim($label) . ' ';
    if(!$strict) {
        if(ini_get('html_errors')){
            $output = print_r($var, true);
            $output = '<pre>'.$label.htmlspecialchars($output, ENT_QUOTES).'</pre>';
        }else{
            $output = $label.print_r($var, true);
        }
    }else{
        ob_start();
        var_dump($var);
        $output = ob_get_clean();
        if (!extension_loaded('xdebug')) {
            $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);
            $output = '<pre>'.$label.htmlspecialchars($output, ENT_QUOTES).'</pre>';
        }
    }
    if($echo){
        echo($output);
    }else{
        return $output;
	}
	return null;
}

//数组保存到文件
function arr2file($filename, $arr=''){
	if(is_array($arr)){
		$con = var_export($arr,true);
	}else{
		$con = $arr;
	}
	$con = "<?php if(!defined('WebFTP')){die('Forbidden Access');};?>\n<?php\nreturn $con;\n?>";
	return file_put_contents($filename, $con);
}

//兼容转义字符处理
//set_magic_quotes_runtime(0);
ini_set("magic_quotes_runtime", 0); 
if(get_magic_quotes_gpc()) {
	function stripslashes_deep($value){
		$value = is_array($value) ? array_map('stripslashes_deep', $value) :  stripslashes($value);
		return $value;
	}
	$_POST    = array_map('stripslashes_deep', $_POST);
	$_GET     = array_map('stripslashes_deep', $_GET);
	$_COOKIE  = array_map('stripslashes_deep', $_COOKIE);
	$_REQUEST = array_map('stripslashes_deep', $_REQUEST);
}

//自定义错误处理
function error_handler_fun($errno, $errmsg, $errfile, $errline, $errvars){
	if(!C('LOG_EXCEPTION_RECORD')) return;
	$user_errors = array(E_USER_ERROR, E_USER_WARNING, E_USER_NOTICE);
    $errortype = array (
        E_ERROR              => 'EMERG',
        E_WARNING            => 'WARNING',//非致命的 run-time 错误。不暂停脚本执行。
        E_PARSE              => 'EMERG',//语法错误
        E_NOTICE             => 'NOTICE',//Run-time 通知。
        E_CORE_ERROR         => 'EMERG',
        E_CORE_WARNING       => 'WARNING',
        E_COMPILE_ERROR      => 'EMERG',
        E_COMPILE_WARNING    => 'WARNING',
        E_USER_ERROR         => 'EMERG',//致命的用户生成的错误。
        E_USER_WARNING       => 'WARNING',//非致命的用户生成的警告。
        E_USER_NOTICE        => 'NOTICE',//用户生成的通知。
        E_STRICT             => 'NOTICE',
        E_RECOVERABLE_ERROR  => 'EMERG',//可捕获的致命错误。
		'INFO'               => 'INFO',//信息: 程序输出信息
		'DEBUG'              => 'DEBUG',// 调试: 调试信息
		'SQL'                => 'SQL',// SQL：SQL语句
    );
	if(isset($errortype[$errno])){
		$error['type'] = $errortype[$errno];
	}else{
		$error['type'] = $errno;
	}
	if(!in_array($error['type'], explode(',',C('LOG_EXCEPTION_TYPE')))){return;}

	$err  = date('[ Y-m-d H:i:s (T) ]').'  ';
    $err .= $error['type'].':  ';
	$err .= $errmsg.'  ';
    $err .= $errfile.'  ';
    $err .= '第'.$errline.'行  ';

    $err .= "\n";

    $destination = DATA_PATH.'Logs/'.date('y_m_d').'.log';
	if(is_file($destination) && floor(C('LOG_FILE_SIZE')) <= filesize($destination) ){
		if(1 == C('LOG_SAVE_TYPE')){
			unlink($destination);
		}else{
			rename($destination,dirname($destination).'/'.time().'-'.basename($destination));
		}
	}
    error_log($err, 3, $destination);
}
?>