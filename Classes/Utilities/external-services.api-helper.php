<?php

namespace ExternalServices\Classes\Utilities;

class Api_Helper {
	CONST DEFAULT_TIMEOUT = 60;

	/** @var String $url */
	private $url;

	/** @var array $options */
	private $options;

	/** @var array|\WP_Error $response */
	private $response;

	public function __construct(String $url) {
		$this->url = $url;
	}

	public function setOptions(array $options) {
		$this->options = $options;
	}

	public function getResponse(): ?array {
		return $this->response;
	}

	public function getResponseCode(): int {
		return $this->response['response']['code'];
	}

	public function connect()  {
		$this->response = wp_remote_request($this->url, $this->options);
	}

	public function getResponseMessage(): ?string {
		switch ($this->response['response']['code']) {
			case 408:
				return 'Connection timed out.';
			case 404:
				return 'Resource doesn\'t exist on the target server.';
			case 403:
				return 'Forbidden. The resource you\'ve tried to access is protected.';
			case 401:
				return 'Unauthorized. Please check your authorization key.';
			case 400:
				return 'Bad request, please verify the integrity of the data you\'re sending.';
			case 301:
				return 'Redirected. The resource you\'re trying to access has been permanently moved.';
			case 200:
				return null;
			case 0:
				return 'No response from the URL.';
			default:
				return 'There\'s been an unexpected error. Please try again ';
		}
	}
}