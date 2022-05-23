<?php


namespace ExternalServices\Classes\Blocks;

use ExternalServices\Classes\Models\ES_Temp_Model;

class Configure_Service_Block extends Block_Base {

	public function __construct( array $data = null ) {
		$tempModel = new ES_Temp_Model();

		$tempData = $tempModel->get();
		$data = unserialize(base64_decode($tempData[0]->data));
		$tempModel->delete(null);

		parent::__construct($data);
	}

	public function getFirstIndexOfConnectionData(): ?Object {
		if ($this->data != null) {
			$data = $this->data['callback-data'];
			return reset($data);
		}

		return null;
	}

	/**
	 * Capitalise first character
	 *
	 * @param String $key
	 *
	 * @return string
	 */
	public function formatDataKey( String $key ): string {
		return ucwords( $key );
	}

	public function getProductCategoryArray(): array {
		return get_terms('product_cat', array(
			'orderby' => 'name',
			'order' => 'asc',
			'hide_empty' => false,
		));
	}

	public function getProductTagsArray(): array {
		return get_terms('product_tag', array(
			'orderby' => 'name',
			'order' => 'asc',
			'hide_empty' => false,
		));
	}
}