<?php
/**
 * 配置管理
 */
class Config_Controller extends Base_Controller {
	public function __construct() {
		parent::__construct();
		$this->checkLogin(Ext_Auth::SYS_EDIT);
	}
	
	public function index() {
		$type = $this->input->get('type');
		if (!$type) $type = 'web';
		$modConfig = load_model('Config');
		
		$cateMod = load_model('Cate');
		$cTree = $cateMod->getTree();
		$topicMod = load_model('Topic');
		$sTree = $topicMod->getList();
		$systemMod = load_model('System');
		$aTree = $systemMod->getList();
		
		if (check_submit()) {
			$data = $this->input->get('con');		
			$modConfig->setConfig($data);
			$modConfig->clearFileCache();
			show_msg("操作成功", "?c=Config&type=$type");
		}
		if ('web' == $type) {
			$skinList = $modConfig->getSkinList();
			$this->output->set('skinList', $skinList);
		}
		if ('skin' == $type) {
			$source = '/template/';
			$this->output->set('skindir', $source);
			$skinList = $modConfig->getSkinList();
			$this->output->set('skinList', $skinList);
		}
		$this->output->set('cTree', $cTree);
		$this->output->set('sTree', $sTree);
		$this->output->set('aTree', $aTree);
		$this->output->set(Wee::$config);
		$this->output->display("config_$type.dwt");	
	}
	
//图图系统	
	public function setSkin() {
		$modConfig = load_model('Config');
		$skin = $this->input->get('skin');
		$modConfig->setConfig('template_skin', $skin);
		$modConfig->clearFileCache();
		$this->output->set(Wee::$config);
	}

	public function clearCache() {
		$type = $this->input->get('type');
		$htmlCachePath = Wee::$config['data_path'] . 'html_cache/' . Wee::$config['template_skin'] . '/';
		switch ($type) {
			case 'index':
				Ext_Dir::del($htmlCachePath . 'index/');
			break;
			case 'cate':
				Ext_Dir::del($htmlCachePath . 'cate/');
			break;
			case 'article':
				Ext_Dir::del($htmlCachePath . 'article/');
			break;	
			case 'html':
				Ext_Dir::del(Wee::$config['data_path'] . 'html_cache/');
			break;	
			case 'file':	
				Ext_Dir::del(Wee::$config['data_path'] . 'cache/');	
			break;		
			case 'tpl':
				Ext_Dir::del(Wee::$config['data_path'] . 'tpl_compile/');
			break;
			default:
				Ext_Dir::del(Wee::$config['data_path'] . 'html_cache/');
				Ext_Dir::del(Wee::$config['data_path'] . 'cache/');	
				Ext_Dir::del(Wee::$config['data_path'] . 'tpl_compile/');	
		}
		show_msg('操作完成');	
	}
}