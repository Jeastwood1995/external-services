<?php

namespace ExternalServices\Classes;

class Views implements Views_Interface
{
	/** @var string  */
    CONST VIEWS_DIR = EXTERNAL_SERVICES_DIR . 'views/';
	/** @var string  */
    protected $table = '';
	/** @var string  */
    protected $data = '';
	/** @var null  */
    protected $controller = null;
	/** @var Loader|string  */
    protected $loader = '';

	/**
	 * Views constructor.
	 */
    public function __construct()
    {
        $this->loader = new Loader();
    }

	/**
	 * Either echo or return html after calling template name and satisfying conditions
	 *
	 * @param $view
	 * @param null $class
	 * @param false $main
	 * @param false $ajax
	 *
	 * @return false|string
	 */
    public function returnView($view, $class = null, $main = false, $ajax = false)
    {
        $this->validateView($view);

        if ($class != null && $this->isTableObject($class)) {
            $this->table = $class;
        } elseif ($class != null && !$this->isTableObject($class)) {
            $this->controller = $class;
        }

        # Store html
        $result = $this->renderView($view, $main);

	    if ($ajax) {
	    	return $result;
	    } else {
	    	echo $result;
	    }
    }

	/**
	 * Vaidate whether template files exists in the views directory
	 *
	 * @param $view
	 *
	 * @return \RuntimeException
	 */
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

	/**
	 * Render html in template file
	 *
	 * @param $view
	 * @param $main
	 *
	 * @return false|string
	 */
    protected function renderView($view, $main) {
        ob_start();

	    echo ($main) ? '<div class="wrap">' : '';
        include(self::VIEWS_DIR . $view . '.phtml');
        echo ($main) ? '</div>' : '';

        return ob_get_clean();
    }
}