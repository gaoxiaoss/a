<?php
/**
 * 主控制器
 */
class Main_Controller extends Base_Controller {
	public function __construct() {
		parent::__construct();
	}
	
	public function index() {		
		$this->assignConfig();
		$this->output->display('index.dwt');
	}
	
	public function checkno() {		
		
		$this->output->display('checkno.dwt');
	}
	
	public function main() {
		$this->checkLogin();
		$this->output->set('_SERVER', $_SERVER);
		$this->output->set('config', Wee::$config);
		$this->output->set('app_path', APP_PATH);
		$this->output->display('main.dwt');	
	}
	
	public function top() {
		$this->checkLogin();
		$adminInfo = load_model('Admin')->getAdmininfo();
		$this->output->set('adminInfo', $adminInfo);
		$this->output->display('top.dwt');
	}

	public function left() {
		$this->checkLogin();
		$this->output->registerTag('checkPre', '$this->top');
		$this->output->display('left.dwt');
	}
	public function bottom() {
		$this->checkLogin();
		$this->output->registerTag('checkPre', '$this->bottom');
		$this->output->display('bottom.dwt');
	}	
//图图系统	
	public function login() {
		if ($this->checkLogin()) {
			show_msg('已经登录过', '?c=Main&a=index', 0);	
		}
		if (check_submit('post')) {
			if (Wee::$config['admin_vcode']) {
				$vcode = $this->input->getTrim('vcode');
				if (!$vcode || (strtolower($vcode) != strtolower(Cookie::get('p_c_vcode')))) {
					show_msg('', '?c=Main&a=checkno', 0);
					return;
				}
			}
			$name = $this->input->getTrim('name');
			$password = $this->input->get('password');
			$adminMod = load_model('Admin');
			$uInfo = $adminMod->login($name, $password);
			if (!$uInfo) {
				show_msg('', '?c=Main&a=checkno', 0);
			}
			show_msg('', '?c=Main&a=index', 0);
		}
		$this->assignConfig();
		$this->output->display('login.dwt');	
	}
	
	public function logout() {
		$adminMod = load_model('Admin');
		$adminMod->logout();
		show_msg('', '?c=Main&a=login', 0);	
	}
	
	public function vcode() {
		$vcode = Ext_String::getSalt();
		Cookie::set('p_c_vcode', $vcode);
		Ext_Image::vcode($vcode, 76, 22);	
	}
	
}