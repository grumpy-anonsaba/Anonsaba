<?php

// Anonsaba 3.0 - Management Console

class Management {
	// Verifying that the supplied password is correct
	checkLogin($user, $password) {
		global $db;
		if (password_verify($password, $db->GetOne('SELECT password FROM '.dbprefix.'staff WHERE username = '.$db->quote($user))) {
			return true;
		} else {
			// Lets grab the users IP so we can notate this in the logs
			if(!empty($_SERVER['HTTP_CLIENT_IP'])){
				// ip from share internet
				$ip = $_SERVER['HTTP_CLIENT_IP'];
			} elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
				// ip pass from proxy
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else {
				$ip = $_SERVER['REMOTE_ADDR'];
			}
			Core::Log(time(), 'Failed Login attempt from: '.$ip);
			// Lets update failed login attempts and add 1 to the previous number
			$loginattempts = $db->GetOne('SELECT failed FROM '.dbprefix.'staff WHERE username = '.$db->quote($user));
			$db->Run('UPDATE '.dbprefix.'staff SET failed = '.$loginattempts++);
			Core::Error('Either the username or Password you supplied is incorrect');
		}
	}