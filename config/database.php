<?php

// Anonsaba 3.0
// Database wrapper

class Database extends PDO {

		public function Run($query) {
			try {
				$stmt = $this->prepare($query);
				$stmt->execute();
				$stmt->closeCursor();
				unset($stmt);
			}
			catch (PDOException $e) {
				echo $e->getMessage();
			}
		}
		public function GetOne($query) {
			try {
				$stmt = $this->prepare($query);
				$stmt->execute();
				$result = $stmt->fetch(PDO::FETCH_ASSOC);
				$stmt->closeCursor();
				return (is_array($result)) ? array_shift($result) : $result;
				unset($stmt);
			}
			catch (PDOException $e) {
				echo $e->getMessage();
			}
		}
		public function GetAll($query) {
			try {
				$stmt = $this->prepare($query);
				$stmt->execute();
				$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();
				return $result;
				unset($stmt);
			}
			catch (PDOException $e) {
				echo $e->getMessage();
			}
		}			
}