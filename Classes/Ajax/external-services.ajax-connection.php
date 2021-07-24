<?php

namespace ExternalServices\Classes\Ajax;

use ExternalServices\Classes\Views;

/**
 * Class that setups and connect to a given API URL
 *
 * Class Ajax_Test_Connection
 * @package ExternalServices\Classes\Ajax
 */
class Ajax_Connection {
	/**
	 * Get data when connecting to API URL
	 */
	public function getConnection() {
		# check the form nonce for CSRF attacks
		if ( wp_verify_nonce( $_REQUEST['_wpnonce'], 'test-connection' ) ) {
			$data = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );

			# initialize curl connection to service url
			$serviceCall = curl_init( $data['serviceURL'] );

			# return transfer as a string
			curl_setopt( $serviceCall, CURLOPT_RETURNTRANSFER, 1 );

			# set timeout to 60 seconds
			curl_setopt( $serviceCall, CURLOPT_TIMEOUT, 120 );

			# Set authorization header if an authorization key has been set
			if ( isset($data['authHeadCheck'] )) {
				$serviceCall = $this->_setAuthenticationHeader( $serviceCall, $data );
			}

			# result of the curl request
			$result = curl_exec( $serviceCall );

			# get either the error message or whether we need to return failed
			list( $message, $failed ) = $this->_getStatusMessage( curl_getinfo( $serviceCall )['http_code'] );

			# If a 'negative' status code has been returned, then this needs to show on the frontend
			if ( $failed ) {
				wp_send_json_error( $message, curl_getinfo( $serviceCall )['http_code'] );
			} else {
				$format = $data['dataFormat'];
				$data = $this->_processData( $result, $format, $data );
				$callback = array(
					'format' => $format,
					'data'   => $data
				);
				wp_send_json_success( json_encode( $callback ) );
			}
		} else {
			wp_send_json_error( 'Failed to verify the form submission. Please submit the form again.', 401 );
		}
	}

	/**
	 * Function that calls view class with data from AJAX connection result
	 */
	public function callView() {
		$post = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );

		# Load class based on string isset or just null
		$class = ( isset( $post['class'] ) ) ? new $post['class']( $post['data'] ) : null;

		# With the converted data, call the configure service view and return the template
		$viewEngine = new Views();
		$html = preg_replace( "/\r|\n|\t/", '', $viewEngine->returnView( $post['view'], $class, false, true ) );
		wp_send_json_success( $html );
	}

	/**
	 * Format the API data depending on XML/JSON/CSV
	 *
	 * @param $data
	 * @param $format
	 * @param $post
	 *
	 * @return false|mixed|string[]
	 */
	private function _processData( $data, $format, $post ) {
		switch ( $format ) {
			case 'xml':
				# Convert data to xml object
				$xml = new \SimpleXMLElement( $data );

				$this->_setDataSession($xml, $format);

				# Get first index
				$firstIndex = key( $xml );

				# Then convert it to an array
				return (array) $xml->$firstIndex;
			case 'csv':
				# Get the options from the user
				$delimeter     = isset( $post['csv-delimeter'] ) ? $post['csv-delimeter'] : null;
				$enclosure     = isset( $post['csv-enclosure'] ) ? $post['csv-enclosure'] : null;
				$escape        = isset( $post['csv-escape'] ) ? $post['csv-escape'] : null;
				$escapeNewLine = isset( $post['csv-end-escape'] ) ? $post['csv-end-escape'] : null;
				$csv = array();

				# Set the default column count if not set or below 0
				if ( isset( $post['csv-columncount'] ) && $post['csv-columncount'] > 1 ) {
					$columnCount = $post['csv-columncount'];
				} else {
					$columnCount = 1;
				}

				# Parse csv data with options
				$data = str_getcsv( $data, $delimeter, $enclosure, $escape );

				# Take of the first number of records via $columnCount to get the headers
				$headers = array_splice($data, 0, $columnCount);
				if ( $escapeNewLine != null ) {
					$this->_escapeEndOfLine( $headers, $data );
				}

				# Add the headers to the csv array
				$csv[] = $headers;

				# Make a multi-dimensional array of the parsed and formatted csv data
				$this->_generateCSVData($data, $csv, $columnCount, $escapeNewLine);

				$this->_setDataSession($csv, $format);

				return array_combine($csv[0], $csv[1]);
			case 'json':
				# Decode the json
				$json = json_decode( $data );

				$this->_setDataSession($json, $format);

				# Get the first key
				$firstKey = key( $json );

				# Then only return the first index
				return (array) $json[ $firstKey ];
		}
	}

	/**
	 * Analyse the status of code of the CURL request, then return true/false. If false then also return one of the failed messages
	 *
	 * @param $statusCode
	 *
	 * @return array
	 */
	private function _getStatusMessage( $statusCode ) {
		$failed  = true;
		$message = '';

		switch ( $statusCode ) {
			case 404:
				$message = 'Resource doesn\'t exist on the target server.';
				break;
			case 403:
				$message = 'Forbidden. The resource you\'ve tried to access is protected.';
				break;
			case 401:
				$message = 'Unauthorized. Please check your authorization key.';
				break;
			case 400:
				$message = 'Bad request, please verify the integrity of the data you\'re sending.';
				break;
			case 301:
				$message = 'Redirected. The resource you\'re trying to access has been permanently moved.';
				break;
			case 200:
				$failed = false;
				break;
			case 0:
				$message = 'URL doesn\'t exist.';
				break;
		}

		return array( $message, $failed );
	}

	/**
	 * Set authorization to CURL request if selected
	 *
	 * @param $ch
	 * @param $data
	 *
	 * @return mixed
	 */
	private function _setAuthenticationHeader( $ch, $data ) {
		switch ( $data['authType'] ) {
			case 'basic':
				curl_setopt( $ch, CURLOPT_USERPWD, $data['basic-username'] . ":" . $data['basic-apiKey'] );
				break;
			case 'token':
				curl_setopt( $ch, CURLOPT_HTTPHEADER, 'Authorization: Bearer ' . $data['token'] );
				break;
		}

		return $ch;
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

		array_push( $csvBatch, current( $batchLastElement ) );
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

	private function _setDataSession( $data, $format ) {
		/*
		if ( isset( $_COOKIE['configure-service-callback-data'] ) && md5( $data ) != md5( $_COOKIE['configure-service-callback-data'] ) ) {
			unset( $_COOKIE['configure-service-callback-data'] );
		}
		$cookieString = base64_encode(json_encode( $data ));
		setcookie( 'configure-service-callback-data', base64_encode( json_encode( $data ) ), strtotime( '+30 minutes' ) );
		$hi = 'hi';
		*/
		$callbackData = base64_encode(json_encode(array(
			'format' => $format,
			'data' => $data
		)));

		session_start();
		$_SESSION['configure-service-callback-data'] = 'test';

		$hi = 'hi';
	}
}