<?php if(!defined('WebFTP')){die('Forbidden Access');}?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>WebFTP</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script language="javascript" type="text/javascript" src="Static/js/webftp.jquery.js"></script>
<script language="javascript" type="text/javascript" src="Static/plugins/niceforms/niceforms.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="Static/plugins/niceforms/niceforms-default.css" />
</head>
<body>
<div id="container">
  <form action="vars.php" method="post" class="niceform">
    <fieldset>
    <dl>
      <dt>
        <label for="color">包含子目录:</label>
      </dt>
      <dd>
        <input type="radio" name="deep_chmod" id="deep_chmod_1" value="1"/>
        <label for="deep_1" class="opt">是</label>
        <input type="radio" name="deep_chmod" id="deep_chmod_0" value="0"  checked="checked"/>
        <label for="deep_0" class="opt">否</label>
      </dd>
    </dl>
    <dl>
      <dt>
        <label for="interests">所有者权限:</label>
      </dt>
      <dd>
        <input type="checkbox" onchange="reloadchmod();" name="chmod[possessor][read]" id="chmod_possessor_read" value="400" />
        <label for="read" class="opt">读取</label>
        <input type="checkbox" name="chmod[possessor][write]" id="chmod_possessor_write" value="200" />
        <label for="write" class="opt">写入</label>
        <input type="checkbox" name="chmod[possessor][run]" id="chmod_possessor_run" value="100" />
        <label for="run" class="opt">执行</label>
      </dd>
    </dl>
    <dl>
      <dt>
        <label for="interests">同组权限:</label>
      </dt>
      <dd>
        <input type="checkbox" name="chmod[group][read]" id="chmod_group_read" value="40" />
        <label for="read" class="opt">读取</label>
        <input type="checkbox" name="chmod[group][write]" id="chmod_group_write" value="20" />
        <label for="write" class="opt">写入</label>
        <input type="checkbox" name="chmod[group][run]" id="chmod_group_run" value="10" />
        <label for="run" class="opt">执行</label>
      </dd>
    </dl>
    <dl>
      <dt>
        <label for="interests">公共权限:</label>
      </dt>
      <dd>
        <input type="checkbox" name="chmod[public][read]" id="chmod_public_read" value="4" />
        <label for="read" class="opt">读取</label>
        <input type="checkbox" name="chmod[public][write]" id="chmod_public_write" value="2" />
        <label for="write" class="opt">写入</label>
        <input type="checkbox" name="chmod[public][run]" id="chmod_public_run" value="1" />
        <label for="run" class="opt">执行</label>
      </dd>
    </dl>
      <dt>
        <label for="interests">数值化权限:</label>
      </dt>
      <dd>
        <input type="text" name="num_chmod" id="num_chmod" size="20" maxlength="120" />
      </dd>
    </dl>

    </fieldset>
  </form>
</div>
<script type="text/javascript">

function get_chmod_num(){
   return parseInt($('#num_chmod').val(), 10);
}
function set_chmod_deep($chmod){
    $chmod = $chmod || 777;
	var $chmod_possessor = parseInt($chmod/100);
	var $chmod_group     = parseInt($chmod/10%10);
	var $chmod_public    = parseInt($chmod%10);
	switch($chmod_possessor){
	    case 0:break;
		case 1:$('#chmod_possessor_run').attr('checked', true);break;
		case 2:$('#chmod_possessor_write').attr('checked', true);break;
		case 3:$('#chmod_possessor_run').attr('checked', true);$('#chmod_possessor_write').attr('checked', true);break;
		case 4:$('#chmod_possessor_read').attr('checked', true);break;
		case 5:$('#chmod_possessor_run').attr('checked', true);$('#chmod_possessor_read').attr('checked', true);break;
		case 6:$('#chmod_possessor_write').attr('checked', true);$('#chmod_possessor_read').attr('checked', true);break;
		case 7:$('#chmod_possessor_run').attr('checked', true);$('#chmod_possessor_write').attr('checked', true);$('#chmod_possessor_read').attr('checked', true);break;
	}
	switch($chmod_group){
	    case 0:break;
		case 1:$('#chmod_group_run').attr('checked', true);break;
		case 2:$('#chmod_group_write').attr('checked', true);break;
		case 3:$('#chmod_group_run').attr('checked', true);$('#chmod_group_write').attr('checked', true);break;
		case 4:$('#chmod_group_read').attr('checked', true);break;
		case 5:$('#chmod_group_run').attr('checked', true);$('#chmod_group_read').attr('checked', true);break;
		case 6:$('#chmod_group_write').attr('checked', true);$('#chmod_group_read').attr('checked', true);break;
		case 7:$('#chmod_group_run').attr('checked', true);$('#chmod_group_write').attr('checked', true);$('#chmod_group_read').attr('checked', true);break;	
	}
	switch($chmod_public){
	    case 0:break;
		case 1:$('#chmod_public_run').attr('checked', true);break;
		case 2:$('#chmod_public_write').attr('checked', true);break;
		case 3:$('#chmod_public_run').attr('checked', true);$('#chmod_public_write').attr('checked', true);break;
		case 4:$('#chmod_public_read').attr('checked', true);break;
		case 5:$('#chmod_public_run').attr('checked', true);$('#chmod_public_read').attr('checked', true);break;
		case 6:$('#chmod_public_write').attr('checked', true);$('#chmod_public_read').attr('checked', true);break;
		case 7:$('#chmod_public_run').attr('checked', true);$('#chmod_public_write').attr('checked', true);$('#chmod_public_read').attr('checked', true);break;		
	}
	
}
function get_chmod_deep(){
    var $deep = 0;
    $("input[name='deep_chmod']").each(function(){
	     $this = $(this);
        if($this.attr("checked")){
              $deep = $this.val();
		}
    }) 
	return $deep;
}
$(function(){
    setInterval(function(){
	    var chmod = 0;
        $("input[id^='chmod']").each(function(){
            $this = $(this);
	            if($this.attr('checked')){
                    chmod = chmod+parseInt($this.val()); 
				}          			  
        }); 
		if(chmod < 10){chmod = '000' +chmod;}else if(chmod < 100){chmod = '00' +chmod;}else{chmod = '0' +chmod;}
		$('#num_chmod').val(chmod);		
    },100);
	<?php if(isset($_REQUEST['chmod'])){echo 'set_chmod_deep('.(int)$_REQUEST['chmod'].')';};?>
});
</script>
</body>
</html>
