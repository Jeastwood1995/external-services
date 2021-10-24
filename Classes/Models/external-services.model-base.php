<?php


namespace ExternalServices\Classes\Models;

abstract class Model_Base {
	/** @var string $tableName */
	protected $tableName;

	/** @var string $query */
	protected $query;

	protected $dbData;

	/** @var \wpdb */
	protected $dbInterface;

	public function __construct() {
		if (empty($tablename)) {
			throw new Exception('Table name not specified');
		}

		$this->dbInterface = $GLOBALS['wpdb'];

		if (!$this->dbInterface->query('DESCRIBE ' . $this->tableName)) {
			throw new \Exception('Table ' . $this->tableName . ' doesn\'t exist!');
		}
	}

	public function get(?array $columns = array()) {
		if (is_array($columns)) {
			if (!empty($columns)) {
				$columnString = "";

				for ($i = 0; $i <= count($columns); $i++) {
					$columnString .= $columns[$i];

					if ($i != count($columns)) {
						$columnString .= ", ";
					} else {
						$columnString .= " ";
					}
				}

				$this->query = "SELECT $columnString FROM $this->tableName";

				return $this->dbInterface->query($this->query);
			} else {
				$this->query = "SELECT * FROM $this->tableName";

				return $this->dbInterface->query($this->query);
			}
		} elseif (is_string($columns)) {
			$this->query = "SELECT $columns FROM $this->tableName"

			return $this->dbInterface->query($this->query);
		} else {

		}
	}

	public function set(?array $columns = array()) {

	}

	public function join(String $table, String $mainId, String $joinId, String $type = "INNER") {

	}

	public function filter(string $columnName) {

	}

	public function sortBy(string $columnName) {

	}
}