<?php

// Anonsaba 3.0 - Management Console

class Management {
	public function validateSession($manage=false) {
		global $db;
		if(isset($_SESSION['manage_username']) && isset($_SESSION['sessionid'])) {
			if($_SESSION['sessionid'] == $db->GetOne('SELECT sessionid FROM '.dbprefix.'staff WHERE username = '.$db->quote($_SESSION['manage_username']))) {
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
		$db->Run('UPDATE '.dbprefix.'staff SET active = '.time().' WHERE username = '.$db->quote($user));
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
		$boards = $db->GetOne('SELECT boards FROM '.dbprefix.'staff WHERE username = '.$db->quote($user));
		$level = $this->getStaffLevel($user);
		if ($boards == 'all' || $level == 1) {
			setcookie('mod_cookie', 'allboards', time() + 1800, '/', webcookie);
		} else {
			setcookie('mod_cookie', $boards, time() + 1800, '/', webcookie);
		}
		$db->Run('UPDATE '.dbprefix.'staff SET sessionid = '.$db->quote($sessionid).' WHERE username = '.$db->quote($user));
		$db->Run('UPDATE '.dbprefix.'staff SET active = '.time().' WHERE username = '.$db->quote($user));
	}
	public function destroySession($user) {
		global $db;
		$db->Run('UPDATE '.dbprefix.'staff SET sessionid = "" WHERE username = '.$db->quote($user));
		unset($_SESSION['manage_username']);
		unset($_SESSION['sessionid']);
		$boards = $db->GetOne('SELECT boards FROM '.dbprefix.'staff WHERE username = '.$db->quote($user));
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
	// Verifying that the supplied password is correct
	public function checkLogin($side, $action) {
		global $db;
		$ip = Core::getIP();
		// If the user doesn't exist throw an error
		if (!$db->GetOne('SELECT * FROM '.dbprefix.'staff WHERE username = '.$db->quote($_POST['username']))) {
			Core::Log(time(), $_POST['username'], 'Failed Login attempt from: '.$ip);
			$this->loginForm('1', 'Either the Username or Password you supplied is incorrect');
		}
		// First lets make sure that the user account isn't locked out!
		if ($this->checkLock($_POST['username']) && $this->checkSuspended($_POST['username'])) {
			if (password_verify($_POST['password'], $db->GetOne('SELECT password FROM '.dbprefix.'staff WHERE username = '.$db->quote($_POST['username'])))) {
				// Lets update the hash 
				// The user will always be able to still login, but if a hacker finds this it will constantly stay changing
				$db->Run('UPDATE '.dbprefix.'staff SET password = '.$db->quote(password_hash($_POST['password'], PASSWORD_ARGON2I)));
				// Set the users active time!
				$this->updateActive($_POST['username']);
				// Delete all failed login attempts!
				$db->Run('UPDATE '.dbprefix.'staff SET failed = 0 WHERE username = '.$db->quote($_POST['username']));
				$db->Run('UPDATE '.dbprefix.'staff SET failedtime = 0 WHERE username = '.$db->quote($_POST['username']));
				// Create the session
				$this->createSession($_POST['username']);
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
				$this->loginForm('1', 'Either the Username or Password you supplied is incorrect');
			}
		}
	}
	public function logOut() {
		global $db;
		$this->destroySession($_SESSION['manage_username']);
		header("Location: ".weburl.'manage/');
	}
	public function checkLock($user) {
		global $db;
		if ($db->GetOne('SELECT failed FROM '.dbprefix.'staff WHERE username = '.$db->quote($user)) >= 3) {
			// Lets check if it's been 30 minutes since 3 failed login attempts
			if ((time() - $db->GetOne('SELECT failedtime FROM '.dbprefix.'staff WHERE username = '.$db->quote($user))) >= 1800) {
				$db->Run('UPDATE '.dbprefix.'staff SET failed = 0 WHERE username = '.$db->quote($user));
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
		if ($db->GetOne('SELECT suspended FROM '.dbprefix.'staff WHERE username = '.$db->quote($user)) == 0) {
			return true;
		} else {
			Core::Log(time(), $user, 'Failed Login attempt to suspended account from IP: '.$ip);
			$this->loginForm('1', 'Either the Username or Password you supplied is incorrect');
		}
	}
	public function getStaffLevel($user) {
		global $db;
		return $db->GetOne('SELECT level FROM '.dbprefix.'staff WHERE username = '.$db->quote($user));
	}
	/* This is the "Main" section function list */
	public function stats() {
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
	public function spp() {
		global $db;
		$this->updateActive($_SESSION['manage_username']);
		die('
			<div class="action">
				<input type="text" value="'.$db->GetOne('SELECT sessionid FROM '.dbprefix.'staff WHERE username = '.$db->quote($_SESSION['manage_username'])).'" />
			</div>
			');
	}
	public function changePass() {
		global $db, $twig_data;
		if (isset($_POST['submit'])) {
			$this->updateActive($_SESSION['manage_username']);
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
	public function news() {
		global $db, $twig_data;
		$this->updateActive($_SESSION['manage_username']);
		if ($this->getStaffLevel($_SESSION['manage_username']) == 1) {
			$twig_data['newspost'] = $db->GetAll('SELECT * FROM '.dbprefix.'front WHERE type = '.$db->quote('news').' ORDER BY date DESC');
			if ($_GET['do'] == 'filesubmit') {
				$upload = new Upload();
				$upload->HandleUploadManage();
				unset($upload);
			} elseif ($_GET['do'] == 'post') {
				if($_POST['id'] != '') {
					$this->updateActive($_SESSION['manage_username']);
					$db->Run('UPDATE '.dbprefix.'front SET message = '.$db->quote($_POST['post']).', subject = '.$db->quote($_POST['subject']).', email = '.$db->quote($_POST['email']).' WHERE id = '.$_POST['id'].' AND type = '.$db->quote('news'));
					Core::Log(time(), $_SESSION['manage_username'], 'Edited a news post');
				} else {
					// Update active time
					$this->updateActive($_SESSION['manage_username']);
					// Post the news post
					$db->Run('INSERT INTO '.dbprefix.'front (`by`, `message`, `date`, `type`, `subject`, `email`) VALUES ('.$db->quote($_SESSION['manage_username']).', '.$db->quote($_POST['post']).', '.time().', '.$db->quote('news').', '.$db->quote($_POST['subject']).', '.$db->quote($_POST['email']).')');
					Core::Log(time(), $_SESSION['manage_username'], 'Created a news post');
				}
			} elseif ($_GET['do'] == 'delpost') {
				$this->updateActive($_SESSION['manage_username']);
				$db->Run('DELETE FROM '.dbprefix.'front WHERE type = "news" and id = '.$_GET['id']);
			} elseif ($_GET['do'] == 'getmsg') {
				$this->updateActive($_SESSION['manage_username']);
				$msg = $db->GetOne('SELECT message FROM '.dbprefix.'front WHERE type = "news" AND id = '.$_GET['id']);
				echo $msg;
				die();
			}
			Core::Output('/manage/site/news.tpl', $twig_data);
		} else {
			Core::Error('You don\'t have permission for this!');
		}
	}
	public function faq() {
		global $db, $twig_data;
		$this->updateActive($_SESSION['manage_username']);
		if ($this->getStaffLevel($_SESSION['manage_username']) == 1) {
			$twig_data['faqspost'] = $db->GetAll('SELECT * FROM '.dbprefix.'front WHERE type = '.$db->quote('faq').' ORDER BY ordr');
			if ($_GET['do'] == 'filesubmit') {
				$upload = new Upload();
				$upload->HandleUploadManage();
				unset($upload);
			} elseif ($_GET['do'] == 'post') {
				if($_POST['id'] != '') {
					$this->updateActive($_SESSION['manage_username']);
					$db->Run('UPDATE '.dbprefix.'front SET message = '.$db->quote($_POST['post']).', subject = '.$db->quote($_POST['subject']).', email = '.$db->quote($_POST['email']).', `ordr` = '.$_POST['order'].' WHERE id = '.$_POST['id'].' AND type = '.$db->quote('faq'));
					Core::Log(time(), $_SESSION['manage_username'], 'Edited a FAQ post');
				} else {
					// Update active time
					$this->updateActive($_SESSION['manage_username']);
					// Post the FAQ post
					$db->Run('INSERT INTO '.dbprefix.'front (`ordr`, `by`, `message`, `date`, `type`, `subject`, `email`) VALUES ('.$_POST['order'].', '.$db->quote($_SESSION['manage_username']).', '.$db->quote($_POST['post']).', '.time().', '.$db->quote('faq').', '.$db->quote($_POST['subject']).', '.$db->quote($_POST['email']).')');
					Core::Log(time(), $_SESSION['manage_username'], 'Created a FAQ post');
				}
			} elseif ($_GET['do'] == 'delpost') {
				$this->updateActive($_SESSION['manage_username']);
				$db->Run('DELETE FROM '.dbprefix.'front WHERE type = "faq" and id = '.$_GET['id']);
			} elseif ($_GET['do'] == 'getmsg') {
				$this->updateActive($_SESSION['manage_username']);
				$msg = $db->GetOne('SELECT message FROM '.dbprefix.'front WHERE type = "faq" AND id = '.$_GET['id']);
				echo $msg;
				die();
			}
			Core::Output('/manage/site/faq.tpl', $twig_data);
		} else {
			Core::Error('You don\'t have permission for this!');
		}
	}
	public function rules() {
		global $db, $twig_data;
		$this->updateActive($_SESSION['manage_username']);
		if ($this->getStaffLevel($_SESSION['manage_username']) == 1) {
			$twig_data['rulespost'] = $db->GetAll('SELECT * FROM '.dbprefix.'front WHERE type = '.$db->quote('rules').' ORDER BY ordr');
			if ($_GET['do'] == 'filesubmit') {
				$upload = new Upload();
				$upload->HandleUploadManage();
				unset($upload);
			} elseif ($_GET['do'] == 'post') {
				if ($_POST['id'] != '') {
					$this->updateActive($_SESSION['manage_username']);
					$db->Run('UPDATE '.dbprefix.'front SET message = '.$db->quote($_POST['post']).', subject = '.$db->quote($_POST['subject']).', email = '.$db->quote($_POST['email']).', `ordr` = '.$_POST['order'].' WHERE id = '.$_POST['id'].' AND type = '.$db->quote('rules'));
					Core::Log(time(), $_SESSION['manage_username'], 'Edited a Rules post');
				} else {
					// Update active time
					$this->updateActive($_SESSION['manage_username']);
					// Post the Rules post
					$db->Run('INSERT INTO '.dbprefix.'front (`ordr`, `by`, `message`, `date`, `type`, `subject`, `email`) VALUES ('.$_POST['order'].', '.$db->quote($_SESSION['manage_username']).', '.$db->quote($_POST['post']).', '.time().', '.$db->quote('rules').', '.$db->quote($_POST['subject']).', '.$db->quote($_POST['email']).')');
					Core::Log(time(), $_SESSION['manage_username'], 'Created a Rules post');
				}
			} elseif ($_GET['do'] == 'delpost') {
				$this->updateActive($_SESSION['manage_username']);
				$db->Run('DELETE FROM '.dbprefix.'front WHERE type = "rules" and id = '.$_GET['id']);
			} elseif ($_GET['do'] == 'getmsg') {
				$this->updateActive($_SESSION['manage_username']);
				$msg = $db->GetOne('SELECT message FROM '.dbprefix.'front WHERE type = "rules" AND id = '.$_GET['id']);
				echo $msg;
				die();
			}
			Core::Output('/manage/site/rules.tpl', $twig_data);
		} else {
			Core::Error('You don\'t have permission for this!');
		}
	}
	public function staff() {
		global $db, $twig_data;
		$this->updateActive($_SESSION['manage_username']);
		if ($this->getStaffLevel($_SESSION['manage_username']) == 1) {
			$twig_data['entry'] = $db->GetAll('SELECT * FROM '.dbprefix.'staff ORDER BY username');
			$twig_data['boards'] = $db->Getall('SELECT * FROM '.dbprefix.'boards ORDER BY name');
			switch ($_GET['do']) {
				case 'suspend':
					$this->updateActive($_SESSION['manage_username']);
					$db->Run('UPDATE '.dbprefix.'staff SET suspended = 1 WHERE id = '.$_GET['id']);
					Core::Log(time(), $_SESSION['manage_username'], 'Suspended '.$db->GetOne('SELECT username FROM '.dbprefix.'staff WHERE id = '.$_GET['id']));
				break;
				case 'del':
					$this->updateActive($_SESSION['manage_username']);
					Core::Log(time(), $_SESSION['manage_username'], 'Deleted '.$db->GetOne('SELECT username FROM '.dbprefix.'staff WHERE id = '.$_GET['id']));
					$db->Run('DELETE FROM '.dbprefix.'staff WHERE id = '.$_GET['id']);
				break;
				case 'unsuspend':
					$this->updateActive($_SESSION['manage_username']);
					$db->Run('UPDATE '.dbprefix.'staff SET suspended = 0 WHERE id = '.$_GET['id']);
					Core::Log(time(), $_SESSION['manage_username'], 'Unsuspended '.$db->GetOne('SELECT username FROM '.dbprefix.'staff WHERE id = '.$_GET['id']));
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
						$db->Run('INSERT INTO '.dbprefix.'staff (username, password, level, suspended, boards) VALUES ('.$db->quote($_POST['username']).', '.$db->quote(password_hash($_POST['password'], PASSWORD_ARGON2I)).', '.$db->quote($_POST['level']).', 0, '.$db->quote($_POST['boards']).')');
						Core::Log(time(), $_SESSION['manage_username'], 'Created '.$_POST['username'].' with '.$level.' privledges');
					} elseif ($_POST['id'] != '' && $_POST['password'] == '') {
						$oldlevel = $db->GetOne('SELECT level FROM '.dbprefix.'staff WHERE id = '.$_POST['id']);
						$oldboards = $db->GetOne('SELECT boards FROM '.dbprefix.'staff WHERE id = '.$_POST['id']);
						$db->Run('UPDATE '.dbprefix.'staff SET level = '.$db->quote($_POST['level']).', boards = '.$db->quote($_POST['boards']).' WHERE id = '.$_POST['id']);
						$newlevel = $db->GetOne('SELECT level FROM '.dbprefix.'staff WHERE id = '.$_POST['id']);
						$newboards = $db->GetOne('SELECT boards FROM '.dbprefix.'staff WHERE id = '.$_POST['id']);
						if ($oldlevel != $newlevel && $oldboards == $newboards) {
							Core::Log(time(), $_SESSION['manage_username'], 'Updated '.$_POST['username'].' level to '.$level);
						} elseif ($oldlevel == $newlevel && $oldboards != $newboards) {
							Core::Log(time(), $_SESSION['manage_username'], 'Updated '.$_POST['username'].' boards');
						} elseif ($oldlevel != $newlevel && $oldboards != $newboards) {
							Core::Log(time(), $_SESSION['manage_username'], 'Updated '.$_POST['username'].' level to '.$level.' and boards');
						}
					} elseif ($_POST['id'] != '' && $_POST['password'] != '') {
						$oldlevel = $db->GetOne('SELECT level FROM '.dbprefix.'staff WHERE id = '.$_POST['id']);
						$oldboards = $db->GetOne('SELECT boards FROM '.dbprefix.'staff WHERE id = '.$_POST['id']);
						$db->Run('UPDATE '.dbprefix.'staff SET level = '.$db->quote($_POST['level']).', boards = '.$db->quote($_POST['boards']).', password = '.$db->quote(password_hash($_POST['password'], PASSWORD_ARGON2I)).' WHERE id = '.$_POST['id']);
						$newlevel = $db->GetOne('SELECT level FROM '.dbprefix.'staff WHERE id = '.$_POST['id']);
						$newboards = $db->GetOne('SELECT boards FROM '.dbprefix.'staff WHERE id = '.$_POST['id']);
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
			$twig_data['entry'] = $db->GetAll('SELECT * FROM '.dbprefix.'logs ORDER BY time DESC LIMIT 25 OFFSET '.($_GET['page'] * 25));
			$pages = $db->GetOne('SELECT COUNT(*) FROM '.dbprefix.'logs');
			$twig_data['page'] = $_GET['page'];
			$twig_data['pages'] = ($pages/25);
			if ($_GET['do'] == 'clearlog') {
				$this->updateActive($_SESSION['manage_username']);
				$db->Run('DELETE FROM '.dbprefix.'logs');
				Core::Log(time(), $_SESSION['manage_username'], 'Deleted all Log items');
			}
		}
		Core::Output('/manage/site/logs.tpl', $twig_data);
	}
	/* This ends the "Site Administration" section function list
	   Begin "Board Administration" function list */
	public function boards() {
		global $db, $twig_data;
		if ($this->getStaffLevel($_SESSION['manage_username']) == 1) {
			$twig_data['boards'] = $db->GetAll('SELECT * FROM '.dbprefix.'boards');
			$twig_data['postcount'] = $db->GetAll('SELECT boardname, COUNT(*) as count FROM '.dbprefix.'posts WHERE deleted <> 1 GROUP BY boardname');
			$twig_data['filetypes'] = $db->GetAll('SELECT name FROM '.dbprefix.'filetypes');
			$twig_data['sections'] = $db->GetAll('SELECT name FROM '.dbprefix.'sections');
			$this->updateActive($_SESSION['manage_username']);
			switch ($_GET['do']) {
				case 'create':
					$this->updateActive($_SESSION['manage_username']);
					if ($_POST['id'] == '') {
						$db->Run('INSERT INTO '.dbprefix.'boards 
									(name, `desc`, class, section, imagesize, postperpage, boardpages, threadhours, markpage, threadreply, postername, locked, email, ads, showid, report, captcha, forcedanon, trial, popular, recentpost, filetypes) 
								VALUES 
									('.$db->quote($_POST['boarddirectory']).', '.$db->quote($_POST['boarddescription']).', '.$db->quote($_POST['type']).', '.$db->quote($_POST['section']).', '.$db->quote($_POST['maximagesize']).', 
									 '.$db->quote($_POST['maxpostperpage']).', '.$db->quote($_POST['maxboardpages']).', '.$db->quote($_POST['maxthreadhours']).', '.$db->quote($_POST['markpage']).', '.$db->quote($_POST['maxthreadreply']).', 
									 '.$db->quote($_POST['defaultpostername']).', '.$db->quote($_POST['locked']).', '.$db->quote($_POST['enableemail']).', '.$db->quote($_POST['enableads']).', '.$db->quote($_POST['enableids']).', 
									 '.$db->quote($_POST['enablereporting']).', '.$db->quote($_POST['enablecaptcha']).', '.$db->quote($_POST['forcedanon']).', '.$db->quote($_POST['trialboard']).', '.$db->quote($_POST['popularboard']).',
									 '.$db->quote($_POST['enablerecentpost']).', '.$db->quote($_POST['filetype']).')');
						if (mkdir(svrpath.$_POST['boarddirectory'], $mode = 0755) && mkdir(svrpath.$_POST['boarddirectory'].'/src', $mode = 0755) && mkdir(svrpath.$_POST['boarddirectory'].'/res', $mode = 0755) && mkdir(svrpath.$_POST['boarddirectory'].'/thumb', $mode = 0755)) {
							file_put_contents(svrpath. $_POST['boarddirectory'] .'/.htaccess' , 'DirectoryIndex board.html');
							file_put_contents(svrpath . $_POST['boarddirectory'] . '/src/.htaccess', 'AddType text/plain .ASM .C .CPP .CSS .JAVA .JS .LSP .PHP .PL .PY .RAR .SCM .TXT'. "\n" . 'SetHandler default-handler');
						}
						Core::Log(time(), $_SESSION['manage_username'], 'Created Board: /'.$_POST['boarddirectory'].'/ - '.$_POST['boarddescription']);
					} else {
						$db->Run('UPDATE '.dbprefix.'boards SET
									`desc` = '.$db->quote($_POST['boarddescription']).',
									class =  '.$db->quote($_POST['type']).',
									section = '.$db->quote($_POST['section']).',
									imagesize = '.$db->quote($_POST['maximagesize']).',
									postperpage = '.$db->quote($_POST['maxpostperpage']).', 
									boardpages = '.$db->quote($_POST['maxboardpages']).', 
									threadhours = '.$db->quote($_POST['maxthreadhours']).', 
									markpage = '.$db->quote($_POST['markpage']).', 
									threadreply = '.$db->quote($_POST['maxthreadreply']).', 
									postername = '.$db->quote($_POST['defaultpostername']).', 
									locked = '.$db->quote($_POST['locked']).', 
									email = '.$db->quote($_POST['enableemail']).', 
									ads = '.$db->quote($_POST['enableads']).', 
									showid = '.$db->quote($_POST['enableids']).', 
									report = '.$db->quote($_POST['enablereporting']).', 
									captcha = '.$db->quote($_POST['enablecaptcha']).', 
									forcedanon = '.$db->quote($_POST['forcedanon']).', 
									trial = '.$db->quote($_POST['trialboard']).', 
									popular = '.$db->quote($_POST['popularboard']).', 
									recentpost = '.$db->quote($_POST['enablerecentpost']).',
									filetypes = '.$db->quote($_POST['filetype']).'
								WHERE id = '.$db->quote($_POST['id']));
						Core::Log(time(), $_SESSION['manage_username'], 'Updated Board: /'.$_POST['boarddirectory'].'/ - '.$_POST['boarddescription']);
					}
				break;
				case 'del':
					$this->updateActive($_SESSION['manage_username']);
					$oldboard = $db->GetOne('SELECT name FROM '.dbprefix.'boards WHERE id = '.$db->quote($_GET['id']));
					$db->Run('DELETE FROM '.dbprefix.'boards WHERE id = '.$db->quote($_GET['id']));
					$dir = svrpath.$oldboard;
					foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST) as $path) {
						$path->isFile() ? unlink($path->getPathname()) : rmdir($path->getPathname());
					}
					rmdir($dir);
					Core::Log(time(), $_SESSION['manage_username'], 'Deleted Board: /'.$oldboard.'/');
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
			$twig_data['filetype'] = $db->GetAll('SELECT * FROM '.dbprefix.'filetypes');
			switch ($_GET['do']) {
				case 'create':
					$this->updateActive($_SESSION['manage_username']);
					if ($_POST['id'] == '') {
						$db->Run('INSERT INTO '.dbprefix.'filetypes (name, image) VALUES ('.$db->quote($_POST['type']).', '.$db->quote($_POST['image']).')');
						Core::Log(time(), $_SESSION['manage_username'], 'Created Filetype: '.$_POST['type']);
					} else {
						$db->Run('UPDATE '.dbprefix.'filetypes SET image = '.$db->quote($_POST['image']).' WHERE id = '.$db->quote($_POST['id']));
						Core::Log(time(), $_SESSION['manage_username'], 'Updated Filetype: '.$_POST['type']);
					}
				break;
				case 'del':
					$this->updateActive($_SESSION['manage_username']);
					$oldtype = $db->GetOne('SELECT name FROM '.dbprefix.'filetypes WHERE id = '.$db->quote($_GET['id']));
					$db->Run('DELETE FROM '.dbprefix.'filetypes WHERE id = '.$db->quote($_GET['id']));
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
			$twig_data['sections'] = $db->GetAll('SELECT * FROM '.dbprefix.'sections ORDER BY `order`');
			switch ($_GET['do']) {
				case 'create':
					$this->updateActive($_SESSION['manage_username']);
					if ($_POST['id'] == '') {
						//Lets make sure the section name/abbr doesn't exist first
						if ($db->GetOne('SELECT COUNT(*) FROM '.dbprefix.'sections WHERE name = '.$db->quote($_POST['name'])) > 0) {
							break;
						} elseif ($db->GetOne('SELECT COUNT(*) FROM '.dbprefix.'sections WHERE abbr = '.$db->quote($_POST['abbr'])) > 0) {
							break;
						} else {
							$db->Run('INSERT INTO '.dbprefix.'sections (`order`, abbr, name, hidden) VALUES ('.$db->quote($_POST['order']).', '.$db->quote($_POST['abbr']).', '.$db->quote($_POST['name']).', '.$db->quote($_POST['hidden']).')');
						}	
						Core::Log(time(), $_SESSION['manage_username'], 'Created Section: '.$_POST['name']);
					} else {
						$db->Run('UPDATE '.dbprefix.'sections SET 
									`order` = '.$db->quote($_POST['order']).',
									abbr = '.$db->quote($_POST['abbr']).',
									name = '.$db->quote($_POST['name']).',
									hidden = '.$db->quote($_POST['hidden']).'
								WHERE id = '.$db->quote($_POST['id']));
						Core::Log(time(), $_SESSION['manage_username'], 'Updated Section: '.$_POST['name']);
					}
				break;
				case 'del':
					$this->updateActive($_SESSION['manage_username']);
					$oldsection = $db->GetOne('SELECT name FROM '.dbprefix.'sections WHERE id = '.$db->quote($_GET['id']));
					$db->Run('DELETE FROM '.dbprefix.'sections WHERE id = '.$db->quote($_GET['id']));
					Core::Log(time(), $_SESSION['manage_username'], 'Deleted Section: '.$oldsection);
				break;
			}
			Core::Output('/manage/board/sections.tpl', $twig_data);
		} else {
			Core::Error('You don\'t have permissions for this!');
		}
	}
}