<?php
// +----------------------------------------------------------------------
// | Copyright (C) 2008-2012 OSDU.Net    www.osdu.net    admin@osdu.net
// +----------------------------------------------------------------------
// | Licensed: ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author:   左手边的回忆 QQ:858908467 E-mail:858908467@qq.com
// +----------------------------------------------------------------------

require(dirname(__FILE__).'/../../Init.php');
header('Content-type: text/javascript');
$config = array(
    'debug'        => C('APP_DEBUG')?'true':'false',
    'lang'         => C('DEFAULT_LANG'),
    'root_path'    => C('ROOT_PATH'),
	'real_path'    => C('REAL_PATH'),

	'list_view_on'     => C('LIST_CONF.LIST_VIEW_ON')?'true':'false',
	'img_view_on'      => C('LIST_CONF.IMG_VIEW_ON')?'true':'false',
	'list_order_sort'  => C('LIST_CONF.LIST_ORDER_SORT'),
	'list_order_type'  => C('LIST_CONF.LIST_ORDER_TYPE'),

	'conf_type'        => json_encode(C('TYPE_CONF')),
	'conf_edit'        => json_encode(C('EDIT_CONF')),
	'conf_upload'      => json_encode(C('UPLOAD_CONF.UPLOAD_ALLOW_TYPE')),
	'conf_search'      => json_encode(C('SEARCH_CONF.SEARCH_ALLOW_TYPE')),

	'cache_cut_time'   => C('CACHE_CUT_TIME'),
	'cache_main_on'    => C('CACHE_MAIN_ON')?'true':'false',
	'cache_main_time'  => C('CACHE_MAIN_TIME'),



);
print <<<END
WebFTP.Config.debug  = {$config['debug']};
WebFTP.Config.lang   = '{$config['lang']}';
WebFTP.Config.path   = {
	root: '{$config['root_path']}',
	real: '{$config['real_path']}',
	parent: '{$config['root_path']}',
    current:'{$config['root_path']}'
}
WebFTP.Config.list   = {
	list_view_on:{$config['list_view_on']},
	img_view_on:{$config['img_view_on']},
	property_view_on:false,
    order_sort:'{$config['list_order_sort']}',
	order_type:'{$config['list_order_type']}'
}

WebFTP.Config.conf ={
	type:{$config['conf_type']},
	edit:{$config['conf_edit']},
	search:{$config['conf_search']},
	upload:{$config['conf_upload']}
}
WebFTP.Config.cache = {
	cut_expire_time:{$config['cache_cut_time']},
	rookie_on:{$config['cache_main_on']},
	rookie_expire_time:{$config['cache_main_time']}
}

END;
?>
