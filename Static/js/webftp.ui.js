/*!
// +----------------------------------------------------------------------
// | Copyright (C) 2008-2012 OSDU.Net    www.osdu.net    admin@osdu.net
// +----------------------------------------------------------------------
// | Licensed: ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author:   左手边的回忆 QQ:858908467 E-mail:858908467@qq.com
// +----------------------------------------------------------------------
*/

WebFTP.UI = {
	name:'浩天 WebFTP',
	version:'V1.0.0',
	poweredby:'浩天科技(iHotte.Com)',
	intro:'UI库'
};


WebFTP.UI.Config = {

};

WebFTP.UI.Init = function(){
	this.List();
	this.Footer();
    this.Gotop();
	this.MainMenu();
	this.ContextMenu();

	this.Main_Menu();
	this.Dir_Menu();
	this.File_Menu();

	this.Toolbar();
	this.Show();
};

WebFTP.UI.Waitme = function(){
	$('#loading').css('display','block');return;
};
WebFTP.UI.Waitmeoff = function(){
	$('#loading').css('display','none');return;
};

//界面刷新
WebFTP.UI.Refresh = function(reload) {
    var reload = reload || false;
	if(reload){
		WebFTP.Api.Opendir(WebFTP.Config.path.current,{reload:true});
	}else{
		this.Init();
		this.Waitmeoff();
	}
};
//WebFTP.Api.Opendir

//显示文件目录 List 列表模式
WebFTP.UI.Display = function(json) {
	if(200 != json.statusCode){
		var message = json.message || '加载出错,如开启本地缓存,浏览器需支持Flash';
		asyncbox.error(message,WebFTP.poweredby);return;
	}
    this.Pathmenu(json.path);//生成路径菜单
	if(!WebFTP.Config.list.list_view_on){
		this.Display2(json);return;
	}
	var html_dirs  = html_files = '';
	//返回上级目录
	  html_dirs += '<tr class="">';
      html_dirs += '  <td><input class="dir-disabled" name="dir-disabled" type="checkbox" value="" disabled /></td>';
      html_dirs += '  <td><span class="ext ext_folder_go"></span></td>';
      html_dirs += '  <td><a href="javascript:WebFTP.Api.Opendir(\'' + json.path.parent + '\');" class="js-slide-to">返回上级目录</a></td>';
	  html_dirs += '  <td>修改时间</td>  <td>文件大小</td>  <td>文件权限</td>';
	  html_dirs += '  <td align="center"  colspan="3">相关操作</td>';
      html_dirs += '</tr>';
	//文件夹列表
	if(0<json.dirs.length){
	  $.each(json.dirs,function(idx,item){
	    html_dirs += '<tr class="">';
        html_dirs += '  <td><input class="dir-checkbox-id" name="dir-checkbox" type="checkbox" value="'+json.path.current + item.name+'" /></td>';
        html_dirs += '  <td><span class="ext ext_folder_open"></span></td>';
        html_dirs += '  <td><a href="javascript:WebFTP.Api.Opendir(\'' + json.path.current + item.name + '\')" id="dir-id-'+ idx+'" dirname="'+ item.name +'" dirpath="'+ json.path.current +'" chmod="'+item.chmod+'">' + item.name + '</a></td>';
        html_dirs += '  <td>' + item.mtime + '</td><td>' + item.size + '</td><td>' + item.chmod + '</td>';
		html_dirs += '  <td><a href="javascript:WebFTP.Api.Download(2,{},\''+item.name+'\')">下载</a></td><td><a href="javascript:WebFTP.Api.Del(2,{},\''+item.name+'\')">删除</a></td><td><a href="javascript:WebFTP.Api.Zip(2,{},\''+item.name+'\')">打包</a></td>';
        html_dirs += '</tr>';
      });
	}
	//文件列表
	if( 0 <json.files.length){
	 $.each(json.files,function(idx,item){
		if(0<=$.inArray(item.ext, ['jpg','jpeg','gif','png','bmp'])){
			var files_show = ' title="双击预览图片" rel="show" colortitle="图片名称：<font color=red>'+item.name+'&nbsp;&nbsp;&nbsp;&nbsp;</font>图片大小: <font color=red>'+item.size+'</font>"';
		}
	    html_files += '<tr class="">';
        html_files += '  <td><input class="file-checkbox-id" name="file-checkbox" type="checkbox" value="'+json.path.current + item.name+'" /></td>';
        html_files += '  <td><span class="ext ext_' + WebFTP.Util.GetExtClass(item.ext) + '"></span></td>';
        html_files += '  <td><a target="_blank" href="'+ json.path.current.replace(WebFTP.Config.path.root,WebFTP.Config.path.real) + item.name + '" id="file-id-'+ idx+'" filename="'+ item.name +'" filepath="'+ json.path.current +'" fileext="'+ item.ext +'" '+files_show+'  chmod="'+item.chmod+'">' + item.name + '</a></td>';
        html_files += '  <td>' + item.mtime + '</td><td>' + item.size + '</td><td>' + item.chmod + '</td>';
		html_files += '  <td><a href="javascript:WebFTP.Api.Download(1,{},\''+item.name+'\')">下载</a></td><td><a href="javascript:WebFTP.Api.Del(1,{},\''+item.name+'\')">删除</a></td><td><a href="javascript:WebFTP.Api.Zip(1,{},\''+item.name+'\')">打包</a></td>';
        html_files += '</tr>';
      });
	}

	/*************************** 数据写入 ***************************************/
	$('#list_main_center').html('')
	var apptools = $('#apptools').html();
	var table = '<table class="tree-browser" cellpadding="0" cellspacing="0"><tbody id="dirs-files-list"><th><font color="green">文件列表加载中...</font></th></tbody></table>';
	$('#list_main_center').html(apptools + table);
	$("tbody[id='dirs-files-list']").html(html_dirs + html_files);
	this.Refresh();
}
//显示文件目录 List 大图预览模式
WebFTP.UI.Display2 = function(json){
	//返回上级目录
	var view_body  = '';
	view_body += '<div>';
	view_body += '   <ol class="f_icon rounded"><a href="javascript:WebFTP.Api.Opendir(\'' + json.path.parent + '\')"><div class="ico_big ext_big_upto"></div></a></ol>';
	view_body += '   <ol class="f_name"><font color="green">返回上级目录</font></ol>';
	view_body += '</div>';

	//文件夹列表
	if( 0 <json.dirs.length){
		$.each(json.dirs,function(idx,item){
			var fmTitle = '目录名称: '+ item.name + '<br />目录大小: 0000<br />目录权限: '+ item.chmod + '<br />';
			fmTitle += '访问时间: '+ item.atime + '<br />修改时间: '+ item.mtime + '<br />改变时间: '+ item.ctime + '<br />';

			view_body += '<div style="position:relative;left:0px;top:0px;">';
			view_body += '   <ol class="f_icon rounded"><a href="javascript:WebFTP.Api.Opendir(\'' + json.path.current + item.name + '/\')" id="dir-id-'+ idx+'" dirname="'+ item.name +'" dirpath="'+ json.path.current +'" chmod="'+item.chmod+'"><div content="'+ fmTitle +'"  class="fmTitle ico_big ext_big_dir"></div></a></ol>';
			view_body += '   <ol class="f_name"><font color="blue">'+ item.name +'</font></ol>';
			view_body += '   <span style="position:absolute;left:10px;top:85px;"><input class="dir-checkbox-id" name="dir-checkbox" type="checkbox" value="'+json.path.current + item.name+'" /></span>';
			view_body += '</div>';
		});
	}
	//文件列表
	if( 0 <json.files.length){
	 $.each(json.files,function(idx,item){
        if(0<=$.inArray(item.ext, ['jpg','jpeg','gif','png','bmp'])){
			var files_show = ' title="双击预览图片" rel="show" colortitle="图片名称：<font color=red>'+item.name+'&nbsp;&nbsp;&nbsp;&nbsp;</font>图片大小: <font color=red>'+item.size+'</font>"';
		}
		var fmTitle = '';
		fmTitle += '文件名称: '+ item.name + '<br />文件大小: '+ item.size + '<br />文件权限: '+ item.chmod + '<br />文件类型: '+ item.ext + '<br />';
		fmTitle += '访问时间: '+ item.atime + '<br />修改时间: '+ item.mtime + '<br />改变时间: '+ item.ctime + '<br />';

		view_body += '<div style="position:relative;left:0px;top:0px;">';
		view_body += '   <ol class="f_icon rounded"><a target="_blank" href="'+json.path.current.replace(WebFTP.Config.path.root,WebFTP.Config.path.real) + item.name + '" id="file-id-'+ idx+'" filename="'+ item.name +'" filepath="'+ json.path.current +'" fileext="'+ item.ext +'" '+files_show+'  chmod="'+item.chmod+'"><div content="'+ fmTitle +'"  class="fmTitle ico_big ext_big_'+ WebFTP.Util.GetExtClass(item.ext) +'"></div></a></ol>';
		view_body += '   <ol class="f_name"><font color="red">'+ item.name +'</font></ol>';
		view_body += '   <span style="position:absolute;left:10px;top:85px;"><input class="file-checkbox-id" name="file-checkbox" type="checkbox" value="'+json.path.current + item.name+'" /></span>';
		view_body += '</div>';
      });
	}
	var view_head   = '<table id="view-dirs-files-list"><tr><td class="rhumbnail">';
	var view_foot   = '</td></tr></table>';

	//写入列表数据
	var apptools = $('#apptools').html();
	$('#list_main_center').html( apptools + view_head + view_body + view_foot );

	//图片预览
	if(WebFTP.Config.list.img_view_on){
		$('#list_main_center div').each(function(idx){
			var $show = $('ol:first', $(this)).find("a[rel='show']");
			var $file = $show.attr('filepath')+$show.attr('filename');
			$show.find('div').css('background-image', 'url("Api.php?module=imageview&file='+ encodeURI($file)+'")');
		});
	}
	this.Refresh();
}
//解析路径菜单
WebFTP.UI.Pathmenu = function(path){
	WebFTP.Config.path.root    = path.root;
	WebFTP.Config.path.parent  = path.parent;
	WebFTP.Config.path.current = path.current;
	var path_str = path.current.replace(path.root, '');
	var path_arr = path_str.split('/');
	var menu = '<span id="list_head_left"></span><span id="list_head_center">';
	menu += '当前目录：<a href="javascript:WebFTP.Api.Opendir(\''+path.root+'\');">根目录/</a>';
	var current_mulu = path.root;
	if (current_mulu.charAt(current_mulu.length-1) != '/'){current_mulu+='/';}
	$.each(path_arr, function(idx, mulu){
		if(mulu){
			current_mulu += mulu+'/';
			menu += '<a href="javascript:WebFTP.Api.Opendir(\''+ current_mulu +'\');">' + mulu + '/</a>';
		}
	});
	menu += '</span><span id="list_head_right">';
	$('#list_head').html(menu);
	this.Refresh();
};

//mainMenu
WebFTP.UI.MainMenu = function(){
	$('#main-menu').find('li').mouseover(function(){
         $(this).addClass('focus');
    });
    $('#main-menu').find('li').mouseout(function(){
         $(this).removeClass('focus');
    });
}

//处理 返回top
WebFTP.UI.Gotop = function(){
	var _top = $(window).scrollTop() + $(window).height() - 80;
	var _left = $(window).width() / 2 + 485;
	$('#js_go_top').css({
		top: _top + 'px',left: _left + 'px'
	}).click(function(){
		$(window).scrollTop(0);$(this).hide();
	});
	if(($(document).height() > $(window).height()) && $(window).scrollTop() > 0){
		$('#js_go_top').show();
	}else{
		$('#js_go_top').hide();
	}
}
//处理文件列表边框
WebFTP.UI.List = function(){
     var ch = $('#list_main_center').outerHeight();
	 $('#list_main_left').height(ch);
     $('#list_main_right').height(ch);
}
//处理footer
WebFTP.UI.Footer = function(){
  var html = '';
  html += '<a href="Readme/about.html" target="_blank">关于程序</a> - ';
  html += '<a href="javascript:alert(\'请联系QQ：858908467\');">加入我们</a> - ';
  html += '<a href="Readme/license.html" target="_blank">服务条款</a> - ';
  html += '<a href="Readme/help.html"  target="_blank">使用帮助</a> - ';
  html += '<a href="Readme/mytz.php" target="_blank">系统环境</a> - ';
  html += '<a href="mailto:858908467@qq.com" target="_self">意见反馈</a><br/>';
  html += 'CopyRight ©2011-2012 <a href="http://www.osdu.net/?webftp" target="_blank">OSDU.Net</a> All Rights Reserved.';
  $('#footer').html(html);
}

WebFTP.UI.Toolbar = function(){
	$('#tool_sort').hover(function(){$('#drop_sort').hide().show();},function(){});
	$('#drop_sort').hover(function(){},function(){$('#drop_sort').hide();});

	//排序类型
	if(!WebFTP.Util.Cookie('list_order_sort')){
		$('#list_order_sort_'+WebFTP.Config.list.order_sort).addClass('checked');
		WebFTP.Util.Cookie('list_order_sort', WebFTP.Config.list.order_sort, {expires: 3600*24});
	}else{
		$('#list_order_sort_'+WebFTP.Util.Cookie('list_order_sort')).addClass('checked');
	}
	$('.list_order_sort').click(function(){
		$('.list_order_sort').removeClass('checked');
		$(this).addClass('checked');
		WebFTP.Config.list.order_sort = $(this).attr('sort');
		WebFTP.Util.Cookie('list_order_sort', WebFTP.Config.list.order_sort, {expires: 3600*24});
		WebFTP.Api.Opendir(WebFTP.Config.path.current,{reload:true});
	});

	//排序方式
	if(!WebFTP.Util.Cookie('list_order_type')){
		$('#list_order_type_'+ WebFTP.Config.list.order_type).addClass('checked');
		WebFTP.Util.Cookie('list_order_type', WebFTP.Config.list.order_type, {expires: 3600*24});
	}else{
		$('#list_order_type_'+WebFTP.Util.Cookie('list_order_type')).addClass('checked');
	}
	$('.list_order_type').click(function(){
		$('.list_order_type').removeClass('checked');
		$(this).addClass('checked');
		WebFTP.Config.list.order_type = $(this).attr('type');
		WebFTP.Util.Cookie('list_order_type', WebFTP.Config.list.order_type, {expires: 3600*24});
		WebFTP.Api.Opendir(WebFTP.Config.path.current,{reload:true});
	});

	//语系处理
	if(!WebFTP.Util.Cookie('list_default_lang')){
		$('#default_lang_'+ WebFTP.Config.list.order_type).addClass('checked');
		WebFTP.Util.Cookie('list_default_lang', WebFTP.Config.lang, {expires: 3600*24});
	}else{
		$('#default_lang_'+WebFTP.Util.Cookie('list_default_lang')).addClass('checked');
	}
	$('.default_lang').click(function(){
		$('.default_lang').removeClass('checked');
		$(this).addClass('checked');
		WebFTP.Config.lang = $(this).attr('lang');
		WebFTP.Util.Cookie('list_default_lang', WebFTP.Config.lang, {expires: 3600*24});
		WebFTP.Api.Opendir(WebFTP.Config.path.current,{reload:true});
	});

}

WebFTP.UI.Show = function(){
	$("#list a[rel^='show']").live("click", function(event){
		$("#list a[rel^='show']").colorbox({
			slideshow:true,transition:"elastic", width:"80%", height:"90%",bgOpacity:0.5,preloading:true
		});
		//阻止第一次点击默认事件, colorbox在某些环境下有第一次点击无效的BUG
		if('show' == $(this).attr('rel')){
			event.preventDefault();
		}
	});
}

//初始化文本菜单域
WebFTP.UI.ContextMenu = function(){
  var main_menu = '';
  main_menu += '<ul id="myMainMenu" class="contextMenu">';
  main_menu += '<li class="main"><a href="#readme" action="#readme">系统菜单</a></li>';
  main_menu += '<li class="set"><a href="#set" action="#set">系统设置</a></li>';
  main_menu += '<li class="cut separator"><a href="#cut" action="#cut">剪切所选</a></li>';
  main_menu += '<li class="copy"><a href="#copy" action="#copy">复制所选</a></li>';
  main_menu += '<li class="paste"><a href="#paste" action="#paste">粘贴所选</a></li>';
  main_menu += '<li class="delete"><a href="#delete" action="#delete">删除所选</a></li>';
  main_menu += '<li class="zip separator"><a href="#zip" action="#zip">压缩文件</a></li>';
  main_menu += '<li class="search separator"><a href="#search" action="#search">搜索文件</a></li>';
  main_menu += '<li class="search"><a href="#chmod" action="#chmod">权限编辑</a></li>';
  main_menu += '<li class="download separator"><a href="#download" action="#download">打包下载</a></li>';
  main_menu += '<li class="selectall"><a href="#selectall" action="#selectall">全选反选</a></li>';
  main_menu += '</ul>';
  var dir_menu = '';
  dir_menu += '<ul id="myDirMenu" class="contextMenu">';
  dir_menu += '<li class="main"><a href="#readme" action="#readme">目录菜单</a></li>';
  dir_menu += '<li class="open separator"><a href="#open" action="#open">打开目录</a></li>';
  dir_menu += '<li class="rename"><a href="#rename" action="#rename">命名目录</a></li>';
  dir_menu += '<li class="cut  separator"><a href="#cut" action="#cut">剪切目录</a></li>';
  dir_menu += '<li class="copy"><a href="#copy" action="#copy">复制目录</a></li>';
  dir_menu += '<li class="delete"><a href="#delete" action="#delete">删除目录</a></li>';
  dir_menu += '<li class="search separator"><a href="#chmod" action="#chmod">权限编辑</a></li>';
  dir_menu += '<li class="zip separator"><a href="#zip" action="#zip">压缩目录</a></li>';
  dir_menu += '<li class="download"><a href="#download" action="#download">打包下载</a></li>';
  dir_menu += '</ul>';
  var file_menu = '';
  file_menu += '<ul id="myFileMenu" class="contextMenu">';
  file_menu += '<li class="main"><a href="#readme" action="#readme">文件菜单</a></li>';
  file_menu += '<li class="edit separator"><a href="#edit" action="#edit">编辑文件</a></li>';
  file_menu += '<li class="rename"><a href="#rename" action="#rename">命名文件</a></li>';
  file_menu += '<li class="cut  separator"><a href="#cut" action="#cut">剪切文件</a></li>';
  file_menu += '<li class="copy"><a href="#copy" action="#copy">复制文件</a></li>';
  file_menu += '<li class="delete"><a href="#delete" action="#delete">删除文件</a></li>';
  file_menu += '<li class="zip separator"><a href="#zip" action="#zip">压缩文件</a></li>';
  file_menu += '<li class="unzip"><a href="#unzip" action="#unzip">解压文件</a></li>';
  file_menu += '<li class="search"><a href="#chmod" action="#chmod">权限编辑</a></li>';
  //file_menu += '<li class="view separator"><a href="#view" action="#view">预览文件</a></li>';
  file_menu += '<li class="download separator"><a href="#download" action="#download">下载文件</a></li>';
  file_menu += '</ul>';
  $('#mycontextMenu').html(main_menu + dir_menu + file_menu);
}


//初始化系统主菜单
WebFTP.UI.Main_Menu = function(){
 $("#list tr").has('a').contextMenu({menu: 'myMainMenu'},function(action, el, pos) {WebFTP.Api.actionMenu('main',{action:action,el:el,pos:pos})});
}

//初始化目录主菜单
WebFTP.UI.Dir_Menu = function(){
 $("#list a[id^='dir-id-']").contextMenu({menu: 'myDirMenu'},function(action, el, pos) {WebFTP.Api.actionMenu('dir',{action:action,el:el,pos:pos})});
}

//初始化文件主菜单
WebFTP.UI.File_Menu = function(){
 $("#list a[id^='file-id-']").contextMenu({menu: 'myFileMenu'},function(action, el, pos) {WebFTP.Api.actionMenu('file',{action:action,el:el,pos:pos})	});
}

//本地缓存全局开关
WebFTP.UI.SwitchCache = function(){
	if(WebFTP.Config.cache.rookie_on){
		WebFTP.Config.cache.rookie_on = false;
		$("a[rel='cacheStyle']").html('开启缓存');
	}else{
		WebFTP.Config.cache.rookie_on = true;
		$("a[rel='cacheStyle']").html('关闭缓存');
	}
}
//文件列表模式切换
WebFTP.UI.SwitchStyle = function(){
	if(WebFTP.Config.list.list_view_on){
		WebFTP.Config.list.list_view_on = false;
		$("a[rel='listStyle']").html('视图风格');
	}else{
		WebFTP.Config.list.list_view_on = true;
		$("a[rel='listStyle']").html('列表风格');
	}
	this.Refresh(true);
}
//缩略模式-文件属性提示
WebFTP.UI.PropertyStyle = function(){
	if(WebFTP.Config.list.property_view_on){
		WebFTP.Config.list.property_view_on = false;
		$("a[rel='propertyStyle']").html('开启提示');
	}else{
		WebFTP.Config.list.property_view_on = true;
		$("a[rel='propertyStyle']").html('关闭提示');
	}
}
//图片缩略图开关
WebFTP.UI.ImageStyle = function(){
	if(WebFTP.Config.list.img_view_on){
		WebFTP.Config.list.img_view_on = false;
		$("a[rel='imageStyle']").html('开启缩略');
	}else{
		WebFTP.Config.list.img_view_on = true;
		$("a[rel='imageStyle']").html('关闭缩略');
	}
}
//快捷热键注册
WebFTP.UI.HotKeys = function(){
	var hotkeysWindowNum   = 4;
	var hotkeysDocumentNum = 6;
	var hotkeysClickNum    = 1;

	/************************ windows 命名空间 ********************************/
	//Ctrl+a 全选/反选
	jQuery(window).bind('keydown', 'Ctrl+a', function (evt){
		if(1 === hotkeysClickNum){WebFTP.Util.SelectAll(); hotkeysClickNum++; return false; }else if(hotkeysWindowNum <= hotkeysClickNum){hotkeysClickNum = 1; return false;}else{hotkeysClickNum++; return false;}
	});

	//Alt+r 刷新
	jQuery(window).bind('keydown', 'Ctrl+r', function (evt){
		if(1 === hotkeysClickNum){	WebFTP.UI.Refresh(true); hotkeysClickNum++; return false; }else if(hotkeysWindowNum <= hotkeysClickNum){hotkeysClickNum = 1; return false;}else{hotkeysClickNum++; return false;}
	});

	//Ctrl+s 列表风格切换
	jQuery(window).bind('keydown', 'Ctrl+s', function (evt){
		if(1 === hotkeysClickNum){ WebFTP.UI.SwitchStyle(); hotkeysClickNum++; return false; }else if(hotkeysWindowNum <= hotkeysClickNum){hotkeysClickNum = 1; return false;}else{hotkeysClickNum++; return false;}
	});

	//Ctrl+q 退出
	jQuery(window).bind('keydown', 'Ctrl+q', function (evt){
		if(1 === hotkeysClickNum){	WebFTP.Api.Loginout(); hotkeysClickNum++; return false; }else if(hotkeysWindowNum <= hotkeysClickNum){hotkeysClickNum = 1; return false;}else{hotkeysClickNum++; return false;}
	});


	/************************ document 命名空间 ********************************/

	//Ctrl+x 全局剪切
	jQuery(document).bind('keydown', 'Ctrl+x', function (evt){
		if(1 === hotkeysClickNum){	WebFTP.Api.Cut(3,{}); hotkeysClickNum++; return false; }else if(hotkeysDocumentNum <= hotkeysClickNum){hotkeysClickNum = 1; return false;}else{hotkeysClickNum++; return false;}
	});

	//Ctrl+c 全局复制
	jQuery(document).bind('keydown', 'Ctrl+c', function (evt){
		if(1 === hotkeysClickNum){	WebFTP.Api.Copy(3,{}); hotkeysClickNum++; return false; }else if(hotkeysDocumentNum <= hotkeysClickNum){hotkeysClickNum = 1; return false;}else{hotkeysClickNum++; return false;}
	});


	//Ctrl+v 全局粘贴
	jQuery(document).bind('keydown', 'Ctrl+v', function (evt){
		if(1 === hotkeysClickNum){	WebFTP.Api.Paste(3,{}); hotkeysClickNum++; return false; }else if(hotkeysDocumentNum <= hotkeysClickNum){hotkeysClickNum = 1; return false;}else{hotkeysClickNum++; return false;}
	});

	//Ctrl+v 全局删除
	jQuery(document).bind('keydown', 'Ctrl+d', function (evt){WebFTP.Api.Del(3,{});return false;});

	//Alt+n 新建目录
	jQuery(document).bind('keydown', 'Alt+n',  function (evt){ WebFTP.Api.NewBuild('dir',{});return false; });

	//Alt+m 新建文件
	jQuery(document).bind('keydown', 'Alt+m',  function (evt){ WebFTP.Api.NewBuild('file',{});return false; });


};
//UI监听
$(function(){
    setInterval("WebFTP.UI.List()", 150);
	//开启快捷键支持
	WebFTP.UI.HotKeys();
	$(window).scroll(function(){WebFTP.UI.Gotop();}).resize(function(){WebFTP.UI.Gotop();});
});