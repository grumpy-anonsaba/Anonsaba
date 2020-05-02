<?php

// Anonsaba 3.0
// This is the installation script for Anonsaba 3.0
	require realpath(dirname(__DIR__)).'/'.'config/config.php';
	require realpath(dirname(__DIR__)).'/'.'modules/core.php';
	
	// Setting 'success' to 0 so it knows what to display
	$twig_data['success'] = 0;
	// Lets run some prerequisite checks
	
	// Lets make sure this server can even run Anonsaba
	// First we need to ensure PHP is at least version 7
	if (phpversion() < 7.4) {
		Core::Error('Please update PHP to at least PHP 7.4');
	}
	// Lets ensure that the config file has min requirements
	// Now lets ensure that $config['installpass'] is actually set!
	if (installpass === '') {
		Core::Error('Install Password cannot be left blank!');
	}
	// Now lets check the lengths of salt and hash!
	if (strlen(salt) <= 23) {
		Core::Error('Please ensure that Salt is contains at least 24 characters!');
	}
	if (strlen(hash) <= 23) {
		Core::Error('Please ensure that Hash is contains at least 24 characters!');
	}
	// Now one final check - Did the user simply just copy hash<->salt?
	if (hash === salt) {
		Core::Error('Please make Salt/Hash unique from eachother!');
	}
	// Now we start the script!
	// First lets make sure that .failed doesn't exist in INSTALL (This is to stop brute force attempts!)
	if (file_exists(svrpath.'INSTALL/.failed')) {
		Core::Error('Please remove the file .failed from the INSTALL folder to continue');
	}
	if (isset($_POST['checkpass'])) {
		// Lets make sure that the install password they entered is what's in config.php
		if ($_POST['installpass'] != installpass) {
			fopen(svrpath.'INSTALL/.failed', 'w');
			Core::Error('Install password incorrect please try again!');
		} else {
			$twig_data['success'] = 1;
			// Lets make sure that anonsaba.sql exist and is bigger than 0 bytes
			if (file_exists('anonsaba.sql') && (filesize('anonsaba.sql') > 0)) {
				$sqlfile = fopen('anonsaba.sql', 'r');
				$readdata = fread($sqlfile, filesize('anonsaba.sql'));
				$readdata = str_replace('PREFIX',dbprefix,$readdata);
				fclose($sqlfile);
			} else {
				Core::Error('It appears there is a problem with anonsaba.sql <br /> Please ensure the file exists, is bigger than 0 bytes, and you have permissions to the file');
			}
			// Now that we have grabbed all the necessary information and compiled it - Lets insert 
			$db->Run('ALTER DATABASE '.database.' CHARACTER SET utf8 COLLATE utf8_general_ci');
			$sqlarray = explode("\n", $readdata);
			foreach ($sqlarray as $key => $sqldata) {
				$sqldata = trim($sqldata);
				if (strstr($sqldata, '--') || strlen($sqldata) === 0) {
					unset($sqlarray[$key]);
				}
			}
			$readdata = implode('',$sqlarray);
			$sqlarray = explode(';',$readdata);
			foreach ($sqlarray as $sqldata) {
				$sqldata = trim($sqldata);
				if (strlen($sqldata) !== 0) { 
					$pos1 = strpos($sqldata, '`');
					$pos2 = strpos($sqldata, '`', $pos1 + 1);
					$tablename = substr($sqldata, $pos1+1, ($pos2-$pos1)-1);
					$db->Run($sqldata);
				}
			}
		}
	}
	if (isset($_POST['submit'])) {
		// Almost done!
		// All we need to do is add in the rest of the information the user entered
		$pass = Core::Encrypt($_POST['password']);
		$conf_names = array('sitename', 'slogan', 'irc', 'timgh', 'timgw', 'rimgh', 'rimgw', 'bm', 'installtime', 'version');
		$conf_values = array($_POST['sitename'], $_POST['slogan'], $_POST['irc'], $_POST['timgh'], $_POST['timgw'], $_POST['rimgh'], $_POST['rimgw'], $_POST['bm'], time(), '3.0');
		// Look I didn't have to run this SQL query twice!
		// Silly grumpy >.<
		for ($i = 10; $i >= 0; --$i) {
			$db->Run('INSERT INTO '.dbprefix.'site_config (config_value, config_name) VALUES ('.$db->quote($conf_values[$i]).', '.$db->quote($conf_names[$i]).')');
		}
		$db->Run('INSERT INTO '.dbprefix.'staff (username, password, level, boards) VALUES ('.$db->quote($_POST['username']).', '.$db->quote($pass).', "admin", "all")');
		// I should honestly just move this into anonsaba.sql
		$file_id = array('1', '2', '3');
		$file_type = array('png', 'jpg', 'gif');
		for ($i = 3; $i >= 0; --$i) {
			$db->Run('INSERT INTO '.dbprefix.'filetypes (id, name) VALUES ('.$file_id[$i].', '.$db->quote($file_type[$i]).')');
		}
		// End silly code
		fopen(svrpath.'.installed', 'w');
		$twig_data['success'] = 2;
	}
	Core::Output('/install/install.tpl', $twig_data);