<?php
class Upload {
	/* Anonsaba 3.0 Upload class */
	function HandleUploadManage() {
		global $db;
		try {
			if (!isset($_FILES['photo']['error']) || is_array($_FILES['photo']['error'])) {
				throw new RuntimeException('Error!');
			}
			switch ($_FILES['photo']['error']) {
				case UPLOAD_ERR_OK:
					break;
				case UPLOAD_ERR_NO_FILE:
					throw new RuntimeException('No file sent.');
				case UPLOAD_ERR_INI_SIZE:
				case UPLOAD_ERR_FORM_SIZE:
					throw new RuntimeException('Exceeded filesize limit.');
				default:
					throw new RuntimeException('Unknown errors.');
			}
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
					throw new RuntimeException('Failed to upload!');
				} else {
					die(sprintf('%s.%s', $file_name, $ext));
				}
			} else {
				throw new RuntimeException('Blank file!');
			}
			die();
		} catch (RuntimeException $e) {
			echo $e->getMessage();
			die();
		}
		die();
	}
}