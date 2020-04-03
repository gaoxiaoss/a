<?php
/**
 * 归档
 */

class Archiver_Controller extends Base_Controller {
	
	public function __construct() {
		parent::__construct();
	}
	//图图系统
	public function index() {
		$this->page = $this->input->getIntval('p');
		$modArticle = load_model('Article');
		$where = array();
		$totalNum = $modArticle->getTotal($where);
		$url = url('archiver', '', array('p' => '@'));
		$pageInfo = new Ext_Page($url, $totalNum, $this->page, 100);
		$list = $modArticle->search($where, $pageInfo->limit()); 
		$this->output->set('list', $list);
		$this->output->set('pageHtml', $pageInfo->html());
		$this->assignData();
		$this->output->display('archiver.dwt');	
	}
}