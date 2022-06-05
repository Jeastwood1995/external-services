<?php


namespace ExternalServices\Classes\Blocks;


use ExternalServices\Classes\Models\ES_Temp_Model;
use ExternalServices\Classes\Utilities\Notices;

class Add_Service_Block extends Block_Base {

	public function __construct( array $data = null ) {
		$tempModel = new ES_Temp_Model();
		$tempModel->delete( null );


		parent::__construct( $data );
	}

	public function getAuthorizationSettingsDisplayStatus(): string {
		return 'none';
	}

	public function getCsvSettingsDisplayStatus(): string {
		return 'none';
	}

	public function getPageKey(): String {
		return 'add_service';
	}

	public function getLoaderHtml(): string {
		return Notices::getLoaderSpinnerHtml( 'Checking connection...' );
	}
}