<?php

// Anonsaba 3.0 Management console

// First lets grab all the files we need!
require_once realpath(dirname(__DIR__)).'/config/config.php';
require_once svrpath.'modules/management/manage.php';
require_once svrpath.'modules/core.php';


// Okay let's start the session now that we have those files
session_set_cookie_params(1800); // Cookie last for 30 minutes
session_start();

// Now let's actually start the management modules/core
$manage = new Management();

if ($manage->validateSession(true)) {
	// Let us begin the WALL OF DECLARES!!!!
	$twig_data['username'] = $_SESSION['manage_username'];
	$twig_data['level'] = $manage->getStaffLevel($_SESSION['manage_username']);
	$twig_data['current'] = $_GET['side'];
	$twig_data['action'] = $_GET['action'];
	// Lets decide who gets access to what!
	switch($_GET['side']) {
		case 'main':
			$twig_data['sectionname'] = 'Main';
			$twig_data['names'] = array('Statistics' , 'Show Posting Password', 'Change Account Password');
			$twig_data['urls'] = array('&action=stats', '&action=pp', '&action=changepass');
			$twig_data['arraynum'] = count($twig_data['names']);
			break;
		case 'site':
			$twig_data['sectionname'] = 'Site Administration';
			$twig_data['names'] = array('News' , 'Rules', 'FAQ', 'Staff', 'Logs', 'Clean up', 'Site configuration');
			$twig_data['arraynum'] = count($twig_data['names']);
			$twig_data['urls'] = array('&action=news', '&action=rules', '&action=faq', '&action=staff', '&action=logs', '&action=clean', '&action=siteconfig');
			$twig_data['arraynum'] = count($twig_data['names']);
			break;
		case 'board':
			if($manage->getStaffLevel($_SESSION['manage_username']) == '1') {
				$twig_data['sectionname'] = 'Boards Administration';
				$twig_data['names'] = array('Add/Delete boards' , 'Board Options', 'Edit filetypes', 'Edit Sections', 'Word filter', 'Spam filter', 'Manage Ads', 'Move threads', 'Rebuild board', 'Rebuild all boards');
				$twig_data['urls'] = array('&action=adddelboard', '&action=boardopt', '&action=filetypes', '&action=sections', '&action=wf', '&action=sf', '&action=ads', '&action=movethread', '&action=rebuildboard', '&action=rebuildall');
				$twig_data['arraynum'] = count($twig_data['names']);
			} elseif($manage->getStaffLevel($_SESSION['manage_username']) == '2') {
				$twig_data['sectionname'] = 'Boards Administration';
				$twig_data['names'] = array('Board Options', 'Word filter', 'Spam filter', 'Move threads', 'Rebuild board', 'Rebuild all boards');
				$twig_data['urls'] = array('&action=boardopt', '&action=wf', '&action=sf', '&action=movethread', '&action=rebuildboard', '&action=rebuildall');
				$twig_data['arraynum'] = count($twig_data['names']);
			}
			break;
		case 'mod':
			$twig_data['sectionname'] = 'Moderation';
			$twig_data['names'] = array('View/Add/Delete Bans', 'View Reports', 'View Appeals', 'View Recent Posts');
			$twig_data['urls'] = array('&action=bans', '&action=reports', '&action=appeal', '&action=recentpost');
			$twig_data['arraynum'] = count($twig_data['names']);
			break;
	}
}
$action = isset($_GET['action']) ? $_GET['action'] : 'stats';
$side = isset($_GET['side']) ? $_GET['side'] : 'main';
if ($_GET['acti'] == 'login') {
	$manage->checkLogin($side, $action);
	$manage->validateSession();
}
switch ($action) {
	case 'logout':
		$manage->logOut();
		break;
	default:
		$manage->ValidateSession();
		page($action);
		break;
}
function page($action) {
	global $manage;
	$manage->$action();
}