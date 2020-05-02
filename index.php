<?php
//Anonsaba 3.0 Main page
	require './config/config.php';
	require './modules/core.php';
	//Is Anonsaba even installed?
	if (!file_exists(svrpath.'.installed')) {
		Core::Error('It appears you haven\'t installed Anonsaba 3.0 please click <a href="/INSTALL/install.php">here</a> to install!');
	}
	// Great lets configure our values then output them!
	$twig_data['sitename'] = Core::GetConfigOption('sitename');
	$twig_data['slogan'] = Core::GetConfigOption('slogan');
	$twig_data['version'] = Core::GetConfigOption('version');
	$twig_data['irc'] = Core::GetConfigOption('irc');
	switch($_GET['view']) {
		case '': 
			$entires = $db->GetAll('SELECT * FROM '.dbprefix.'front WHERE type = '.$db->quote('news').' ORDER BY date DESC LIMIT 5 OFFSET '.($_GET['page'] * 5));
			break;
		case 'faq':
			$entires = $db->GetAll('SELECT * FROM '.dbprefix.'front WHERE type = '.$db->quote('faq').' ORDER BY date DESC');
			break;
		case 'rules':
			$entries = $db->GetAll('SELECT FROM '.dbprefix.'front WHERE type = '.$db->quote('rules').' ORDER BY date ASC');
			break;
		default:
			$entires = $db->GetAll('SELECT * FROM '.dbprefix.'front WHERE type = '.$db->quote('news').' ORDER BY date DESC LIMIT 5 OFFSET '.($_GET['page'] * 5));
			break;
	}
	$twig_data['entries'] = $entries;
	$twig_data['view'] = $_GET['view'];
	Core::Output('/index.tpl', $twig_data);