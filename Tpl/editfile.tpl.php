<?php
if(!defined('WebFTP')){die('Forbidden Access');}

function get_language($ext){
	$language = 'text';
	$lans = C('EDIT_CONF.EDIT_ALLOW_TYPE');
	foreach($lans as $lan => $exts){
		foreach($exts as $ex){
			if($ext == $ex){
				$language = $lan;
				break 2;
			}
		}
	}
	return $language;
}
$file             = array();
$file['file_utf'] = trim($_REQUEST['file']);
$file['file']     = u2g($file['file_utf']);
$file['content']  = file($file['file']);

$file['encode']   = get_encode($file['file']);

$file['line']     = count($file['content']);
$file['size']     = dealsize(filesize($file['file']));
$file['chmod']    = substr(sprintf('%o', @fileperms($file['file'])), -4);
$file['language'] = get_language(get_ext($file['file']));
if('GB2312' == $file['encode']){
	$file['encode_selected']['UTF-8']  = '';
	$file['encode_selected']['GB2312'] = 'selected="selected"';
}else{
	$file['encode_selected']['UTF-8']  = 'selected="selected"';
	$file['encode_selected']['GB2312'] = '';
}

//
?>
<?php
$textarea = array();
$textarea['main']['width']  = C('EDIT_CONF.EDITOR_CONF.WIDTH')-35;
$textarea['main']['height'] = C('EDIT_CONF.EDITOR_CONF.HEIGHT')-250;
$textarea['edit']['width']  = $textarea['main']['width']  - 10;
$textarea['edit']['height'] = $textarea['main']['height'] - 10;

$textarea['language'] = $file['language']?$file['language']:'text';
$textarea['content'] = '';
foreach($file['content'] as $key => $val){
	if('UTF-8' == $file['encode'] || 'UTF-8 BOM' == $file['encode']){
		$textarea['content'] .= htmlspecialchars($val);
	}elseif('GB2312' == $file['encode']){
		$textarea['content'] .= g2u(htmlspecialchars($val));
	}else{
		$textarea['content'] .= htmlspecialchars($val);
	}
}
?>
<?php
print <<<END
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>WebFTP 编辑器</title>
<link href="Static/css/edit.css" media="screen" rel="stylesheet" type="text/css" />
<script type="text/javascript">
var editfile   = '{$file['file_utf']}';
var mainwidth  = '{$textarea['main']['width']}';
var mainheight = '{$textarea['main']['height']}';
</script>
<script src="Static/plugins/codepress/codepress.js" type="text/javascript"></script>
<script src="Static/js/webftp.jquery.js" type="text/javascript"></script>

<link type="text/css" rel="stylesheet" href="Static/plugins/asyncbox/skins/Ext/asyncbox.css"  />
<script type="text/javascript" src="Static/plugins/asyncbox/AsyncBox.v1.4.js"></script>

<script src="Static/js/webftp.editor.js" type="text/javascript"></script>
<style type="text/css">
.editor_bottom{ padding: 0px; clear: both; height: 60px; width: 800px; margin-top: 5px; margin-right: auto; margin-bottom: 0px; margin-left: auto; }
</style>
</head>
<body class="logged_out windows env-production">
<div style="position: absolute; top: -1200px; left: -1200px;" id="loading"><img src="Static/images/edit/loading.gif" /></div>
<div id="slider" style="width:{$textarea['main']['width']}px;">
  <div class="frames">
    <div id="files">
      <div class="file">
        <div class="meta">
          <div class="info"><span class="icon"><img alt="Txt" height="16" src="Static/images/edit/txt.png" width="16" /></span><span class="mode" title="File Mode" style="line-hight:60px;">{$file['chmod']}</span><span>行数:{$file['line']}</span><span>大小:{$file['size']}</span><span>编码:{$file['encode']}</span></div>
          <ul class="actions"><li></li><li></li><li></li></ul>
        </div>
        <div class="data type-php"><textarea id="codepress2" class="codepress {$textarea['language']} linenumbers-on" style="width:{$textarea['edit']['width']}px;height:{$textarea['edit']['height']}px;" wrap="off">{$textarea['content']}</textarea></div>
      </div>
    </div>
  </div>
</div>
<div class="editor_bottom">
<form name="editfile" id="editfile">
文件另存为：<input name="newname" id="newname" value="" />
保存文件编码:<select name="charset" id="charset">
<option value="GB2312" {$file['encode_selected']['GB2312']}>GB2312</option>
<option value="UTF-8" {$file['encode_selected']['UTF-8']}>UTF-8</option>
</select>
强制编码重载:<select name="reloadCharSet" id="reloadCharSet" onchange="reloadEditFile();">
<option value="GB2312">GB2312</option>
<option value="UTF-8" selected="selected">UTF-8</option>
</select>
</form>
</div>
</body>
</html>
END;
?>