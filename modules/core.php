<?php

// Anonsaba 3.0
// The brains behind all of this

Class Core {
	public static function Encrypt($value) {
		return password_hash($value, PASSWORD_ARGON2I);
	}
	public static function Error($val) {
		global $twig_data, $twig;
		$twig_data['site_name'] = self::GetConfigOption('site_name');
		$twig_data['version'] = self::GetConfigOption('version');
		$twig_data['errormsg'] = $val;
		self::Output('/error.tpl', $twig_data);
		die();
	}
	public static function Output($template, $data) {
		global $twig;
		echo $twig->display($template, $data);
	}
	public static function GetConfigOption($value) {
		global $db;
		return $db->GetOne('SELECT config_value from '.dbprefix.'site_config WHERE config_name = '.$db->quote($value));
	}
}