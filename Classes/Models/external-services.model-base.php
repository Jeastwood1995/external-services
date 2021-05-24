<?php


namespace ExternalServices\Classes\Models;


abstract class Model_Base {

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