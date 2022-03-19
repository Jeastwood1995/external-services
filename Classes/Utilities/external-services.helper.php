<?php

namespace ExternalServices\Classes\Utilities;

class Helper {
	public function getApiOptionsFromAddServiceFormData(array $formData): array {
		return array(
			'method' => 'GET',
			'timeout' => Api_Helper::DEFAULT_TIMEOUT,
			'headers' => array(
				'Content-type' => 'application/' . $formData['dataFormat'],
				'Authorization' => $this->_getAuthorizationTypeFromAddServiceFormData($formData['authType'], $formData),
			)
		);
	}

	private function _getAuthorizationTypeFromAddServiceFormData(String $authType, array $formData): ?String {
		switch ( $authType ) {
			case 'basic':
				return 'Basic ' . base64_encode($formData['basic-username'] . ':' . $formData['basic-password']);
			case 'token':
				return 'Bearer' . $formData['api-token'];
			default:
				return null;
		}
	}

	public function processDataFromAPI(array $apiResponse, String $dataType) {

	}
}