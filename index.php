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
	$twig_data['url'] = weburl;
	switch($_GET['view']) {
		case 'faq':
			$qry = $db->prepare('SELECT * FROM '.dbprefix.'front WHERE type = ? ORDER BY ordr');
			$qry->execute(array('faq'));
			$entries = $qry->fetchAll();
			break;
		case 'rules':
			$qry = $db->prepare('SELECT * FROM '.dbprefix.'front WHERE type = ? ORDER BY ordr');
			$qry->execute(array('rules'));
			$entries = $qry->fetchAll();
			break;
		default:
			$qry = $db->prepare('SELECT * FROM '.dbprefix.'front WHERE type = ? ORDER BY date DESC LIMIT 5 OFFSET ?');
			$qry->execute(array('news', ($_GET['page'] * 5)));
			$entries = $qry->fetchAll();
			break;
	}
	/* Old code snippit */
	$sections = array();
	$qry = $db->prepare('SELECT id FROM '.dbprefix.'boards LIMIT 1');
		   $qry->execute();
		   $results_boardexist = $qry->fetchAll();
	if (count($results_boardsexist) >= 0) {
		$qry = $db->prepare('SELECT * FROM '.dbprefix.'sections ORDER BY `order` ASC');
		$qry->execute();
		$sections = $qry->fetchAll();
		foreach($sections AS $key=>$section) {
			$qry = $db->prepare('SELECT * FROM '.dbprefix.'boards WHERE section = ? ORDER BY name ASC');
			$qry->execute(array($section['name']));
			$results = $qry->fetchAll();
			foreach($results AS $line) {
				$sections[$key]['boards'][] = $line;
			}
		}
	}
	/* End old code snippit */
	$qry = $db->prepare('SELECT name FROM '.dbprefix.'boards');
		   $qry->execute();
		   $boards = $qry->fetchAll();
	foreach ($boards as $board) {
		$total += Core::GetSize(svrpath.$board['name']);
	}
	$qry = $db->prepare('SELECT COUNT(*) FROM '.dbprefix.'front WHERE type = ?');
		   $qry->execute(array('news'));
		   $result = $qry->fetch();
	$pages = (is_array($result)) ? array_shift($result) : $result;
	
	$qry = $db->prepare('SELECT * FROM '.dbprefix.'boards WHERE recentpost = 0');
		   $qry->execute();
	$brdqry = $qry->fetchAll();
	$board = array();
	foreach ($brdqry as $line) {
		$board[] = $line['name'];
	}
	$newboard = implode(', ', $board);
	if ($brdqry) {
		$execute = 'AND boardname NOT IN ('.$db->quote($newboard).') ';
	} else {
		$execute = '';
	}
	$qry = $db->prepare('SELECT * FROM '.dbprefix.'posts WHERE deleted = 0 '.$execute.'ORDER BY time DESC LIMIT 5');
		   $qry->execute();
	$twig_data['recentposts'] = $qry->fetchAll();
	
	$qry = $db->prepare('SELECT COUNT(*) FROM '.dbprefix.'posts WHERE deleted  = 0');
		   $qry->execute();
		   $result = $qry->fetch();
	$twig_data['postcount'] = (is_array($result)) ? array_shift($result) : $result;
	
	$qry = $db->prepare('SELECT COUNT(DISTINCT ipid) FROM '.dbprefix.'posts WHERE deleted = 0');
		   $qry->execute();
		   $result = $qry->fetch();
	$twig_data['uniqueusers'] = (is_array($result)) ? array_shift($result) : $result;

	$twig_data['boards'] = $sections;
	$twig_data['pages'] = ($pages/5);
	$twig_data['page'] = $_GET['page'];
	$twig_data['entries'] = $entries;
	$twig_data['view'] = $_GET['view'];
	$twig_data['activecontent'] = Core::formatSizeUnits($total);
	Core::Output('/index.tpl', $twig_data);