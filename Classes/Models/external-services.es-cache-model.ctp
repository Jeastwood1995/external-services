<?php


namespace ExternalServices\Classes\Models;


use ExternalServices\Classes\Setup\Db_Setup;

class Es_Cache_Model extends Model_Base {
	/**
	 * Es_Cache_Model constructor.
	 * @throws \ErrorException
	 */
	public function __construct() {
		$this->tableName = Db_Setup::EXTERNAL_SERVICES_CACHE_TABLE;
		parent::__construct();
	}
}