<?php


namespace ExternalServices\Classes\Models;


use ExternalServices\Classes\Setup\Db_Setup;

class ES_Log_Model extends Model_Base {
	/**
	 * ES_Log_Model constructor.
	 * @throws \ErrorException
	 */
	public function __construct() {
		$this->tableName = Db_Setup::EXTERNAL_SERVICES_LOG_TABLE;
		parent::__construct();
	}
}