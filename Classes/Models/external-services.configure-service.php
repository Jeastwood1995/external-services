<?php


namespace ExternalServices\Classes\Models;


class Configure_Service extends Model_Base {

	/**
	 * Capitalise first character
	 *
	 * @param $key
	 *
	 * @return string
	 */
	public function formatDataKey($key) {
		return ucwords($key);
	}
}