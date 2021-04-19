<?php

// Anonsaba 3.0
// BoardCore class 
// This class handles generating the board page

class BoardCore {
	var $board = array();
	public function board ($board) {
		global $db;
		if ($board != '') {
			$qry = $db->prepare('SELECT * FROM '.dbprefix.'boards WHERE name = ?');
				   $qry->execute(array($board));
			$results = $qry->fetchAll();
			foreach ($results[0] as $key=>$line) {
				if (!is_numeric($key)) {
					$this->board[$key] = $line;
				}
			}
			$qry = $db->prepare('SELECT COUNT(DISTINCT ipid) FROM '.dbprefix.'posts WHERE boardname = ? AND deleted = 0');
				   $qry->execute(array($this->board['name']));
				   $result = $qry->fetch();
			$this->board['uniqueposts'] = (is_array($result)) ? array_shift($result) : $result;
		}
	}
	public function refreshAll() {
		$this->refreshPages();
		//self::refreshThreads();
	}
	public function refreshPages() {
		global $db, $twig_data, $twig;
		$twig_data['filetypes'] = $this->board['filetypes'];
		$qry = $db->prepare('SELECT * FROM '.dbprefix.'files WHERE board = ?');
			   $qry->execute(array($this->board['name']));
		$twig_data['files'] = $qry->fetchAll();
		$twig_data['timgh'] = Core::GetConfigOption('timgh');
		$twig_data['timgw'] = Core::GetConfigOption('timgw');
		$twig_data['rimgh'] = Core::GetConfigOption('rimgh');
		$twig_data['rimgw'] = Core::GetConfigOption('rimgw');
		$twig_data['board'] = $this->board;
		$twig_data['weburl'] = weburl;
		$qry = $db->prepare('SELECT * FROM '.dbprefix.'posts WHERE boardname = ? AND deleted = 0');
			   $qry->execute(array($this->board['name']));
		$twig_data['posts'] = $qry->fetchAll();
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
		$twig_data['sitename'] = Core::GetConfigOption('sitename');
		$data = $twig->render('/board/board_page.tpl', $twig_data);
		$data = str_replace('\t', '',$data);
		$data = str_replace('&nbsp;\r\n', '&nbsp;',$data);
		$this->printPage(svrpath.$this->board['name'].'/board.html', $this->board['name'], $data);
	}
	public function printPage($filename, $board, $content) {
		$tempfile = tempnam(svrpath . $board . '/res', 'tmp'); 
		$fp = fopen($tempfile, 'w');
		fwrite($fp, $content);
		fclose($fp);
		if (!@rename($tempfile, $filename)) {
			copy($tempfile, $filename);
			unlink($tempfile);
		}
		chmod($filename, 0664);
	}
}