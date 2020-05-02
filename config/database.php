<?php

// Anonsaba 3.0
// Database wrapper

class Database extends PDO {

		public function Run($query) {
			try {
				$stmt = $this->prepare($query);
				$stmt->execute();
			}
			catch (PDOException $e) {
				echo $e->getMessage();
			}
		}
}