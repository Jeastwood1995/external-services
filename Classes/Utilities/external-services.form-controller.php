<?php

namespace ExternalServices\Classes\Utilities;

use ExternalServices\Classes\Models\ES_Temp_Model;

class Form_Controller {
    public function processAddServicePostData() {
        $formData = filter_input_array( INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		$helper = new Helper();
		$apiHelper = new Api_Helper($formData['serviceUrl']);
		$apiHelper->setOptions($helper->getApiOptionsFromAddServiceFormData($formData));
		$apiHelper->connect();

		if ($apiHelper->getResponseCode() != 200) {
			$apiErrorResponse = $apiHelper->getResponseMessage();
			Notices::displayJsAlert("The connection failed: $apiErrorResponse");
		}

		list($data, $success) = $helper->processAddServiceDataFromApiResponse($apiHelper->getResponse(), $formData);

		if (!$success) {
			Notices::displayJsAlert($data);
		}

		$tempModel = new ES_Temp_Model();
	    $tempData = base64_encode(serialize(array('connection-details' => $formData, 'callback-data' => $data)));

	    $tempModel->set(array('data' => $tempData));

        wp_redirect( admin_url( '/admin.php?page=external-services-configure' ) );
        exit;
    }

	public function processConfigureServicePostData() {
		$formData = filter_input_array( INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		$hi = '';
	}
}