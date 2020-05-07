<?php

// Anonsaba 3.0 - Management Console

class Management {
	public static function validateSession($manage=false) {
		global $db;
		if(isset($_SESSION['manage_username']) && isset($_SESSION['sessionid'])) {
			if($_SESSION['sessionid'] == $db->GetOne('SELECT sessionid FROM '.dbprefix.'staff WHERE username = '.$db->quote($_SESSION['manage_username']))) {
				return true;
			} else {
				self::destroySession($_SESSION['manage_username']);
				self::loginForm('1', 'Invalid Session!');
			}
		} else {
			if(!$manage) {
				die(self::loginForm('2', ''));
			} else {
				return false;
			}
		}
	}
	public static function createSession($user) {
		global $db;
		$chars = hash;
		$sessionid = '';
		for ($i = 0; $i < strlen($chars); ++$i) {
			$sessionid .= $chars[mt_rand(0, strlen($chars) - 1)];
		}
		$_SESSION['sessionid'] = $sessionid;
		$_SESSION['manage_username'] = $user;
		$boards = $db->GetOne('SELECT boards FROM '.dbprefix.'staff WHERE username = '.$db->quote($user));
		$level = self::getStaffLevel($user);
		if ($boards == 'all' || $level == 1) {
			setcookie('mod_cookie', 'allboards', time() + 3600, '/', cookies);
		} else {
			setcookie('mod_cookie', $boards, time() + 3600, '/', cookies);
		}
		$db->Execute('UPDATE '.dbprefix.'staff SET sessionid = '.$db->quote($sessionid).' WHERE username = '.$db->quote($val));
		$db->Execute('UPDATE `'.dbprefix.'staff` SET `active` = '.time().' WHERE `username` = '.$db->quote($val));
	}
	public static function destroySession($user) {
		global $db;
		$db->Run('UPDATE '.dbprefix.'staff SET sessionid = "" WHERE username = '.$db->quote($user));
		
		unset($_SESSION['manage_username']);
		unset($_SESSION['sessionid']);
		$boards = $db->GetOne('SELECT boards FROM '.dbprefix.'staff WHERE username = '.$db->quote($user));
		$level = self::getStaffLevel($user);
		if ($boards == 'all' || $level == 1) {
			setcookie('mod_cookie', 'allboards', time() - 3600, '/', cookies);
		} else {
			setcookie('mod_cookie', $boards, time() - 3600, '/', cookies);
		}
	}
	public static function loginForm($error, $errormsg) {
		if ($error == '1') {
			$twig_data['errorfound'] = true;
			$twig_data['errormsg'] = $errormsg;
		} else {
			$twig_data['errormsg'] = '';
		}
		Core::Output('/manage/login.tpl', $twig_data);
		die();
	}
	// Verifying that the supplied password is correct
	public static function checkLogin($side, $action) {
		global $db;
		$ip = Core::getIP();
		// If the user doesn't exist throw an error
		if (!$db->GetOne('SELECT * FROM '.dbprefix.'staff WHERE username = '.$db->quote($_POST['username']))) {
			Core::Log(time(), $_POST['username'], 'Failed Login attempt from: '.$ip);
			self::loginForm('1', 'Either the username or Password you supplied is incorrect');
		}
		// First lets make sure that the user account isn't locked out!
		if (self::checkLock($_POST['username']) && self::checkSuspended($_POST['username'])) {
			if (password_verify($_POST['password'], $db->GetOne('SELECT password FROM '.dbprefix.'staff WHERE username = '.$db->quote($_POST['username'])))) {
				// Lets update the hash 
				// The user will always be able to still login, but if a hacker finds this it will constantly stay changing
				$db->Run('UPDATE '.dbprefix.'staff SET password = '.$db->quote(password_hash($_POST['password'], PASSWORD_ARGON2I)));
				// Set the users active time!
				$db->Run('UPDATE '.dbprefix.'staff SET active = '.time());
				self::createSession($_POST['username']);
				Core::Log(time(), $_POST['username'], 'Logged in');
				header("Location: ".weburl.'management/index.php?side='.$side.'&action='.$action.'');
			} else {
				Core::Log(time(), $_POST['username'], 'Failed Login attempt from: '.$ip);
				// Lets update failed login attempts and add 1 to the previous number
				$loginattempts = $db->GetOne('SELECT failed FROM '.dbprefix.'staff WHERE username = '.$db->quote($_POST['username'])) + 1;
				$db->Run('UPDATE '.dbprefix.'staff SET failed = '.$loginattempts.' WHERE username = '.$db->quote($_POST['username']));
				// Lets update the failed time as well
				$db->Run('UPDATE '.dbprefix.'staff SET failedtime = '.time().' WHERE username = '.$db->quote($_POST['username']));
				self::loginForm('1', 'Either the username or Password you supplied is incorrect');
			}
		}
	}
	public static function checkLock($user) {
		global $db;
		if ($db->GetOne('SELECT failed FROM '.dbprefix.'staff WHERE username = '.$db->quote($user)) >= 3) {
			// Lets check if it's been 30 minutes since 3 failed login attempts
			if ((time() - $db->GetOne('SELECT failedtime FROM '.dbprefix.'staff WHERE username = '.$db->quote($user))) >= 1800) {
				$db->Run('UPDATE '.dbprefix.'staff SET failed = 0 WHERE username = '.$db->quote($user));
				return true;
			} else {
				self::loginForm('1', 'Either the username or Password you supplied is incorrect');
			}
		} else {
			return true;
		}
	}
	public static function checkSuspended($user) {
		global $db;
		$ip = Core::getIP();
		if ($db->GetOne('SELECT suspended FROM '.dbprefix.'staff WHERE username = '.$db->quote($user)) == 0) {
			return true;
		} else {
			Core::Log(time(), $user, 'Failed Login attempt to suspended account from IP: '.$ip);
			self::loginForm('1', 'Either the username or Password you supplied is incorrect');
		}
	}
	public static function getStaffLevel($user) {
		global $db;
		return $db->GetOne('SELECT level FROM '.dbprefix.' WHERE username = '.$db->quote($user));
	}
}