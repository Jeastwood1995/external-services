<?php


namespace ExternalServices\Classes\Tables;


class Archived_Services extends \WP_List_Table {
	public function csvTest() {
		$handle = fopen(ABSPATH . 'products_20210707_100914.csv', 'r');
		$csv = fgetcsv($handle);

		$hi = 'hi';
	}
}