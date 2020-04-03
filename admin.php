<?php

define('APP_PATH', rtrim(dirname(__FILE__), '/\\') . DIRECTORY_SEPARATOR);

if (!is_file(APP_PATH . 'data/config.php')) {
	header('Location: install.php');	
}
require_once './core/loader.php';

Wee::$config['controller_path'] = APP_PATH . 'admin/';
Wee::$config['entrance'] = Wee::ENTRANCE_ADMIN;
Wee::run();