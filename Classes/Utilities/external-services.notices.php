<?php

namespace ExternalServices\Classes\Utilities;

class Notices {

	/**
	 * @var string $message
	 */
    private $message;

	/**
     * Constructor
     *
	 * @param $message
	 */
    public function __construct(string $message = "") {
        if (!empty($this->message)) {
            $this->message = $message;
        }
    }
}