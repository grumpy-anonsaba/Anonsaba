<?php
//Anonsaba 3.0 Main page
	require './config/config.php';
	require './modules/core.php';
	//Is Anonsaba even installed?
	if (!file_exists(svrpath.'.installed')) {
		Core::Error('It appears you haven\'t installed Anonsaba 3.0 please click <a href="/INSTALL/install.php">here</a> to install!');
	}
	// Great lets configure our values then output them!
	$twig_data['slogan'] = Core::GetConfigOption('slogan');
	$twig_data['version'] = Core::GetConfigOption('version');
	$twig_data['irc'] = Core::GetConfigOption('irc');
	switch($_GET['view']) {
		case 'faq':
			$entries = $db->GetAll('SELECT * FROM '.dbprefix.'front WHERE type = '.$db->quote('faq').' ORDER BY date DESC');
			break;
		case 'rules':
			$entries = $db->GetAll('SELECT FROM '.dbprefix.'front WHERE type = '.$db->quote('rules').' ORDER BY date ASC');
			break;
		default:
			$entries = $db->GetAll('SELECT * FROM '.dbprefix.'front WHERE type = '.$db->quote('news').' ORDER BY date DESC LIMIT 5 OFFSET '.($_GET['page'] * 5));
			break;
	}
	/* Old code snippit */
	$sections = array();
	$results_boardexist = $db->GetAll('SELECT id FROM '.dbprefix.'boards LIMIT 1');
	if (count($results_boardsexist) >= 0) {
		$sections = $db->GetAll('SELECT * FROM  '.dbprefix.'sections ORDER BY `order` ASC');
		foreach($sections AS $key=>$section) {
			$results = $db->GetAll('SELECT * FROM '.dbprefix.'boards WHERE section = '.$db->quote($section['name']).' ORDER BY name ASC');
			foreach($results AS $line) {
				$sections[$key]['boards'][] = $line;
			}
		}
	}
	/* End old code snippit */
	$twig_data['recentposts'] = $db->GetAll('SELECT * FROM '.dbprefix.'posts WHERE deleted = 0 ORDER BY time DESC LIMIT 5');
	$twig_data['postcount'] = $db->GetOne('SELECT COUNT(*) FROM '.dbprefix.'posts WHERE deleted = 0');
	$twig_data['uniqueusers'] = $db->GetOne('SELECT COUNT(DISTINCT ipid) FROM '.dbprefix.'posts WHERE deleted = 0');
	$twig_data['boards'] = $sections;
	$twig_data['pages'] = $db->GetOne('SELECT COUNT(*) FROM  '.dbprefix.'front WHERE type = '.$db->quote('news'));
	$twig_data['entries'] = $entries;
	$twig_data['view'] = $_GET['view'];
	Core::Output('/index.tpl', $twig_data);