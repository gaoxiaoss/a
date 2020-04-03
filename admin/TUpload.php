<?php
/**
 * 上传管理
 */
class TUpload_Controller extends Base_Controller {
	public function __construct() {
		parent::__construct();
	}
	//图图系统
	public function index() {
		$uid = $this->input->getIntval('uid');
		$tid = $this->input->getIntval('id');
		$hash = $this->input->get('hash');
		if ($hash != md5(Wee::$config['encrypt_key'] . $uid . $tid)) {
			exit("Auth 验证失败");	
		}
		$uInfo = load_model('Admin')->getByUid($uid);
		if (!$uInfo) {
			exit('用户不存在');
		}
		$modAttach = load_model('Attach');
		$file = $modAttach->makeAttachName2();
		$path = $modAttach->getAttachPath2($file);
		
		try {
			$rs = Ext_Upload::save('Filedata', $path);
			if (!$rs['error']) {
				$attachFile = $file . '.' . $rs['ext'];
				$data = $attachFile;
				echo $data;
			} else {
				echo $rs['errorMsg'];	
			}
		} catch (Error $e) {
			echo $e->getMessage();
		}
	}

}