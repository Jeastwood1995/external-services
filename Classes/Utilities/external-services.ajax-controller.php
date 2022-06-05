<?php

namespace ExternalServices\Classes\Utilities;

use ExternalServices\Classes\Models\ES_Temp_Model;
use ExternalServices\Classes\Utilities\View_Controller;

/**
 * Class that setups and connect to a given API URL
 *
 * Class Ajax_Test_Connection
 * @package ExternalServices\Classes\Ajax
 */
class Ajax_Controller {

    /**
     * Checks whether a nonce from a form submission is valid
     * 
     */
    public function checkFormNonce() {
		if ( wp_verify_nonce( $_REQUEST['nonce_val'], $_REQUEST['nonce_key'] ) ) {
            wp_send_json_success(null, 200);
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
		$viewEngine = new View_Controller();
		$html = preg_replace( "/\r|\n|\t/", '', $viewEngine->returnView( $post['view'], $class, false, true ) );
		wp_send_json_success( $html );
	}
}