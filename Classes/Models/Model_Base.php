<?php


namespace ExternalServices\Classes\Models;

abstract class Model_Base {

	protected $tableName;

	protected $tableData;

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

	public function get() {

	}

	public function set() {

	}

	public function filter() {

	}

	public function sortBy() {

	}
}