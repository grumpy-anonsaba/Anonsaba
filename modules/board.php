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
	public static function printPage($filename, $contents, $board) {
		global $db;
		$tempfile = tempnam(fullpath . $board . '/res', 'tmp'); /* Create the temporary file */
		$fp = fopen($tempfile, 'w');
		fwrite($fp, $contents);
		fclose($fp);
		if (!@rename($tempfile, $filename)) {
			copy($tempfile, $filename);
			unlink($tempfile);
		}
		chmod($filename, 0664); /* it was created 0600 */
	}
}