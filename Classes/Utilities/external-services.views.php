<?php

namespace ExternalServices\Classes\Utilities;

use ExternalServices\Classes\Blocks\Block_Base;

class Views implements Views_Interface
{
	/** @var string  */
    CONST VIEWS_DIR = EXTERNAL_SERVICES_DIR . 'views/';
	/** @var string  */
    protected $table = '';
	/** @var string  */
    protected $data = '';
	/** @var Block_Base */
    protected $blockClass = null;

	/**
	 * Either echo or return html after calling template name and satisfying conditions
	 *
	 * @param string $view
	 * @param object|null $class
	 * @param false $mainView
	 * @param false $fromAjax
	 *
	 * @return false|string
	 */
    public function returnView(string $view, object $class = null, bool $mainView = false, bool $fromAjax = false)
    {
        $this->validateView($view);

        if ($class != null && $this->isTableObject($class)) {
            $this->table = $class;
        } elseif ($class != null && !$this->isTableObject($class)) {
            $this->blockClass = $class;
        }

        # Store html
        $result = $this->renderView($view, $mainView);

	    if ($fromAjax) {
	    	return $result;
	    } else {
	    	echo $result;
	    }
    }

	/**
	 * Validate whether template files exists in the views directory
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
     *
     * @return bool
     */
    public function isTableObject(object $object): bool {
        return (is_subclass_of($object, 'WP_List_Table'));
    }

    /**
     * @return Block_Base|null
     */
    public function getBlockClass(): ?Block_Base {
        return $this->blockClass;
    }

    /**
     * @param string $view
     * @param bool $main
     *
     * @return false|string
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