<?php

namespace ExternalServices\Classes\Utilities;

use Exception;

class Helper {
	public function getApiOptionsFromAddServiceFormData(array $formData): array {
		return array(
			'method' => 'GET',
			'timeout' => Api_Helper::DEFAULT_TIMEOUT,
			'headers' => array(
				'Content-type' => 'application/' . $formData['dataFormat'],
				'Authorization' => $this->_getAuthorizationTypeFromFormData($formData['authType']),
			)
		);
	}

	private function _getAuthorizationTypeFromFormData(String $authType, array $formData): String {
		switch ( $authType ) {
			case 'basic':
				return 'Basic: ' . base64_encode();
			case 'token':
				curl_setopt( $ch, CURLOPT_HTTPHEADER, 'Authorization: Bearer ' . $data['token'] );
				break;
		}
	}
}