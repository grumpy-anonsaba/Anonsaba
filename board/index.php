<?php
// Anonsaba 3.0 Board stuff!

	require '../config/config.php';
	require '../modules/core.php';
	
	/* Post uploads */
	if ($_GET['action'] == 'post') {
		$is_mod = false;
		$mod_first = false;
		$mod_user = '';
		// Let's check if the user is a mod
		if ($_POST['modpass'] != '') {
			$qry = $db->prepare('SELECT sessionid, username FROM '.dbprefix.'staff');
				   $qry->execute();
			$mc = $qry->fetchAll();
			foreach ($mc as $mod_check) {
				if (Core::sDecrypt($mod_check['sessionid']) == $_POST['modpass']) {
					$mod_first = true;
					$mod_user = $mod_check['username'];
				}
			}
			if ($mod_first) {
				// User passed initial  check
				$qry = $db->prepare('SELECT php_sessionid FROM '.dbprefix.'staff WHERE username = ?');
					   $qry->execute(array($mod_user));
				$mod_second = $qry->fetch();
				if (password_verify($_POST['sessid'], $mod_second['php_sessionid'])) {
					$is_mod = true;
				} else {
					// Destroy the management session
					$qry = $db->prepare('UPDATE '.dbprefix.'staff SET sessionid = "", php_sessionid = "" WHERE username = ?');
						   $qry->execute(array($mod_user));
				}
			}
		}
		// Declare our JSON stuff
		$result = '';
		$reason = '';
		$rid = '';
		// Lets make sure the board isn't locked
		$qry = $db->prepare('SELECT locked FROM boards WHERE name = ?');
			   $qry->execute(array($_POST['board']));
		$locked = $qry->fetch();
		if ($locked['locked'] == 1 && !$is_mod) {
			$result = 'failed';
			$reason = 'Board is locked';
		} else {
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
			$result = 'success';
			$rid = ''.$id.'';
		}
		$results = array('result' => $result, 'reason' => $reason, 'id' => $rid);
		die(json_encode($results));
	}
	/* Begin the wall of declares */
	$qry = $db->prepare('SELECT * FROM '.dbprefix.'boards WHERE name = ?');
		   $qry->execute(array($_GET['board']));
	$board = $qry->fetchAll();
	if ($board) {
		$twig_data['boardname'] = $board[0]['name'];
		$twig_data['boarddesc'] = $board[0]['desc'];
		$twig_data['boardlock'] = $board[0]['locked'];
		$twig_data['board'] = $board;
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
		$qry = $db->prepare('SELECT * FROM '.dbprefix.'files WHERE board = ? AND type IN ('.$files.')');
			   $qry->execute(array($board[0]['name']));
		$twig_data['thread_files'] = $qry->fetchAll();
		$twig_data['boards'] = $sections;
		$qry = $db->prepare('SELECT COUNT(id) as count, id FROM '.dbprefix.'files WHERE board = ? GROUP BY id');
			   $qry->execute(array($board[0]['name']));
		$twig_data['thread_files_count'] = $qry->fetchAll();
		Core::Output('/board/board_page.tpl', $twig_data);
	}