/*!
// +----------------------------------------------------------------------
// | Copyright (C) 2008-2012 OSDU.Net    www.osdu.net    admin@osdu.net
// +----------------------------------------------------------------------
// | Licensed: ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author:   左手边的回忆 QQ:858908467 E-mail:858908467@qq.com
// +----------------------------------------------------------------------
*/

var WebFTP = window.WebFTP = {
	name:'WebFTP',
	version:'V2.3.1',
	poweredby:'OSDU.Net',
	intro:'核心接口'
};

//APP配置参数
WebFTP.Config = {
	'autoRefreshList':new Array()//自动刷新目录列表

	//WebFTP.Config.autoRefreshList[WebFTP.Util.Md5('admin')] = true;
	//alert(WebFTP.Config.autoRefreshList[WebFTP.Util.Md5('admin')]);
};

//
WebFTP.CheckRefreshList = function(path){

}
//调试Console控制台
WebFTP.Console = {
	log:function(msg){
		console.log(msg);
	},
	warn:function(msg){
		console.warn(msg);
	},
	error:function(msg){
		console.error(msg);
	},
	time:function(name){
		var name = name+'(执行时间)';
		console.time(name)
	},
	timeEnd:function(name){
		var name = name+'(执行时间)';
		console.timeEnd(name);
	},
	count:function(title){
		var title = title+'(执行次数)' || '执行次数';
		console.count(title)},
	dir:function(object){
		console.dir(object);
	},
	dirxml:function(node){
		console.dirxml(node);
	},
	clear:function(){
		console.clear();
	}
};

WebFTP.Ajax = {
	Send:function(url, data, option){
		WebFTP.UI.Waitme();
	    $.ajax({
			url: url, type: option.type || 'POST',
			async: option.async || true, cache: option.cache || true,
			complete:option.complete || function(XMLHttpRequest, textStatus){},
			success:option.success || function(data){},
			error:option.error || WebFTP.Ajax.ThrowError,
			dataType:option.dataType || 'json',
			timeout:option.timeout? option.timeout*1000:10*1000,
			data: data || {}
		});
	},
	ThrowError:function(xhr, textStatus, thrownError){
		var msg = '';
		if('timeout' == textStatus){
			msg += '<div>Http status: AJAX请求超时</div>';
		}else if('error' == textStatus){
			msg += '<div>Http status:  '+xhr.status+' '+xhr.statusText+'</div>';
			msg += '<div>Http readyState:  '+xhr.readyState+'</div>';
			msg += '<div>thrownError:  '+thrownError+'</div>';
			msg += '<div>responseText: '+xhr.responseText+'</div>';
		}else{
			msg += textStatus+':'+thrownError;
		}
		asyncbox.error(msg, '请求出错-'+WebFTP.poweredby);
	}
}