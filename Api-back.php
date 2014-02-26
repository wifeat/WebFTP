<?php
// +----------------------------------------------------------------------
// | Copyright (C) 2008-2012 OSDU.Net    www.osdu.net    admin@osdu.net
// +----------------------------------------------------------------------
// | Licensed: ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author:   左手边的回忆 QQ:858908467 E-mail:858908467@qq.com
// +----------------------------------------------------------------------

//全局操作接口

//系统初始化
require(dirname(__FILE__).'/Init.php');
//记录初始时间
G('_run_start');

$module = isset($_REQUEST['module']) ? trim($_REQUEST['module']) : 'unknown';
$action = isset($_REQUEST['action']) ? trim($_REQUEST['action']) : 'unknown';
$isajax = isset($_REQUEST['isajax']) ? (bool)$_REQUEST['isajax'] : false;

define('MODULE_NAME', $module);
define('ACTION_NAME', $action);

//登陆检测
if(false == Auth::is_login()){
	if($isajax){
		return_json(301,'登陆超时或未登录！');
	}else{
		exit('<center><font color="red">登陆超时或未登录！</font></center>');
	}
}

//权限检测
if(false == Auth::is_allow()){
	if($isajax){
		return_json(300,'没有《'.strtoupper($module).'》模块操作权限,请联系系统管理员！');
	}else{
		exit('<center><font color="red">没有操作权限，请联系系统管理员！</font></center>');
	}
}

//模块检测
if(null == C('ALLOWED_MODULE.*')){
	if(null == C('ALLOWED_MODULE.'.$module)){
		if($isajax){
			return_json(300,'《'.strtoupper($module).'》模块被禁用,请联系系统管理员！');
		}else{
			exit('<center><font color="red">《'.strtoupper($module).'》模块被禁用,请联系系统管理员！</font></center>');
		}
	}
}

//
if(isset($_COOKIE['list_default_lang'])&&in_array($lan = trim($_COOKIE['list_default_lang']),array('gb2312','utf8'))){
	define('LANG_GBK', $lan=='gb2312'?true:false);
}else{
	define('LANG_GBK', true);
}
/*********************************** 系统核心模块 ************************************/
if('list' == $module){//文件列表
	if(!is_dir(C('ROOT_PATH'))){
		return_json(300,'无法访问初始根目录,请检查 "ROOT_PATH" 配置参数！');
	}
	$path = array();
	$path['root']    = str_replace('\\','/',realpath(trim(C('ROOT_PATH'))).'/');
	$path['current'] = str_replace('\\','/',realpath(u2g($_REQUEST['path']))).'/';
	$path['parent']  = str_replace('\\','/',realpath(dirname($path['current']))).'/';

	//返回数据
	$data = array();
	$data['path']['root']    = trim(C('ROOT_PATH'),'/').'/';
	$data['path']['current'] = $data['path']['root'].str_replace($path['root'], '', $path['current']);
	$data['path']['parent']  = (strlen($path['parent']) < strlen($path['root']))?($data['path']['current']):($data['path']['root'].str_replace($path['root'],'',$path['parent']));

	if(strlen($path['current']) < strlen($path['root'])){
		return_json(300,'Sorry, 你无权查看 '.C('ROOT_PATH').' 目录以外的文件！');
	}elseif(is_dir($path['current'])){
		$sdir = $sfile = $data['dirs'] = $data['files'] = array();
		File::show_dir($path['current'], $sdir, $sfile, 0);
		$data['statusCode'] = 200;
		$data['message'] = 'Success！';
		foreach($sdir as $val){
			$dir_arr_temp = array();
			$dir = $data['path']['current'].$val.'/';
			if(in_array($dir, C('LIST_CONF.DISPLAY_NOTALLOW'))){continue;}
			$dir_arr_temp = stat($dir);
			$dir_arr_temp['name']  = LANG_GBK?g2u($val):$val;
			$dir_arr_temp['chmod'] = substr(sprintf('%o', fileperms($dir)), -4);
			$dir_arr_temp['atime'] = date('Y-m-d H:i:s', $dir_arr_temp['atime']);
			$dir_arr_temp['mtime'] = date('Y-m-d H:i:s', $dir_arr_temp['mtime']);
			$dir_arr_temp['ctime'] = date('Y-m-d H:i:s', $dir_arr_temp['ctime']);
			$dir_arr_temp['size']  = 'no size';
			$data['dirs'][]        = $dir_arr_temp;
		}

		foreach($sfile as $val){
			$file_arr_temp = array();
			$file = $data['path']['current'].$val;
			if(in_array($file, C('LIST_CONF.DISPLAY_NOTALLOW'))){continue;}
			$file_arr_temp = stat($file);
			$file_arr_temp['name']  = LANG_GBK?g2u($val):$val;
			$file_arr_temp['chmod'] = substr(sprintf('%o', fileperms($file)), -4);
			$file_arr_temp['atime'] = date('Y-m-d H:i:s', $file_arr_temp['atime']);
			$file_arr_temp['mtime'] = date('Y-m-d H:i:s', $file_arr_temp['mtime']);
			$file_arr_temp['ctime'] = date('Y-m-d H:i:s', $file_arr_temp['ctime']);
			$file_arr_temp['ext']   = get_ext($file);
			$file_arr_temp['_size'] = $file_arr_temp['size'];
			$file_arr_temp['size']  = dealsize($file_arr_temp['size']);
			$data['files'][]        = $file_arr_temp;
		}

        //处理文件列表排序
		if(!empty($_POST['order'])){
			$order = explode('|', $_POST['order']);//type(name、size、ext、mtime)|sort(asc、desc)
			//目录排序
			if(0<count($data['dirs'])){
				$arr = array();
				foreach ($data['dirs'] as $key => $value){
					$arr['name'][$key]   = $value['name'];
					$arr['size'][$key]   = $value['size'];
					$arr['ext'][$key]    = $value['name'];
					$arr['mtime'][$key]  = $value['mtime'];
				}
				if('desc' == $order[1]){
					array_multisort($arr[$order[0]], SORT_DESC, $data['dirs']);
				}else{
					array_multisort($arr[$order[0]], SORT_ASC, $data['dirs']);
				}
			}
			//文件排序
			if(0<count($data['files'])){
				$arr = array();
				foreach ($data['files'] as $key => $value) {
					$arr['size'][$key]   = $value['_size'];
					$arr['name'][$key]   = $value['name'];
					$arr['ext'][$key]    = $value['ext'];
					$arr['mtime'][$key]  = $value['mtime'];
				}
				if('desc' == $order[1]){
					array_multisort($arr[$order[0]], SORT_DESC, $data['files']);
				}else{
					array_multisort($arr[$order[0]], SORT_ASC, $data['files']);
				}
			}
		}
		$data['path']['current'] = g2u($data['path']['current']);
		$data['path']['parent']  = g2u($data['path']['parent']);
		$data['runtime'] = G('_run_start','_run_end',6);
		exit(json_encode($data));
	}else{
		return_json(300,'Sorry,未知错误,无法打开你请求的目录:'.g2u($data['path']['current']).'！');
	}
//目录详情
}elseif('property' == $module){
        $data = array();
		$path  = trim($_REQUEST['path']);
        $info  = File::getProperty(u2g($path));
		$message  = '<font color="green">当前目录：</font><font color="red">'.$path.'</font><br />';
		$message .= '<font color="green">目录详情：</font><font color="red">共'.$info['dir'].' 个目录，'.$info['file'].' 个文件</font><br />';
		$message .= '<font color="green">读写类型：</font><font color="red">'.($info['readable']?'可读, ':'不可读, ').($info['writable']?'可写':'不可写').'</font><br />';
		$message .= '<font color="green">总计大小：</font><font color="red">'.dealsize($info['size']).'</font><br />';
		$message .= '<font color="green">执行耗时：</font><font color="red">'.G('_run_start','_run_end',6).' 秒</font><br />';
		return_json(200, $message);
}elseif('rename' == $module){
	$path    = trim($_POST['path'], '/').'/';
	$oldname = u2g($path.trim($_POST['oldname'], '/'));
	if(false == LANG_GBK){$oldname = g2u($oldname);}
	$newname = u2g($path.trim($_POST['newname'], '/'));
	$data = array();
    if(file_exists($newname) || false == file_exists($oldname)){
		$statusCode = 300;
		$message    = '<font color="blue">命名失败：</font><font color="red">源文件不存在或新文件名和已文件有冲突</font><br />';
	}else{
		if(rename($oldname, $newname)){
			$statusCode = 200;
			$message    = '<font color="green">原始名称：</font><font color="red">'.basename($_POST['oldname']).'</font><br />';
			$message   .= '<font color="green">新文件名：</font><font color="red">'.basename($_POST['newname']).'</font><br />';
		}else{
			$statusCode = 300;
			$message    = '<font color="blue">命名失败：</font><font color="red">错误原因未知</font><br />';
		}
	}
	$message   .= '<font color="green">执行耗时：</font><font color="red">'.G('_run_start','_run_end',6).' 秒</font><br />';
	return_json($statusCode, $message);
}elseif('downfile' == $module){//文件下载
    if('file' == $action){
		$file = u2g(trim($_GET['file']));
		if(is_file($file)){
			header('Content-type: application/force-download');
			header('Content-Disposition: attachment; filename='.basename($_GET['file']));
			header('Content-length: '.filesize($file));
			readfile($file);
			exit();
		}else{
			exit("<script>alert('文件不存在!!');</script>");
		}
	}elseif('dir' == $action){
		$arr = array();
			$dir = u2g(trim($_GET['dir'],'/').'/');
			$filename = basename($dir).'-'.date('m-d').'.zip';
			$tempfile = DATA_PATH.'Cache/'.$filename;
			if(is_dir($dir)){
				if(is_file($tempfile)){
					unlink($tempfile);
				}
			    include(INC_ROOT.'PclZip.class.php');
		        $Zip = new PclZip($tempfile);
			    if($Zip->create(array($dir), PCLZIP_OPT_REMOVE_PATH, dirname($dir).'/')){
			        header('Content-type: application/force-download');
			        header('Content-Disposition: attachment; filename='.$filename);
			        header('Content-length:'.filesize($tempfile));
					readfile($tempfile);
					unlink($tempfile);
			        exit();
				}else{
				    exit('<script>alert("下载失败!!");</script>');
				}
			}else{
			    exit("<script>alert('目录不存在!!');</script>");
			}
	}else{exit();}
//ZIP解压
}elseif('unzip' == $module){
	$statusCode = 300;
	$message = 'Sorry,免费版不支持文件解压，QQ：858908467！';
	return_json($statusCode, $message);
//ZIP压缩
}elseif('zip' == $module){
	$statusCode = 300;
	$message = 'Sorry,免费版不支持文件压缩，QQ：858908467！';
	return_json($statusCode, $message);

}elseif('delete' == $module){
	$path  = u2g(trim($_POST['path']));
	if(true == LANG_GBK){
		$files = explode('|',u2g(trim($_POST['files'])));
	}else{
		$files = explode('|',(trim($_POST['files'])));
	}

	$nfile = $ndir = 0; $message = '';
	foreach($files as $f){
		$fs = $path.$f;
		if(empty($f) || !file_exists($fs)){continue;}
		$info = array('dir'=>0,'file'=>0);$err = array('dir'=>array(),'file'=>array());
		if(is_dir($fs) && File::del_dir($fs.'/',$info,$err)){
			$ndir += $info['dirs'];	$nfile += $info['files'];
			foreach(array_merge($err['dirs'], $err['files']) as $val){
				$message.= '<font color="blue">删除失败：</font><font color="red"> '.g2u($val).'</font><br />';
			}
		}elseif(is_file($fs) && unlink($fs)){
			++$nfile;
		}else{
			$message .= '<font color="blue">删除失败：</font><font color="red"> '.g2u($f).'</font><br />';
		}
	}
	$message   .= '<font color="green">总计删除：</font><font color="red">'.$ndir.'目录，'.$nfile.'个文件</font><br />';
	$message   .= '<font color="green">执行耗时：</font><font color="red">'.G('_run_start','_run_end',6).' 秒</font><br />';
	return_json(200,$message);
//文件移动、复制、粘贴
}elseif('paste' == $module){
	$path_from  = u2g(trim($_POST['path_from'],'/').'/');
	$path_to    = u2g(trim($_POST['path_to'],'/').'/');
	$files      = explode('|',u2g(trim($_POST['files'])));
	$type       = trim($_POST['type']);
	$cover      = (bool)trim($_POST['cover']);
	if('cut' == $type){$cut = true;}else{$cut = false;}

	//极速模式
	if($cut && 1 == count($files) && !file_exists($path_to.$files[0])){
		$result   = rename($path_from.$files[0],$path_to.$files[0]);
		$message  = '<font color="green">极速移动：</font><font color="red">'.($result?'移动成功':'移动失败').'</font><br />';
		$message .= '<font color="green">执行耗时：</font><font color="red">'.G('_run_start','_run_end',6).' 秒</font><br />';
		return_json(200, $message);
	}
	//普通模式
	$temp = array('from'=>array(),'to'=>array()); $coverfiles = $info = $data = array();
	foreach($files as $val){
	    if(is_file($path_from.$val)){
		    $temp['from'][] = $path_from.$val;
			$temp['to'][]   = $path_to.$val;
		}elseif(is_dir($path_from.$val)){
		   	$temp['from'][]  = trim($path_from.$val,'/').'/';
			$temp['to'][]    = trim($path_to.$val,'/').'/';
		}else{}
	}
	File::copy($temp['from'],$temp['to'],$cover,$cut,$coverfiles, $info);
	$message  = '<font color="green">目录变更：</font><font color="red">'.g2u($path_from).'</font><font color="blue">  =>  </font><font color="red">'.g2u($path_to).'</font><br />';
	if (!$cover && is_array($coverfiles) && !empty($coverfiles)){
		foreach ($coverfiles as $i){
		    $statusCode = 201;
			$result = array('type'=>$type,'path_from'=>$path_from,'path_to'=>$path_to,'files'=>trim($_REQUEST['files']),'cover'=>$cover);
		    $coverfile = str_replace($path_from,'',$i);
			$message .= '<font color="blue">覆盖文件：</font><font color="red">'.g2u($coverfile).'</font><br />';
		}
	}else{
		$statusCode = 200;$result = array();
		$message .= '<font color="green">变更详情：</font><font color="red">共'.(($cut)?'移动':'复制').'目录'.$info['dirs'].'个,文件'.$info['files'].'个</font><br />';
		$message .= '<font color="green">总计大小：</font><font color="red">'.dealsize($info['size']).'</font><br />';
	}
	$message .= '<font color="green">执行耗时：</font><font color="red">'.G('_run_start','_run_end',6).' 秒</font><br />';
    return_json($statusCode,$message,$result);
//文件权限变更
}elseif('chmod' == $module){
	if('show' == $action){
		include(TPL_ROOT.'chmodfile.tpl.php');exit();
	}else{
		$files = explode('|',u2g(trim($_POST['files'])));
		$deep  = (bool)$_POST['deep'];
		include(INC_ROOT.'Chmod.conf.php');
		$chmod = trim($_POST['chmod']);
		$nfile = 0; $ndir = 0; $message = '';
        foreach($files as $f){
			if($deep && is_dir($f)){
				$info = $err = array();
				File::chmod(rtrim($f,'/').'/',get_chmod($chmod),$info,$err);
				$nfile += $info['files'];$ndir += $info['dirs'];
				foreach($err['dirs'] as $val)
					$message .= '<font color="blue">修改失败：</font><font color="red"> '.g2u($val).'</font><br />';
				foreach($err['files'] as $val)
					$message .= '<font color="blue">修改失败：</font><font color="red"> '.g2u($val).'</font><br />';
			}else{
				if(is_dir($f)){$ndir++;}else{$nfile++;}
				if(false == chmod($f, get_chmod($chmod))){
					$message .= '<font color="blue">修改失败：</font><font color="red"> '.g2u($f).'</font><br />';
				}
			}
		}
		$message   .= '<font color="green">权限变更：</font><font color="red"> 0'.$chmod.'</font><br />';
		$message   .= '<font color="green">总计修改：</font><font color="red">'.$ndir.'个目录，'.$nfile.'个文件</font><br />';
		$message   .= '<font color="green">执行耗时：</font><font color="red">'.G('_run_start','_run_end',6).' 秒</font><br />';
		return_json(200,$message);
	}
//新建目录、文件
}elseif('newbuild' == $module){
    $name = u2g(trim($_POST['path'], '/').'/'.trim($_POST['name']));
	$type = trim($_POST['type']);
	if('file' == $type){
		if(is_file($name)){
			$statusCode = 300;
			$message  = '<font color="blue">新建失败：</font><font color="red">文件已存在</font><br />';
		}else{
			file_put_contents($name, 'newfile at '.date('Y-m-d H:i:s'));
			if(fopen($name, 'a+')){
				$statusCode = 200;
				$message    = '<font color="green">新建成功：</font><font color="red">'.$_POST['name'].'</font><br />';
			}else{
				$statusCode = 300;
				$message    = '<font color="blue">新建失败：</font><font color="red">错误原因未知?</font><br />';
			}
		}
	}elseif('dir' == $type){
		if(is_dir($name)){
			$statusCode = 300;
			$message    = '<font color="blue">新建失败：</font><font color="red">目录已存在</font><br />';
		}else{
			if(mkdir($name, 0755)){
				$statusCode = 200;
				$message    = '<font color="green">新建成功：</font><font color="red">'.$_POST['name'].'</font><br />';
			}else{
				$statusCode = 300;
				$message    = '<font color="blue">新建失败：</font><font color="red">错误原因未知</font><br />';
			}
		}
	}else{$statusCode = 300;$message = 'Error:not exist this action!';}
	$message   .= '<font color="green">执行耗时：</font><font color="red">'.G('_run_start','_run_end',6).' 秒</font><br />';
	return_json($statusCode,$message);
//图片缩略预览
}elseif('imageview' == $module){
	include(INC_ROOT.'Thumb.class.php');
	$file = u2g(trim($_REQUEST['file']));
	$thumbFile = DATA_PATH.'Cache/'.substr(md5($file),2,12).'.jpg';
	$Thumb = new Thumb($file,120,100);
	if($Thumb->get()){
		$Thumb->show();
	}else{
		if(false === strpos($file,'Data/Cache/')){
			//$Thumb->create();
			$Thumb->show(DATA_PATH.'Public/'.'nothumb.jpg');
		}else{
			$Thumb->show($file);
		}
	}
	if(C('CACHE_DATA_DEL')){$Thumb->del();}
//文本编辑
}elseif('editfile' == $module){
    if('save' !== $action){
		include(TPL_ROOT.'editfile.tpl.php');
	}elseif('save' == $action){
		$file    = u2g($_POST['file']);
		$code    = urldecode(trim($_POST['code']));
		$charset = trim($_POST['charset']);
		$newname = trim($_POST['newname']);
		$data = array();
		$data['message']  = '<font color="green">目标文件：</font><font color="red">'.g2u($file).'</font><br />';
		if(file_exists($file)){
			$oldcharset = get_encode($file);
			if('UTF-8' == $charset && 'GB2312' == $oldcharset){
			}elseif('UTF-8' == $charset && 'UTF-8' == $oldcharset){
			}elseif('UTF-8' == $charset && 'UTF-8 BOM' == $oldcharset){
				$code = stripBOM($code);//处理UTF-8 BOM 文件头
			}elseif('GB2312' == $charset && 'GB2312' == $oldcharset){
			}elseif('GB2312' == $charset && 'UTF-8' == $oldcharset){
				$code = u2g($code);
			}elseif('GB2312' == $charset && 'UTF-8 BOM' == $oldcharset){
				$code = u2g($code);
			}
			if(!empty($newname)){
				$file = dirname($file).'/'.$newname;
				if(!file_exists($file)){
					$fp     = @fopen($file, 'w+');
					$result = file_put_contents($file, $code, LOCK_EX);
					if($fp && $result){
						$statusCode = 200;
						$message    = '<font color="green">保存成功：</font><font color="red">文件已经保存</font><br />';
					}else{
						$statusCode = 300;
						$message    = '<font color="blue">保存失败：</font><font color="red">错误原因未知</font><br />';
					}
				}else{
					$statusCode = 300;
					$message    = '<font color="blue">另存失败：</font><font color="red">'.g2u($file).'已存在</font><br />';
				}
			}elseif(empty($newname) && file_put_contents($file, $code, LOCK_EX)){
				$statusCode = 200;
				$message    = '<font color="green">保存成功：</font><font color="red">文件已经保存</font><br />';
				$message   .= '<font color="green">编码变更：</font><font color="red">'.$oldcharset.'=>'.$charset.'</font><br />';
			}else{
				$statusCode = 300;
				$message    = '<font color="blue">保存失败：</font><font color="red">错误原因未知</font><br />';
			}
		}else{
			$statusCode = 300;
			$message  = '<font color="green">保存失败：</font><font color="red">目标文件不存在</font><br />';
		}
		$message .= '<font color="green">执行耗时：</font><font color="red">'.G('_run_start','_run_end',6).' 秒</font><br />';
		return_json($statusCode,$message);
	}

//批量上传文件
}elseif('upload' == $module){
	include(TPL_ROOT.'upload.tpl.php');
}else{
	$message    = '<font color="green">错误命令：</font><font color="red">未知API</font><br />';
	$message   .= '<font color="green">执行耗时：</font><font color="red">'.G('_run_start','_run_end',6).' 秒</font><br />';
	return_json(300,$message);
}
?>
