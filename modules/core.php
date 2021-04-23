<?php

// Anonsaba 3.0
// The brains behind all of this

Class Core {
	public static function Encrypt($value) {
		return password_hash($value, PASSWORD_ARGON2I);
	}
	public static function Error($val) {
		global $twig_data, $twig;
		$twig_data['errormsg'] = $val;
		self::Output('/error.tpl', $twig_data);
		die();
	}
	public static function Output($template, $data) {
		global $twig;
		$data['sitename'] = self::GetConfigOption('sitename');
		$data['version'] = self::GetConfigOption('version');
		$data['weburl'] = weburl;
		echo $twig->display($template, $data);
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