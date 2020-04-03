<?php
	
define('APP_PATH', rtrim(dirname(__FILE__), '/\\') . DIRECTORY_SEPARATOR);
require_once './core/loader.php';
Wee::run('Attach', 'index');