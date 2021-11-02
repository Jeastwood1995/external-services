<?php


namespace ExternalServices\Classes\Models;


use ExternalServices\Classes\Setup\Db_Setup;

class ES_Custom_Auth_Model extends Model_Base {
	/**
	 * ES_Custom_Auth_Model constructor.
	 * @throws \ErrorException
	 */
	public function __construct() {
		$this->tableName = Db_Setup::EXTERNAL_SERVICES_AUTHENTICATION_CONFIG_TABLE;
		parent::__construct();
	}
}