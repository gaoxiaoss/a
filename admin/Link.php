<?php
/**
 * 友情链接
 */
class Link_Controller extends Base_Controller {
	
	public function __construct() {
	parent::__construct();
	$this->checkLogin(Ext_Auth::CONTENT_EDIT);
	}
	//图图系统
	public function index() {
		$p = $this->input->getIntval('p');
		$id = $this->input->getIntval('id');
		$keyword = $this->input->get('keyword');
		$order = $this->input->get('order');
		$by = $this->input->get('by');
		$status = $this->input->get('status');
		$modLink = load_model('Link');
		$where = array();
		if ($id) {
			$where['id'] = $id;	
		}
		if ($keyword) {
			$where[] = "title LIKE '%{$keyword}%'";	
		}
		if (Ext_Valid::check($status, 'number')) {
			$where['status'] = $status;	
		}
		if (!$order) {
			$order = 'id';	
		}
		if (!$by) {
			$by = 'DESC';	
		}
		$total = $modLink->getTotal($where);
		$url = "javascript:showpage('@')";
		$pageInfo = new Ext_Page($url, $total, $p, Wee::$config['web_admin_pagenum']);
		$list = $modLink->getAll($where, $pageInfo->limit(), $order, $by);
		$this->output->set('list', $list);
		$this->output->set('pageHtml', $pageInfo->html());
		$this->output->set(array(
			'p' => $p,
			'id' => $id,
			'keyword' => $keyword,
			'order' => $order,
			'by' => $by,
			'status' => $status
		));
		$this->output->display('link_show.dwt');
	}	
	
	
	public function add() {
		$id = $this->input->getIntval('id');
		
		if (check_submit('submit')) {
			$title = $this->input->getTrim('title');
			$url = $this->input->getTrim('url');
			$logo = $this->input->getTrim('logo');
			if (!$title) {
				show_msg("网站名称不能为空");	
			}
			$data = array(
				'title' => $title,
				'url' => $url,
				'logo' => $logo,
				'oid' => $this->input->getIntval('oid'),
				'type' => $this->input->getIntval('type')	
			);
			
			load_model('Config')->clearFileCache();
			if ($id) {
				load_model('Link')->set($id, $data);
				show_msg("修改成功", '?c=Link');
			} else {
				load_model('Link')->add($data);
				show_msg("添加成功", '?c=Link');
			}
		}
	}
	
	public function edit() {
		$id = $this->input->getIntval('id');
		$list = load_model('Link')->getList();
		if ($id) {
			$info = load_model('Link')->get($id);
			$this->output->set($info);	
		}
		$this->output->set('list', $list);
		$this->output->set('id', $id);
		$this->output->display('link_add.dwt');
	}
	
	public function setStatus() {
		$id = $this->input->get('id');
		$status = $this->input->getIntval('status');
		if (is_array($id)) {
			foreach ($id as $value) {
				load_model('Link')->set($value, array('status' => $status));
			}	
		} else {
			load_model('Link')->set($id, array('status' => $status));
		}		
	}
	
	public function del() {
		$id = $this->input->get('id');
		if (is_array($id)) {
			foreach ($id as $value) {
				load_model('Link')->del($id);
			}	
		} else {
			load_model('Link')->del($id);
		}
	}
	
}