<?php

// Anonsaba 3.0
// BoardCore class 
// This class handles generating the board page

class BoardCore {
	var $board = array();
	public function board ($board) {
		global $db;
		if ($board != '') {
			$results = $db->GetAll('SELECT * FROM '.dbprefix.'boards WHERE name = '.$db->quote($board));
			foreach ($results[0] as $key=>$line) {
				if (!is_numeric($key)) {
					$this->board[$key] = $line;
				}
			}
			$this->board['uniqueposts'] = $db->GetOne('SELECT COUNT(DISTINCT ipid) FROM '.dbprefix.'posts WHERE boardname = '.$db->quote($this->board['name']).' AND deleted = 0');
		}
	}
	public function refreshAll() {
		self::refreshPages();
		self::refreshThreads();
	}
	public function refreshPages() {
		global $db, $twig_data, $twig;
		$twig_data['filetypes'] = $this->board['filetypes'];
		$twig_data['files'] = $db->GetAll('SELECT * FROM '.dbprefix.'files WHERE boardname = '.$db->quote($this->board['name']));;
		$twig_data['timgh'] = Core::GetConfigOption('timgh');
		$twig_data['timgw'] = Core::GetConfigOption('timgw');
		$twig_data['rimgh'] = Core::GetConfigOption('rimgh');
		$twig_data['rimgw'] = Core::GetConfigOption('rimgw');
		$twig_data['posts'] = $db->GetAll('SELECT * FROM '.dbprefix.'posts WHERE boardname = '.$db->quote($this->board['name']).' AND deleted = 0');
		$data = $twig->render('/board/board_page.tpl', $twig_data);
		$data = str_replace('\t', '',$data);
		$data = str_replace('&nbsp;\r\n', '&nbsp;',$data);
		self::printPage(svrpath.$this->board['name'].'/board.html', $this->board['name'], $data);
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