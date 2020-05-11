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
			setcookie('mod_cookie', 'allboards', time() + 1800, '/', cookies);
		} else {
			setcookie('mod_cookie', $boards, time() + 1800, '/', cookies);
		}
		$db->Run('UPDATE '.dbprefix.'staff SET sessionid = '.$db->quote($sessionid).' WHERE username = '.$db->quote($user));
		$db->Run('UPDATE '.dbprefix.'staff SET active = '.time().' WHERE username = '.$db->quote($user));
	}
	public static function destroySession($user) {
		global $db;
		$db->Run('UPDATE '.dbprefix.'staff SET sessionid = "" WHERE username = '.$db->quote($user));
		
		unset($_SESSION['manage_username']);
		unset($_SESSION['sessionid']);
		$boards = $db->GetOne('SELECT boards FROM '.dbprefix.'staff WHERE username = '.$db->quote($user));
		$level = self::getStaffLevel($user);
		if ($boards == 'all' || $level == 1) {
			setcookie('mod_cookie', 'allboards', time() - 1800, '/', cookies);
		} else {
			setcookie('mod_cookie', $boards, time() - 1800, '/', cookies);
		}
	}
	public static function loginForm($error, $errormsg) {
		if ($error == '1') {
			$twig_data['errorfound'] = true;
			$twig_data['errormsg'] = $errormsg;
			$twig_data['username'] = $_POST['username'];
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
			self::loginForm('1', 'Either the Username or Password you supplied is incorrect');
		}
		// First lets make sure that the user account isn't locked out!
		if (self::checkLock($_POST['username']) && self::checkSuspended($_POST['username'])) {
			if (password_verify($_POST['password'], $db->GetOne('SELECT password FROM '.dbprefix.'staff WHERE username = '.$db->quote($_POST['username'])))) {
				// Lets update the hash 
				// The user will always be able to still login, but if a hacker finds this it will constantly stay changing
				$db->Run('UPDATE '.dbprefix.'staff SET password = '.$db->quote(password_hash($_POST['password'], PASSWORD_ARGON2I)));
				// Set the users active time!
				$db->Run('UPDATE '.dbprefix.'staff SET active = '.time());
				// Delete all failed login attempts!
				$db->Run('UPDATE '.dbprefix.'staff SET failed = 0 WHERE username = '.$db->quote($_POST['username']));
				$db->Run('UPDATE '.dbprefix.'staff SET failedtime = 0 WHERE username = '.$db->quote($_POST['username']));
				// Create the session
				self::createSession($_POST['username']);
				// Log that this user has logged in!
				Core::Log(time(), $_POST['username'], 'Logged in');
				// Point them to the main page
				header("Location: ".weburl.'manage/index.php?side='.$side.'&action='.$action.'');
			} else {
				Core::Log(time(), $_POST['username'], 'Failed Login attempt from: '.$ip);
				// Lets update failed login attempts and add 1 to the previous number
				$loginattempts = $db->GetOne('SELECT failed FROM '.dbprefix.'staff WHERE username = '.$db->quote($_POST['username'])) + 1;
				$db->Run('UPDATE '.dbprefix.'staff SET failed = '.$loginattempts.' WHERE username = '.$db->quote($_POST['username']));
				// Lets update the failed time as well
				$db->Run('UPDATE '.dbprefix.'staff SET failedtime = '.time().' WHERE username = '.$db->quote($_POST['username']));
				self::loginForm('1', 'Either the Username or Password you supplied is incorrect');
			}
		}
	}
	public static function logOut() {
		global $db;
		self::destroySession($_SESSION['manage_username']);
		header("Location: ".weburl.'manage/');
	}
	public static function checkLock($user) {
		global $db;
		if ($db->GetOne('SELECT failed FROM '.dbprefix.'staff WHERE username = '.$db->quote($user)) >= 3) {
			// Lets check if it's been 30 minutes since 3 failed login attempts
			if ((time() - $db->GetOne('SELECT failedtime FROM '.dbprefix.'staff WHERE username = '.$db->quote($user))) >= 1800) {
				$db->Run('UPDATE '.dbprefix.'staff SET failed = 0 WHERE username = '.$db->quote($user));
				return true;
			} else {
				self::loginForm('1', 'Either the Username or Password you supplied is incorrect');
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
			self::loginForm('1', 'Either the Username or Password you supplied is incorrect');
		}
	}
	public static function getStaffLevel($user) {
		global $db;
		return $db->GetOne('SELECT level FROM '.dbprefix.'staff WHERE username = '.$db->quote($user));
	}
	/* This is the "Main" section function list */
	public static function stats() {
		global $db, $twig_data;
		$db->Run('UPDATE '.prefix.'staff SET active = '.time().' WHERE username = '.$db->quote($_SESSION['manageusername']));
		$twig_data['version'] = Core::GetConfigOption('version');
		if (file_get_contents('http://www.anonsaba.org/ver.php') != Core::GetConfigOption('version')) {
			$update = '1';
		} else {
			$update = '0';
		}
		$twig_data['update'] = $update;
		$howlong = time() - Core::GetConfigOption('installtime');
		if ($howlong < 86400) {
			$twig_data['installdate'] = 'Today';
		} else {
			$twig_data['installdate'] = Core::GetConfigOption('installtime');
		}
		switch (dbtype) {
			case 'mysql':
				$twig_data['databasetype'] = 'MySQL';
			break;
		}
		$twig_data['boardnum'] = $db->GetOne('SELECT COUNT(*) FROM `'.dbprefix.'boards`');
		$twig_data['numpost'] = $db->GetOne('SELECT COUNT(*) FROM `'.dbprefix.'posts`');
		$twig_data['postlast1'] = $db->GetOne('SELECT COUNT(*) FROM '.dbprefix.'posts WHERE time BETWEEN '.(time() - 86400).' AND '.time());
		$twig_data['banlast1'] = $db->GetOne('SELECT COUNT(*) FROM '.dbprefix.'bans WHERE time BETWEEN '.(time() - 86400).' AND '.time());
		$time1 = time() - 86400;
		$twig_data['postdate1'] = date('m/d', time());
		for ($x = 2; $x <= 30; $x++)  {
			if ($x >= 3) {
				$time[$x] = ($time[$x-1] - 86400);
				$twig_data['postdate'.$x] = date('m/d', $time[$x]);
			} elseif ($x == 2) {
				$time[$x] = ($time1 - 86400);
				$twig_data['postdate'.$x] = date('m/d', $time1);
			}
			$twig_data['postlast'.$x] = $db->GetOne('SELECT COUNT(*) FROM '.dbprefix.'posts WHERE time BETWEEN '.($time[$x] - 86400).' AND '.$time[$x]);
			$twig_data['banlast'.$x] = $db->GetOne('SELECT COUNT(*) FROM '.dbprefix.'bans WHERE time BETWEEN '.($time[$x] - 86400).' AND '.$time[$x]);
		}
		Core::Output('/manage/main/welcome.tpl', $twig_data);
	}
	public static function spp() {
		global $db;
		die('
			<div class="action">
				<input type="text" value="'.$db->GetOne('SELECT sessionid FROM '.dbprefix.'staff WHERE username = '.$db->quote($_SESSION['manage_username'])).'" />
			</div>
			');
	}
	public static function memory() {
		return substr(memory_get_peak_usage() / 1024 / 1024, 0, 4);
	}
}