<?php


namespace ExternalServices\Classes\Blocks;


abstract class Block_Base {

	/**
	 * @var $data
	 */
	protected $data;

	/**
	 * Controller_Base constructor.
	 *
	 * @param $data
	 */
	public function __construct($data = null) {
		if ($data) {
			$this->data = $data;
		}
	}

	/**
	 * @return mixed
	 */
	public function getData() {
		return $this->data;
	}
}