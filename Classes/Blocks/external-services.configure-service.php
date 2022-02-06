<?php


namespace ExternalServices\Classes\Blocks;

class Configure_Service extends Block_Base {

	public function __construct( array $data = null ) {
		parent::__construct( $data );
		$hi = '';
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
}