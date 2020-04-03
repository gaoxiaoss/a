<?php
/**
 * 手机版电脑版切换
 */
class Change_Controller extends Base_Controller {
	
	public function __construct() {
		parent::__construct();
	}
	
	public function index(){
		$ua = $this->input->get('ua');
		@$url = $_SERVER["HTTP_REFERER"];
		if (!$url){
			$url = Wee::$config['web_url'];
		}

		if ($ua == 'mobile'){
			Cookie::set('template_skin', 'mobile'); //mobile
			header("Location:$url");
			return;
		}elseif ($ua == 'pc'){
			Cookie::set('template_skin', 'default'); //default
			header("Location:$url");
			return;
		}else{
			Cookie::set('template_skin', 'default'); //default
			header("Location:$url");
			return;
		}
	}
	//图图系统
	public function screen(){
			$screen = $_COOKIE['screen'];
			echo $screen;
	
	}	
}