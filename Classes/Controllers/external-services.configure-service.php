<?php


namespace ExternalServices\Classes\Controllers;


class Configure_Service extends Controller_Base {

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