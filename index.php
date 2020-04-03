<?php

define('APP_PATH', rtrim(dirname(__FILE__), '/\\') . DIRECTORY_SEPARATOR);

if (!is_file(APP_PATH . 'data/config.php')) {
	header('Location: install.php');	
}

require_once './core/loader.php';
Wee::run();