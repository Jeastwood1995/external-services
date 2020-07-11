<?php

namespace ExternalServices\Classes;

use ExternalServices\Classes\Tables\viewServicesTables;

class Views implements viewsInterface
{

    CONST VIEWS_DIR = EXTERNAL_SERVICES_DIR . 'views/';

    private static $instance = null;

    public static function init() {
        if (self::$instance == null)
        {
            self::$instance = new Views();
        }

        return self::$instance;
    }

    public function returnView($view, $object = '',$data = '')
    {
        //echo "<pre>";
        //wp_die(var_dump($object));
        //echo "</pre>";
        $this->validateView($view);

        $result = $this->renderView($view, $object, $data);

        echo $result;
    }

    public function validateView($view)
    {
        //$view = self::VIEWS_DIR . $view . '.phtml';

        if (!file_exists(self::VIEWS_DIR . $view . '.phtml')) {
            return new \RuntimeException('View file not found: ' . self::VIEWS_DIR . $view);
        }
    }

    public function isTableObject($object)
    {
        // TODO: Implement isTableObject() method.
    }

    protected function renderView($view, $object, $data) {
        ob_start();
        if ($object != '') {
            extract($data);
        }

        echo '<div class="wrap">';
        include(self::VIEWS_DIR . $view . '.phtml');
        echo '</div>';

        return ob_get_clean();
    }
}