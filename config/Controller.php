<?php
/**
 * 控制器基类
 */
class Controller {	
	 	
	protected $db;	
	protected $cache;	
	protected $input;	
	protected $output;
	//图图系统
	public function __construct() {
		$this->input  = &Wee::$input;
		$this->output = &Wee::$output;
		if (Wee::$config['cache_auto_start']) {
			$this->cache = load_cache();
		}
		if (Wee::$config['db_auto_start']) {
			$this->db = load_db();	
		}
	}	
	
	public function __call($action, $args) {
		show_msg("$action: The action does not exist");
	}
}