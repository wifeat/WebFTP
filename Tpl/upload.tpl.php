<?php
if(!defined('WebFTP')){die('Forbidden Access');}

function filekzm($a){
		$c=strrchr($a,'.');
		if($c){return $c;}else{return '';}
}

// 获取GET
function getGet($v){
		if(isset($_GET[$v])){return $_GET[$v];}else{return '';}
}

// 获取POST
function getPost($v){
		if(isset($_POST[$v])){return $_POST[$v];}else{return '';}
}

//$type = (isset($_REQUEST['type']) && !empty($_REQUEST['type']))?$_REQUEST['type']:'show';
if('save' == $action){
	mb_http_input("utf-8");
	mb_http_output("utf-8");
	//---------------------------------------------------------------------------------------------
	//组件设置a.MD5File为2，3时 的实例代码
	if(getGet('access2008_cmd')=='2'){ // 提交MD5验证后的文件信息进行验证
		getGet("access2008_File_name");// 	'文件名
		getGet("access2008_File_size");//	'文件大小，单位字节
		getGet("access2008_File_type");//	'文件类型 例如.gif .png
		getGet("access2008_File_md5");//	'文件的MD5签名

		die('0'); //返回命令  0 = 开始上传文件， 2 = 不上传文件，前台直接显示上传完成
	}
	if(getGet('access2008_cmd')=='3'){ //提交文件信息进行验证
		getGet("access2008_File_name");// 	'文件名
		getGet("access2008_File_size");//	'文件大小，单位字节
		getGet("access2008_File_type");//	'文件类型 例如.gif .png

		die('0'); //返回命令 0 = 开始上传文件,1 = 提交MD5验证后的文件信息进行验证, 2 = 不上传文件，前台直接显示上传完成
	}
	//---------------------------------------------------------------------------------------------

	$type=get_ext($_FILES["Filedata"]["name"]);
	$uploadfile = u2g(trim(urldecode($_REQUEST['path']),'/').'/'.$_FILES["Filedata"]["name"]);
	if ((in_array('*', C('UPLOAD_CONF.UPLOAD_ALLOW_TYPE'))||in_array($type, C('UPLOAD_CONF.UPLOAD_ALLOW_TYPE'))) && ($_FILES["Filedata"]["size"] < C('UPLOAD_CONF.UPLOAD_MAX_SIZE')*1024)){
		if ($_FILES["Filedata"]["error"] > 0){
		    echo '<div class="notification attention png_bg"><div><span style="float:left;">上传失败: </span>'.$_FILES["Filedata"]["name"].'！</div></div>';
			echo '<div class="notification error png_bg"><div><span style="float:left;">错误信息: </span>'.$_FILES["Filedata"]["error"] .'！</div></div>';
		    exit();
		}else{
			$file = array();
			$file['msg_attention'] = '<div class="notification attention png_bg"><div><span style="float:left;">上传失败: </span>'.$_FILES["Filedata"]["name"].'</div></div>';
			$file['msg_success_normal']  = '<div class="notification success png_bg"><div><span style="float:left;">上传成功: </span>'.$_FILES["Filedata"]["name"].'</div></div>';
			$file['msg_success_cover']   = '<div class="notification attention png_bg"><div><span style="float:left;">上传成功: </span>'.$_FILES["Filedata"]["name"].' 已覆盖</div></div>';

			$file['file_type']     = '<span style="float:left;">文件类型: </span>'.$type.'<br />';
			$file['file_size']     = '<span style="float:left;">文件大小: </span>'.dealsize($_FILES["Filedata"]["size"]).'<br />';
			//$file['file_md5']      = '<span style="float:left;">MD5 校验 : </span>'.getGet("access2008_File_md5").'<br />';
			$file['info']          = '<div class="notification information png_bg"><div>'.$file['file_type'].$file['file_size'].'</div></div>';

			$file['msg_error_exist']   = '<div class="notification error png_bg"><div><span style="float:left;">错误信息: </span>'.$_FILES["Filedata"]["name"] .'文件已存在</div></div>';
			$file['msg_error_cover']   = '<div class="notification error png_bg"><div><span style="float:left;">错误信息: </span>覆盖上传失败</div></div>';
			$file['msg_error_md5']     = '<div class="notification error png_bg"><div><span style="float:left;">错误信息: </span>文件MD5校验失败</div></div>';
			$file['msg_error_unknow']  = '<div class="notification error png_bg"><div><span style="float:left;">错误信息: </span>未知错误</div></div>';
			$file['msg_error_notallow']= '<div class="notification error png_bg"><div><span style="float:left;">错误信息: </span>请检查文件类型和文件大小是否符合标准</div></div>';

			/*
			if(getGet("access2008_File_md5") !== md5_file($_FILES["Filedata"]["tmp_name"])){
				echo $file['msg_attention'],$file['info'];
				exit($file['msg_error_md5']);
			}*/

			if(file_exists($uploadfile) && !(bool)$_REQUEST['cover']){
				exit($file['msg_attention'].$file['info'].$file['msg_error_exist']);
			}elseif(file_exists($uploadfile) && (bool)$_REQUEST['cover']){
				if(unlink($uploadfile) && move_uploaded_file($_FILES["Filedata"]["tmp_name"], $uploadfile)){
					exit($file['msg_success_cover'].$file['info']);
				}else{
					exit($file['msg_attention'].$file['info'].$file['msg_error_cover']);
				}
			}else{
			    if(move_uploaded_file($_FILES["Filedata"]["tmp_name"], $uploadfile)){
					exit($file['msg_success_normal'].$file['info']);
				}else{
					exit($file['msg_attention'].$file['info'].$file['msg_error_unknow']);
				}
			}
		}
	}else{
		echo '<div class="notification attention png_bg"><div><span style="float:left;">上传失败: </span>'.$_FILES["Filedata"]["name"].'！</div></div>';
		echo '<div class="notification error png_bg"><div><span style="float:left;">错误信息: </span>请检查文件类型、大小是否符合要求！</div></div>';
		exit();
	}
}
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="zh_cn" lang="zh_cn">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>多文件上传组件</title>
<style type="text/css">
/*************** Notifications ***************/


* html .uploadmsg { width:460px; overflow:hidden; }
*+html .uploadmsg { width:460px; overflow:hidden; }
.notification { border-radius: 6px 6px 6px 6px; position: relative; margin: 0 0 15px 0; padding: 0; border: 1px solid; background-position: 10px 11px !important; background-repeat: no-repeat !important; font-size: 13px; width: 99.8%; }
.attention { background: #fffbcc url('static/images/ico/exclamation.png') 10px 11px no-repeat; border-color: #e6db55; color: #666452; }
.information { background: #dbe3ff url('static/images/ico/information.png'); border-color: #a2b4ee; color: #585b66; }
.success { background: #d5ffce url('static/images/ico/tick_circle.png'); border-color: #9adf8f; color: #556652; }
.error { background: #ffcece url('static/images/ico/cross_circle.png'); border-color: #df8f8f; color: #665252; }
.notification div { display:block; font-style:normal; padding: 10px 10px 10px 36px; line-height: 1.5em; }
.notification .close { color:#990000; font-size:9px; position:absolute; right:5px; top:5px; }
</style>
</head>
<body bgcolor="#ffffff" style="text-align:center;">
<script language="JavaScript" type="text/javascript"> 
<!-- 
function getCookie(name){ 
	var strCookie=document.cookie; 
	var arrCookie=strCookie.split("; "); 
	for(var i=0;i<arrCookie.length;i++){ 
		var arr=arrCookie[i].split("="); 
		if(arr[0]==name)return arr[1]; 
	} 
	return ''; 
} 
//--> 
</script>
<script language="javascript">
//文件大小格式化
function dealsize(size){
	var danwei = ['Byte','KB','MB','GB' ];
	var d = 0;
	while( size >= 900 ){
		size = round(size*100/1024)/100;
		d++;
	}
	return size+' '+danwei[d];
}

  function challs_flash_update(){ //Flash 初始化函数
  	var a={};
  	//定义变量为Object 类型

  	a.title = "上传文件"; //设置组件头部名称

  	a.FormName = "Filedata";
  	//设置Form表单的文本域的Name属性

  	a.url="Api.php";
  	//设置服务器接收代码文件

  	a.parameter="";
  	//设置提交参数，以GET形式提交

  	a.typefile=[
			"All File (*.*)","*.*;", 
			"Web File(*.html,*.htm,*.zip, ...)","*.html;*.htm;*.php;*.asp;*.js;*.css;*.gif;*.png;*.jpg;*.jpeg;*.zip;",
			"Script File(*.php,*.js,*css,...)","*.php;*.asp;*.js;*.css;",
			"Images File(*.gif,*.png,*.jpg,...)","*.gif;*.png;*.jpg;*.jpeg;*.bmp;"
	];
  	//设置可以上传文件 数组类型
  	//"Images (*.gif,*.png,*.jpg)"为用户选择要上载的文件时可以看到的描述字符串,
  	//"*.gif;*.png;*.jpg"为文件扩展名列表，其中列出用户选择要上载的文件时可以看到的 Windows 文件格式，以分号相隔
  	//2个为一组，可以设置多组文件类型

  	a.UpSize=<?php echo C('UPLOAD_CONF.upload_total_size')/1024;?>;
  	//可限制传输文件总容量，0或负数为不限制，单位MB

  	a.fileNum=<?php echo intval(C('UPLOAD_CONF.file_num'));?>;
  	//可限制待传文件的数量，0或负数为不限制

  	a.size=<?php echo C('UPLOAD_CONF.upload_max_size')/1024;?>;
  	//上传单个文件限制大小，单位MB，可以填写小数类型

  	a.FormID=['module','action','path','cover','<?php echo Session::name();?>','<?php echo C('COOKIE_PREFIX');?>username','<?php echo C('COOKIE_PREFIX');?>tokey'];
  	//设置每次上传时将注册了ID的表单数据以POST形式发送到服务器
  	//需要设置的FORM表单中checkbox,text,textarea,radio,select项目的ID值,radio组只需要一个设置ID即可
  	//参数为数组类型，注意使用此参数必须有 challs_flash_FormData() 函数支持

  	a.autoClose=-1;
  	//上传完成条目，将自动删除已完成的条目，值为延迟时间，以秒为单位，当值为 -1 时不会自动关闭，注意：当参数CompleteClose为false时无效

  	a.CompleteClose=false;
  	//设置为true时，上传完成的条目，将也可以取消删除条目，这样参数 UpSize 将失效, 默认为false

  	a.repeatFile=true;
  	//设置为true时，可以过滤用户已经选择的重复文件，否则可以让用户多次选择上传同一个文件，默认为false

  	a.returnServer=true;
  	//设置为true时，组件必须等到服务器有反馈值了才会进行下一个步骤，否则不会等待服务器返回值，直接进行下一步骤，默认为false

  	a.MD5File = 0;
  	//设置MD5文件签名模式，参数如下 ,注意：FLASH无法计算超过100M的文件,在无特殊需要时，请设置为0
  	//0为关闭MD5计算签名
  	//1为直接计算MD5签名后上传
  	//2为计算签名，将签名提交服务器验证，在根据服务器反馈来执行上传或不上传
  	//3为先提交文件基本信息，根据服务器反馈，执行MD5签名计算或直接上传，如果是要进行MD5计算，计算后，提交计算结果，在根据服务器反馈，来执行是否上传或不上传

  	a.loadFileOrder=true;
  	//选择的文件加载文件列表顺序，TRUE = 正序加载，FALSE = 倒序加载

  	a.mixFileNum=1;
  	//至少选择的文件数量，设置这个将限制文件列表最少正常数量（包括等待上传和已经上传）为设置的数量，才能点击上传，0为不限制

  	a.ListShowType = 1;
  	//文件列表显示类型：1 = 传统列表显示，2 = 缩略图列表显示（适用于图片专用上传）

  	a.InfoDownRight = "等待上传：%1%个  已上传：%2%个";
  	//右下角统计信息的文本设置,文本中的 %1% = 等待上传数量的替换符号，%2% = 已经上传数量的替换符号

  	a.TitleSwitch = true;
  	//是否显示组件头部

  	a.ForceFileNum=0;
  	//强制条目数量，已上传和待上传条目相加等于为设置的值（不包括上传失败的条目），否则不让上传, 0为不限制，设置限制后mixFileNum,autoClose和fileNum属性将无效！

  	a.autoUpload = false;
  	//设置为true时，用户选择文件后，直接开始上传，无需点击上传，默认为false;

  	a.adjustOrder = true;
  	//设置为true时，用户可以拖动列表，重新排列位置

  	a.deleteAllShow = true;
  	//设置是否显示，全部清除按钮

  	return a ;
  	//返回Object
  }

   //每次上传完成调用的函数，并传入一个Object类型变量，包括刚上传文件的大小，名称，上传所用时间,文件类型
  function challs_flash_onComplete(a){
  	var name=a.fileName; //获取上传文件名
  	var size=dealsize(a.fileSize); //获取上传文件大小，单位字节
  	var time=a.updateTime; //获取上传所用时间 单位毫秒
  	var type=a.fileType; //获取文件类型，在 Windows 上，此属性是文件扩展名。
	var msg = '';//dealsize(size)
	msg += '<div class="notification success png_bg"><div>';
	msg += '<span style="float:left;">上传耗时: </span>'+time;
	msg += '</div></div>';
	//document.getElementById('show').innerHTML += msg;

  }

  //获取服务器反馈信息事件
  function challs_flash_onCompleteData(a){
  	//document.getElementById('show').innerHTML+='<font color="#ff0000">服务器端反馈信息：</font><br />'+a+'<br />';
    document.getElementById('show').innerHTML+= a+'<br />';
  }
   //开始一个新的文件上传时事件,并传入一个Object类型变量，包括刚上传文件的大小，名称，类型
  function challs_flash_onStart(a){
  	var name=a.fileName; //获取上传文件名
  	var size=a.fileSize; //获取上传文件大小，单位字节
  	var type=a.fileType; //获取文件类型，在 Windows 上，此属性是文件扩展名。
  	//document.getElementById('show').innerHTML += '&nbsp;&nbsp;'+name+'>>开始上传！<br />';

  	return true; //返回 false 时，组件将会停止上传
  }

  //上传文件列表全部上传完毕事件,参数 a 数值类型，返回上传失败的数量
  function challs_flash_onCompleteAll(a){
    var msg = '<br /><br /><div class="notification success png_bg"><div><span style="float:left;">上传完毕: </span>上传失败'+a+'个！</div></div>';
  	document.getElementById('show').innerHTML += msg;
  }

  function challs_flash_deleteAllFiles(){ //清空按钮点击时，出发事件
  	//返回 true 清空，false 不清空
  	//return confirm("你确定要清空列表吗?");
	return true;
  }

  //上传文件发生错误事件，并传入一个Object类型变量，包括错误文件的大小，名称，类型
  function challs_flash_onError(a){
  	var err=a.textErr; //错误信息
  	var name=a.fileName; //获取上传文件名
  	var size=a.fileSize; //获取上传文件大小，单位字节
  	var type=a.fileType; //获取文件类型，在 Windows 上，此属性是文件扩展名。
	var msg = '';
	msg += '<br /><br /><div class="notification success png_bg"><div>';
	msg += '<span style="float:left;">上传失败: </span>'+name;
	msg += '<span style="float:left;">文件类型: </span>'+type;
	msg += '<span style="float:left;">文件大小: </span>'+size;
	msg += '<span style="float:left;">错误信息: </span>'+err;
	msg += '</div></div>';
  	//document.getElementById('show').innerHTML += msg;
  }

  // 使用FormID参数时必要函数
  function challs_flash_FormData(a){
  	try{
  		var value = '';
  		var id=document.getElementById(a);
  		if(id.type == 'radio'){
  			var name = document.getElementsByName(id.name);
  			for(var i = 0;i<name.length;i++){
  				if(name[i].checked){
  					value = name[i].value;
  				}
  			}
  		}else if(id.type == 'checkbox'){
  			var name = document.getElementsByName(id.name);
  			for(var i = 0;i<name.length;i++){
  				if(name[i].checked){
  					if(i>0) value+=",";
  					value += name[i].value;
  				}
  			}
  		}else{
  			value = id.value;
  		}
  		return value;
  	}catch(e){
  		return '';
  	}
  }

  function challs_flash_style(){ //组件颜色样式设置函数
  	var a = {};

  	/*  整体背景颜色样式 */
  	a.backgroundColor=['#f6f6f6','#f3f8fd','#dbe5f1'];	//颜色设置，3个颜色之间过度
  	a.backgroundLineColor='#5576b8';					//组件外边框线颜色
  	a.backgroundFontColor='#066AD1';					//组件最下面的文字颜色
  	a.backgroundInsideColor='#FFFFFF';					//组件内框背景颜色
  	a.backgroundInsideLineColor=['#e5edf5','#34629e'];	//组件内框线颜色，2个颜色之间过度
  	a.upBackgroundColor='#ffffff';						//上翻按钮背景颜色设置
  	a.upOutColor='#000000';								//上翻按钮箭头鼠标离开时颜色设置
  	a.upOverColor='#FF0000';							//上翻按钮箭头鼠标移动上去颜色设置
  	a.downBackgroundColor='#ffffff';					//下翻按钮背景颜色设置
  	a.downOutColor='#000000';							//下翻按钮箭头鼠标离开时颜色设置
  	a.downOverColor='#FF0000';							//下翻按钮箭头鼠标移动上去时颜色设置

  	/*  头部颜色样式 */
  	a.Top_backgroundColor=['#e0eaf4','#bcd1ea']; 		//颜色设置，数组类型，2个颜色之间过度
  	a.Top_fontColor='#245891';							//头部文字颜色


  	/*  按钮颜色样式 */
  	a.button_overColor=['#FBDAB5','#f3840d'];			//鼠标移上去时的背景颜色，2个颜色之间过度
  	a.button_overLineColor='#e77702';					//鼠标移上去时的边框颜色
  	a.button_overFontColor='#ffffff';					//鼠标移上去时的文字颜色
  	a.button_outColor=['#ffffff','#dde8fe']; 			//鼠标离开时的背景颜色，2个颜色之间过度
  	a.button_outLineColor='#91bdef';					//鼠标离开时的边框颜色
  	a.button_outFontColor='#245891';					//鼠标离开时的文字颜色

  	/* 文件列表样式 */
  	a.List_scrollBarColor="#000000"						//列表滚动条颜色
  	a.List_backgroundColor='#EAF0F8';					//列表背景色
  	a.List_fontColor='#333333';							//列表文字颜色
  	a.List_LineColor='#B3CDF1';							//列表分割线颜色
  	a.List_cancelOverFontColor='#ff0000';				//列表取消文字移上去时颜色
  	a.List_cancelOutFontColor='#D76500';				//列表取消文字离开时颜色
  	a.List_progressBarLineColor='#B3CDF1';				//进度条边框线颜色
  	a.List_progressBarBackgroundColor='#D8E6F7';		//进度条背景颜色
  	a.List_progressBarColor=['#FFCC00','#FFFF00'];		//进度条进度颜色，2个颜色之间过度

  	/* 错误提示框样式 */
  	a.Err_backgroundColor='#C0D3EB';					//提示框背景色
  	a.Err_LineColor='#5D7CBB';							//提示框边框线景色
  	a.Err_cancelOverColor='#0066CC';					//提示框取消按钮移上去时颜色
  	a.Err_cancelOutColor='#FF0000';						//提示框取消按钮离开时颜色
  	a.Err_fontColor='#245891';							//提示框文字颜色
  	return a;
  }
  
  var isMSIE = (navigator.appName == "Microsoft Internet Explorer");
  function thisMovie(movieName){
  	if(isMSIE){
  		return window[movieName];
  	}else{
  		return document[movieName];
  	}
  }
</script>
<form name="Filedata" type="POST">
  <div class="notification attention png_bg">
    <div><span style="float:left;">同名文件处理：</span>
      <input name="cover" type="radio" value="1" id="cover" checked>
      覆盖同名文件&nbsp;&nbsp;
      <input name="cover" type="radio" value="0" >
      保留原始文件 </div>
  </div>
  </div>
  <input name="<?php echo Session::name();?>" id="<?php echo Session::name();?>" type="hidden" value="<?php echo Session::id();?>">
  <input name="<?php echo C('COOKIE_PREFIX');?>username" id="<?php echo C('COOKIE_PREFIX');?>username" type="hidden" value="">
  <input name="<?php echo C('COOKIE_PREFIX');?>tokey" id="<?php echo C('COOKIE_PREFIX');?>tokey" type="hidden" value="">
  <input name="module" id="module" type="hidden" value="upload">
  <input name="action" id="action" type="hidden" value="save">
  <input name="path" id="path" type="hidden" value="<?php echo urlencode($_REQUEST['path']);?>">
</form>
<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=10,0,0,0" width="460" height="280" id="update" align="middle">
  <param name="allowFullScreen" value="false" />
  <param name="allowScriptAccess" value="always" />
  <param name="movie" value="upload.swf" />
  <param name="quality" value="high" />
  <param name="bgcolor" value="#ffffff" />
  <embed src="upload.swf" quality="high" bgcolor="#ffffff" width="480" height="280" name="update" align="middle" allowscriptaccess="always" allowfullscreen="false" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
</object>
<div class="uploadmsg" id="show">
  <div style="margin-top:20px; width:400px; text-align:left;"></div>
</div>
<script type="text/javascript">
document.getElementById('<?php echo C('COOKIE_PREFIX');?>username').value = getCookie('<?php echo C('COOKIE_PREFIX');?>username');
document.getElementById('<?php echo C('COOKIE_PREFIX');?>tokey').value = getCookie('<?php echo C('COOKIE_PREFIX');?>tokey');
</script>
</body>
</html>