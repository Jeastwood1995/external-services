<?php


namespace ExternalServices\Classes\Controllers;


abstract class Controller_Base {

	protected $data;

	public function __construct($data) {
		$this->data = $data;
	}

	public function getData() {
		return $this->data;
	}
}