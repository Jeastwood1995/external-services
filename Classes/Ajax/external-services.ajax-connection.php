<?php

namespace ExternalServices\Classes\Ajax;

/**
 * Class that setups and connect to a given API URL
 *
 * Class Ajax_Test_Connection
 * @package ExternalServices\Classes\Ajax
 */
class Ajax_Connection
{
    public function getConnection() {

         if (wp_verify_nonce($_REQUEST['_wpnonce'], 'test-connection')) {
            $data = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            # initialize curl connection to service url
            $serviceCall = curl_init($data['serviceURL']);

             #return transfer as a string
             curl_setopt($serviceCall, CURLOPT_RETURNTRANSFER, 1);

            # Set authorization header if an authorization key has been set
            if ($data['authKey']) {
                $authKey = base64_encode(':' . $data['authKey']);
                $header = array();
                $header[] = 'Content-length: 0';
                $header[] = 'Content-type: application/json';
                $header[] = 'Authorization: Basic ' . $authKey;

                curl_setopt($serviceCall, CURLOPT_HEADER, $header);
            }

            $result = curl_exec($serviceCall);

            $hi = var_dump($result);

            $sup = 'yo';
        } else {
             wp_send_json_error('Failed to verify the form submission. Please submit the form again.', 401);
         }
    }
}