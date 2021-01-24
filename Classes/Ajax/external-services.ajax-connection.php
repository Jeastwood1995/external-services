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
    /**
     * Get data when connecting to API URL
     */
    public function getConnection()
    {
        # check the form nonce for CSRF attacks
        if (wp_verify_nonce($_REQUEST['_wpnonce'], 'test-connection')) {
            $data = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            # initialize curl connection to service url
            $serviceCall = curl_init($data['serviceURL']);

            # return transfer as a string
            curl_setopt($serviceCall, CURLOPT_RETURNTRANSFER, 1);

            # set timeout to 60 seconds
            curl_setopt($serviceCall, CURLOPT_TIMEOUT, 60);

            # Set authorization header if an authorization key has been set
            if ($data['authKey']) {
                $authKey = base64_encode(':' . $data['authKey']);
                $auth_header = "Authorization: Basic " . base64_encode(':' . $authKey);

                curl_setopt($serviceCall, CURLOPT_HEADER, $auth_header);
            }

            # result of the curl request
            $result = curl_exec($serviceCall);

            # get either the error message or whether we need to return failed
            list($message, $failed) = $this->_getStatusMessage(curl_getinfo($serviceCall)['http_code']);

            # If a 'negative' status code has been returned, then this needs to show on the frontend
            if ($failed) {
                wp_send_json_error($message, curl_getinfo($serviceCall)['http_code']);
            } else {
                $data = $this->_processData($result, $data['dataFormat']);
                echo json_encode($data);
            }
        } else {
            wp_send_json_error('Failed to verify the form submission. Please submit the form again.', 401);
        }
    }

    /**
     * Function that
     */
    public function callView() {
        $data = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        $hi = 'hi';
    }

    /**
     * Format the API data depending on XML/JSON/CSV
     *
     * @param $data
     * @param $format
     * @return false|mixed|string[]
     */
    private function _processData($data, $format)
    {
        switch ($format) {
            case 'xml':
                # Convert data to xml object
                $xml = new \SimpleXMLElement($data);

                # Get first index
                $firstIndex = key($xml);

                # Then convert it to an array
                $firstArray = (array)$xml->$firstIndex;

                # Get the array keys of the first array then return them
                $firstArrayKeys = array_keys($firstArray);
                return $firstArrayKeys;
            case 'csv':

                $lines = explode("\n", $data);
                $head = str_getcsv(array_shift($lines));

                return explode(';', $head[0]);
            case 'json':
                //return json_decode($data);
                $json = json_decode($data);

                $firstKey = key($json);
                $firstArray = (array)$json[$firstKey];

                $firstArrayKeys = array_keys($firstArray);
                return $firstArrayKeys;
        }
    }

    /**
     * Analyse the status of code of the CURL request, then return true/false. If false then also return one of the failed messages
     *
     * @param $statusCode
     * @return array
     */
    private function _getStatusMessage($statusCode) {
        $failed = true;
        $message = '';

        switch ($statusCode) {
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
}