<?php
// Anonsaba 3.0 Board stuff!

	require '../config/config.php';
	require '../modules/core.php';
	
	/* Begin the wall of declares */
	$qry = $db->prepare('SELECT * FROM '.dbprefix.'boards WHERE name = ?');
		   $qry->execute(array($_GET['board']));
	$board = $qry->fetchAll();
	if ($board) {
		$twig_data['boardname'] = $board[0]['name'];
		$twig_data['boarddesc'] = $board[0]['desc'];
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
		$twig_data['boards'] = $sections;
		Core::Output('/board/board_page.tpl', $twig_data);
	} else {
		echo 'No';
	}