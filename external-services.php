<?php
/**
 * Plugin Name: External Services
 * Plugin URI: Custom
 * Description: Plugin to link external service API keys to your woocommerce site
 * Author: Jeastwood1995
 * Author URI: https://woocommerce.com/
 * Text Domain: external-services
 * License: GPLv2 or later
 * Version: 1.0.0
 *
 * @package External_Services
 */

# deny direct access to file
defined( 'ABSPATH' ) || die("Sorry this isn't for unseen eyes");

# define plugin root directory
define('EXTERNAL_SERVICES_DIR', plugin_dir_path(__FILE__));

# Define plugin file
define('EXTERNAL_SERVICES_FILE', __FILE__);

# Define plugin url
define('EXTERNAL_SERVICES_FILE_URL', plugin_dir_url(__FILE__));

# Define version constant
define('EXTERNAL_SERVICES_VERSION', get_file_data(EXTERNAL_SERVICES_FILE, array('Version' => 'Version'), false)['Version']);

# Define CSS dir url
define('EXTERNAL_SERVICES_CSS', EXTERNAL_SERVICES_FILE_URL . 'css/');

# Define JS dir url
define('EXTERNAL_SERVICES_JS', EXTERNAL_SERVICES_FILE_URL . 'js/');

# Require the initialization class
require_once(EXTERNAL_SERVICES_DIR . 'Classes/external-services.init.php');

# Let's get ready to rumble
\ExternalServices\Classes\ES_init::init();