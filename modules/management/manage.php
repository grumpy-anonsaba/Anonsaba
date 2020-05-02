<?php

// Anonsaba 3.0 - Management Console

class Management {
	// Verifying that the supplied password is correct
	public static function checkLogin($user, $password) {
		global $db;
		$ip = Core::getIP();
		// If the user doesn't exist throw an error
		if (!$db->GetOne('SELECT * FROM '.dbprefix.'staff WHERE username = '.$db->quote($user))) {
			Core::Log(time(), $user, 'Failed Login attempt from: '.$ip);
			Core::Error('Either the username or Password you supplied is incorrect');
		}
		// First lets make sure that the user account isn't locked out!
		if (self::checkLock($user) && self::checkSuspended($user)) {
			if (password_verify($password, $db->GetOne('SELECT password FROM '.dbprefix.'staff WHERE username = '.$db->quote($user)))) {
				// Lets update the hash 
				// The user will always be able to still login, but if a hacker finds this it will constantly stay changing
				$db->Run('UPDATE '.dbprefix.'staff SET password = '.$db->quote(password_hash($password, PASSWORD_ARGON2I)));
				// Set the users active time!
				$db->Run('UPDATE '.dbprefix.'staff SET active = '.time());
				return true;
			} else {
				Core::Log(time(), $user, 'Failed Login attempt from: '.$ip);
				// Lets update failed login attempts and add 1 to the previous number
				$loginattempts = $db->GetOne('SELECT failed FROM '.dbprefix.'staff WHERE username = '.$db->quote($user)) + 1;
				$db->Run('UPDATE '.dbprefix.'staff SET failed = '.$loginattempts.' WHERE username = '.$db->quote($user));
				// Lets update the failed time as well
				$db->Run('UPDATE '.dbprefix.'staff SET failedtime = '.time().' WHERE username = '.$db->quote($user));
				Core::Error('Either the username or Password you supplied is incorrect');
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
				Core::Error('This account is currently locked and cannot be logged into');
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
			Core::Error('This account is currently locked and cannot be logged into');
		}
	}
}