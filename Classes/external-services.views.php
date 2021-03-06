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

    public function returnView($view, $class = '', $main = false, $ajax = false)
    {
        $this->validateView($view);

        if ($class != '' && $this->isTableObject($class)) {
            $this->table = $class;
        } elseif ($class != '' && !$this->isTableObject($class)) {
            $this->controller = $class;
        }

        $result = $this->renderView($view, $main);

	    if ($ajax) {
	    	return $result;
	    } else {
	    	echo $result;
	    }
    }

    public function validateView($view)
    {
        if (!file_exists(self::VIEWS_DIR . $view . '.phtml')) {
            return new \RuntimeException('View file not found: ' . self::VIEWS_DIR . $view . '. Also make sure all view files have a phtml extension. </br>');
        }
    }

    public function isTableObject($object)
    {
        return (is_subclass_of($object, 'WP_List_Table'));
    }

    public function getController() {
    	return $this->controller;
    }

    protected function renderView($view, $main) {
        ob_start();

	    echo ($main) ? '<div class="wrap">' : '';
        include(self::VIEWS_DIR . $view . '.phtml');
        echo ($main) ? '</div>' : '';

        return ob_get_clean();
    }
}