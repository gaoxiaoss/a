<?php
/**
 * 基础控制器
 */
class Base_Controller extends Controller {
	public function __construct() {
		parent::__construct();
		$this->init();
	}
	
	protected function init() {
		if ($skin = $this->input->get('skin')) {
			if (in_array($skin, load_model('Config')->getSkinList())) {
				Cookie::set('template_skin', $skin);
			}
		}
		if ($templateSkin = Cookie::get('template_skin')) {
			Wee::$config['template_skin'] = $templateSkin;	
		}
		
		$this->output->registerTag(array(
			'image' => array('Tag', 'image'),
			'article' => array('Tag', 'article'),
			'zhuanti' => array('Tag', 'zhuanti'),
			'match' => array('Tag', 'match'),
			'tags' => array('Tag', 'tags'),
			'links' => array('Tag', 'links'),
			'searchurl' => array('Tag', 'searchurl'),
			'tagsurl' => array('Tag', 'tagsurl'),
			'tagsallurl' => array('Tag', 'tagsallurl'),
			'rssurl' => array('Tag', 'rssurl'),
			'systemurl' => array('Tag', 'systemurl'),
			'sitemapurl' => array('Tag', 'sitemapurl'),
			'topicurl' => array('Tag', 'topicurl'),
			'links' => array('Tag', 'Links'),
			'relevant' => array('Tag', 'relevant'),
			'relevant_vod' => array('Tag', 'relevant_vod'),
			'adsense' => array('Tag', 'adsense'),
		));
		
		$pluginDir = APP_PATH . 'plugin/';
		$pluginFiles = Ext_Dir::getDirList($pluginDir, Ext_Dir::TYPE_FILE, array(), array('php'));
		foreach ($pluginFiles as $value) {
			import_file($pluginDir . $value);
		} 
	}
	//图图系统
	protected function assignData() {
		$this->output->set(array(
			'sys_name' => Wee::$config['sys_name'],
			'sys_url' => Wee::$config['sys_url'],
			'sys_ver' => Wee::$config['sys_ver'],
			'web_uri' => Wee::$config['web_uri'],
			'web_dir' => Wee::$config['web_dir'],
			'web_script' => Wee::$config['web_script'],
			'web_host' => Wee::$config['web_host'],
			'web_copyright' => Wee::$config['web_copyright'],
			'web_description' => Wee::$config['web_description'],
			'web_skin' => Wee::$config['template_skin'],
			'web_skin_dir' => 'template/'. Wee::$config['template_skin'].'/',
			'web_style_dir' => 'template/'. Wee::$config['template_skin'].'/style.css',
			'web_email' => Wee::$config['web_email'],
			'web_hotkey' => Wee::$config['web_hotkey'],
			'web_icp' => Wee::$config['web_icp'],
			'web_keywords' => Wee::$config['web_keywords'],
			'web_name' => Wee::$config['web_name'],
			'web_path' => Wee::$config['web_path'],
			'web_tongji' => Wee::$config['web_tongji'],
			'web_url' => Wee::$config['web_url'],
			'ad_switch' => Wee::$config['ad_switch'],
			'url_mode' => Wee::$config['url_mode'],
			'url_suffix' => Wee::$config['url_suffix'],
			'web_c_p' => Wee::$config['web_c_p'],
			'web_ds' => Wee::$config['web_ds'],
			'web_cy_appid' => Wee::$config['web_cy_appid'],			
			'web_cy_conf' => Wee::$config['web_cy_conf'],
			'web_uy_uid' => Wee::$config['web_uy_uid'],
			'web_cookie' => Cookie::get('template_skin'),
		));	
		$cMod = load_model('Cate');
		$cList = $cMod->getList();
		$cTree = $cMod->getTree();
		$this->output->set(array(
			'cateTree' => $cTree,
			'cateList' => $cList,
		));
	}
}