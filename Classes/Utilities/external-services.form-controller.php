<?php

namespace ExternalServices\Classes\Utilities;

class Form_Controller {
    public function processAddServicePostData() {
        $formData = filter_input_array( INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		//$apiHelper = new Api_Helper($formData['serviceURL']);
		//$apiHelper->setConnectionOptionsArray(array());

        wp_redirect( admin_url( '/admin.php?page=external-services-configure' ) );
        exit;
    }
}