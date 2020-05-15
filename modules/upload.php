<?php
class Upload {
	/* Anonsaba 3.0 Upload class */
	function HandleUploadManage() {
		global $db;
		$imagefile_name = isset($_FILES['photo']) ? $_FILES['photo']['name'] : '';
		if ($imagefile_name != '') {
			$directory = svrpath.'manage/images/';
			$file_name = time() . mt_rand(1, 99);
			$file_info = new finfo(FILEINFO_MIME_TYPE);
			if (false === $ext = array_search(
				$file_info->file($_FILES['photo']['tmp_name']),
				array(
					'jpg' => 'image/jpeg',
					'png' => 'image/png',
					'gif' => 'image/gif',
				),
				true
			)) {
				die('Invalid file format.');
			}
			if (!move_uploaded_file($_FILES['photo']['tmp_name'], sprintf(svrpath.'manage/images/%s.%s', $file_name, $ext))) {
				die('Didn\'t move file ;-; <br> Here is some info for you <br>.'.print_r($_FILES).'<br>Server path: '.$directory);
			} else {
				die(sprintf('%s.%s', $file_name, $ext));
			}
		} else {
			echo "Blank file";
			die();
		}
	}
}