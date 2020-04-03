<?php
/**
 * 广告管理
 */
class Adsense_Controller extends Base_Controller {
	
	public function __construct() {
	parent::__construct();
	$this->checkLogin(Ext_Auth::CONTENT_EDIT);
	}
	
	public function index() {
		$p = $this->input->getIntval('p');
		$id = $this->input->getIntval('id');
		$keyword = $this->input->get('keyword');
		$order = $this->input->get('order');
		$by = $this->input->get('by');
		$status = $this->input->get('status');
		$modAdsense = load_model('Adsense');
		$where = array();
		if ($id) {
			$where['id'] = $id;	
		}
		if ($keyword) {
			$where[] = "des LIKE '%{$keyword}%'";	
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
		$total = $modAdsense->getTotal($where);
		$url = "javascript:showpage('@')";
		$pageInfo = new Ext_Page($url, $total, $p, Wee::$config['web_admin_pagenum']);
		$list = $modAdsense->getAll($where, $pageInfo->limit(), $order, $by);	
		
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
		$this->output->display('adsense_show.dwt');

	}
	
	public function setStatus() {
		$id = $this->input->get('id');
		$status = $this->input->getIntval('status');
		if (is_array($id)) {
			foreach ($id as $value) {
				load_model('Adsense')->set($value, array('status' => $status));
			}	
		} else {
			load_model('Adsense')->set($id, array('status' => $status));
		}		
	}
	
	public function edit() {
		$id = $this->input->getIntval('id');
		$list = load_model('Adsense')->getList();
		if ($id) {
			$info = load_model('Adsense')->get($id);
			$this->output->set($info);	
		}
		$this->output->set('list', $list);
		$this->output->set('id', $id);
		$this->output->display('adsense_edit.dwt');
	}	
	
	public function preview() {
		$id = $this->input->getIntval('id');
		$info = load_model('Adsense')->get($id);
		$info['content'] = str_replace('{$web_url}', Wee::$config['web_url'], $info['content']);
		$info['content'] = str_replace('{$web_path}', Wee::$config['web_path'], $info['content']);
		echo $info['content'];
	}
//图图系统	
	public function add() {
		$id = $this->input->getIntval('id');
		if (check_submit('submit')) {
			$title = $this->input->getTrim('title');
			$content = $this->input->getTrim('content');
			$des = $this->input->getTrim('des');
			$status = $this->input->getTrim('status');
			if (!$title) {
				show_msg("广告标识不能为空");	
			}
			$modAdsense = load_model('Adsense');
			$list = $modAdsense->getList("title = '$title'");
			if ($list) {
				if ($list[0]['id'] != $id) {
					show_msg("该标识已经存在");
				}	
			}
			$modAdsense = load_model('Adsense');
			$data = array(
				'title' => $title,
				'content' => $content,
				'des' => $des,
				'status' => $status,
			);
			
			load_model('Config')->clearFileCache();
			
			if ($id) {
				$modAdsense->set($id, $data);
				show_msg("修改成功", '?c=Adsense');
			} else {
				$modAdsense->add($data);
				show_msg("添加成功", '?c=Adsense');
			}
		}
	}
	
	public function del() {
		$id = $this->input->get('id');
		if (is_array($id)) {
			foreach ($id as $value) {
				load_model('Adsense')->del($id);
			}	
		} else {
			load_model('Adsense')->del($id);
		}
	}
}