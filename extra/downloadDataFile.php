<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');

//if (!empty($_GET['token']) && wp_verify_nonce($_GET['token'])) {
	_includePluginClasses();
	_fetchAndProcessTempData();
//} else {
//	die('Unauthorized');
//}

function _includePluginClasses() {
	$pluginPath = plugin_dir_path( EXTERNAL_SERVICES_FILE );

	require_once($pluginPath . 'Classes/Setup/external-services.db-setup.php');
	require_once( $pluginPath . 'Classes/Models/external-services.es-temp-model.php' );
}

function _fetchAndProcessTempData() {
	$tempModel = new \ExternalServices\Classes\Models\ES_Temp_Model();

	$result = $tempModel->get()[0];
	$data = unserialize(base64_decode($result->data));

	$hi = '';
}