<?php
// +----------------------------------------------------------------------
// | Copyright (C) 2008-2012 OSDU.Net    www.osdu.net    admin@osdu.net
// +----------------------------------------------------------------------
// | Licensed: ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author:   左手边的回忆 QQ:858908467 E-mail:858908467@qq.com
// +----------------------------------------------------------------------

//全局配置文件

if(!defined('WebFTP')){die('Forbidden Access');}
return array(
/* 本地初始根目录 */
	'ROOT_PATH'     => './../', //本地路径 以'./'开头，以'/'结尾
	'REAL_PATH'		=> './../', //URL路径  默认为"./"

/* 启用模块(小写,*启用所有),禁用注释掉相关条目即可 */
	'ALLOWED_MODULE'  =>array(
		'*'   		=> '全功能模式',
		'list'  	=>	'列出目录(核心功能,不能禁用)',
		'property'	=>	'目录详情(目录文件数,大小及读写的相关属性)',
		//'rename '	=>	'重命名(重命名文件、目录)',
		'downfile'	=>	'文件下载',
		//'unzip'		=>	'ZIP解压',
		'zip'		=>	'ZIP压缩',
		'delete'	=>	'删除文件',
		'paste'		=>	'文件移动、复制、粘贴',
		'chmod'		=>	'文件权限变更(Linux)',
		'newbuild'	=>	'新建目录、文件',
		'imageview' => 	'图片缩略预览',
		//'editfile' 	=> 	'文本代码编辑',
		'upload' 	=> 	'批量上传文件',
	),

/* 文件列表配置 */
	'LIST_CONF' => array(
		'list_view_on'         => false,//开启列表模式
		'img_view_on'          => true,//是否开启图片预览
		'list_order_sort'  	   => 'asc',//asc:顺序 desc:倒序
		'list_order_type' 	   => 'name',//1:name 2:size 3:ext 4:mtime

		'display_notallow'       => array(
			'./../WebFTP/','./../WebSQL/',
		),
	),

/* 文件上传配置 */
	'UPLOAD_CONF' => array(
		//上传单个文件限制大小，单位KB
		'upload_max_size'           => 1024*1024,
		//可限制传输文件总容量，单位KB
		'upload_total_size'         => 1024*1024,
		//可限制待传文件的数量
		'file_num'                  => 50,
		'upload_allow_type'         => array(
			'*',//不限类型
			'html','htm','css','js',//网页及相关文件
			'png','bmp','jpg','jpeg','gif',//图片文件
			'swf','flv','mp3',//多媒体文件
		),
	),



/******************************** 以下参数请谨慎设置 一般保留默认即可************************/
	'DEFAULT_LANG'  	   => 'gb2312',//默认语系<gb2312|utf8>
	'CACHE_DATA_DEL'       => true, //是否即时删除缓存数据
	'CACHE_MAIN_ON'        => true,	 //是否启用浏览器缓存
	'CACHE_MAIN_TIME'      => 120,	 //浏览器数据缓存周期(秒)
	'CACHE_CUT_TIME'       => 25,	 //浏览器剪贴板缓存周期(秒)

	'MAX_TIME_LIMIT'        => 120,
	'DEFAULT_CHARSET'       => 'utf-8',

	'APP_DEBUG'             => false,
	'SYSTEM_NAME'           => 'WebFTP',
	'SYSTEM_VERSION'        => 'V2.5 终结版',
	'LOG_EXCEPTION_RECORD'  => true,//记录日志
	'LOG_EXCEPTION_TYPE'    => 'EMERG,ALERT,CRIT,ERR,WARNING,NOTICE,INFO,DEBUG',
	//'LOG_EXCEPTION_TYPE'  => 'EMERG,ALERT,CRIT,ERR,WARNING,NOTICE,INFO,DEBUG,SQL',
	'LOG_FILE_SIZE'         => 1048576 * 2,	//默认2MB
	'LOG_SAVE_TYPE'         => 2,			//1：只保留最新日志,2: 保留所有日志，

/* Cookie设置 和 Session共用 */
	'COOKIE_EXPIRE'         => 3600*24,
	'COOKIE_DOMAIN'         => '',
	'COOKIE_PATH'           => '',
	'COOKIE_PREFIX'         => 'webftp_',

/* 文件类型配置 格式：'图标'=>array('扩展名1', '扩展名2', '扩展名N'),  */
	'TYPE_CONF' => array(
		'archive' =>array('archive'),'asp' =>array('asp'),'audio' =>array('audio', 'mp3', 'mid'),'authors' =>array('authors'),	'bin' =>array('bin'),'bmp' =>array('bmp'),
		'c' =>array('c'),'calc' =>array('calc'),'cd' =>array('cd'),'copying' =>array('copying'),'cpp' =>array('cpp'),'css' =>array('css'),
		'deb' =>array('deb'),'default' =>array('default'),'doc' =>array('doc'),'draw' =>array('draw'),	'eps' =>array('eps'),'exe' =>array('exe'),
		'floder' =>array('floder'),'floder-home' =>array('floder-home'),'floder-open' =>array('floder-open'),'floder-page' =>array('floder-page'),'floder-parent' =>array('floder-parent'),
		'gif' =>array('gif'),'gzip' =>array('gzip'),	'h' =>array('h'),'hpp' =>array('hpp'),'html' =>array('html', 'htm'),'ico' =>array('ico'),'image' =>array('image'),'install' =>array('install'),
		'java' =>array('java'),'jpg' =>array('jpg'),'js' =>array('js'),	'log' =>array('log'),'makefile' =>array('makefile'),
		'package' =>array('package'),'pdf' =>array('pdf'),'php' =>array('php'),'playlist' =>array('playlist'),'png' =>array('png'),'pres' =>array('pres'),'psd' =>array('psd'),'py' =>array('py'),
		'rar' =>array('rar'),'rb' =>array('rb'),'readme' =>array('readme'),'rpm' =>array('rpm'),'rss' =>array('rss'),'rtf' =>array('rtf'),
		'script' =>array('script'),'source' =>array('source'),'sql' =>array('sql'),	'tar' =>array('tar'),'tex' =>array('tex'),'text' =>array('text','txt'),'tiff' =>array('tiff'),
		'unknown' =>array('unknown'),	'vcal' =>array('vcal'),'video' =>array('video'),	'xml' =>array('xml'),'zip' =>array('zip'),
	),

/* 文件在线编辑配置 */
	'EDIT_CONF' => array(
		'editor_conf'     => array('width'=>900, 'height'=>600),
		'edit_allow_type' => array(
			'高亮语法'    => array('扩展名1', '扩展名2', '扩展名N'),
			'asp' 		  => array('asp', 'aspx'),'autoit' 	  => array('autoit'),'csharp' 	  => array('csharp'),'css'		  => array('css'),'generic' 	  => array('generic'),'html' 		  => array('htm', 'html', 'tpl'),'java' 		  => array('jar'),
			'javascript'  => array('js'),'perl' 		  => array('perl'),'php' 		  => array('php', 'php3'),'ruby' 		  => array('ruby'),'sql' 		  => array('sql'),'vbscript'	  => array('vbs'),'xsl'	      => array('php'),'text'	      => array('log', 'txt', 'ini'),
		),
	),

/* 文件搜索配置 */
	'SEARCH_CONF' => array(
		'search_allow_type'         => array(
			'php','php3','asp','txt','jsp','inc','ini','pas','cpp','bas','in','lang','out','htm','html','cs','config','js',
			'htc','css','c','sql','bat','vbs','cgi','dhtml','shtml','xml','xsl','aspx','tpl','ihtml','htaccess','dwt','lib','lbi',
		),
	),

/* 认证配置 */
	'AUTH' => 	array(
		'type'=>1,//1：local:本地程序认证,2 : 远程API认证
		//type 选2时有效
		'key' => 'sjfhJKSH',//8位远程通信密匙
		'api' => 'http://www.xx.com/WebFTP/Api/login.php',//远程认证地址
	),
);
?>