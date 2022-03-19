<?php

namespace ExternalServices\Classes\Utilities;

class Api_Helper {
	CONST DEFAULT_TIMEOUT = 60;

	/** @var String $url */
	private $url;

	/** @var array $options */
	private $options;

	/** @var array $response */
	private $response;

	public function __construct(String $url) {
		$this->url = $url;
	}

	public function setOptions(array $options) {
		$this->options = $options;
	}

	public function getResponse(): array {
		return $this->response;
	}

	public function connect()  {
		$this->response = wp_remote_request($this->url, $this->options);
	}
}