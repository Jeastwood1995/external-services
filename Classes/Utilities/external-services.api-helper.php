<?php

namespace ExternalServices\Classes\Utilities;

class Api_Helper {
    /**
     * @var CurlHandle
     */
    private $curlInit;

    public static function init(String $url) {
        $curlInit = curl_init($url);
    }
}