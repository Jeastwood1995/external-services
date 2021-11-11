<?php

namespace ExternalServices\Classes\Utilities;

use ExternalServices\Classes\Models\ES_Temp_Model;

class Helper {
	/**
	 * @var ES_Temp_Model  $tempModel
	 */
	private $tempModel;

	public function __construct() {
		$this->tempModel = new ES_Temp_Model();
	}

	/**
	 * Download file of gathered data from the API call
	 */
	public function downloadDataFile() {
		$sessionData = $this->tempModel->get();
		$data = unserialize(base64_decode($sessionData[0]->data));

		$hi = '';
	}
}