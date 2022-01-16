<?php
// Anonsaba 3.0 Board stuff!

	require '../config/config.php';
	require '../modules/core.php';
	
	/* Post uploads */
	if ($_GET['action'] == 'post') {
		$qry = $db->prepare('SELECT id + 1 FROM '.dbprefix.'posts WHERE boardname = ? ORDER by id DESC LIMIT 0, 1');
			   $qry->execute(array($_POST['board']));
		$idq = $qry->fetch();
		$id = ($idq) ? $idq['id + 1'] : 1;
		$ipid = Core::sEncrypt(Core::getIP());
		$qry = $db->prepare('SELECT ip, ipid FROM '.dbprefix.'posts');
			   $qry->execute();
		$ipidq = $qry->fetchAll();
		foreach ($ipidq as $r) {
			if (Core::sDecrypt($r['ip']) == Core::getIP()) {
				$ipid = $r['ipid'];
			}
		}
		$qry = $db->prepare('INSERT INTO '.dbprefix.'posts (`id`, `name`, `email`, `subject`, `message`, `password`, `parent`, `ip`, `boardname`, `ipid`, `bumped`, `time`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
			   $qry->execute(array($id, $_POST['username'], $_POST['email'], $_POST['subject'], $_POST['post'], password_hash($_POST['password'], PASSWORD_ARGON2I), 0, Core::sEncrypt(Core::getIP()), $_POST['board'], $ipid, time(), time()));
	}
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
		$qry = $db->prepare('SELECT 
								    id,
									name,
									email,
									subject,
									message,
									password,
									level,
									parent,
									time,
									deleted,
									ipid,
									sticky,
									`lock`,
									bumped,
									cleared,
									report,
									banmessage
							FROM '.dbprefix.'posts WHERE boardname = ? AND deleted = 0 AND parent = 0 ORDER BY sticky DESC, bumped DESC, time DESC');
			   $qry->execute(array($board[0]['name']));
		$twig_data['thread_posts'] = $qry->fetchAll();
		$qry = $db->prepare('SELECT
								id as threadid,
								(SELECT COUNT(*) FROM '.dbprefix.'posts WHERE parent = threadid) as replies
							FROM '.dbprefix.'posts WHERE PARENT = 0 AND boardname = ?');
				$qry->execute(array($board[0]['name']));
		$twig_data['thread_replies'] = $qry->fetchAll();
		$files = '"jpg", "png", "gif", "youtube"';
		echo $query;
		$qry = $db->prepare('SELECT * FROM '.dbprefix.'files WHERE board = ? AND type IN ('.$files.')');
			   $qry->execute(array($board[0]['name']));
		$twig_data['thread_files'] = $qry->fetchAll();
		$twig_data['boards'] = $sections;
		Core::Output('/board/board_page.tpl', $twig_data);
	}