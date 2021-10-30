<?php


namespace ExternalServices\Classes\Models;


use ExternalServices\Classes\Setup\Db_Setup;

class ES_Temp_Model extends Model_Base {
	/**
	 * ES_Temp_Model constructor.
	 * @throws \ErrorException
	 */
	public function __construct() {
		$this->tableName = Db_Setup::EXTERNAL_SERVICES_TEMP_TABLE;
		parent::__construct();
	}
}