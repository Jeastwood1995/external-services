<?php

namespace ExternalServices\Classes\Ajax;

use ExternalServices\Classes\Controllers\Configure_Service;
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
			curl_setopt( $serviceCall, CURLOPT_TIMEOUT, 60 );

			# Set authorization header if an authorization key has been set
			if ( $data['authKey'] ) {
				$authKey     = base64_encode( ':' . $data['authKey'] );
				$auth_header = "Authorization: Basic " . base64_encode( ':' . $authKey );

				curl_setopt( $serviceCall, CURLOPT_HEADER, $auth_header );
			}

			# result of the curl request
			$result = curl_exec( $serviceCall );

			# get either the error message or whether we need to return failed
			list( $message, $failed ) = $this->_getStatusMessage( curl_getinfo( $serviceCall )['http_code'] );

			# If a 'negative' status code has been returned, then this needs to show on the frontend
			if ( $failed ) {
				wp_send_json_error( $message, curl_getinfo( $serviceCall )['http_code'] );
			} else {
				$data = $this->_processData( $result, $data['dataFormat'] );
				wp_send_json_success(json_encode( $data ));
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
		$data = '';

		# If data does exist, then flip the keys and values, the values will be transformed from the keys
		if (!empty($post['data'])) {
			$data = $post['data'];
			# Remove HTML encoded characters
			array_walk($data, array($this, '_formatValues'));

			# Then flip the values around and build the Table header values
			$data = array_flip($data);
			array_walk($data, array($this, '_buildHeaders'));
		}

		# With the converted data, call the configure service view and return the template
		$viewEngine = new Views();
		$html = preg_replace("/\r|\n|\t/", '', $viewEngine->returnView('configureService', new Configure_Service($data), false, true));
		wp_send_json_success($html);
	}

	/**
	 * Format the API data depending on XML/JSON/CSV
	 *
	 * @param $data
	 * @param $format
	 *
	 * @return false|mixed|string[]
	 */
	private function _processData( $data, $format ) {
		switch ( $format ) {
			case 'xml':
				# Convert data to xml object
				$xml = new \SimpleXMLElement( $data );

				# Get first index
				$firstIndex = key( $xml );

				# Then convert it to an array
				$firstArray = (array) $xml->$firstIndex;

				# Get the array keys of the first array then return them
				$firstArrayKeys = array_keys( $firstArray );

				return $firstArrayKeys;
			case 'csv':

				$lines = explode( "\n", $data );
				$head  = str_getcsv( array_shift( $lines ) );

				return explode( ';', $head[0] );
			case 'json':
				//return json_decode($data);
				$json = json_decode( $data );

				$firstKey   = key( $json );
				$firstArray = (array) $json[ $firstKey ];

				$firstArrayKeys = array_keys( $firstArray );

				return $firstArrayKeys;
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

		return array($message, $failed);
	}

	/**
	 * Build the array value by stripping HTMl encoded characters, adding a space before any encountered numbers and capitalizing first letter
	 *
	 * @param $row
	 * @param $key
	 */
	private function _formatValues(&$row) {
		$row = wp_specialchars_decode($row, ENT_QUOTES);
		$row = str_replace('"', '', $row);
	}

	/**
	 * Capitalize the new flipped key and add a space before the first number
	 *
	 * @param $row
	 * @param $key
	 */
	private function _buildHeaders(&$row, &$key) {
		$row = ucwords(preg_replace('/(?=\d)/', ' ', $key));
	}
}