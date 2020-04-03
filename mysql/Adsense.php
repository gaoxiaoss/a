<?php
/**
 * 广告
 */
class Adsense_Model extends Model {
	
	public function __construct() {
		parent::__construct();	
		$this->setTable('#@_adsense', 'id');
	}
	
	public function getByTitle($title) {
		$res = $this->db->table('#@_adsense')->where(array('title' => $title))->getOne();
		if ($res) {
			$res['content'] = str_replace('{$web_url}', Wee::$config['web_url'], $res['content']);
			$res['content'] = str_replace('{$web_path}', Wee::$config['web_path'], $res['content']);
		}
		return $res;	
	}
	
	public function getTotal($where) {
		$res = $this->db->table('#@_adsense')
				->field("COUNT(*) AS num")
				->where($where)
				->getOne();
		return $res['num'];
	}
	
	public function getAll($where, $limit = '0, 10', $order = 'id', $by = 'DESC') {
		$res = $this->db->table('#@_adsense')
			->where($where)
			->limit($limit)
			->order("$order $by")
			->getAll();
		foreach ($res as & $value) {
			$value['content'];
		}
		return $res;	
	}
	
	public function getAd($title) {
		$cacheKey = "Adsense_".$title.'_js';
		if ($cacheData = $this->cache->getFromFile($cacheKey)) {
			return $cacheData;
		}
		//$str = $this->cache->getFromFile("Adsense_".$title);
		$info = $this->getByTitle($title);		
		$str = $info['content'];
		
		if (!$str) {
			$str = "还没添加广告哦";
		}		
		$str = str_replace('{$web_url}', Wee::$config['web_url'], $str);
		$str = str_replace('{$web_path}', Wee::$config['web_path'], $str);
		$str = str_replace('"', '\"', $str);
		$str = str_replace("\r", "\\r", $str);
		$str = str_replace("\n", "\\n", $str);
		//$str = str_replace("//", "\/\/", $str);
		$str = 'document.writeln("'.$str . '");';
	
		$this->cache->setToFile($cacheKey, $str);
		
		return $str;
	}
	
	public function getByStatus($title) {
		$val = $this->db->table('#@_adsense')->where(array('title' => $title))->getOne();
		if ($val) {
			$res = $val['status'];
		}
		return $res;	
	}
}