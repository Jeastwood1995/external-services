<?php

namespace ExternalServices\Classes\Models;

use ExternalServices\Classes\Setup\Db_Setup;

class Main extends Model_Base {
	protected $tableName = Db_Setup::EXTERNAL_SERVICES_MAIN_TABLE_NAME;
}