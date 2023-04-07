<?php

namespace ExternalServices\Classes\Utilities;

use ExternalServices\Classes\Models\ES_Temp_Model;

class Form_Controller {
	/**
	 * @var Form_Helper
	 */
	private $formHelper;

	public function __construct() {
		$this->formHelper = new Form_Helper();
	}

    public function processAddServicePostData() {
        $formData = filter_input_array( INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		$apiHelper = new Api_Helper($formData['serviceUrl']);
		$apiHelper->setOptions($this->formHelper->getApiOptionsFromAddServiceFormData($formData));
		$apiHelper->connect();

		if ($apiHelper->getResponseCode() != 200) {
			$apiErrorResponse = $apiHelper->getResponseMessage();
			Notices::displayJsAlert("The connection failed: $apiErrorResponse");
		}

		list($data, $success) = $this->formHelper->processAddServiceDataFromApiResponse($apiHelper->getResponse(), $formData);

		if (!$success) {
			Notices::displayJsAlert($data);
		}

		$tempModel = new ES_Temp_Model();
	    $tempData = base64_encode(serialize(array('connection-details' => $formData, 'callback-data' => $data)));

	    $result = $tempModel->set(array('data' => $tempData));

		if ( ! $result ) {
			Notices::displayJsAlert("There has been an error whilst processing this request: $result");
		} else {
			wp_redirect( admin_url( '/admin.php?page=external-services-configure' ) );
			exit;
		}
    }

	public function processConfigureServicePostData() {
		$formData = filter_input_array( INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		$connectionDetails = unserialize(base64_decode($formData['connectionDetails']));

		$this->formHelper->setMainServiceData($connectionDetails, $formData['cronSchedule']);

		$this->formHelper->processConfigureServiceFormData($formData);
	}
}