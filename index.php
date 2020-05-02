<?php
//Anonsaba 3.0 Main page
	require './config/config.php';
	require './modules/core.php';
	//Is Anonsaba even installed?
	if (!file_exists(svrpath.'.installed')) {
		Core::Error('It appears you haven\'t installed Anonsaba 3.0 please click <a href="/INSTALL/install.php">here</a> to install!');
	}
	$twig_data['sitename'] = Core::GetConfigOption('sitename');
	$twig_data['slogan'] = Core::GetConfigOption('slogan');
	$twig_data['version'] = Core::GetConfigOption('version');
	$twig_data['irc'] = Core::GetConfigOption('irc');
	isset($_GET['view']) ? $_GET['view'] : '';
	if ($_GET['view'] == '') {
		$entires = $db->GetAll('SELECT * FROM '.dbprefix.'front WHERE type = '.$db->quote('news').' ORDER BY date DESC LIMIT 5 OFFSET '.($_GET['page'] * 5));
	} elseif ($_GET['view'] == 'faq') {
		$entires = $db->GetAll('SELECT * FROM '.dbprefix.'front WHERE type = '.$db->quote('faq').' ORDER BY date DESC');
	} elseif ($_GET['view'] == 'rules') {
		$entries = $db->GetAll('SELECT FROM '.dbprefix.'front WHERE type = '.$db->quote('rules').' ORDER BY date ASC');
	}
	$twig_data['entries'] = $entries;
	Core::Output('/index.tpl', $twig_data);