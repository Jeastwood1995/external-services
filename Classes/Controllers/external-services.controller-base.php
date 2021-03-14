<?php


namespace ExternalServices\Classes\Controllers;


abstract class Controller_Base {

	/**
	 * @var $data
	 */
	protected $data;

	/**
	 * Controller_Base constructor.
	 *
	 * @param $data
	 */
	public function __construct($data) {
		$this->data = $data;
	}

	/**
	 * @return mixed
	 */
	public function getData() {
		return $this->data;
	}
}