<?php
/**
 * 广告
 */

class Ad_Controller extends Base_Controller {
	
	public function __construct() {
		parent::__construct();
	}
	//图图系统
	public function index() {
		$title = Ext_Filter::sqlChars($this->input->getTrim('title'));
		$str = load_model('Adsense')->getAd($title);
		echo $str;
	}

}