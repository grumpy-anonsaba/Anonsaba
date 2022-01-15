<?php
include 'main/main.php';
// Anonsaba 3.0 - Management Console
class Management {

     /**************************************************************************************************************************** This is the Login function list ****************************************************************************************************************************/
	public function validateSession($manage=false) {
		global $db;
		if(isset($_SESSION['manage_username']) && isset($_SESSION['sessionid'])) {
			$qry = $db->prepare('SELECT sessionid FROM '.dbprefix.'staff WHERE username = ?');
				   $qry->execute(array($_SESSION['manage_username']));
				   $result = $qry->fetch();
			$sessionid = (is_array($result)) ? array_shift($result) : $result;
			if($_SESSION['sessionid'] == $sessionid) {
				return true;
			} else {
				$this->destroySession($_SESSION['manage_username']);
				$this->loginForm('1', 'Invalid Session!');
			}
		} else {
			if(!$manage) {
				die($this->loginForm('2', ''));
			} else {
				return false;
			}
		}
	}
	public function updateActive($user) {
		global $db;
		$qry = $db->prepare('UPDATE '.dbprefix.'staff SET active = ? WHERE username = ?');
			   $qry->execute(array(time(), $user));
	}
	public function createSession($user) {
		global $db;
		$chars = hash;
		$sessionid = '';
		for ($i = 0; $i < strlen($chars); ++$i) {
			$sessionid .= $chars[mt_rand(0, strlen($chars) - 1)];
		}
		$_SESSION['sessionid'] = $sessionid;
		$_SESSION['manage_username'] = $user;
		$qry = $db->prepare('SELECT boards FROM '.dbprefix.'staff WHERE username = ?');
			   $qry->execute(array($user));
			   $result = $qry->fetch();
		$boards = (is_array($result)) ? array_shift($result) : $result;
		$level = $this->getStaffLevel($user);
		if ($boards == 'all' || $level == 1) {
			setcookie('mod_cookie', 'allboards', time() + 1800, '/', webcookie);
		} else {
			setcookie('mod_cookie', $boards, time() + 1800, '/', webcookie);
		}
		$qry = $db->prepare('UPDATE '.dbprefix.'staff SET sessionid = ? WHERE username = ?');
			   $qry->execute(array($sessionid, $user));
		$this->updateActive($user);
	}
	public function destroySession($user) {
		global $db;
		$qry = $db->prepare('UPDATE '.dbprefix.'staff SET sessionid = "" WHERE username = ?');
			   $qry->execute(array($user));
		unset($_SESSION['manage_username']);
		unset($_SESSION['sessionid']);
		$qry = $db->prepare('SELECT boards FROM '.dbprefix.'staff WHERE username = ?');
			   $qry->execute(array($user));
			   $result = $qry->fetch();
		$boards = (is_array($result)) ? array_shift($result) : $result;
		$level = $this->getStaffLevel($user);
		if ($boards == 'all' || $level == 1) {
			setcookie('mod_cookie', 'allboards', time() - 1800, '/', webcookie);
		} else {
			setcookie('mod_cookie', $boards, time() - 1800, '/', webcookie);
		}
	}
	public function loginForm($error, $errormsg) {
		$twig_data['action'] = isset($_GET['action']) ? $_GET['action'] : 'stats';
		$twig_data['current'] = isset($_GET['side']) ? $_GET['side'] : 'main';
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
	public function checkLock($user) {
		global $db;
		$qry = $db->prepare('SELECT failed FROM '.dbprefix.'staff WHERE username = ?');
			   $qry->execute(array($user));
			   $result = $qry->fetch();
		$failedcount = (is_array($result)) ? array_shift($result) : $result;
		if ($failedcount >= 3) {
			// Lets check if it's been 30 minutes since 3 failed login attempts
			$qry = $db->prepare('SELECT failedtime FROM '.dbprefix.'staff WHERE username = ?');
				   $qry->execute(array($user));
				   $result = $qry->fetch();
			$failedtime = (is_array($result)) ? array_shift($result) : $result;
			if ((time() - $failedtime) >= 1800) {
				$qry = $db->prepare('UPDATE '.dbprefix.'staff SET failed = 0 WHERE username = ?');
					   $qry->execute(array($user));
				return true;
			} else {
				$this->loginForm('1', 'Either the Username or Password you supplied is incorrect');
			}
		} else {
			return true;
		}
	}
	public function checkSuspended($user) {
		global $db;
		$ip = Core::getIP();
		$qry = $db->prepare('SELECT suspended FROM '.dbprefix.'staff WHERE username = ?');
			   $qry->execute(array($user));
			   $result = $qry->fetch();
		$suspend = (is_array($result)) ? array_shift($result) : $result;
		if ($suspend == 0) {
			return true;
		} else {
			Core::Log(time(), $user, 'Failed Login attempt to suspended account from IP: '.$ip);
			$this->loginForm('1', 'Either the Username or Password you supplied is incorrect');
		}
	}
	public function getStaffLevel($user) {
		global $db;
		$qry = $db->prepare('SELECT level FROM '.dbprefix.'staff WHERE username = ?');
			   $qry->execute(array($user));
			   $result = $qry->fetch();
		$stafflevel = (is_array($result)) ? array_shift($result) : $result;
		return $stafflevel;
	}
	public function checkLogin($side, $action) {
		global $db;
		$ip = Core::getIP();
		// If the user doesn't exist throw an error
		$qry = $db->prepare('SELECT * FROM '.dbprefix.'staff WHERE username = ?');
			   $qry->execute(array($_POST['username']));
			   $username = $qry->fetch();
		if (!$username) {
			Core::Log(time(), $_POST['username'], 'Failed Login attempt from: '.$ip);
			$this->loginForm('1', 'Either the Username or Password you supplied is incorrect');
		}
		// First lets make sure that the user account isn't locked out!
		if ($this->checkLock($_POST['username']) && $this->checkSuspended($_POST['username'])) {
			$qry = $db->prepare('SELECT password FROM '.dbprefix.'staff WHERE username = ?');
				   $qry->execute(array($_POST['username']));
				   $result = $qry->fetch();
			$currentpass = (is_array($result)) ? array_shift($result) : $result;
			if (password_verify($_POST['password'], $currentpass)) {
				// Lets update the hash 
				// The user will always be able to still login, but if a hacker finds this it will constantly stay changing
				$qry = $db->prepare('UPDATE '.dbprefix.'staff SET password = ?');
					   $qry->execute(array(password_hash($_POST['password'], PASSWORD_ARGON2I)));
				// Set the users active time!
				$this->updateActive($_POST['username']);
				// Delete all failed login attempts!
				$qry = $db->prepare('UPDATE '.dbprefix.'staff SET failed = 0, failedtime = 0 WHERE username = ?');
					   $qry->execute(array($_POST['username']));
				// Create the session
				$this->createSession($_POST['username']);
				// Log that this user has logged in!
				Core::Log(time(), $_POST['username'], 'Logged in');
				// Point them to the main page
				header("Location: ".weburl.'manage/index.php?side='.$side.'&action='.$action.'');
			} else {
				Core::Log(time(), $_POST['username'], 'Failed Login attempt from: '.$ip);
				// Lets update failed login attempts and add 1 to the previous number
				$qry = $db->prepare('SELECT failed FROM '.dbprefix.'staff WHERE username = ?');
					   $qry->execute(array($_POST['username']));
					   $result = $qry->fetch();
				$loginattempts = ((is_array($result)) ? array_shift($result) : $result) + 1;
				$qry = $db->prepare('UPDATE '.dbprefix.'staff SET failed = ?, failedtime = ? WHERE username = ?');
					   $qry->execute(array($loginattempts, time(), $_POST['username']));
				$this->loginForm('1', 'Either the Username or Password you supplied is incorrect');
			}
		}
	}
	public function logOut() {
		global $db;
		$this->destroySession($_SESSION['manage_username']);
		header("Location: ".weburl.'manage/');
	}
     /**************************************************************************************************************************** This ends the Login function list ****************************************************************************************************************************/
     /**************************************************************************************************************************** This is the "Main" section function list ****************************************************************************************************************************/
	public function stats() {
		global $db, $twig_data;
		$this->updateActive($_SESSION['manage_username']);
		$twig_data['version'] = Core::GetConfigOption('version');
		if (file_get_contents('https://www.anonsaba.org/ver.php') != Core::GetConfigOption('version')) {
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
		$qry = $db->prepare('SELECT COUNT(*) FROM '.dbprefix.'boards');
			   $qry->execute();
			   $result = $qry->fetch();
		$twig_data['boardnum'] = (is_array($result)) ? array_shift($result) : $result;
		
		$qry = $db->prepare('SELECT COUNT(*) FROM '.dbprefix.'posts');
			   $qry->execute();
			   $result = $qry->fetch();
		$twig_data['numpost'] = (is_array($result)) ? array_shift($result) : $result;
		
		$qry = $db->prepare('SELECT COUNT(*) FROM '.dbprefix.'posts WHERE time BETWEEN ? AND ?');
			   $qry->execute(array((time() - 86400), time()));
			   $result = $qry->fetch();
		$twig_data['postlast1'] = (is_array($result)) ? array_shift($result) : $result;
		
		$qry = $db->prepare('SELECT COUNT(*) FROM '.dbprefix.'bans WHERE time BETWEEN ? AND ?');
			   $qry->execute(array((time() - 86400), time()));
			   $result = $qry->fetch();
		$twig_data['banlast1'] = (is_array($result)) ? array_shift($result) : $result;
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
			$qry = $db->prepare('SELECT COUNT(*) FROM '.dbprefix.'posts WHERE time BETWEEN ? AND ?');
				   $qry->execute(array(($time[$x] - 86400), $time[$x]));
				   $result = $qry->fetch();
			$twig_data['postlast'.$x] = (is_array($result)) ? array_shift($result) : $result;
			
			$qry = $db->prepare('SELECT COUNT(*) FROM '.dbprefix.'bans WHERE time BETWEEN ? AND ?');
				   $qry->execute(array(($time[$x] - 86400), $time[$x]));
				   $result = $qry->fetch();			
			$twig_data['banlast'.$x] = (is_array($result)) ? array_shift($result) : $result;
		}
		Core::Output('/manage/main/welcome.tpl', $twig_data);
	}
	public function spp() {
		global $db;
		$this->updateActive($_SESSION['manage_username']);
		$qry = $db->prepare('SELECT sessionid FROM '.dbprefix.'staff WHERE username = ?');
			   $qry->execute(array($_SESSION['manage_username']));
			   $result = $qry->fetch();
		$postpass = (is_array($result)) ? array_shift($result) : $result;
		die('
			<div class="action">
				<input type="text" value="'. $postpass .'" />
			</div>
			');
	}
	function changePass() {
		global $db, $twig_data;
		if (isset($_POST['submit'])) {
			$this->updateActive($_SESSION['manage_username']);
			$qry = $db->prepare('SELECT password FROM '.dbprefix.'staff WHERE username = ?');
				   $qry->execute(array($_SESSION['manage_username']));
				   $result = $qry->fetch();
			$oldpass = (is_array($result)) ? array_shift($result) : $result;
			// First lets make sure the old password matches what they currently have
			if (!password_verify($_POST['oldpass'], $oldpass)) {
				$twig_data['error'] = true;
				$twig_data['message'] = 'Incorrect old Password entered';
			} elseif ($_POST['newpass'] != $_POST['newpass2']) {
				$twig_data['error'] = true;
				$twig_data['message'] = 'New passwords do not match!';
			} elseif ($_POST['oldpass'] == $_POST['newpass']) {
				$twig_data['error'] = true;
				$twig_data['message'] = 'Old password cannot match New password!';
			} else {
				$qry = $db->prepare('UPDATE '.dbprefix.'staff SET password = ? WHERE username = ?');
					   $qry->execute(array(password_hash($_POST['newpass'], PASSWORD_ARGON2I), $_SESSION['manage_username']));
				$twig_data['confirm'] = true;
				$twig_data['message'] = 'Password successfully changed!';
			}
		}
		Core::Output('/manage/main/changepass.tpl', $twig_data);
	}

     /**************************************************************************************************************************** This ends the "Main" section function list ****************************************************************************************************************************/
	 
	 /**************************************************************************************************************************** This is the "Site Admin" section function list ****************************************************************************************************************************/
	public function news() {
		global $db, $twig_data;
		$this->updateActive($_SESSION['manage_username']);
		if ($this->getStaffLevel($_SESSION['manage_username']) == 1) {
			$qry = $db->prepare('SELECT * FROM '.dbprefix.'front WHERE type = ? ORDER by date DESC');
				   $qry->execute(array('news'));
			$twig_data['newspost'] = $qry->fetchAll();
			if ($_GET['do'] == 'filesubmit') {
				$upload = new Upload();
				$upload->HandleUploadManage();
				unset($upload);
			} elseif ($_GET['do'] == 'post') {
				if($_POST['id'] != '') {
					$this->updateActive($_SESSION['manage_username']);
					$qry = $db->prepare('UPDATE '.dbprefix.'front SET message = ?, subject = ?, email = ? WHERE id = ? AND type = ?');
						   $qry->execute(array($_POST['post'], $_POST['subject'], $_POST['email'], $_POST['id'], 'news'));
					Core::Log(time(), $_SESSION['manage_username'], 'Edited a news post');
				} else {
					// Update active time
					$this->updateActive($_SESSION['manage_username']);
					// Post the news post
					$qry = $db->prepare('INSERT INTO '.dbprefix.'front (`by`, `message`, `date`, `type`, `subject`, `email`) VALUES (?, ?, ?, ?, ?, ?)');
						   $qry->execute(array($_SESSION['manage_username'], $_POST['post'], time(), 'news', $_POST['subject'], $_POST['email']));
					Core::Log(time(), $_SESSION['manage_username'], 'Created a news post');
				}
			} elseif ($_GET['do'] == 'delpost') {
				$this->updateActive($_SESSION['manage_username']);
				$qry = $db->prepare('DELETE FROM '.dbprefix.'front WHERE type = ? AND id = ?');
					   $qry->execute(array('news', $_GET['id']));
				Core::Log(time(), $_SESSION['manage_username'], 'Deleted a news post');
			} elseif ($_GET['do'] == 'getmsg') {
				$this->updateActive($_SESSION['manage_username']);
				$qry = $db->prepare('SELECT message FROM '.dbprefix.'front WHERE type = ? AND id = ?');
					   $qry->execute(array('news', $_GET['id']));
					   $result = $qry->fetch();
				$msg = (is_array($result)) ? array_shift($result) : $result;
				echo $msg;
				die();
			}
			Core::Output('/manage/site/news.tpl', $twig_data);
		} else {
			Core::Error('You don\'t have permission for this!');
		}
	}
	public function rules() {
		global $db, $twig_data;
		$this->updateActive($_SESSION['manage_username']);
		if ($this->getStaffLevel($_SESSION['manage_username']) == 1) {
			$qry = $db->prepare('SELECT * FROM '.dbprefix.'front WHERE type = ? ORDER by ordr DESC');
				   $qry->execute(array('rules'));
			$twig_data['rulespost'] = $qry->fetchAll();
			if ($_GET['do'] == 'filesubmit') {
				$upload = new Upload();
				$upload->HandleUploadManage();
				unset($upload);
			} elseif ($_GET['do'] == 'post') {
				if ($_POST['id'] != '') {
					$this->updateActive($_SESSION['manage_username']);
					$qry = $db->prepare('UPDATE '.dbprefix.'front SET message = ?, subject = ?, email = ?, ordr = ? WHERE id = ? and type = ?');
						   $qry->execute(array($_POST['post'], $_POST['subject'], $_POST['email'], $_POST['order'], $_POST['id'], 'rules'));
					Core::Log(time(), $_SESSION['manage_username'], 'Edited a Rules post');
				} else {
					// Update active time
					$this->updateActive($_SESSION['manage_username']);
					// Post the Rules post
					$qry = $db->prepare('INSERT INTO '.dbprefix.'front (`ordr`, `by`, `message`, `date`, `type`, `subject`, `email`) VALUES (?, ?, ?, ?, ?, ?, ?)');
						   $qry->execute(array($_POST['order'], $_SESSION['manage_username'], $_POST['post'], time(), 'rules', $_POST['subject'], $_POST['email']));
					Core::Log(time(), $_SESSION['manage_username'], 'Created a Rules post');
				}
			} elseif ($_GET['do'] == 'delpost') {
				$this->updateActive($_SESSION['manage_username']);
				$qry = $db->prepare('DELETE FROM '.dbprefix.'front WHERE type = ? and id = ?');
					   $qry->execute(array('rules', $_GET['id']));
				Core::Log(time(), $_SESSION['manage_username'], 'Deleted a rules post');
			} elseif ($_GET['do'] == 'getmsg') {
				$this->updateActive($_SESSION['manage_username']);
				$qry = $db->prepare('SELECT message FROM '.dbprefix.'front WHERE type = ? AND id = ?');
					   $qry->execute(array('rules', $_GET['id']));
					   $result = $qry->fetch();
				$msg = (is_array($result)) ? array_shift($result) : $result;
				echo $msg;
				die();
			}
			Core::Output('/manage/site/rules.tpl', $twig_data);
		} else {
			Core::Error('You don\'t have permission for this!');
		}
	}
	public function faq() {
		global $db, $twig_data;
		$this->updateActive($_SESSION['manage_username']);
		if ($this->getStaffLevel($_SESSION['manage_username']) == 1) {
			$qry = $db->prepare('SELECT * FROM '.dbprefix.'front WHERE type = ? ORDER by ordr DESC');
				   $qry->execute(array('faq'));
			$twig_data['faqspost'] = $qry->fetchAll();
			if ($_GET['do'] == 'filesubmit') {
				$upload = new Upload();
				$upload->HandleUploadManage();
				unset($upload);
			} elseif ($_GET['do'] == 'post') {
				if ($_POST['id'] != '') {
					$this->updateActive($_SESSION['manage_username']);
					$qry = $db->prepare('UPDATE '.dbprefix.'front SET message = ?, subject = ?, email = ?, ordr = ? WHERE id = ? and type = ?');
						   $qry->execute(array($_POST['post'], $_POST['subject'], $_POST['email'], $_POST['order'], $_POST['id'], 'faq'));
					Core::Log(time(), $_SESSION['manage_username'], 'Edited a FAQ post');
				} else {
					// Update active time
					$this->updateActive($_SESSION['manage_username']);
					// Post the FAQ post
					$qry = $db->prepare('INSERT INTO '.dbprefix.'front (`ordr`, `by`, `message`, `date`, `type`, `subject`, `email`) VALUES (?, ?, ?, ?, ?, ?, ?)');
						   $qry->execute(array($_POST['order'], $_SESSION['manage_username'], $_POST['post'], time(), 'faq', $_POST['subject'], $_POST['email']));
					Core::Log(time(), $_SESSION['manage_username'], 'Created a FAQ post');
				}
			} elseif ($_GET['do'] == 'delpost') {
				$this->updateActive($_SESSION['manage_username']);
				$qry = $db->prepare('DELETE FROM '.dbprefix.'front WHERE type = ? and id = ?');
					   $qry->execute(array('faq', $_GET['id']));
				Core::Log(time(), $_SESSION['manage_username'], 'Deleted a FAQ post');
			} elseif ($_GET['do'] == 'getmsg') {
				$this->updateActive($_SESSION['manage_username']);
				$qry = $db->prepare('SELECT message FROM '.dbprefix.'front WHERE type = ? AND id = ?');
					   $qry->execute(array('faq', $_GET['id']));
					   $result = $qry->fetch();
				$msg = (is_array($result)) ? array_shift($result) : $result;
				echo $msg;
				die();
			}
			Core::Output('/manage/site/faq.tpl', $twig_data);
		} else {
			Core::Error('You don\'t have permission for this!');
		}
	}
	public function staff() {
		global $db, $twig_data;
		$this->updateActive($_SESSION['manage_username']);
		if ($this->getStaffLevel($_SESSION['manage_username']) == 1) {
			$qry = $db->prepare('SELECT * FROM '.dbprefix.'staff ORDER BY username');
				   $qry->execute();
			$twig_data['entry'] = $qry->fetchAll();
			
			$qry = $db->prepare('SELECT * FROM '.dbprefix.'boards ORDER BY name');
				   $qry->execute();
			$twig_data['boards'] = $qry->fetchAll();
			switch ($_GET['do']) {
				case 'suspend':
					$this->updateActive($_SESSION['manage_username']);
					$qry = $db->prepare('UPDATE '.dbprefix.'staff SET suspended = 1 WHERE id = ?');
						   $qry->execute(array($_GET['id']));
					
					$qry = $db->prepare('SELECT username FROM '.dbprefix.'staff WHERE id = ?');
						   $qry->execute(array($_GET['id']));
						   $result = $qry->fetch();
					$user = (is_array($result)) ? array_shift($result) : $result;
					Core::Log(time(), $_SESSION['manage_username'], 'Suspended '.$user);
				break;
				case 'del':
					$this->updateActive($_SESSION['manage_username']);
					$qry = $db->prepare('SELECT username FROM '.dbprefix.'staff WHERE id = ?');
						   $qry->execute(array($_GET['id']));
						   $result = $qry->fetch();
					$user = (is_array($result)) ? array_shift($result) : $result;
					Core::Log(time(), $_SESSION['manage_username'], 'Deleted '.$user);
					
					$qry = $db->prepare('DELETE FROM '.dbprefix.'staff WHERE id = ?');
						   $qry->execute(array($_GET['id']));
				break;
				case 'unsuspend':
					$this->updateActive($_SESSION['manage_username']);
					$qry = $db->prepare('UPDATE '.dbprefix.'staff SET suspended = 0 WHERE id = ?');
						   $qry->execute(array($_GET['id']));
					
					$qry = $db->prepare('SELECT username FROM '.dbprefix.'staff WHERE id = ?');
						   $qry->execute(array($_GET['id']));
						   $result = $qry->fetch();
					$user = (is_array($result)) ? array_shift($result) : $result;
					Core::Log(time(), $_SESSION['manage_username'], 'Unsuspended '.$user);
				break;
				case 'create':
					$this->updateActive($_SESSION['manage_username']);
					switch($_POST['level']) {
						case '1':
							$level = 'Administrator';
						break;
						case '2':
							$level = 'Super Moderator';
						break;
						case '3':
							$level = 'Moderator';
						break;
					}
					if ($_POST['id'] == '') {
						$qry = $db->prepare('INSERT INTO '.dbprefix.'staff (username, password, level, suspended, boards) VALUES (?, ?, ?, ?, ?)');
							   $qry->execute(array($_POST['username'], password_hash($_POST['password'], PASSWORD_ARGON2I), $_POST['level'], 0, $_POST['boards']));
						Core::Log(time(), $_SESSION['manage_username'], 'Created '.$_POST['username'].' with '.$level.' privileges');
					} elseif ($_POST['id'] != '' && $_POST['password'] == '') {
						$qry = $db->prepare('SELECT level FROM '.dbprefix.'staff WHERE id = ?');
							   $qry->execute(array($_POST['id']));
							   $result = $qry->fetch();
						$oldlevel = (is_array($result)) ? array_shift($result) : $result;
						
						$qry = $db->prepare('SELECT boards FROM '.dbprefix.'staff WHERE id = ?');
							   $qry->execute(array($_POST['id']));
							   $result = $qry->fetch();
						$oldboards = (is_array($result)) ? array_shift($result) : $result;
						
						$qry = $db->prepare('UPDATE '.dbprefix.'staff SET level = ?, boards = ? WHERE id = ?');
							   $qry->execute(array($_POST['level'], $_POST['boards'], $_POST['id']));

						$qry = $db->prepare('SELECT level FROM '.dbprefix.'staff WHERE id = ?');
							   $qry->execute(array($_POST['id']));
							   $result = $qry->fetch();
						$newlevel = (is_array($result)) ? array_shift($result) : $result;
						
						$qry = $db->prepare('SELECT boards FROM '.dbprefix.'staff WHERE id = ?');
							   $qry->execute(array($_POST['id']));
							   $result = $qry->fetch();
						$newboards = (is_array($result)) ? array_shift($result) : $result;
						
						if ($oldlevel != $newlevel && $oldboards == $newboards) {
							Core::Log(time(), $_SESSION['manage_username'], 'Updated '.$_POST['username'].' level to '.$level);
						} elseif ($oldlevel == $newlevel && $oldboards != $newboards) {
							Core::Log(time(), $_SESSION['manage_username'], 'Updated '.$_POST['username'].' boards');
						} elseif ($oldlevel != $newlevel && $oldboards != $newboards) {
							Core::Log(time(), $_SESSION['manage_username'], 'Updated '.$_POST['username'].' level to '.$level.' and boards');
						}
					} elseif ($_POST['id'] != '' && $_POST['password'] != '') {
						$qry = $db->prepare('SELECT level FROM '.dbprefix.'staff WHERE id = ?');
							   $qry->execute(array($_POST['id']));
							   $result = $qry->fetch();
						$oldlevel = (is_array($result)) ? array_shift($result) : $result;
						
						$qry = $db->prepare('SELECT boards FROM '.dbprefix.'staff WHERE id = ?');
							   $qry->execute(array($_POST['id']));
							   $result = $qry->fetch();
						$oldboards = (is_array($result)) ? array_shift($result) : $result;
						
						$qry = $db->prepare('UPDATE '.dbprefix.'staff SET level = ?, boards = ?, password = ? WHERE id = ?');
							   $qry->execute(array($_POST['level'], $_POST['boards'], password_hash($_POST['password'], PASSWORD_ARGON2I), $_POST['id']));

						$qry = $db->prepare('SELECT level FROM '.dbprefix.'staff WHERE id = ?');
							   $qry->execute(array($_POST['id']));
							   $result = $qry->fetch();
						$newlevel = (is_array($result)) ? array_shift($result) : $result;
						
						$qry = $db->prepare('SELECT boards FROM '.dbprefix.'staff WHERE id = ?');
							   $qry->execute(array($_POST['id']));
							   $result = $qry->fetch();
						$newboards = (is_array($result)) ? array_shift($result) : $result;
						
						if ($oldlevel != $newlevel && $oldboards == $newboards) {
							Core::Log(time(), $_SESSION['manage_username'], 'Updated '.$_POST['username'].' level to '.$level);
						} elseif ($oldlevel == $newlevel && $oldboards != $newboards) {
							Core::Log(time(), $_SESSION['manage_username'], 'Updated '.$_POST['username'].' boards');
						} elseif ($oldlevel != $newlevel && $oldboards != $newboards) {
							Core::Log(time(), $_SESSION['manage_username'], 'Updated '.$_POST['username'].' level to '.$level.' and boards');
						} elseif ($oldlevel == $newlevel && $oldboards == $newboards) {
							Core::Log(time(), $_SESSION['manage_username'], 'Updated '.$_POST['username'].' password');
						}
					}
				break;
			}
			Core::Output('/manage/site/staff.tpl', $twig_data);
		} else {
			Core::Error('You don\'t have permissions for this!');
		}
	}
	public function logs() {
		global $db, $twig_data;
		if ($this->getStaffLevel($_SESSION['manage_username']) == 1) {
			$this->updateActive($_SESSION['manage_username']);
			$qry = $db->prepare('SELECT * FROM '.dbprefix.'logs ORDER BY time DESC LIMIT 25 OFFSET ?');
				   $qry->execute(array(($_GET['page'] * 25)));				   
			$twig_data['entry'] = $qry->fetchAll();
			
			$qry = $db->prepare('SELECT COUNT(*) FROM '.dbprefix.'logs');
				   $qry->execute();
				   $result = $qry->fetch();
			$pages = (is_array($result)) ? array_shift($result) : $result;
			$twig_data['page'] = $_GET['page'];
			$twig_data['pages'] = ($pages/25);
			if ($_GET['do'] == 'clearlog') {
				$this->updateActive($_SESSION['manage_username']);
				$qry = $db->prepare('DELETE FROM '.dbprefix.'logs');
					   $qry->execute();
				Core::Log(time(), $_SESSION['manage_username'], 'Deleted all Log items');
			}
		}
		Core::Output('/manage/site/logs.tpl', $twig_data);
	}
     /**************************************************************************************************************************** This ends the "Site Admin" section function list ****************************************************************************************************************************/
	 
	 /**************************************************************************************************************************** This is the "Board Admin" section function list ****************************************************************************************************************************/
	public function boards() {
		global $db, $twig_data;
		if ($this->getStaffLevel($_SESSION['manage_username']) == 1) {
			$qry = $db->prepare('SELECT * FROM '.dbprefix.'boards');
				   $qry->execute();
			$twig_data['boards'] = $qry->fetchAll();
			
			$qry = $db->prepare('SELECT name as boardname, (SELECT COUNT(*) FROM '.dbprefix.'posts WHERE boardname = '.dbprefix.'boards.name AND deleted <> 1) count FROM '.dbprefix.'boards');
				   $qry->execute();
			$twig_data['postcount'] = $qry->fetchAll();
			
			$qry = $db->prepare('SELECT name FROM '.dbprefix.'filetypes');
				   $qry->execute();
			$twig_data['filetypes'] = $qry->fetchAll();
			
			$qry = $db->prepare('SELECT name FROM '.dbprefix.'sections');
				   $qry->execute();
			$twig_data['sections'] = $qry->fetchAll();
			$this->updateActive($_SESSION['manage_username']);
			switch ($_GET['do']) {
				case 'create':
					$this->updateActive($_SESSION['manage_username']);
					if ($_POST['id'] == '') {
						$qry = $db->prepare('INSERT INTO '.dbprefix.'boards 
									(name, `desc`, class, section, imagesize, postperpage, boardpages, threadhours, markpage, threadreply, postername, locked, email, ads, showid, report, captcha, forcedanon, trial, popular, recentpost, filetypes) 
								VALUES 
									(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
								$qry->execute(array(
													$_POST['boarddirectory'], 
													$_POST['boarddescription'], 
													$_POST['type'], 
													$_POST['section'], 
													$_POST['maximagesize'], 
													$_POST['maxpostperpage'], 
													$_POST['maxboardpages'], 
													$_POST['maxthreadhours'], 
													$_POST['markpage'], 
													$_POST['maxthreadreply'], 
													$_POST['defaultpostername'], 
													$_POST['locked'], 
													$_POST['enableemail'], 
													$_POST['enableads'], 
													$_POST['enableids'], 
													$_POST['enablereporting'], 
													$_POST['enablecaptcha'], 
													$_POST['forcedanon'], 
													$_POST['trialboard'], 
													$_POST['popularboard'],
													$_POST['enablerecentpost'], 
													$_POST['filetype']
													));
						if (mkdir(svrpath.'board/'.$_POST['boarddirectory'], $mode = 0755) && mkdir(svrpath.'board/'.$_POST['boarddirectory'].'/src', $mode = 0755) && mkdir(svrpath.'board/'.$_POST['boarddirectory'].'/res', $mode = 0755) && mkdir(svrpath.'board/'.$_POST['boarddirectory'].'/thumb', $mode = 0755)) {
							file_put_contents(svrpath.'board/'.$_POST['boarddirectory'] . '/src/.htaccess', 'AddType text/plain .ASM .C .CPP .CSS .JAVA .JS .LSP .PHP .PL .PY .RAR .SCM .TXT'. "\n" . 'SetHandler default-handler');
						}
						Core::Log(time(), $_SESSION['manage_username'], 'Created Board: /'.$_POST['boarddirectory'].'/ - '.$_POST['boarddescription']);
					} else {
						$qry = $db->prepare('UPDATE '.dbprefix.'boards SET 
												`desc` = ?,
												class = ?,
												section = ?,
												imagesize = ?,
												postperpage = ?,
												boardpages = ?,
												threadhours = ?,
												markpage = ?,
												threadreply = ?,
												postername = ?,
												locked = ?,
												email = ?,
												ads = ?,
												showid = ?,
												report = ?,
												captcha = ?,
												forcedanon = ?,
												trial = ?,
												popular = ?,
												recentpost = ?,
												filetypes = ?
											WHERE id = ?');
								$qry->execute(array( 
													$_POST['boarddescription'], 
													$_POST['type'], 
													$_POST['section'], 
													$_POST['maximagesize'], 
													$_POST['maxpostperpage'], 
													$_POST['maxboardpages'], 
													$_POST['maxthreadhours'], 
													$_POST['markpage'], 
													$_POST['maxthreadreply'], 
													$_POST['defaultpostername'], 
													$_POST['locked'], 
													$_POST['enableemail'], 
													$_POST['enableads'], 
													$_POST['enableids'], 
													$_POST['enablereporting'], 
													$_POST['enablecaptcha'], 
													$_POST['forcedanon'], 
													$_POST['trialboard'], 
													$_POST['popularboard'],
													$_POST['enablerecentpost'], 
													$_POST['filetype'],
													$_POST['id']));
						$board_core = new BoardCore();
						$board_core->board($_POST['boarddirectory']);
						$board_core->refreshAll();
						Core::Log(time(), $_SESSION['manage_username'], 'Updated Board: /'.$_POST['boarddirectory'].'/ - '.$_POST['boarddescription']);
					}
				break;
				case 'del':
					$this->updateActive($_SESSION['manage_username']);
					$qry = $db->prepare('SELECT name FROM '.dbprefix.'boards WHERE id = ?');
						   $qry->execute(array($_GET['id']));
						   $result = $qry->fetch();
					$oldboard = (is_array($result)) ? array_shift($result) : $result;
					if ($oldboard) {
						$qry = $db->prepare('DELETE FROM '.dbprefix.'boards WHERE id = ?');
							   $qry->execute(array($_GET['id']));
						$dir = svrpath.'board/'.$oldboard;
						foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST) as $path) {
							$path->isFile() ? unlink($path->getPathname()) : rmdir($path->getPathname());
						}
						rmdir($dir);
						Core::Log(time(), $_SESSION['manage_username'], 'Deleted Board: /'.$oldboard.'/');
					}
				break;
			}
			Core::Output('/manage/board/boards.tpl', $twig_data);
		} else {
			Core::Error('You don\'t have permissions for this!');
		}
	}
	public function filetypes() {
		global $db, $twig_data;
		if ($this->getStaffLevel($_SESSION['manage_username']) == 1) {
			$this->updateActive($_SESSION['manage_username']);
			$qry = $db->prepare('SELECT * FROM '.dbprefix.'filetypes');
				   $qry->execute();
			$twig_data['filetype'] = $qry->fetchAll();
			switch ($_GET['do']) {
				case 'create':
					$this->updateActive($_SESSION['manage_username']);
					if ($_POST['id'] == '') {
						$qry = $db->prepare('INSERT INTO '.dbprefix.'filetypes (name, image) VALUES (?, ?)');
							   $qry->execute(array($_POST['type'], $_POST['image']));
						Core::Log(time(), $_SESSION['manage_username'], 'Created Filetype: '.$_POST['type']);
					} else {
						$qry = $db->prepare('UPDATE '.dbprefix.'filetypes SET image = ? WHERE id = ?');
							   $qry->execute(array($_POST['image'], $_POST['id']));
						Core::Log(time(), $_SESSION['manage_username'], 'Updated Filetype: '.$_POST['type']);
					}
				break;
				case 'del':
					$this->updateActive($_SESSION['manage_username']);
					$qry = $db->prepare('SELECT name FROM '.dbprefix.'filetypes WHERE id = ?');
						   $qry->execute(array($_GET['id']));
						   $result = $qry->fetch();
					$oldtype = (is_array($result)) ? array_shift($result) : $result;
					
					$qry = $db->prepare('DELETE FROM '.dbprefix.'filetypes WHERE id = ?');
						   $qry->execute(array($_GET['id']));
					Core::Log(time(), $_SESSION['manage_username'], 'Deleted Filetype: '.$oldtype);
				break;
			}
			Core::Output('/manage/board/filetypes.tpl', $twig_data);
		} else {
			Core::Error('You don\'t have permissions for this!');
		}
	}
	public function sections() {
		global $db, $twig_data;
		if ($this->getStaffLevel($_SESSION['manage_username']) == 1) {
			$this->updateActive($_SESSION['manage_username']);
			$qry = $db->prepare('SELECT * FROM '.dbprefix.'sections ORDER BY `order`');
				   $qry->execute();
			$twig_data['sections'] = $qry->fetchAll();
			switch ($_GET['do']) {
				case 'create':
					$this->updateActive($_SESSION['manage_username']);
					if ($_POST['id'] == '') {
						//Lets make sure the section name/abbr doesn't exist first
						$qry = $db->prepare('SELECT COUNT(*) FROM '.dbprefix.'sections WHERE name = ?');
							   $qry->execute(array($_POST['name']));
							   $result = $qry->fetch();
						$sectexist = (is_array($result)) ? array_shift($result) : $result;
						
						$qry = $db->prepare('SELECT COUNT(*) FROM '.dbprefix.'sections WHERE abbr = ?');
							   $qry->execute(array($_POST['abbr']));
							   $result = $qry->fetch();
						$abbrexist = (is_array($result)) ? array_shift($result) : $result;
						if ($sectexist > 0) {
							break;
						} elseif ($abbrexist > 0) {
							break;
						} else {
							$qry = $db->prepare('INSERT INTO '.dbprefix.'sections (`order`, abbr, name, hidden) VALUES (?, ?, ?, ?)');
								   $qry->execute(array($_POST['order'], $_POST['abbr'], $_POST['name'], $_POST['hidden']));
							Core::Log(time(), $_SESSION['manage_username'], 'Created Section: '.$_POST['name']);
						}	
					} else {
						$qry = $db->prepare('UPDATE '.dbprefix.'sections SET `order` = ?, abbr = ?, name = ?, hidden = ? WHERE id = ?');
							   $qry->execute(array($_POST['order'], $_POST['abbr'], $_POST['name'], $_POST['hidden'], $_POST['id']));
						Core::Log(time(), $_SESSION['manage_username'], 'Updated Section: '.$_POST['name']);
					}
				break;
				case 'del':
					$this->updateActive($_SESSION['manage_username']);
					$qry = $db->prepare('SELECT name FROM '.dbprefix.'sections WHERE id = ?');
						   $qry->execute(array($_GET['id']));
						   $result = $qry->fetch();
					$oldsection = (is_array($result)) ? array_shift($result) : $result;
					
					$qry = $db->prepare('DELETE FROM '.dbprefix.'sections WHERE id = ?');
						   $qry->execute(array($_GET['id']));
					Core::Log(time(), $_SESSION['manage_username'], 'Deleted Section: '.$oldsection);
				break;
			}
			Core::Output('/manage/board/sections.tpl', $twig_data);
		} else {
			Core::Error('You don\'t have permissions for this!');
		}
	}
	public function rebuildall() {
		global $db, $twig_data;
		if ($this->getStaffLevel($_SESSION['manage_username']) <= 2) {
			$this->updateActive($_SESSION['manage_username']);
			switch ($_GET['do']) {
				case 'run':
					$this->updateActive($_SESSION['manage_username']);
					$start = microtime(true);
					$dir = svrpath.'pages_cache';
					foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST) as $path) {
						$path->isFile() ? unlink($path->getPathname()) : rmdir($path->getPathname());
					}
					rmdir($dir);
					Core::Log(time(), $_SESSION['manage_username'], 'Cleared Twig cache');
					$time_elapsed_secs = round(microtime(true) - $start, 2);
					$results = array('done' => 'success', 'time' => $time_elapsed_secs);
					die(json_encode($results));
				break;
			}
			Core::Output('/manage/board/rebuildall.tpl', $twig_data);
		} else {
			
		}
	}
}