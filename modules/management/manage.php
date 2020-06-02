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
		$twig_data['current'] = $_GET['side'];
		$twig_data['action'] = $_GET['action'];
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
		$db->Run('UPDATE '.dbprefix.'staff SET active = '.time().' WHERE username = '.$db->quote($_SESSION['manage_username']));
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
	public static function changePass() {
		global $db, $twig_data;
		if (isset($_POST['submit'])) {
			$db->Run('UPDATE '.dbprefix.'staff SET active = '.time().' WHERE username = '.$db->quote($_SESSION['manage_username']));
			// First lets make sure the old password matches what they currently have
			if (!password_verify($_POST['oldpass'], $db->GetOne('SELECT password FROM '.dbprefix.'staff WHERE username = '.$db->quote($_SESSION['manage_username'])))) {
				$twig_data['error'] = true;
				$twig_data['message'] = 'Incorrect old Password entered';
			} elseif ($_POST['newpass'] != $_POST['newpass2']) {
				$twig_data['error'] = true;
				$twig_data['message'] = 'New passwords do not match!';
			} elseif ($_POST['oldpass'] == $_POST['newpass']) {
				$twig_data['error'] = true;
				$twig_data['message'] = 'Old password cannot match New password!';
			} else {
				$db->Run('UPDATE '.dbprefix.'staff SET password = '.$db->quote(password_hash($_POST['newpass'], PASSWORD_ARGON2I)));
				$twig_data['confirm'] = true;
				$twig_data['message'] = 'Password successfully changed!';
			}
		}
		Core::Output('/manage/main/changepass.tpl', $twig_data);
	}
	/* This ends the "Main" section function list
	   Begin "Site Administration" function list */
	public static function news() {
		global $db, $twig_data;
		if (self::getStaffLevel($_SESSION['manage_username']) == 1) {
			$twig_data['newspost'] = $db->GetAll('SELECT * FROM '.dbprefix.'front WHERE type = '.$db->quote('news').' ORDER BY date DESC');
			if ($_GET['do'] == 'filesubmit') {
				$upload = new Upload();
				$upload->HandleUploadManage();
				unset($upload);
			} elseif ($_GET['do'] == 'post') {
				if($_POST['id'] != '') {
					$db->Run('UPDATE '.dbprefix.'staff SET active = '.time());
					$db->Run('UPDATE '.dbprefix.'front SET message = '.$db->quote($_POST['post']).', subject = '.$db->quote($_POST['subject']).', email = '.$db->quote($_POST['email']).' WHERE id = '.$_POST['id'].' AND type = '.$db->quote('news'));
					Core::Log(time(), $_SESSION['manage_username'], 'Edited a news post');
				} else {
					// Update active time
					$db->Run('UPDATE '.dbprefix.'staff SET active = '.time());
					// Post the news post
					$db->Run('INSERT INTO '.dbprefix.'front (`by`, `message`, `date`, `type`, `subject`, `email`) VALUES ('.$db->quote($_SESSION['manage_username']).', '.$db->quote($_POST['post']).', '.time().', '.$db->quote('news').', '.$db->quote($_POST['subject']).', '.$db->quote($_POST['email']).')');
					Core::Log(time(), $_SESSION['manage_username'], 'Created a news post');
				}
			} elseif ($_GET['do'] == 'delpost') {
				$db->Run('DELETE FROM '.dbprefix.'front WHERE type = "news" and id = '.$_GET['id']);
			} elseif ($_GET['do'] == 'getmsg') {
				$msg = $db->GetOne('SELECT message FROM '.dbprefix.'front WHERE type = "news" AND id = '.$_GET['id']);
				echo $msg;
				die();
			}
			Core::Output('/manage/site/news.tpl', $twig_data);
		} else {
			Core::Error('You don\'t have permission for this!');
		}
	}
	public static function faq() {
		global $db, $twig_data;
		if (self::getStaffLevel($_SESSION['manage_username']) == 1) {
			$twig_data['faqspost'] = $db->GetAll('SELECT * FROM '.dbprefix.'front WHERE type = '.$db->quote('faq').' ORDER BY id');
			if ($_GET['do'] == 'filesubmit') {
				$upload = new Upload();
				$upload->HandleUploadManage();
				unset($upload);
			} elseif ($_GET['do'] == 'post') {
				if($db->GetOne('SELECT * FROM'.dbprefix.'front WHERE type = "faq" and id = '.$_POST['id'])) {
					$db->Run('UPDATE '.dbprefix.'staff SET active = '.time());
					$db->Run('UPDATE '.dbprefix.'front SET message = '.$db->quote($_POST['post']).', subject = '.$db->quote($_POST['subject']).', email = '.$db->quote($_POST['email']).' WHERE id = '.$_POST['id'].' AND type = '.$db->quote('faq'));
					Core::Log(time(), $_SESSION['manage_username'], 'Edited a FAQ post');
				} else {
					// Update active time
					$db->Run('UPDATE '.dbprefix.'staff SET active = '.time());
					// Post the FAQ post
					$db->Run('INSERT INTO '.dbprefix.'front (`id`, `by`, `message`, `date`, `type`, `subject`, `email`) VALUES ('.$_POST['id'].', '.$db->quote($_SESSION['manage_username']).', '.$db->quote($_POST['post']).', '.time().', '.$db->quote('faq').', '.$db->quote($_POST['subject']).', '.$db->quote($_POST['email']).')');
					Core::Log(time(), $_SESSION['manage_username'], 'Created a FAQ post');
				}
			} elseif ($_GET['do'] == 'delpost') {
				$db->Run('DELETE FROM '.dbprefix.'front WHERE type = "faq" and id = '.$_GET['id']);
			} elseif ($_GET['do'] == 'getmsg') {
				$msg = $db->GetOne('SELECT message FROM '.dbprefix.'front WHERE type = "faq" AND id = '.$_GET['id']);
				echo $msg;
				die();
			}
			Core::Output('/manage/site/faq.tpl', $twig_data);
		} else {
			Core::Error('You don\'t have permission for this!');
		}
	}
	public static function rules() {
		global $db, $twig_data;
		if (self::getStaffLevel($_SESSION['manage_username']) == 1) {
			$twig_data['rulespost'] = $db->GetAll('SELECT * FROM '.dbprefix.'front WHERE type = '.$db->quote('rules').' ORDER BY id');
			if ($_GET['do'] == 'filesubmit') {
				$upload = new Upload();
				$upload->HandleUploadManage();
				unset($upload);
			} elseif ($_GET['do'] == 'post') {
				if($db->GetOne('SELECT * FROM'.dbprefix.'front WHERE type = "rules" and id = '.$_POST['id'])) {
					$db->Run('UPDATE '.dbprefix.'staff SET active = '.time());
					$db->Run('UPDATE '.dbprefix.'front SET message = '.$db->quote($_POST['post']).', subject = '.$db->quote($_POST['subject']).', email = '.$db->quote($_POST['email']).' WHERE id = '.$_POST['id'].' AND type = '.$db->quote('rules'));
					Core::Log(time(), $_SESSION['manage_username'], 'Edited a Rules post');
				} else {
					// Update active time
					$db->Run('UPDATE '.dbprefix.'staff SET active = '.time());
					// Post the Rules post
					$db->Run('INSERT INTO '.dbprefix.'front (`id`, `by`, `message`, `date`, `type`, `subject`, `email`) VALUES ('.$_POST['id'].', '.$db->quote($_SESSION['manage_username']).', '.$db->quote($_POST['post']).', '.time().', '.$db->quote('rules').', '.$db->quote($_POST['subject']).', '.$db->quote($_POST['email']).')');
					Core::Log(time(), $_SESSION['manage_username'], 'Created a Rules post');
				}
			} elseif ($_GET['do'] == 'delpost') {
				$db->Run('DELETE FROM '.dbprefix.'front WHERE type = "rules" and id = '.$_GET['id']);
			} elseif ($_GET['do'] == 'getmsg') {
				$msg = $db->GetOne('SELECT message FROM '.dbprefix.'front WHERE type = "rules" AND id = '.$_GET['id']);
				echo $msg;
				die();
			}
			Core::Output('/manage/site/rules.tpl', $twig_data);
		} else {
			Core::Error('You don\'t have permission for this!');
		}
	}
	public static function logs() {
		global $db, $twig_data;
		if (self::getStaffLevel($_SESSION['manage_username']) == 1) {
			$twig_data['entry'] = $db->GetAll('SELECT * FROM '.dbprefix.'logs ORDER BY time DESC LIMIT 25 OFFSET '.($_GET['page'] * 25));
			$pages = $db->GetOne('SELECT COUNT(*) FROM '.dbprefix.'logs');
			$twig_data['page'] = $_GET['page'];
			$twig_data['pages'] = ($pages/25);
			if ($_GET['do'] == 'clearlog') {
				$db->Run('UPDATE '.dbprefix.'staff SET active = '.time().' WHERE username = '.$db->quote($_SESSION['manage_username']));
				$db->Run('DELETE FROM '.dbprefix.'logs');
				Core::Log(time(), $_SESSION['manage_username'], 'Deleted all Log items');
			}
		}
		Core::Output('/manage/site/logs.tpl', $twig_data);
	}
}