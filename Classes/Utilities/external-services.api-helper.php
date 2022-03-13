<?php

namespace ExternalServices\Classes\Utilities;

class Api_Helper {
	CONST DEFAULT_TIMEOUT = 60;

	/** @var String $url */
	private $url;

	/** @var array $options */
	private $options;

	public function __construct(String $url) {
		$this->url = $url;
	}

	public function setOptions(array $options) {
		$this->options = $options;
	}

	public function connect() {
		$response = wp_remote_request($this->url, $this->options);

		$hi = '';
	}

	/*
	public function connect() {
		$options = array(
			'method' => 'GET',
			'timeout' => self::DEFAULT_TIMEOUT,
			'headers' => array(
				'Content-type' => 'application/json',
				'Accept' => 'application/json; charset=utf-8',
				'Authorization' => '',
			)
		);
	}
	*/
}