<?php

namespace ExternalServices\Classes;

use ExternalServices\Classes\Models\Model_Base;

class Views implements Views_Interface
{
	/** @var string  */
    CONST VIEWS_DIR = EXTERNAL_SERVICES_DIR . 'views/';
	/** @var string  */
    protected $table = '';
	/** @var string  */
    protected $data = '';
	/** @var Model_Base */
    protected $model = null;
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
	 * @param string $view
	 * @param object|null $class
	 * @param false $main
	 * @param false $ajax
	 *
	 * @return false|string
	 */
    public function returnView(string $view, object $class = null, bool $main = false, bool $ajax = false)
    {
        $this->validateView($view);

        if ($class != null && $this->isTableObject($class)) {
            $this->table = $class;
        } elseif ($class != null && !$this->isTableObject($class)) {
            $this->model = $class;
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
	 * @param string $view
	 * @throws \RuntimeException
	 */
    public function validateView(string $view)
    {
        if (!file_exists(self::VIEWS_DIR . $view . '.phtml')) {
            throw new \RuntimeException('View file not found: ' . self::VIEWS_DIR . $view . '. Also make sure all view files have a phtml extension. </br>');
        }
    }

    /**
     * @param object $object
     * @return bool|mixed
     */
    public function isTableObject(object $object)
    {
        return (is_subclass_of($object, 'WP_List_Table'));
    }

    /**
     * @return Model_Base|null
     */
    public function getModel() {
        return $this->model;
    }

    /**
     * @param string $view
     * @param bool $main
     * @return false|string|void
     */
    protected function renderView(string $view, bool $main)
    {
        ob_start();

        echo ($main) ? '<div class="wrap">' : '';
        include(self::VIEWS_DIR . $view . '.phtml');
        echo ($main) ? '</div>' : '';

        return ob_get_clean();
    }
}