<?php

// Anonsaba 3.0
// BoardCore class 
// This class handles generating the board page

class BoardCore {
	var $board = array();
	public static function board ($board) {
		global $db;
		if ($board != '') {
			$results = $db->GetOne('SELECT * FROM '.dbprefix.'boards WHERE name = '.$db->quote($board));
			foreach ($results[0] as $key=>$line) {
				if (!is_numeric($key)) {
					$this->board[$key] = $line;
				}
			}
			$this->board['uniqueposts'] = $db->GetOne('SELECT COUNT(DISTINCT ipid) FROM '.dbprefix.'posts WHERE boardname = '.$db->quote($this->board['name']).' AND deleted = 0');
		}
	}
	public static function refreshPages() {
		global $db, $twig_data;
			$twig_data['filetypes'] = $this->board['filetypes'];
			
	}
	public static function refreshAll() {
		self::refreshPages();
		self::refreshThreads();
	}
}