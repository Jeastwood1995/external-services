<?php


namespace ExternalServices\Classes\Models;


class Configure_Service extends Model_Base {

	/**
	 * Configure_Service constructor.
	 *
	 * @param null $data
	 */
	public function __construct($data = null) {
		parent::__construct($data);
		/*
		if (!empty($data) && !isset($_COOKIE['configure-service-callback-data'])) {
			if ((!empty($data)) && (isset($_COOKIE['configure-service-callback-data'])) && md5($data) != md5($_COOKIE['configure-service-callback-data'])){

			}
		}
		*/
		if (!empty($data)) {
			if (isset($_COOKIE['configure-service-callback-data']) && md5($data) != md5($_COOKIE['configure-service-callback-data'])) {
				unset($_COOKIE['configure-service-callback-data']);
			}

			setcookie('configure-service-callback-data', base64_encode(json_encode($data)), strtotime('+30 minutes'));
			$hi = 'hi';
		}
	}

	/**
	 * Capitalise first character
	 *
	 * @param $key
	 *
	 * @return string
	 */
	public function formatDataKey( $key ) {
		return ucwords( $key );
	}

	/**
	 * Download file of gathered data from the API call
	 */
	public function downloadDataFile() {
		$data = json_decode(base64_decode($_COOKIE['configure-service-callback-data']));
		$hi = 'hi';
	}
}