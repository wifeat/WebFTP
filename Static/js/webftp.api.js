/*!
// +----------------------------------------------------------------------
// | Copyright (C) 2008-2012 OSDU.Net    www.osdu.net    admin@osdu.net
// +----------------------------------------------------------------------
// | Licensed: ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author:   左手边的回忆 QQ:858908467 E-mail:858908467@qq.com
// +----------------------------------------------------------------------
*/

WebFTP.Api = {
	name:'浩天 WebFTP',
	version:'V1.0.0',
	poweredby:'浩天科技(iHotte.Com)',
	intro:'API执行接口'

};

WebFTP.Api.Config = {

};

//菜单指令调度
WebFTP.Api.actionMenu = function(type,option){
    var type = type || '';
    var option = option || {};
    if('main' == type){
        switch(option.action){
            case 'set':admin(3,option);break;

	        case 'cut':WebFTP.Api.Cut(3,option);break;
	        case 'copy':WebFTP.Api.Copy(3,option);break;
			case 'paste':WebFTP.Api.Paste(3,option);break;
	        case 'delete':WebFTP.Api.Del(3,option);break;

			case 'zip':WebFTP.Api.Zip(3, option);break;

			case 'search':WebFTP.Api.Search(3,option);break;
			case 'chmod':WebFTP.Api.Chmod(3, option);break;

	        case 'download':WebFTP.Api.Download(3,option);break;
		    case 'selectall': WebFTP.Util.SelectAll();break;
	    }
    }else if('dir' == type){
        switch(option.action){
            case 'open':
				WebFTP.Api.Opendir($(option.el).attr('dirpath') + $(option.el).attr('dirname'),{});
				break;
			case 'rename':WebFTP.Api.Rename(2,option);break;

	        case 'cut':WebFTP.Api.Cut(2,option);break;
	        case 'copy':WebFTP.Api.Copy(2,option);break;
	        case 'delete':WebFTP.Api.Del(2,option);break;

			case 'chmod':WebFTP.Api.Chmod(2, option);break;

	        case 'zip':WebFTP.Api.Zip(2, option);break;
	        case 'download':WebFTP.Api.Download(2,option);break;
	    }
    }else if('file' == type){
        switch(option.action){
	        case 'edit':WebFTP.Api.Edit(1,option);break;
			case 'rename':WebFTP.Api.Rename(1,option);break;

	        case 'cut':WebFTP.Api.Cut(1,option);break;
	        case 'copy':WebFTP.Api.Copy(1,option);break;
	        case 'delete':WebFTP.Api.Del(1,option);break;

	        case 'zip':WebFTP.Api.Zip(1, option);break;
	        case 'unzip':WebFTP.Api.Unzip(1, option);break;
			case 'chmod':WebFTP.Api.Chmod(1, option);break;
			case 'view':WebFTP.Api.Viewzip(1, option);break;
	        case 'download':WebFTP.Api.Download(1,option);break;
	    }
    }else{
        asyncbox.error('未知操作指令 !','系统提示-'+WebFTP.poweredby);
    }
}

//打开目录
WebFTP.Api.Opendir = function(path,option){
	var option = option || {};
    var path   = path || WebFTP.Config.path.root;
	if(path.charAt(path.length-1) != '/'){path+='/';}
	//缓存可用直接载入缓存
	if(WebFTP.Config.cache.rookie_on && !option.reload && !WebFTP.Config.autoRefreshList[WebFTP.Util.Md5(path)]){
	   var json = WebFTP.Util.Rookie(WebFTP.Util.Md5(path));
	   if(json && 200 == json.statusCode){
	      WebFTP.UI.Display(json);return;
	   }
	}

	//处理文件列表排序
	if(WebFTP.Util.Cookie('list_order_type') && WebFTP.Util.Cookie('list_order_sort')){
		var order = WebFTP.Util.Cookie('list_order_type') + '|' + WebFTP.Util.Cookie('list_order_sort');
	}
	WebFTP.Ajax.Send('Api.php', {'isajax':true,'module':'list','path':path,'order':order}, {
		success:function(json){
			if(WebFTP.Config.cache.rookie_on && 200 == json.statusCode){
				WebFTP.Config.autoRefreshList[WebFTP.Util.Md5(path)] = false;
				WebFTP.Util.Rookie(WebFTP.Util.Md5(path),json,{expire:WebFTP.Config.path.rookie_expire_time});
			}; WebFTP.UI.Display(json);
		},timeout:20
	});
};

//获取当前目录属性
WebFTP.Api.GetProperty = function(){
	WebFTP.UI.Waitme();
	var data   = {'isajax':true,'module':'property','path':WebFTP.Config.path.current};
	var option = {
		success:function(json){
		   asyncbox.alert(json.message,'目录属性-'+WebFTP.poweredby);
		   WebFTP.UI.Waitmeoff();
		}
	}
	WebFTP.Ajax.Send('Api.php', data, option);
}
//重命名
WebFTP.Api.Rename = function(type,option){
	var szMsg  = '[\\/:*?"\'<>|：？“’《》]';
    if(1 == type || 2 == type){
	    option.oldname = (type == 2)?$(option.el).attr('dirname'):$(option.el).attr('filename');
		option.newname = option.oldname;
		asyncbox.prompt(((type == 2)?'目录':'文件')+'重命名-'+WebFTP.poweredby, '请输入新'+((type == 2)?'目录':'文件')+'名（<font color="red">不能含非法字符</font>）:<br />重命名 '+option.oldname, option.oldname,'text',function(action,val){
　　		if(action == 'ok'){
				for(i=1;i<szMsg.length+1;i++){
					if(val.indexOf(szMsg.substring(i-1,i))>-1){
						asyncbox.tips('请勿输入非法字符如:'+szMsg,'error');return;
					}
				}
				option.newname = val;
				WebFTP.Api.Rename(3,option);
　　　		}
　		});
    }else if(3 == type ){
	    if($.trim(option.newname) == $.trim(option.oldname)){
			asyncbox.tips('新旧文件名不能相同','error');return;
		}
		WebFTP.UI.Waitme();
		WebFTP.Ajax.Send('Api.php', {'isajax':true,'module':'rename','path':WebFTP.Config.path.current,'oldname':option.oldname, 'newname':option.newname}, {
			success:function(json){
				if(200 == json.statusCode){
					asyncbox.alert(json.message,'文件目录重命名-'+WebFTP.poweredby);
					WebFTP.UI.Refresh(true);
	            }else{
	                asyncbox.error(json.message,'文件目录重命名-'+WebFTP.poweredby);
	            }
			},timeout:5
		});
    }else{
	   asyncbox.tips('Sorry,未知重命名操作！','error');return;
	}
}
//文件搜索
WebFTP.Api.Search = function(type,option){
	asyncbox.tips('Sorry, 暂不支持文件搜索！','error');return;
}
//新建文件、目录
WebFTP.Api.NewBuild = function(type,option){
	var szMsg  = '[\\/:*?"\'<>|：？“’《》]';
    if('file' == type){
　		asyncbox.prompt('新建文件-'+WebFTP.poweredby, '请输入新文件名（<font color="red">不能含非法字符</font>）:', 'newfile.php','text',function(action,val){
　　		if(action == 'ok'){
				for(i=1;i<szMsg.length+1;i++){
					if(val.indexOf(szMsg.substring(i-1,i))>-1){
						asyncbox.tips('请勿输入非法字符如:'+szMsg,'error');return;
					}
				}
				if('' == val){
					asyncbox.tips('文件名不能留空','error');return;
				}else{
					option.type = 'file';
					option.name = val;
					WebFTP.Api.NewBuild('do',option);
				}
　　　		}
　		});
	}else if('dir' == type){
　		asyncbox.prompt('新建目录-'+WebFTP.poweredby, '请输入新目录名（<font color="red">不能含非法字符</font>）:', 'newdir', 'text',function(action,val){
　　		if(action == 'ok'){
				for(i=1;i<szMsg.length+1;i++){
					if(val.indexOf(szMsg.substring(i-1,i))>-1){
						asyncbox.tips('请勿输入非法字符如:'+szMsg,'error');return;
					}
				}
				if('' == val){
					asyncbox.tips('目录名不能留空','error');return;
				}else{
					option.type = 'dir';
					option.name = val;
					WebFTP.Api.NewBuild('do',option);
				}
　　　		}
　		});
    }else if('do' == type ){
		WebFTP.Ajax.Send('Api.php', {'isajax':true,'module':'newbuild', 'type':option.type, 'path':WebFTP.Config.path.current, 'name':option.name}, {
			success:function(json){
				if(200 == json.statusCode){
					asyncbox.alert(json.message,'新建文件目录-'+WebFTP.poweredby);
					WebFTP.UI.Refresh(true);
	            }else{
	                asyncbox.error(json.message,'新建文件目录-'+WebFTP.poweredby);
	            }
			},timeout:5
		});
    }else{return;}
}
//删除文件
WebFTP.Api.Del = function(type,option,name){
	var option = option || {};
	if(1 == type){
    	asyncbox.confirm('<font color="red">删除操作不可恢复,请谨慎操作,确定删除：<font color="blue">'+ (name || $(option.el).attr('filename')) +' ?</font></font>', '文件删除-'+WebFTP.poweredby,function(action){
	    	if(action == 'ok'){
				option.files = name ||$(option.el).attr('filename');
				WebFTP.Api.Del('do',option);
			}else{
		    	return;
			}
	    });
    }else if(2 == type){
    	asyncbox.confirm('<font color="red">删除操作不可恢复,请谨慎操作,确定删除：<font color="blue">'+(name || $(option.el).attr('dirname'))+' ?</font></font>',  '目录删除-'+WebFTP.poweredby,function(action){
	    	if(action == 'ok'){
				option.files = name || $(option.el).attr('dirname');
				WebFTP.Api.Del('do',option);
			}
		    return;
	    });
    }else if(3 == type){
	    var data = WebFTP.Util.SelectCheck();
		if(1 > data.length){asyncbox.tips('请至少选择一个文件或目录!','error');return;}
		var confirm_str = '';
		$.each( data, function(idx, item){
			data[idx] = item.replace(WebFTP.Config.path.current,'');
			confirm_str += '<font color="blue">删除：'+item+'</font><br />';
		});
        asyncbox.confirm('<font color="red">删除操作不可恢复,请谨慎操作,确定删除 ?</font><br />'+confirm_str, '批量删除-'+WebFTP.poweredby, function(action){
	        if(action == 'ok'){
				if(1 > data.length){asyncbox.tips('请至少选择一个文件或目录!','error');return;}
				option.files = data.join('|');
				WebFTP.Api.Del('do',option);
		    }return;
        });
    }else if('do' == type){
		WebFTP.Ajax.Send('Api.php', {'isajax':true,'module':'delete','path':WebFTP.Config.path.current,'files':option.files}, {
			success:function(json){
				if(200 == json.statusCode){
	                asyncbox.alert(json.message, '批量删除-'+WebFTP.poweredby);
	            }else{
	                asyncbox.error(json.message, '批量删除-'+WebFTP.poweredby);
	            }
				WebFTP.UI.Refresh(true);
			},timeout:30
		});
	}
}

//压缩ZIP文档
WebFTP.Api.Zip = function(type,option){
    var type = type;
    var option = option || {};
    if(1 == type){
		option.files  = $(option.el).attr('filename');
		option.action = 'file';
        WebFTP.Api.Zip(3,option);
    }else if(2 == type){
	    option.files  = $(option.el).attr('dirname');
		option.action = 'dir';
        WebFTP.Api.Zip(3,option);
    }else if(3 == type ){
	    var data = WebFTP.Util.SelectCheck();
		option.type = option.type || 'dir-file';
	    if(1 > data.length && !option.files){
		    asyncbox.tips('请至少选择一个文件或目录!','error');return;
	    }else{
		    var files = option.files || data.join('|');
			WebFTP.UI.Waitme();
	        $.ajax({
				type: 'POST',url: 'Api.php', timeout:600000, dataType:"json",error: WebFTP.ajaxErrorMsg	,
				data:{'module':'zip','action':option.action,'path':window.current_path,'files':files},
				success:function(json){
					if(200 == json.statusCode){
						asyncbox.alert(json.message, 'ZIP压缩-'+WebFTP.poweredby);
						WebFTP.UI.Refresh(true);
					}else{
						asyncbox.error(json.message, 'ZIP压缩-'+WebFTP.poweredby);
					}
				}

			});
        }
    }else{
	   asyncbox.tips('Sorry,未知压缩操作！','error');return;
	}
}
//文件剪切
WebFTP.Api.Cut = function(type,option){
	this.Copy(type,option);
	WebFTP.Util.Cookie('cut_type', 'cut', {expires: WebFTP.Config.cache.cut_expire_time});
}
//文件复制
WebFTP.Api.Copy = function(type,option){
	var timeout = WebFTP.Config.cache.cut_expire_time || 20;
	var path    = WebFTP.Config.path.current;
    if(1 == type){
		WebFTP.Util.Cookie('cut_path_from', path, {expires: timeout});
        WebFTP.Util.Cookie('cut_files', $(option.el).attr('filename'), {expires: timeout});
    }else if(2 == type){
	    WebFTP.Util.Cookie('cut_path_from', path, {expires: timeout});
        WebFTP.Util.Cookie('cut_files', $(option.el).attr('dirname'), {expires: timeout});
    }else if(3 == type){
        var data = WebFTP.Util.SelectCheck();
	    if(0 < data.length){
		    $.each(data,function(idx, item){
			     data[idx] = item.replace(WebFTP.Config.path.current,'');
			});
	        var files = (data.join('|'));
			WebFTP.Util.Cookie('cut_path_from', path, {expires: timeout});
            WebFTP.Util.Cookie('cut_files', files, {expires: timeout});
	    }else{
	        asyncbox.tips('Sorry,请至少选择一个文件!','error');return;
	    }
    }
	WebFTP.Util.Cookie('cut_type', 'copy', {expires: timeout});
	WebFTP.Util.Cookie('cut_cover', '0', {expires: timeout});
}

//文件粘贴
WebFTP.Api.Paste = function(type,option){
    var option  = option || {};
    var path_from   = WebFTP.Util.Cookie('cut_path_from');
	var path_to     = WebFTP.Util.Cookie('cut_path_to') || WebFTP.Config.path.current;
    var files  = WebFTP.Util.Cookie('cut_files');
	var type   = WebFTP.Util.Cookie('cut_type');
	var cover  = WebFTP.Util.Cookie('cut_cover') || '0';
    var sure   = option.sure || '0';

    //递归死循环检测
	var $return = false;
	if(files){
		$.each(files.split('|'), function(idx, item){
			if(-1 != path_to.lastIndexOf(path_from+item+'/')){
				asyncbox.tips('Sorry, 源路径和目标路径冲突, 无法执行粘贴操作!','error');
				$return =  true;
			}
		});
	}
	if($return){
		return;
	}else{
		if(path_from == path_to){
			asyncbox.tips('Sorry，源路径不能与目标路径相同!','error');return;
		}else if( (path_from && path_to && files) && ('cut' == type || 'copy' == type) ){
			WebFTP.Config.autoRefreshList[WebFTP.Util.Md5(path_from)] = true;//将原始目录加入刷新列表
			WebFTP.Ajax.Send('Api.php', {'isajax':true,'module':'paste','sure':sure,'type':type,'path_from':path_from,'path_to':path_to,'files':files,'cover':cover}, {
				success:paste_callback,timeout:60
			});
		}else{
			asyncbox.tips('Sorry, 剪贴板过期或没选择文件, 无法执行粘贴操作!','error');return;
		}
	}
}
function paste_callback(json){
	var timeout = WebFTP.Config.cache.cut_expire_time || 30;
	if(200 == json.statusCode){
	    asyncbox.alert(json.message, '文件粘贴-'+WebFTP.poweredby);
		WebFTP.Util.Cookie('cut_files', null);
		WebFTP.Util.Cookie('cut_path_to', null);
		WebFTP.Util.Cookie('cut_path_from', null);
		WebFTP.Util.Cookie('cut_type', null);
		WebFTP.Util.Cookie('cut_cover', null);
		WebFTP.UI.Refresh(true);
	}else if(201 == json.statusCode){
		WebFTP.Util.Cookie('cut_files', json.result.files, {expires: timeout});
		WebFTP.Util.Cookie('cut_path_to', json.result.path_to, {expires: timeout});
		WebFTP.Util.Cookie('cut_path_from', json.result.path_from, {expires: timeout});
		WebFTP.Util.Cookie('cut_type', json.result.type, {expires: timeout});
		WebFTP.Util.Cookie('cut_cover', 1, {expires: timeout});
		asyncbox.confirm('<font color="red">覆盖操作不可恢复,请谨慎操作,确定覆盖?：<br /><font color="blue">'+json.message+'</font>',  '是否覆盖-文件粘贴-'+WebFTP.poweredby, function(action){
	    	if(action == 'ok'){
		    	WebFTP.Api.Paste();return;
			}else{
				WebFTP.Util.Cookie('cut_files', null);
				WebFTP.Util.Cookie('cut_path_to', null);
				WebFTP.Util.Cookie('cut_path_from', null);
				WebFTP.Util.Cookie('cut_type', null);
				WebFTP.Util.Cookie('cut_cover', null);
		    	return;
			}
	    });
	}else{
		asyncbox.error(json.message, '文件粘贴-'+WebFTP.poweredby);
        return false;
	}
}

//安全退出
WebFTP.Api.Loginout = function(){
　  asyncbox.confirm('<font color="red">确定退出WebFTP ?</font>','注销登录-'+WebFTP.poweredby,function(action){　　
	if(action == 'ok'){
　　　　　document.location.href = './?m=login&a=out';return;
　　　  }
　  });
}
//重置密码
WebFTP.Api.Resetpass = function(){
	asyncbox.prompt('密码修改-'+WebFTP.poweredby, '请输入新密码:', '','text',function(action,val){
		if('ok' != action) return;
		if($.trim(val).length < 4){asyncbox.tips('Sorry, 密码长度不能小于4位!','error');return;}
		WebFTP.Ajax.Send('index.php?m=login&a=resetpasswd', {'isajax':true,'module':'Auth','action':'resetpasswd','newpasswd':val}, {
			success:function(json){
				if(200 == json.statusCode){
	            	asyncbox.alert(json.message, '密码修改-'+WebFTP.poweredby);
	           	}else{
	               	asyncbox.error(json.message, '密码修改-'+WebFTP.poweredby);
	            }
			},timeout:5
		});
	});

}

//权限编辑
WebFTP.Api.Chmod = function(type,option){
    if(1 == type){
        option.file  = $(option.el).attr('filepath') + $(option.el).attr('filename');
		option.chmod = $(option.el).attr('chmod');
        WebFTP.Api.Chmod(3,option);
    }else if(2 == type){
	   option.file  = $(option.el).attr('dirpath') + $(option.el).attr('dirname') + '/';
       option.chmod = $(option.el).attr('chmod');
       WebFTP.Api.Chmod(3,option);
    }else if(3 == type ){
	    var selects = WebFTP.Util.SelectCheck();
	    if(1 > selects.length && !option.file){
		    asyncbox.tips('请至少选择一个文件或目录!','error');return;
	    }else{
		    var files = option.file || selects.join('|');
            option.chmod = option.chmod	|| 777;
	        asyncbox.open({
　　　          url : 'Api.php',width : 480, height : 360, scrolling:'no',title:'修改权限'+WebFTP.poweredby,
                data : {module:'chmod',action:'show',files:encodeURI(files),chmod:option.chmod},　
			    tipsbar : {title : '目录文件权限修改',content : '目录文件权限修改...'},
			    btnsbar : [{text: '提交更改',action  : 'save_chmod' },{text: '关闭窗口',action  : 'close_widow' }],
　　　          callback : function(action,iframe){
　　　　　          var $this = $(this);
				    if('save_chmod' == action){
						var data   = {
							'isajax':true,'module':'chmod','action':'save','path':WebFTP.Config.path.current,
							'chmod':iframe.get_chmod_num(),'deep':iframe.get_chmod_deep(),files:files
                        }
						var option = {
							success:function(json){
								if(200 == json.statusCode){
	                    			asyncbox.alert(json.message,'权限变更-'+WebFTP.poweredby);
	                			}else{
	                    			asyncbox.error(json.message,'权限变更-'+WebFTP.poweredby);
	                			}
								WebFTP.UI.Waitmeoff();
							},timeout:6
						}
						WebFTP.Ajax.Send('Api.php', data, option);
				    }else if('close_widow' == action){
                        return;
                    }
                }
　          });
        }
    }else{return;}
}
//下载文件
WebFTP.Api.Download = function(type,option,name){
	var tool = 'top=50,left=120,width=500,height=560,scrollbars=yes,toolbar=yes,menubar=yes,scrollbars=yes,resizable=yes,location=yes,status=yes';
    if(1 == type){
        var file = name?(WebFTP.Config.path.current+name):($(option.el).attr('filepath') + $(option.el).attr('filename'));
        var url  = 'Api.php?module=downfile&action=file&file='+encodeURI($.trim(file));
		window.open(url, '下载文件', tool);
    }else if(2 == type){
	   var dir = name?(WebFTP.Config.path.current+name):($(option.el).attr('dirpath') + $(option.el).attr('dirname') + '/');
       var url  = 'Api.php?module=downfile&action=dir&dir='+encodeURI($.trim(dir));
	   window.open(url, '下载文件', tool);
    }else{
	    asyncbox.tips('只支持单文件和单目录下载!','error');return;
	}
}

//文本编辑
WebFTP.Api.Edit = function(type, option){
    var type = type || 0;
    var option = option || {};
	if('unknown' == WebFTP.Util.GetLanguage($(option.el).attr('fileext'))){
	    asyncbox.tips('Sorry,暂不支持编辑'+ $(option.el).attr('fileext') +'格式文件！','error');
	    return;
	}else{
        var file = ($(option.el).attr('filepath') + $(option.el).attr('filename'));
		var edit_width = WebFTP.Config.conf.edit.editor_conf.width || 900; var edit_height = WebFTP.Config.conf.edit.editor_conf.height || 600;
　      asyncbox.open({
　　　      id : 'editfile', url : 'Api.php',width : edit_width,height : edit_height,
            data : {module:'editfile',action:'show',file:file},title:'编辑：'+$(option.el).attr('filename'),　
			tipsbar : {title : '文件编辑',content : '文件编辑器...'},
			btnsbar : $.btn.OKOKOKOKOKCANCEL,
			btnsbar : [
				{text: '保存文件',action  : 'file_save' },
				{text: '获取代码',action  : 'file_getcode' },
				{text: '预览文件',action  : 'file_view' },
				//{text: '代码高亮',action  : 'file_highlight' },
				{text: '显示行号',action  : 'file_line' },
				//{text: '自动完成',action  : 'file_autoComplete' },
				{text: '关闭窗口',action  : 'close_widow' }
			],
　　　      callback : function(action, iframe){
　　　　　      var $this = this;
				if('file_getcode' == action){
				    iframe.getcode();return false;
				}else if('file_view' == action){
					iframe.view(WebFTP.Config.path);return false;
				}else if('file_highlight' == action){
					iframe.highlight();return false;
				}else if('file_line' == action){
					iframe.line();return false;
				}else if('file_autoComplete' == action){
					iframe.autoComplete();return false;
				}else if('file_save' == action){
					iframe.save();return false;
				}else if('close_widow' == action){
					$.close($this.id);return;
					var msg = '<font color="red">关闭窗口会造成未保存代码丢失,    <br />确定关闭？</font>';
　					asyncbox.confirm(msg,'代码编辑-'+WebFTP.poweredby,function(action){
　　　					if(action == 'ok'){
　　　　　					$.close($this.id);
　　　					}
　					});
					return false;
				}
　　　      }
　      });
        return;
	}
}


//批量上传
WebFTP.Api.Upload = function(type, option){
	WebFTP.UI.Waitme();
    asyncbox.open({
　　　  url : 'Api.php',width : 520,height : 500,
        title :'批量上传文件- '+WebFTP.poweredby,
        data : {'module':'upload','action':'show','path':WebFTP.Config.path.current},　　　
        tipsbar : {title : '文件上传',content : '批量上传文件...'},
	    btnsbar : [{text: '关闭窗口',action  : 'close_window' }],
	    callback : function(action){WebFTP.UI.Refresh(true);}
　  });
    return;
}

//压缩ZIP文档
WebFTP.Api.Zip = function(type,option,name){
    if(1 == type){
		option.action = 'file';
		option.files = name?(WebFTP.Config.path.current+name):($(option.el).attr('filepath') + $(option.el).attr('filename'));
		WebFTP.Api.Zip(3,option);
    }else if(2 == type){
		option.action = 'dir';
		option.files = name?(WebFTP.Config.path.current+name):($(option.el).attr('dirpath') + $(option.el).attr('dirname') + '/');
		WebFTP.Api.Zip(3,option);
    }else if(3 == type ){
	    var data = WebFTP.Util.SelectCheck();
		var action = option.action || 'all';
	    if(1 > data.length && !option.files){
		    asyncbox.tips('请至少选择一个文件或目录!','error');return;
	    }else{
		    var files = option.files || data.join('|');
			WebFTP.Ajax.Send('Api.php', {'isajax':true,'module':'zip','action':action,'files':files,'path':WebFTP.Config.path.current}, {
				success:function(json){
					if(200 == json.statusCode){
						asyncbox.alert(json.message, 'ZIP压缩-'+WebFTP.poweredby);
						WebFTP.UI.Refresh(true);
					}else{
						asyncbox.error(json.message, 'ZIP压缩-'+WebFTP.poweredby);
					}
					WebFTP.UI.Waitmeoff();
				},timeout:600
			});
        }
    }else{return;}
}
//解压ZIP文档
WebFTP.Api.Unzip = function(type,option){
	if('zip' != $(option.el).attr('fileext')){
	   asyncbox.tips('Sorry,暂不支持解压非ZIP格式文件！','error');return;
	}
    asyncbox.prompt('ZIP解压-'+WebFTP.poweredby,'请输入解压目录（解压到当前请输入 <font color="red">"."</font>）:',$(option.el).attr('filename').replace('.'+$(option.el).attr('fileext'),''),'text',function(action,val){　　　
        if(action == 'ok'){
			if('' == val){ asyncbox.tips('Sorry,解压目录不能为空！','error');	return;}
            asyncbox.confirm('<font color="red">存在同名文件覆盖 ?</font>', 'ZIP解压-'+WebFTP.poweredby, function(action){
　　　          if(action == 'ok'){var remove = '1';}else{var remove = '0';}
				WebFTP.Ajax.Send('Api.php', {'isajax':true,'module':'unzip','action':'?','path':WebFTP.Config.path.current,'name':$(option.el).attr('filename'),'unzippath':val+'/','remove':remove}, {
					success:function(json){
						if(200 == json.statusCode){
							asyncbox.alert(json.message, 'ZIP解压-'+WebFTP.poweredby);
							WebFTP.UI.Refresh(true);
						}else{
							asyncbox.error(json.message, 'ZIP解压-'+WebFTP.poweredby);
						}
						WebFTP.UI.Waitmeoff();
					},timeout:120
				});
			});
　　　  }else{
          return;
        }
　  });
}
