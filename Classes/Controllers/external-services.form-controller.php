<?php

namespace ExternalServices\Classes\Controllers;

/**
 * Class Form_Controller
 * @package ExternalServices\Classes\Controllers
 */
class Form_Controller
{
    /** @var object */
    protected $data;

    /**
     *
     */
    public function controllerFormSubmit()
    {
        $nonce = (isset($_REQUEST['_wpnonce'])) ? sanitize_text_field($_REQUEST['_wpnonce']) : '';

        if (wp_verify_nonce($nonce, 'form-controller')) {

        } else {
            echo 'You\'re session expired, please go back and try again.<br><br>';
            echo '<a href="' . $_REQUEST["_wp_http_referer"] . '">Back</a>';
            wp_die();
        }
    }
}