<?php
/**
 * 专题管理
 */
class Topic_Controller extends Base_Controller {
	public function __construct() {
		parent::__construct();
		$this->checkLogin(Ext_Auth::CATE_EDIT);
	}
	
	public function parseName() {
		$name = $this->input->getTrim('name');
		$eng_name = Pingyin::GetPinyin($name);
		echo $eng_name;
	}

	public function index() {
		$p = $this->input->getIntval('p');
		$tid = $this->input->getIntval('tid');
		$keyword = $this->input->get('keyword');
		$order = $this->input->get('order');
		$by = $this->input->get('by');
		$status = $this->input->get('status');
		$topicMod = load_model('Topic');
		$where = array();
		if ($tid) {
			$where['tid'] = $tid;	
		}
		if ($keyword) {
			$where[] = "name LIKE '%{$keyword}%'";	
		}
		if (Ext_Valid::check($status, 'number')) {
			$where['status'] = $status;	
		}
		if (!$order) {
			$order = 'tid';	
		}
		if (!$by) {
			$by = 'DESC';	
		}
		$total = $topicMod->getTotal($where);
		$url = "javascript:showpage('@')";
		$pageInfo = new Ext_Page($url, $total, $p, Wee::$config['web_admin_pagenum']);
		$list = $topicMod->getAll($where, $pageInfo->limit(), $order, $by);
		$this->output->set('list', $list);
		$this->output->set('pageHtml', $pageInfo->html());
		$this->output->set(array(
			'p' => $p,
			'tid' => $tid,
			'keyword' => $keyword,
			'order' => $order,
			'by' => $by,
			'status' => $status
		));
		$this->output->display('topic_show.dwt');
	}
	//图图系统
	public function delete() {
			$ids = $this->input->get('ids');
			if (!$ids) {
				show_msg("至少要选择一个专题");	
			}
			if (!is_array($ids)) {
				$ids = array($ids);	
			}
			$topicMod = load_model('Topic');
			$sTree = $topicMod->getList();
			foreach ($ids as $tid) {
				$tid = intval($tid);
				$topicMod->del($tid);
			}
			load_model('Config')->clearFileCache();
			show_msg("删除专题成功", '?c=Topic');
	}

	public function setStatus() {
		$tid = $this->input->get('tid');
		$status = $this->input->getIntval('status');
		if (is_array($tid)) {
		foreach ($tid as $value) {
		load_model('Topic')->set($value, array('status' => $status));
			}	
		} else {
		load_model('Topic')->set($tid, array('status' => $status));
		}		
	}
	
	public function add() {
		$tid = $this->input->getIntval('tid');
		$topicMod = load_model('Topic');
		
		$sList = $topicMod->getList();
		if ($tid) {
			if (!isset($sList[$tid])) {
				show_msg("$tid: 专题不存在");
			}
			$this->output->set($sList[$tid]);
		}
		
		if (check_submit()) {
			$data['name'] = $this->input->getTrim('name');
			$data['oid'] = $this->input->getIntval('oid');
			$data['eng_name'] = $this->input->getTrim('eng_name');
			if (!$data['name']) {
			show_msg('专题名称不能为空');	
			}
			$data['stpl'] = $this->input->getTrim('stpl');
			$data['stitle'] = $this->input->getTrim('stitle');
			$data['skeywords'] = $this->input->getTrim('skeywords');
			$data['sdescription'] = $this->input->getTrim('sdescription');
			$data['cover'] = $this->input->get('cover');
			$data['content'] = $this->input->getTrim('content');
	
			if (!$data['sdescription']) {
				$content = trim(strip_tags($data['content']));
				if ($content) {
					$data['sdescription'] = Ext_String::cut(preg_replace('/\s/', '', $content), 140);	
				}	
			}
	
			load_model('Config')->clearFileCache();
			if ($tid) {
				$this->db->table('#@_topic')
					->where("tid = $tid")
					->update($data);
				show_msg('编辑专题成功', '?c=Topic');	
			} else {
				$this->db->table('#@_topic')
					->insert($data);
				show_msg('添加专题成功', '?c=Topic');		
			}
				
		}
		$adminInfo = load_model('Admin')->getAdminInfo();
		$hash = md5(Wee::$config['encrypt_key'] . $adminInfo['uid'] . $tid);
		$this->output->set('uid', $adminInfo['uid']);
		$this->output->set('id', $tid);
		$this->output->set('hash', $hash);
		$this->output->display('topic_add.dwt');	
	}
}