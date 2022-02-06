<?php


namespace ExternalServices\Classes\Blocks;


abstract class Block_Base {

	/**
	 * @var array $data
	 */
	protected $data;

	/**
	 * Block_Base constructor.
	 *
	 * @param array|null $data
	 */
	public function __construct(array $data = null) {
		if ($data) {
			$this->data = $data;
		}
	}

	/**
	 * @return array|null
	 */
	public function getData(): ?array {
		return $this->data;
	}

	/**
	 * @param array $data
	 *
	 * @return void
	 */
	public function setData(array $data) {
		$this->data = $data;
	}
}