<?php

namespace ExternalServices\Classes\Utilities;

class Form_Controller {
    public function processAddServicePostData() {
        $formData = filter_input_array( INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		$helper = new Helper();
		$apiHelper = new Api_Helper($formData['serviceUrl']);
		$apiHelper->setOptions($helper->getApiOptionsFromAddServiceFormData($formData));
		$apiHelper->connect();

		$helper->processDataFromAPI($apiHelper->getResponse(), $formData['dataFormat']);

        wp_redirect( admin_url( '/admin.php?page=external-services-configure' ) );
        exit;
    }
}