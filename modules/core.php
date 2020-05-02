<?php

// Anonsaba 3.0
// The brains behind all of this

Class Core {
	public static function Encrypt($value) {
		return openssl_encrypt($value, 'aes-128-gcm', salt);
	}
	public static function Error($val) {
		global $twig_data, $twig, $db;
		$twig_data['site_name'] = self::GetConfigOption('sitename');
		$twig_data['version'] = self::GetConfigOption('version');
		$twig_data['errormsg'] = $val;
		self::Output('/error.tpl', $twig_data);
		die();
	}
	public static function Output($val1, $val2) {
		global $twig;
		echo $twig->display($val1, $val2);
	}
	public static function GetConfigOption($value) {
		global $db;
		return $db->Run('SELECT config_value from '.dbprefix.'site_config WHERE config_name = '.$value);
	}
}