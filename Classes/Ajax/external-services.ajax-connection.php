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

             # return transfer as a string
             curl_setopt($serviceCall, CURLOPT_RETURNTRANSFER, 1);
             # set timeout
             curl_setopt($serviceCall, CURLOPT_TIMEOUT, 60);

            # Set authorization header if an authorization key has been set
            if ($data['authKey']) {
                $authKey = base64_encode(':' . $data['authKey']);
                $auth_header = "Authorization: Basic " . base64_encode(':' . $authKey);

                curl_setopt($serviceCall, CURLOPT_HEADER, $auth_header);
            }

            # result of the curl request
            $result = curl_exec($serviceCall);

            $info = curl_getinfo($serviceCall);

            $failed = true;

            switch (curl_getinfo($serviceCall)['http_code']) {
                case 404:
                    $message = 'Resource doesn\'t exist on the target server.';
                    //wp_send_json_error('Connection failed: status code 404 returned. Resource doesn\'t exist on the target server.');
                    break;
                case 403:
                    $message = 'Forbidden. The resource you\'ve tried to access is protected.';
                    break;
                case 401:
                    $message = 'Unauthorized. Please check your authorization key.';
                    //wp_send_json_error('Connection failed: status code of 401 returned. Please check your authorization key.');
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
                    //wp_send_json_error('Connection failed: URL doesn\'t exist.');
                    break;
            }

            if ($failed) {
                wp_send_json_error($message, curl_getinfo($serviceCall)['http_code']);
            } else {
                $data = $this->_processData($result, $data['dataFormat']);
                print_r($data);
            }
        } else {
             wp_send_json_error('Failed to verify the form submission. Please submit the form again.', 401);
         }
    }

    /**
     *
     *
     * @param $data
     * @param $format
     * @return false|mixed|string[]
     */
    protected function _processData($data, $format) {
        switch ($format) {
            case 'xml':
                $xml = new \SimpleXMLElement($data);
                $firstIndex = key($xml);
                $firstArray = (array) $xml->$firstIndex;

                $firstArrayKeys = array_keys($firstArray);
                return $firstArrayKeys;
                break;
            case 'csv':
                $lines = explode("\n", $data);
                $head = str_getcsv(array_shift($lines));

                return explode(';', $head[0]);
                break;
            case 'json':
                return json_decode($data);
                break;
        }
    }
}