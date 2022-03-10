<?php


namespace ExternalServices\Classes\Blocks;


use ExternalServices\Classes\Models\ES_Temp_Model;
use ExternalServices\Classes\Utilities\Notices;

class Add_Service extends Block_Base {
	public function __construct( array $data = null, bool $delete = false ) {
		parent::__construct( $data );

		if ($delete) {
			$tempModel = new ES_Temp_Model();
			$tempModel->delete(null);
		}
	}

	public function getLoaderHtml(): string {
		return Notices::getLoaderSpinnerHtml('Checking connection...');
	}
}