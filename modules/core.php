<?php

// Anonsaba 3.0
// The brains behind all of this

Class Core {
	public static function Encrypt($value) {
		return password_hash($value, PASSWORD_ARGON2I);
	}
	public static function sEncrypt($data, $key=salt) {
		// Remove the base64 encoding from our key
		$encryption_key = base64_decode($key);
		// Generate an initialization vector
		$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
		// Encrypt the data using AES 256 encryption in CBC mode using our encryption key and initialization vector.
		$encrypted = openssl_encrypt($data, 'aes-256-cbc', $encryption_key, 0, $iv);
		// The $iv is just as important as the key for decrypting, so save it with our encrypted data using a unique separator (::)
		return base64_encode($encrypted . '::' . $iv);
	}
	public static function sDecrypt($data, $key=salt) {
		// Remove the base64 encoding from our key
		$encryption_key = base64_decode($key);
		// To decrypt, split the encrypted data from our IV - our unique separator used was "::"
		list($encrypted_data, $iv) = explode('::', base64_decode($data), 2);
		return openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);
	}
	public static function Error($val) {
		global $twig_data, $twig;
		$twig_data['errormsg'] = $val;
		self::Output('/error.tpl', $twig_data);
		die();
	}
	public static function Output($template, $data, $board=false) {
		global $twig, $twig_board;
		$data['sitename'] = self::GetConfigOption('sitename');
		$data['version'] = self::GetConfigOption('version');
		$data['weburl'] = weburl;
		$template = ($board) ? $twig_board->display($template, $data) : $twig->display($template, $data);
		echo $template;
	}
	public static function GetConfigOption($value) {
		global $db;
		$qry = $db->prepare('SELECT config_value FROM '.dbprefix.'site_config WHERE config_name = ?');
		$qry->execute(array($value));
		$value = $qry->fetch();
		return (is_array($value)) ? array_shift($value) : $value;
	}
	public static function Log($time, $user="Anonsaba", $message) {
		global $db;
		$qry = $db->prepare('INSERT INTO '.dbprefix.'logs (time, user, message) VALUES (?,?,?)');
		$qry->execute(array($time, $user, $message));
	}
	public static function getIP() {
		if(!empty($_SERVER['HTTP_CLIENT_IP'])){
			// ip from share internet
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
			// ip pass from proxy
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}
	public static function formatSizeUnits($bytes) {
	// Snippet from PHP Share: http://www.phpshare.org
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' Bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' Byte';
        } else {
            $bytes = '0 Bytes';
        }
		return $bytes;
	}
	public static function GetSize($dir) {
		$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS));
		$totalSize = 0;
		foreach ($iterator as $file) {
			$totalSize += $file->getSize();
		}
		return $totalSize;
	}
}