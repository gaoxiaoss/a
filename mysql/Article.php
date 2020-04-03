<?php
/**
 * 文章
 */
class Article_Model extends Model {
	public function __construct() {
		parent::__construct();
	}	
	
	public function search($where, $limit = '0, 10', $order = 'id', $by = 'DESC') {
		if (!isset($where['status'])) {
			$where['status'] = 1;	
		}
		if ($where['status'] < 0) {
			unset($where['status']);	
		}
		$res = $this->db->table('#@_article')
					->field('id, cid, title, short_title, tag, color, cover, author, comeurl, remark, hits, star, status, up, down, addtime')->where($where)
					->limit($limit)
					->order($order.' '.$by)
					->getAll();
		foreach ($res as &$value) {
			$value = $this->getVo($value);	
		}
		unset($value);
		return $res;
	}
	
	public function getTotal($where = array()) {
		if (!isset($where['status'])) {
			$where['status'] = 1;	
		}
		if ($where['status'] < 0) {
			unset($where['status']);	
		}
		$res = $this->db->table('#@_article')
					->field("COUNT(*) AS num")
					->where($where)
					->getOne();
		return $res['num'];	
	}
	
	public function getVo($value) {
		$modAttach = load_model('Attach');
		$cMod = load_model('Cate');
		
		$value['cate'] = load_model('Cate')->get($value['cid']);
		
		if ($value['tag']) {
			$value['tagArr'] = explode(',', $value['tag']);	
		} else {
			$value['tagArr'] = array();	
		}
		$value['url'] = $this->getUrl($value['id']);
		$value['pubdate'] = Ext_Date::format($value['addtime']);
		$value['pubdate2'] = Ext_Date::format($value['addtime'],'Y-m-d H:i'); //Y-m-d H:i:s
		$value['pubdate3'] = Ext_Date::format($value['addtime'],'Y-m-d');
		$value['pubdate4'] = Ext_Date::format($value['addtime'],'m-d');
		
		$value['cover_url'] = '';
		if ($value['cover']) {
			$value['cover_url'] = $modAttach->getAttachUrl($value['cover']);
		}
		return $value;
	}
	
	public function getUrl($id, $page = 0) {
		if (Wee::$config['url_html_content']) {
			$url = Wee::$config['web_url'] . $this->_getName($id, $page);
		} else {
			if ($page > 1) {
				$url = url('Article', '', array('id' => $id, 'p' => $page));
			} else {
				$url = url('Article', '', array('id' => $id));	
			}
		}
		return $url;
	}
	
	public function getPath($id, $page = 0) {
		return APP_PATH . $this->_getName($id, $page);
	}
	
	private function _getName($id, $page = 0) {
		$cateMod = load_model('Cate');
		$cTree   = $cateMod->getTree();
		$get_cid = $this->getCid($id);
		$cid = $get_cid['cid'];
		$get_info= $cateMod->get($cid);	

		//判断是否顶级目录
		if ($get_info['pid'] == '0')
		{
			$mod = $cTree[$cid]['eng_name'];
		}else{
			$mod = $get_info['eng_name'];
		}
		if ($page > 1) {
			$name = "{$mod}/{$id}-{$page}" . Wee::$config['url_suffix']; 	
		} else {
			$name = "{$mod}/{$id}" . Wee::$config['url_suffix'];	
		}
		if (Wee::$config['url_dir_content']) {
			$name = Wee::$config['url_dir_cate'] . '/' . $name;
		} 
		return $name;
	}
	
	public function getCid($id) {
		if (!isset($id)) {
			$id = 1;
		}
		if ($id < 0) {
			unset($id);	
		}	
		$res = $this->db->table('#@_article')
					->field("cid")
					->where("id = '$id'")
					->getOne();		
		return $res;	
	}
	
	public function get($articleId) {
		$cacheKey = __METHOD__ . "{$articleId}";
		$cacheData = $this->cache->getFromBox($cacheKey);
		if ($cacheData) {
			return $cacheData;	
		}
		$rs = $this->db->table('#@_article')->where("id = $articleId")->getOne();
		if ($rs) {
			$rs = $this->getVo($rs);
		}
		$this->cache->setToBox($cacheKey, $rs);
		return $rs;	
	}
	
     public function getPre($id,$cid) {
		$res  = $this->search("id > $id AND cid = $cid AND status = 1", 1, 'id', 'ASC');
		if ($res) {
			$res = $res[0];
		} 
		return $res;
	}

	
	public function getNext($id,$cid) {
		$res  = $this->search("id < $id AND cid = $cid AND status = 1", 1, 'id', 'DESC');
		if ($res) {
			$res = $res[0];
		} 
		return $res;	
	}
	
	public function del($articleId) {
		$modAttach = load_model('Attach');
		$attachList = $modAttach->getAttachList($articleId);
		
		if (!empty($attachList)) {
			foreach ($attachList as $value) {
				$modAttach->delByInfo($value);
			}
		}
		
		$this->setTags($articleId, null);
		$rs = $this->db->table('#@_article')->where("id = $articleId")->delete();
		return $rs;
	}
	
	public function set($id, $data) {
		$rs = $this->db->table('#@_article')->where(array('id' => $id))->update($data);
		return $rs;	
	}
	
	public function add($data) {
		$this->db->table('#@_article')->insert($data);
		return $this->db->insertId();	
	}
	
	public function parseTags($title) {
		$tagList = $this->getTags();
		$tag = array();
		if ($tagList) {
			foreach ($tagList as $value) {
				if (false !== strpos($title, $value['tag'])) {
					$tag[] = $value['tag'];
					$title = str_replace($value['tag'], '', $title);	
				}
			}
		}
		return implode(',', $tag);	
	}
	
	public function Dz_Segments($title) {
		$rows = strip_tags($title);
		$arr = array(' ',' ',"\s", "\r\n", "\n", "\r", "\t", ">", "“", "”","<br />");
		$qc_rows = str_replace($arr, '', $rows);
		if(strlen($qc_rows)>2400){
			$qc_rows = substr($qc_rows, '0', '2400');
		}
		$data = @implode('', file("http://keyword.discuz.com/related_kw.html?title=$qc_rows&ics=utf-8&ocs=utf-8"));
		preg_match_all("/<kw>(.*)A\[(.*)\]\](.*)><\/kw>/",$data, $out, PREG_SET_ORDER);
	
		$tagList = $out;
		$tag = array();
		if ($tagList) {
			foreach ($tagList as $value) {
				if (false !== strpos($title, $value['2'])) {
					$tag[] = $value['2'];
					$title = str_replace($value['2'], '', $title);	
				}
			}
		}
		return implode(',', $tag);
	}	

	public function getTags($limit = 0) {
		if ($limit) {
			$this->db->limit($limit);	
		}
		//$rs = $this->db->table('#@_tags')->field("tag, COUNT(*) AS num")->group('tag')->getAll();		
		$by = 'count DESC';
		$rs = $this->db->table('#@_tags')
					->field('tag, count( * ) AS count')->group('tag')
					->limit($limit)
					->order($by)
					->getAll();
		foreach ($rs as & $value) {
			$value = $this->getTagVo($value);	
		}
		return $rs;	
	}
	
	public function getTagsAll() {
		$by = 'count DESC';
		$rs = $this->db->table('#@_tags')
					->field('tag, count( * ) AS count')->group('tag')
					->order($by)
					->getAll();
		foreach ($rs as & $value) {
			$value = $this->getTagVo($value);	
		}
		return $rs;	
	}	
	
	public function getTagVo($value) {
		$value['url'] = url('Tags', '', array('tag' => $value['tag']));
		$value['star'] = mt_rand(0, 4);
		return $value;
	}
	
	public function setTags($articleId, $tag, $title = '') {
		$this->db->table('#@_tags')->where("article_id = $articleId")->delete();
		if ($tag) {
			$tag = explode(',', $tag);
			$data = array();
			foreach ($tag as $key => $value) {
				$data[$key] = array(
					'tag' => trim($value),
					'article_id' => $articleId,
					'title' => $title,	
				);
			}
			$this->db->table('#@_tags')->insert($data);
		} else {
			$this->db->table('#@_tags')->where(array('article_id' => $articleId))->delete();
		}	
	}
	
	public function getTagsTotal($tag) {
		$res = $this->db->table('#@_tags')
				->field("COUNT(tag) AS num")
				->where("tag = '$tag'")
				->getOne();
		return $res['num'];
	}
		
	public function getTagsTotal_All() {
		$rs = $this->db->table('#@_tags')
					->field('tag, count( * ) AS count')->group('tag')
					->order('count DESC')
					->getAll();
		$str = count($rs);
		return $str;
	}
	
	public function getTagsArticle($tag, $limit = '0, 10') {
		$res = $this->db->table('#@_tags')->where(array('tag' => $tag))->limit($limit)->getAll();
		if ($res) {
			$ids = Ext_Array::cols($res, 'article_id');
			if (false !== strpos($limit, ',')) {
				list(,$limit) = explode(',', $limit);
			}
			$res = $this->search(array('id' => $ids), $limit);	
		}
		return $res;
	}
}