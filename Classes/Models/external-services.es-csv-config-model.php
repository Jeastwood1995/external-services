<?php


namespace ExternalServices\Classes\Models;


use ExternalServices\Classes\Setup\Db_Setup;

class ES_Csv_Config_Model extends Model_Base {
	/**
	 * ES_Csv_Config_Model constructor.
	 * @throws \ErrorException
	 */
	public function __construct() {
		$this->tableName = Db_Setup::EXTERNAL_SERVICES_CSV_CONFIG_TABLE;
		parent::__construct();
	}
}