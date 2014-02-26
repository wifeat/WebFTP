<?php
// +----------------------------------------------------------------------
// | Copyright (C) 2008-2012 OSDU.Net    www.osdu.net    admin@osdu.net
// +----------------------------------------------------------------------
// | Licensed: ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author:   左手边的回忆 QQ:858908467 E-mail:858908467@qq.com
// +----------------------------------------------------------------------

if(!defined('WebFTP')){die('Forbidden Access');}
class Auth
{
	//验证是否登录
	static function is_login(){
		$username = Session::get('username');
		$tokey    = Session::get('tokey');
		if(empty($username) || empty($tokey)){
			return false;
		}
		return ($username == Cookie::get('username') &&$tokey == Cookie::get('tokey'));
	}

	//验证是否权限
	static function is_allow(){
		$auth_arr = explode('|',Session::get('userauth'));
		return (in_array('*', $auth_arr) || in_array(MODULE_NAME, $auth_arr))?true:false;
	}

	//登出
	static function login_out(){
		Session::set('username',null);
		Session::get('userauth',null);
		Session::set('tokey',null);
		Cookie::del('username');
		Cookie::del('tokey');
	}

	//本地登陆认证
	static function local_login_check(){
		if(1 != C('AUTH.TYPE')){
			Session::set('login_error', '当前系统不支持直接登录！');
			redirect('./?m=login&a=in');
		}
		$username   = trim($_POST['username']);
		$password   = trim($_POST['password']);
		$verifycode = trim($_POST['verifycode']);
		$localcode  = Session::get('verify_code');
		if(empty($localcode) || $verifycode != $localcode){
			Session::set('login_error', '数据非法！');
			redirect('./?m=login&a=in');
		}
		$user_info = self::get_user_data($username);
		if($user_info['username'] == $username && $user_info['password'] == md5($password)){
			Cookie::set('username', $username);
			Cookie::set('tokey', self::get_tokey($username,$password));
			Session::set('username', $username);
			Session::set('userauth', implode('|',$user_info['auth']));
			Session::set('tokey', self::get_tokey($username,$password));
			Session::set('login_error', null);
			redirect('./');
		}else{
			Session::set('login_error', '账户不存在或密码有误！');
			redirect('./?m=login&a=in');
		}
	}

	static function api_login_check(){
		if(2 != C('AUTH.TYPE')){
			Session::set('login_error', '当前系统不支持API登录！');
			redirect('./?m=login&a=in');
		}
		if(trim($_GET['key']) != C('AUTH.KEY')){
			Session::set('login_error', 'API通讯密匙无效！');
			redirect('./?m=login&a=in');
		}
		$api_check_url = C('AUTH.API').'?key='.C('AUTH.KEY').'&username='.$_GET['username'].'&tokey='.$_GET['tokey'];
		$api_return_data = file_get_contents($api_check_url);
		$api_return_data = json_decode($api_return_data, TRUE);
		if(200 == $api_return_data['statusCode']){
			Cookie::set('username', $_GET['username']);
			Cookie::set('tokey', $_GET['tokey']);
			Session::set('username', $_GET['username']);
			Session::set('userauth', implode('|',$api_return_data['userauth']));
			Session::set('tokey', $_GET['tokey']);
			Session::set('login_error', null);
			redirect('./');
		}else{
			Session::set('login_error', $api_return_data['message']);
			redirect('./?m=login&a=in');
		}
	}

	static function get_tokey($username,$password){
		return md5($username.C('AUTH.KEY').$password);
	}

	static function get_verify_code(){
		$verify = md5(base64_encode(time()));
		Session::set('verify_code', $verify);
		return $verify;
	}


	/********* AUTH.TYPE' 为1(local:本地程序认证)是有效 ***************/
	//添加、修改本地管理员
	static function add_user($name,$password,$auth=array()){
		if(empty($name) || empty($password)){return false;}
		$data = self::encode_user_data(array('username'=>$name,'password'=>md5($password), 'auth'=>$auth));
		return file_put_contents(self::get_user($name), $data);
	}
	//删除本地管理员
	static function del_user($name){
		return is_file(self::get_user($name)) ? unlink(self::get_user($name)) : true;
	}

	//更新管理员密码
	static function update_user_password(){
		$username = Session::get('username');
		$userinfo = self::get_user_data($username);
		if(!empty($username) && $username == $userinfo['username']){
			$userinfo['password'] = $_POST['newpasswd'];
			if(self::add_user($userinfo['username'],$userinfo['password'],$userinfo['auth'])){
				return_json(200,'密码已更新,请谨记新密码：<font color="red">'.$_POST['newpasswd'].'</font>');
			}
		}
		return_json(300,'更新失败：<font color="red">你可能无权更改此项设置！</font>');
	}

	//获取User本地数据路径
	static function get_user($name){
		return DATA_PATH.'User/'.md5($name).'.php';
	}
	//获取管理员信息
	static function get_user_data($name){
		$file = self::get_user($name);
		if($data=1 && is_file($file)){
			$data = file_get_contents($file);
			$data = self::decode_user_data($data);
		}
		if(!is_array($data)){
			$data = array('username'=>'XXX','password'=>'XXX','auth'=>'XXX');
		}
		return $data;
	}
	static function encode_user_data($data){
		$encode = '<?php if(!defined("WebFTP")){die("Forbidden Access");}?>';
		$data   = $encode.serialize($data);
		return $data;
	}
	static function decode_user_data($data){
		$decode = '<?php if(!defined("WebFTP")){die("Forbidden Access");}?>';
		return unserialize(str_replace($decode,'',$data));
	}
}
?>
