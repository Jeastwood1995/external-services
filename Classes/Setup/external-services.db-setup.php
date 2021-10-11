<?php

namespace ExternalServices\Classes\Setup;

use ExternalServices\Classes\ES_init;

class Db_Setup {
	/** @var string */
	CONST EXTERNAL_SERVICES_MAIN_TABLE_NAME = 'wp_external_services';
	/** @var string */
	CONST EXTERNAL_SERVICES_CSV_CONFIG_TABLE = self::EXTERNAL_SERVICES_MAIN_TABLE_NAME . '_csv_config';
	/** @var string */
	CONST EXTERNAL_SERVICES_AUTHENTICATION_CONFIG_TABLE = self::EXTERNAL_SERVICES_MAIN_TABLE_NAME . '_custom_auth_config';
	/** @var string */
	CONST EXTERNAL_SERVICES_TEMP_TABLE = self::EXTERNAL_SERVICES_MAIN_TABLE_NAME . '_temp';
	/** @var string */
	CONST EXTERNAL_SERVICES_CACHE_TABLE = self::EXTERNAL_SERVICES_MAIN_TABLE_NAME . '_cache';
	/** @var string */
	CONST EXTERNAL_SERVICES_LOG_TABLE = self::EXTERNAL_SERVICES_MAIN_TABLE_NAME . '_log';
	/** @var string */
	CONST EXTERNAL_SERVICES_DB_OPTION_VALUE = 'external_services_db_version';

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
		return !$this->dbInterface->query('DESCRIBE ' . self::EXTERNAL_SERVICES_MAIN_TABLE_NAME);
	}

	/**
	 * Install DB tables
	 *
	 * @throws \Exception
	 */
	public function install() {
		if ($this->checkForInstall()) {
			try {
				# Install main table
				$mainTable = "CREATE TABLE IF NOT EXISTS " . self::EXTERNAL_SERVICES_MAIN_TABLE_NAME . "(
				service_id INT AUTO_INCREMENT PRIMARY KEY,
				service_name VARCHAR(255) NOT NULL COMMENT 'Identification name for the service',
				service_url VARCHAR(255) NOT NULL COMMENT 'URL to call every {cron_job} minutes',
				cron_run SMALLINT NOT NULL COMMENT 'Time in minutes when to call URL. 0 indicates only call once and not schedule',
				date_created DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Date and time for when service was created',
				date_modified DATETIME ON UPDATE CURRENT_TIMESTAMP COMMENT 'Date and time when the service was updated last'
			)";

				dbDelta($mainTable);

				# CSV options table install
				$csvTable = "CREATE TABLE IF NOT EXISTS " . self::EXTERNAL_SERVICES_CSV_CONFIG_TABLE . "(
    			csv_id INT AUTO_INCREMENT PRIMARY KEY,
    			service_id INT COMMENT 'Primary ID of main table',
    			deliminator VARCHAR(30) COMMENT 'Value to deliminate row data by',
    			enclosure VARCHAR(30) COMMENT 'Value to enclose each row data',
    			escape_value VARCHAR(30) COMMENT 'Strips away the defined character defined here from each CSV row',
    			column_count TINYINT DEFAULT 1 COMMENT 'Number of columns user defines to map each row to the correct column header. Defaults to 1',
				new_line_escape TINYINT DEFAULT 0 COMMENT 'Escapes the last column row via a new line. Defaults to 0',
    			FOREIGN KEY (service_id) REFERENCES " . self::EXTERNAL_SERVICES_MAIN_TABLE_NAME . "(service_id)
    		)";

				dbDelta($csvTable);

				#
				$customAuthTable = "CREATE TABLE IF NOT EXISTS " . self::EXTERNAL_SERVICES_AUTHENTICATION_CONFIG_TABLE . "(
				auth_id INT AUTO_INCREMENT PRIMARY KEY,
				service_id INT COMMENT 'Primary ID of main table',
				auth_type VARCHAR(10) COMMENT 'Type of authentication, either basic or bearer (for now)',
				basic_auth_username VARCHAR(50) COMMENT 'Username for basic authentication',
				basic_auth_password VARCHAR(100) COMMENT 'Password for basic authentication',
				bearer_token VARCHAR(255) COMMENT 'API for bearer authentication',
				FOREIGN KEY (service_id) REFERENCES " . self::EXTERNAL_SERVICES_MAIN_TABLE_NAME . "(service_id)
			)";

				dbDelta($customAuthTable);

				# Temp table install
				$tempTable = "CREATE TABLE IF NOT EXISTS " . self::EXTERNAL_SERVICES_TEMP_TABLE . "(
				temp_id INT AUTO_INCREMENT PRIMARY KEY,
				data BLOB COMMENT 'Serialized data of current add service session'
			)";

				dbDelta($tempTable);

				# Cache table install
				$cacheTable = "CREATE TABLE IF NOT EXISTS " . self::EXTERNAL_SERVICES_CACHE_TABLE . "(
				cache_id INT AUTO_INCREMENT PRIMARY KEY,
				service_id INT COMMENT 'Primary ID of main table, blank if user is still in an open session',
				temp_id INT COMMENT 'Primary ID of temp table, blank if service isn\'t configured and user selects download file. Gets deleted when the user saves the service or after a service hasn\'t been set after 30 minutes.',
				data BLOB COMMENT 'Cached data retrieved from service, update if data has changed.',
				FOREIGN KEY (service_id) REFERENCES " . self::EXTERNAL_SERVICES_MAIN_TABLE_NAME . "(service_id),
				FOREIGN KEY (service_id) REFERENCES " . self::EXTERNAL_SERVICES_TEMP_TABLE . "(temp_id)
			)";

				dbDelta($cacheTable);

				$logTable = "CREATE TABLE IF NOT EXISTS " . self::EXTERNAL_SERVICES_LOG_TABLE . "(
				log_id INT AUTO_INCREMENT PRIMARY KEY,
				service_id INT COMMENT 'Primary ID of main table',
				date_created DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Date and time for when service was called',
				status BOOLEAN COMMENT 'True/False whether the call was successful or not',
				message VARCHAR(255) COMMENT 'Contains error message or details of call',
				FOREIGN KEY (service_id) REFERENCES " . self::EXTERNAL_SERVICES_MAIN_TABLE_NAME . "(service_id)			
			)";

				dbDelta($logTable);

				# Add the module version as a wp option (stored in wp_options)
				add_option(self::EXTERNAL_SERVICES_DB_OPTION_VALUE, get_plugin_data( EXTERNAL_SERVICES_FILE, false )['Version']);
			} catch (\Exception $e) {
				throw new \Exception($e->getMessage());
			}
		}
	}

	/**
	 * Uninstalls all ES related db tables
	 */
	public function uninstall() {
		$foreignKeyCheck = $this->dbInterface->query("SELECT @@foreign_key_checks;");

		$this->dbInterface->query("SET FOREIGN_KEY_CHECKS=0;");

		$this->dbInterface->query("DROP TABLE " . self::EXTERNAL_SERVICES_MAIN_TABLE_NAME);
		$this->dbInterface->query("DROP TABLE " . self::EXTERNAL_SERVICES_CSV_CONFIG_TABLE);
		$this->dbInterface->query("DROP TABLE " . self::EXTERNAL_SERVICES_AUTHENTICATION_CONFIG_TABLE);
		$this->dbInterface->query("DROP TABLE " . self::EXTERNAL_SERVICES_TEMP_TABLE);
		$this->dbInterface->query("DROP TABLE " . self::EXTERNAL_SERVICES_CACHE_TABLE);
		$this->dbInterface->query("DROP TABLE " . self::EXTERNAL_SERVICES_LOG_TABLE);

		if ($foreignKeyCheck== 1) {
			$this->dbInterface->query("SET FOREIGN_KEY_CHECKS=1;");
		}

		delete_option(self::EXTERNAL_SERVICES_DB_OPTION_VALUE);
	}
}