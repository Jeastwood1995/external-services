<?php


namespace ExternalServices\Classes;

use ExternalServices\Classes\Tables\viewServicesTables;
use http\Exception\RuntimeException;

class Views
{
    /**
     * Define Views dir
     */
    const VIEWS_DIR = EXTERNAL_SERVICES_DIR . 'views/';

    /**
     * Also define any Table classes
     */
    const TABLES_DIR = self::VIEWS_DIR . 'Tables/';

    /**
     * Holds the singleton instance of this class
     *
     * @var Views
     */
    static $instance = false;

    /**
     * Singleton
     *
     * @static
     */
    public static function init()
    {
        if ( ! self::$instance ) {
            self::$instance = new Views();
        }

        return self::$instance;
    }

    public function servicesView() {
        $this->validateViews('viewServices.php', new viewServicesTables());


    }

    protected function validateViews($view, $object = '') {
    }
}