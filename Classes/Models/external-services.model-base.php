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

	/**
	 * Model_Base constructor.
	 * @throws \ErrorException
	 */
	public function __construct() {
		if (empty($this->tableName)) {
			throw new \ErrorException('Table name not specified');
		}

		$this->dbInterface = $GLOBALS['wpdb'];

		if (!$this->dbInterface->query('DESCRIBE ' . $this->tableName)) {
			throw new \ErrorException('Table ' . $this->tableName . ' doesn\'t exist!');
		}
	}

	/**
	 * Get selected column names or all columns from a db table if left blank
	 *
	 * @param array $columns
	 *
	 * @return array|object|null
	 * @throws \ErrorException
	 */
	public function get(array $columns = array()) {
		if (is_array($columns)) {
			if (!empty($columns)) {
				if (count($columns) > 1) {
					$columnString = "";

					for ($i = 0; $i < count($columns); $i++) {
						$columnString .= $columns[$i];

						if ($i != count($columns) - 1) {
							$columnString .= ", ";
						}
					}

					$this->query = "SELECT $columnString FROM $this->tableName";
				} else {
					$this->query = "SELECT $columns[0] FROM $this->tableName";
				}
			} else {
				$this->query = "SELECT * FROM $this->tableName";
			}

			return $this->dbInterface->get_results($this->query);
		} else {
			throw new \ErrorException('Passed in type must either be a string or an array of columns');
		}
	}

	/**
	 * Set a row of data in a table
	 *
	 * @param array $data
	 * @param int|null $id
	 */
	public function set(array $data, ?int $id = null) {
		if ($id != null) {
			$queryResult = $this->dbInterface->get_results("SHOW KEYS FROM $this->tableName WHERE Key_name = 'PRIMARY'")[0];

			$this->dbInterface->update($this->tableName, $data, array($queryResult->Column_name => $id));
		} else {
			$this->dbInterface->insert($this->tableName, $data);
		}
	}

	/**
	 * Delete a row of data from a table, or truncate a table if just null is passed through
	 *
	 * @param array|null $where
	 */
	public function delete(?array $where) {
		if ($where != null) {
			$this->dbInterface->delete($this->tableName, $where);
		} else {
			$foreignKeyCheck = $this->dbInterface->query("SELECT @@foreign_key_checks;");

			$this->dbInterface->query("SET FOREIGN_KEY_CHECKS=0;");

			$this->dbInterface->query("TRUNCATE TABLE $this->tableName");

			if ($foreignKeyCheck== 1) {
				$this->dbInterface->query("SET FOREIGN_KEY_CHECKS=1;");
			}
		}
	}

	public function join(String $table, String $mainId, String $joinId, String $type = "INNER") {

	}

	public function filter(string $columnName) {

	}

	public function sortBy(string $columnName) {

	}
}