<?php

namespace ExternalServices\Classes\Models;

use ExternalServices\Classes\Setup\Db_Setup;

class ES_Main_Model extends Model_Base {
	/**
	 * ES_Main_Model constructor.
	 * @throws \ErrorException
	 */
	public function __construct() {
		$this->tableName = Db_Setup::EXTERNAL_SERVICES_MAIN_TABLE_NAME;
		parent::__construct();
	}
}