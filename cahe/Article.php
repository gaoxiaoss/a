<?php
/**
 * 文章管理
 */
class Article_Controller extends Base_Controller {
	public function __construct() {
		parent::__construct();
		$this->checkLogin(Ext_Auth::CONTENT_EDIT);
	}	
	
	public function show() {
		$p = $this->input->getIntval('p');
		$cid = $this->input->getIntval('cid');
		$star = $this->input->get('star');
		$status = $this->input->get('status');
		$keyword = $this->input->getTrim('keyword');
		$order = $this->input->get('order');
		$by = $this->input->get('by');
		$cateMod = load_model('Cate');
		$cTreeStr = $cateMod->printTree('cid', $cid, false);
		$where = array();
		if ($cid) {
			$cate = $cateMod->getPlace($cid);
			if ($cate['sonId']) {
				$where['cid'] = $cate['sonId'];
				array_unshift($where['cid'], $cid);
			} else {
				$where['cid'] = $cid;
			}
		} 
		if (Ext_Valid::check($star, 'number')) {
			$where['star'] = $star;	
		}
		if (Ext_Valid::check($status, 'number')) {
			$where['status'] = $status;
		}
		if ($keyword) {
			$where[] = "title LIKE '%$keyword%' OR id LIKE '%$keyword%'";
		}
		
		if (!$order) {
			$order = 'id';	
		}
		if (!$by) {
			$by = 'DESC';	
		}
		if (!isset($where['status'])) {
			$where['status'] = -1;
		}
		$articleMod = load_model('Article');
		$modAttach = load_model('Attach');
		$url = "javascript:showpage('@')";
		$totalNum = $articleMod->getTotal($where);
		$page = new Ext_Page($url, $totalNum, $p, Wee::$config['web_admin_pagenum']);
		$articleList = $articleMod->search($where, $page->limit(), $order, $by);
	
		$modAttach = load_model('Attach');
		foreach ($articleList as & $value) {
			if ($value['cover']) {
				$value['cover_is_http'] = $modAttach->isHttp($value['cover']);
				if ($value['cover_is_http']) {
					$value['cover_thumb_url'] = $value['cover_url'];
				} else {
					$value['cover_thumb_url'] = $modAttach->getAttachUrl($modAttach->getThumbAttach($value['cover']));	
				}
			}
		}
		
		$moveCTreeStr = $cateMod->printTree('movecid', 0, false, false);
		$this->output->set('pageHtml', $page->html());
		$this->output->set(array(
			'cid' => $cid,
			'star' => $star,
			'status' => $status,
			'keyword' => $keyword,
			'order' => $order,
			'by' => $by,
			'p' => $p,
			'articleList' => $articleList,
			'cTreeStr' => $cTreeStr,
			'moveCTreeStr' => $moveCTreeStr,
		));
		$this->output->display('article_show.dwt');
	}
//图图系统	
	public function setStar() {
		$id = $this->input->get('id');
		$star = $this->input->getIntval('star');
		$articleMod = load_model('Article');
		$articleMod->set($id, array('star' => $star));	
	}
	
	public function setStatus() {
		$id = $this->input->get('id');
		$status = $this->input->getIntval('status');
		$articleMod = load_model('Article');
		if (!is_array($id)) {
			$id = array($id);	
		}			
		foreach ($id as $value) {
			$articleMod->set($value, array('status' => $status));		
		}	
	}
	
	public function setAddtime() {
		$id = $this->input->get('id');
		$day = $this->input->getIntval('day');
		$articleMod = load_model('Article');
		$time = time();//当前时间
		
		if ($day == '3') {
			$addtime = $time - (3600*24*2); //两天前的时间
		}elseif ($day == '2') {
			$addtime = $time - (3600*24*1); //一天前的时间
		}else{
			$addtime = $time; //当前时间
		}		
			
		if (!is_array($id)) {
			$id = array($id);	
		}
		foreach ($id as $value) {
			$articleMod->set($value, array('addtime' => $addtime));	
		}	
	}
	
	public function delArticle() {
		$id = $this->input->get('id');
		$articleMod = load_model('Article');
		if (!is_array($id)) {
			$id = array($id);	
		}		
		foreach ($id as $value) {
			$articleMod->del($value);
		}
	}	
	
	public function moveCate() {
		$id = $this->input->get('id');
		$cid = $this->input->get('cid');
		if (!is_array($id)) {
			$id = array($id);	
		}		
		$articleMod = load_model('Article');
		foreach ($id as $value) {
			$articleMod->set($value, array('cid' => $cid));
		}	
	}	
	
	public function parseTag() {
		$title = $this->input->getTrim('title');
		$tag = load_model('Article')->parseTags($title);
		echo $tag;
	}
	
	public function Dz_Segment() {
		$title = $this->input->getTrim('title');
		$tag = load_model('Article')->Dz_Segments($title);
		echo $tag;
	}

	public function add() {
		$id = $this->input->getIntval('id');
		$cid = $this->input->getIntval('cid');
		$cateMod = load_model('Cate');
		$cList = $cateMod->getList();
		$articleMod = load_model('Article');
		if ($id) {
			$articleInfo = $articleMod->get($id);
			if (!$articleInfo) {
				show_msg("$id: 文章不存在");
			}
			$this->output->set($articleInfo);
			$cid = $articleInfo['cid'];
		}
		$cTreeStr = $cateMod->printTree('cid', $cid, false, false);	
		if (check_submit()) {
			$data = array( 
				'cid' => $this->input->getIntval('cid'),
				'star' => $this->input->getIntval('star'),
				'status' => $this->input->getIntval('status'),
				'title' => $this->input->getTrim('title'),
				'short_title' => $this->input->getTrim('short_title'),
				'tag' => $this->input->getTrim('tag'),
				'color' => $this->input->get('color'),
				'comeurl' => $this->input->getTrim('comeurl'),
				'cover' => $this->input->getTrim('cover'),
				'author' => $this->input->getTrim('author'),
				'hits' => $this->input->getIntval('hits'),
				'addtime' => $this->input->getTrim('addtime'),
				'keywords' => $this->input->getTrim('keywords'),
				'remark' => $this->input->getTrim('remark'),
				'content' => $this->input->getTrim('content'),
			);
			if (!$data['cid']) {
				show_msg("请选择分类");	
			}
			if (!$data['title']) {
				show_msg("标题不能为空");		
			}
			if ($data['addtime']) {
				$data['addtime'] = strtotime($data['addtime']);	
			} else {
				$data['addtime'] = time();	
			}
			if (!$data['remark']) {
				$content = trim(strip_tags($data['content']));
				if ($content) {
					$data['remark'] = Ext_String::cut(preg_replace('/\s/', '', $content), 140);	
				}	
			}
			$modAttach = load_model('Attach');
			$aList = $modAttach->getAll($id, $this->adminInfo['uid']);
			if (!$data['cover']) {
				if (count($aList) > 0) {
					$tmp = reset($aList);
					$data['cover'] = $tmp['file'];
				}	
			}
			$attachRemark = $this->input->get('attach_remark');
			if ($id) {
				$articleMod->set($id, $data);
				
				if ($articleInfo['tag'] != $data['tag']) {
					$articleMod->setTags($id, $data['tag'], $data['title']);
				}
				foreach ($aList as $value) {
					$attachId = $value['id'];
					if ($value['remark'] != $attachRemark[$attachId]) {
						$data = array('remark' => trim($attachRemark[$attachId]));
						$modAttach->set($attachId, $data);
					}	
				}
				show_msg("编辑成功", "?c=Article&a=show");
			} else {
				$articleId = $articleMod->add($data);
				$articleMod->setTags($articleId, $data['tag'], $data['title']);
				foreach ($aList as $value) {
					$attachId = $value['id'];
					$data = array('article_id' => $articleId);
					if ($value['remark'] != $attachRemark[$attachId]) {
						$data['remark'] = trim($attachRemark[$attachId]);
					}
					$modAttach->set($attachId, $data);	
				}
				show_msg("添加成功, 继续添加新内容", "?c=Article&a=add");
			}			
		}

		$adminInfo = load_model('Admin')->getAdminInfo();
		$hash = md5(Wee::$config['encrypt_key'] . $adminInfo['uid'] . $id);
		$this->output->set('uid', $adminInfo['uid']);
		$this->output->set('id', $id);
		$this->output->set('hash', $hash);
		$this->output->set('cTreeStr', $cTreeStr);
		$this->output->display('article_add.dwt');	
	}
}