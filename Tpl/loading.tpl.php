<?php if(!defined('INC_ROOT')){die('Forbidden Access');} ?>
<div id="pageloading" style="left:0px;top:0;position:absolute;margin:0;z-index:2050;border:0px;width:99%;height:99%;background-color:#eeeeee;filter:Alpha(Opacity=30);opacity:0.7"> </div>
<div id='loading_div' style="left:0px;width:170px;height:80px;top:-100px;position:absolute;z-index:3001;border:0px;border:2px solid #4499ee;background-color:#ffffff;">
  <table border=0 width=100% height=100% >
    <tr height=100% width=100%>
      <td valign=middle align=right width=35%><img src="static/images/main/loading.gif" border=0 /> </td>
      <td valign=middle align=left width=65%> 程序加载中... </td>
    </tr>
  </table>
</div>
<script>
window.nav = new function()
{
	this.isOpera=(window.opera&&navigator.userAgent.match(/opera/gi))?true:false;
	this.isIE=(!this.isOpera&&document.all&&navigator.userAgent.match(/msie/gi))?true:false;
	this.isSafari=(!this.isIE&&navigator.userAgent.match(/safari/gi))?true:false;
	this.isGecko=(!this.isIE&&navigator.userAgent.match(/gecko/gi))?true:false;
	this.isFirefox=(!this.isIE&&navigator.userAgent.match(/firefox/gi))?true:false;
}
var ptime = 10;
var timehandle;
function $$(id) { return document.getElementById(id); }
window.onloading = 1;
var toppx = parseInt(600/2)-70;
function loading(){
    div_move(-100,ptime,false);
		setTimeout("loadingerror()",20000);
	if (!nav.isIE ){
		window.onloading = 0;
		$$('pageloading').style.display = "none";
		$$('loading_div').style.display = "none";
	}else{
		div_move(-100,ptime,false);
		setTimeout("loadingerror()",20000);
	}
		
}

function div_move(top,pausetime,shang)
{
	var steppx = parseInt(Math.abs(top-window.toppx)*15)/100;
	if (steppx == 0 ) steppx = 0.2;
	//alert(steppx);
	if (shang && top<=-500)
	{
		$$('pageloading').style.display = "none";
		$$('loading_div').style.display = "none";
		return;
	}
	if (!shang && top>=window.toppx){return;}
	$$('loading_div').style.top = top;
	var nexttop = shang?top-steppx:top+steppx;
	var shang = shang?"true":"false";
	var evalstr = "div_move("+nexttop+","+pausetime+","+shang+");";
	window.timehandle = setTimeout(evalstr,pausetime);
}

function unloading(){
	if (window.onloading){div_move(window.toppx,window.ptime,1);}
	window.onloading = 0;
}


window.onload = loading;
setTimeout("loadingerror()",1000*20);

$$('loading_div').style.left = parseInt(document.body.clientWidth/2)-85;

function loadingerror(){
	if (window.onloading) {
       alert('加载时出错, 尝试刷新本页', '加载失败');
	   unloading();
	}
}
</script>