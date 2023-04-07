<?php

namespace ExternalServices\Classes\Models;

use ExternalServices\Classes\Setup\Db_Setup;

class Es_Configuration_Model extends Model_Base {
	/**
	 * Es_Configuration_Model constructor.
	 * @throws \ErrorException
	 */
	public function __construct() {
		$this->tableName = Db_Setup::EXTERNAL_SERVICES_CONFIGURATION_TABLE;
		parent::__construct();
	}
}