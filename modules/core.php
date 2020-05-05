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
		echo $twig->display($template, $data);
	}
	public static function GetConfigOption($value) {
		global $db;
		return $db->GetOne('SELECT config_value from '.dbprefix.'site_config WHERE config_name = '.$db->quote($value));
	}
	public static function Log($time, $user="Anonsaba", $message) {
		global $db;
		$db->Run('INSERT INTO '.dbprefix.'logs (time, user, message) VALUES ('.$time.', '.$db->quote($user).', '.$db->quote($message).')');
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
}