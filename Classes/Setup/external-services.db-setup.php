<?php

namespace ExternalServices\Classes\Setup;

use PHPMailer\PHPMailer\Exception;

class Db_Setup {
	/** @var string */
	CONST EXTERNAL_SERVICES_MAIN_TABLE_NAME = 'external_services';

	/** @var \wpdb */
	protected $dbInterface;

	/**
	 * __construct, init global db class
	 */
	public function __construct() {
		$this->dbInterface = $GLOBALS['wpdb'];
	}

	/**
	 * Check to see if database tables need to be installed, uses only the main table to check
	 *
	 * @return bool|int
	 */
	public function checkForInstall() {
		return $this->dbInterface->query('DESCRIBE ' . $this->dbInterface->prefix . self::EXTERNAL_SERVICES_MAIN_TABLE_NAME);
	}

	/**
	 * Install DB tables
	 *
	 * @throws \Exception
	 */
	public function install() {
		try {
			$mainTable = 'CREATE TABLE IF NOT EXISTS ' . $this->dbInterface->prefix . self::EXTERNAL_SERVICES_MAIN_TABLE_NAME . '(
			. id INT(6) AUTO_INCREMENT PRIMARY KEY,
			. service_name VARCHAR(255) NOT NULL,
			. service_url VARCHAR(255) NOT NULL,
			. cron_run VARCHAR(255) NOT NULL,
			. date_created DATETIME DEFAULT CURRENT_TIMESTAMP,
			. date_modified DATETIME ON UPDATE CURRENT_TIMESTAMP
			)';

			$this->dbInterface->query($mainTable);
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage());
		}
	}
}