<?php

namespace ExternalServices\Classes;

class Views implements Views_Interface
{

    CONST VIEWS_DIR = EXTERNAL_SERVICES_DIR . 'views/';

    protected $table = '';

    protected $data = '';

    protected $controller = '';

    protected $loader = '';

    public function __construct()
    {
        $this->loader = new Loader();
    }

    public function returnView($view, $class = '')
    {
        $this->validateView($view);

        if ($class != '' && $this->isTableObject($class)) {
            $this->table = $class;
        } elseif ($class != '' && !$this->isTableObject($class)) {
            $this->controller = $class;
        }

        $result = $this->renderView($view);

        echo $result;
    }

    public function validateView($view)
    {
        if (!file_exists(self::VIEWS_DIR . $view . '.phtml')) {
            return new \RuntimeException('View file not found: ' . self::VIEWS_DIR . $view . '. Also make sure all view files have a phtml extension. </br>');
        }
    }

    public function isTableObject($object)
    {
        if (is_subclass_of($object, 'WP_List_Table')) {
            return true;
        } else {
            return false;
        }
    }

    protected function renderView($view) {
        ob_start();

        echo '<div class="wrap">';
        include(self::VIEWS_DIR . $view . '.phtml');
        echo '</div>';

        return ob_get_clean();
    }
}