<?php

// Anonsaba 3.0
// Requirement: PHP 7.4 ^
//				Twig installed via composer
//				MySQL
// Before Attempting to install this file please view the How to Install post <insert post link here>
// Configuration file


	$config = array();
	
	// Install password
	$config['installpass'] = ''; // This is to protect your install from being hijacked prior to you being ready
	
	// Database configuration
	$config['conntype'] = false; // Change this to true if you want to connect with a Socket
	$config['dbtype'] = 'mysql'; // Please choose your DB type - JK only MySQL (Future release maybe?)
	$config['dbhost'] = 'localhost'; // This is where the Database host is (Ex. localhost, 127.0.0.1, 255.255.255.255)
	$config['dbname'] = ''; // This is the name of your database
	$config['dbuser'] = ''; // Enter the user account you wish to connect with here
	$config['dbpass'] = ''; // Please navigate to /config/encrypt.php via webbrowser and enter your password there to get an encrypted version. Paste the result here
	$config['dbprefix'] = ''; // Use prefixes on all your tables (Ex. CHAN_)
	$config['dbconstant'] = false; // Do you want to use a constant connection to your SQL?
	
	// Website paths
	$config['svrpath'] = realpath(dirname(__DIR__)).'/'; // Do not change this unless you know what you're doing
	$config['weburl'] = ''; // Please enter your websites full address here. (Note: https is supported)
	$config['webcookie'] = ''; // Example: www.4chan.org -> .4chan.org
	
	// Twig Paths
	$config['dir'] = $config['fullpath'].'pages';
	$config['cache'] = $config['fullpath'].'pages_cache';
	
	// Security
	$config['hash'] = ''; // Please enter 24+ LETTERS AND NUMBERS here
	
	// That's all no need to modify anything below this line
	if (!isset($db)) {
		require 'database.php';
		if ($config['conntype']) {
			$dsn = $config['dbtype'].':unix_socket='.$config['dbhost'].';dbname='.$config['dbname'];
		} else {
			$dsn = $config['dbtype'].':host='.$config['dbhost'].';dbname='.$config['dbname'];
		}
		$db = new Database($dsn, $config['dbuser'], $config['dbpass']);
	}
	if (!isset($twig)) {
		require_once dirname(__DIR__, 3).'/vendor/autoload.php';
		$loader = new \Twig\Loader\FilesystemLoader($config['dir']);
		$twig = new \Twig\Environment($loader, ['cache' => $config['cache'],]);
	}
	foreach ($config as $key=>$value) {
		define($key, $value);
	}
	unset($config);