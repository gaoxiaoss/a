<?php
/**
 * 文章管理
 */
class System_Controller extends Base_Controller {
	public function __construct() {
		parent::__construct();
		$this->checkLogin(Ext_Auth::CATE_EDIT);
	}
	
	public function parseName() {
		$name = $this->input->getTrim('name');
		$eng_name = Pingyin::GetPinyin($name);
		echo $eng_name;
	}
	
	public function show() {
		$systemMod = load_model('System');
		$sTree = $systemMod->getList();
		if (empty($sTree)) {
			show_msg("暂时还没有文章, 请先添加文章", "?c=System&a=add");	
		}
		$this->output->set('sTree', $sTree);
		$this->output->display('system_show.dwt');
	}
	
	public function updateOid() {
		if (check_submit()) {
			$oid = $this->input->get('oid');
			$systemMod = load_model('System');
			$sList = $systemMod->getList();
			if (!empty($oid)) {
				foreach ($oid as $id => $value) {
					$value = intval($value);
					if ($value != $sList[$id]['oid']) {
						$this->db->table('#@_system')
							->where("id = $id")
							->update(array('oid' => $value));	
					}
				}
			}
			load_model('Config')->clearFileCache();
			show_msg("更新排序成功", '?c=System&a=show');	
		}
	}
	
	public function delete() {
			$ids = $this->input->get('ids');
			if (!$ids) {
				show_msg("至少要选择一个文章");	
			}
			if (!is_array($ids)) {
				$ids = array($ids);	
			}
			$systemMod = load_model('System');
			$sTree = $systemMod->getList();
			foreach ($ids as $id) {
				$id = intval($id);
				$systemMod->del($id);
			}
			load_model('Config')->clearFileCache();
			show_msg("删除文章成功", '?c=System&a=show');
	}
	
	public function setStatus() {
		$id = $this->input->getIntval('id');
		$status = $this->input->getIntval('status');
		$systemMod = load_model('System');
		load_model('Config')->clearFileCache();
		$systemMod->set($id, array('status' => $status));
	}
	//图图系统
	public function add() {
		$id = $this->input->getIntval('id');
		$systemMod = load_model('System');
		
		$sList = $systemMod->getList();
		if ($id) {
			if (!isset($sList[$id])) {
				show_msg("$id: 文章不存在");
			}
			$this->output->set($sList[$id]);
		}
		
		if (check_submit()) {
			$data['name'] = $this->input->getTrim('name');
			$data['oid'] = $this->input->getIntval('oid');
			$data['eng_name'] = $this->input->getTrim('eng_name');
			if (!$data['name']) {
				show_msg('文章名称不能为空');	
			}
			$data['stitle'] = $this->input->getTrim('stitle');
			$data['skeywords'] = $this->input->getTrim('skeywords');
			$data['sdescription'] = $this->input->getTrim('sdescription');
			$data['content'] = $this->input->getTrim('content');
	
			if (!$data['sdescription']) {
				$content = trim(strip_tags($data['content']));
				if ($content) {
					$data['sdescription'] = Ext_String::cut(preg_replace('/\s/', '', $content), 140);	
				}	
			}
	
			load_model('Config')->clearFileCache();
			if ($id) {
				$this->db->table('#@_system')
					->where("id = $id")
					->update($data);
				show_msg('编辑文章成功', '?c=System&a=show');	
			} else {
				$this->db->table('#@_system')
					->insert($data);
				show_msg('添加文章成功', '?c=System&a=show');		
			}
				
		}
		$adminInfo = load_model('Admin')->getAdminInfo();
		$hash = md5(Wee::$config['encrypt_key'] . $adminInfo['uid'] . $id);
		$this->output->set('uid', $adminInfo['uid']);
		$this->output->set('id', $id);
		$this->output->set('hash', $hash);
		$this->output->display('system_add.dwt');	
	}
}