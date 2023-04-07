<?php

namespace ExternalServices\Classes\Utilities;

use ExternalServices\Classes\Models\Es_Configuration_Model;
use ExternalServices\Classes\Models\ES_Main_Model;

class Form_Helper {
	public function getApiOptionsFromAddServiceFormData(array $formData): array {
		$options = array(
			'method' => 'GET',
			'timeout' => Api_Helper::DEFAULT_TIMEOUT,
			'headers' => array(
				'Content-type' => 'application/' . $formData['dataFormat'],
			)
		);

		if (isset($formData['authType'])) {
			$options['headers']['Authorization'] = $this->_getAuthorizationTypeFromAddServiceFormData($formData['authType'], $formData);
		}

		return $options;
	}

	private static function _getAuthorizationTypeFromAddServiceFormData(String $authType, array $formData): ?String {
		switch ( $authType ) {
			case 'basic':
				return 'Basic ' . base64_encode($formData['basicUsername'] . ':' . $formData['basicPassword']);
			case 'token':
				return 'Bearer' . $formData['apiToken'];
			default:
				return null;
		}
	}

	public function processAddServiceDataFromApiResponse(?array $apiResponse, array $formData): array {
		switch ($formData['dataFormat']) {
			case "json":
				list($data, $success) = $this->processJsonData($apiResponse['body']);
				break;
			case "xml":
				list($data, $success) = $this->processXMLData($apiResponse['body']);
				break;
			case "csv":
				list($data, $success) = $this->processCSVData($apiResponse['body'], $formData);
				break;
			default:
				$data = array();
				$success = false;
		}

		return array($data, $success);
	}

	public static function processJsonData(String $responseBody): array {
		return array(json_decode($responseBody), true);
	}

	public static function processXMLData(String $responseBody): array {
		# XML processing involves having the xml extension installed on the client's server,
		# so if it doesn't exist then can't do anything with XML data
		if (function_exists('simplexml_load_string')) {
			return array(simplexml_load_string($responseBody), true);
		}

		return array('XML extension isn\'t loaded in your PHP configuration file. Please add this if you want to process XML data', false);
	}

	public function processCSVData(String $responseBody, array $formData): array {
		# Get the options from the user
		$delimeter     = $formData['csv-delimeter'] ?? null;
		$enclosure     = $formData['csv-enclosure'] ?? null;
		$escape        = $formData['csv-escape'] ?? null;
		$escapeNewLine = $formData['csv-end-escape'] ?? null;
		$csv = array();

		# Set the default column count if not set or below 0
		if ( isset( $formData['csv-columncount'] ) && $formData['csv-columncount'] > 1 ) {
			$columnCount = $formData['csv-columncount'];
		} else {
			$columnCount = 1;
		}

		# Parse csv data with options
		$data = str_getcsv( $responseBody, $delimeter, $enclosure, $escape );

		# Take of the first number of records via $columnCount to get the headers
		$headers = array_splice($data, 0, $columnCount);
		if ( $escapeNewLine != null ) {
			$this->_escapeEndOfLine( $headers, $data );
		}

		# Add the headers to the csv array
		$csv[] = $headers;

		# Make a multidimensional array of the parsed and formatted csv data
		$this->_generateCSVData($data, $csv, $columnCount, $escapeNewLine);

		return array($csv, true);
	}

	/**
	 * Explode the last record of a row via new line, with the the first element add it as the last element of the batch and the other to the start of the csv
	 *
	 * @param $csvBatch
	 * @param $csvData
	 */
	private function _escapeEndOfLine(&$csvBatch, &$csvData) {
		$batchLastElement = explode( PHP_EOL, array_pop( $csvBatch ) );
		$last = end( $batchLastElement );
		$last = preg_replace( "/[^a-zA-Z0-9]+/", "", html_entity_decode( $last, ENT_QUOTES ) );
		reset($batchLastElement);

		$csvBatch[] = current( $batchLastElement );
		array_unshift( $csvData, $last );
	}

	private function _generateCSVData(&$data, &$csv, $columnCount, $escapeNewLine) {
		do {
			$batchData = array_splice( $data, 0, $columnCount );

			if ( $escapeNewLine != null ) {
				$this->_escapeEndOfLine($batchData, $data);
			}

			$csv[] = $batchData;
		} while (count($data) >= $columnCount);
	}

	public function setMainServiceData(array $connectionDetails, string $cronRun) {
		$mainModel = new ES_Main_Model;
		$mainModelData = array(
			'service_name' => $connectionDetails['serviceName'],
			'service_url' => $connectionDetails['serviceUrl'],
			'cron_run' => $cronRun,
		);

		$serviceId = $mainModel->set($mainModelData);

		$hi = '';
	}

	/**
	 * @param array $form
	 *
	 * @return void
	 */
	public function processConfigureServiceFormData(array $form) {
		$configurationModel = new Es_Configuration_Model();
		$modelArray = array();

		foreach ($form['fields'] as $dataKey => $dataValue ) {
			$keyDataArray = array();

			if (!isset($dataValue['checked'])) {
				continue;
			}

			if (is_array($dataValue["mapping"])) {
				foreach ($dataValue["mapping"] as $key => $value) {
					$var = '';
				}
			} else {
				$hi = '';
			}
		}
	}
}