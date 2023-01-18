<?php

namespace ExternalServices\Classes;

use ExternalServices\Classes\Blocks\Configure_Service_Block;
use ExternalServices\Classes\Blocks\Add_Service_Block;
use ExternalServices\Classes\Setup\Db_Setup;
use ExternalServices\Classes\Tables\Archived_Services;
use ExternalServices\Classes\Tables\Completed_Jobs;
use ExternalServices\Classes\Tables\Services_Table;
use ExternalServices\Classes\Utilities\Ajax_Controller;
use ExternalServices\Classes\Utilities\Form_Controller;
use ExternalServices\Classes\Utilities\Helper;
use ExternalServices\Classes\Utilities\View_Controller;

class ES_init {
	/** @var string */
	const EXTERNAL_SERVICES_CLASSES_DIR = EXTERNAL_SERVICES_DIR . 'Classes' . DIRECTORY_SEPARATOR;

	/** @var string */
	const EXTERNAL_SERVICES_AUTOLOAD_PREFIX = 'external-services.';

	/**
	 * Holds the singleton instance of this class
	 *
	 * @var ES_init
	 */
	private static $instance = false;

	/**
	 * @var \ExternalServices\Classes\Utilities\View_Controller
	 */
	protected $views;

	/**
	 * @var \ExternalServices\Classes\Utilities\Ajax_Controller
	 */
	protected $ajaxController;

	/**
	 * @var \ExternalServices\Classes\Setup\Db_Setup;
	 */
	protected $dbSetup;

	/**
	 * @var \ExternalServices\Classes\Utilities\Helper;
	 */
	protected $helper;

	/**
	 * @var \ExternalServices\Classes\Utilities\Form_Controller
	 */
	protected $formController;

	/**
	 * ES_init constructor.
	 */
	public function __construct() {
		# Get required files, only if composer is not installed on the webserver
		//if (!exec('composer -v')) {
		$this->_requireFiles();
		//}

		# Init views and loader classes
		$this->_initClasses();

		# Install up db tables and any upgrade scripts
		register_activation_hook( EXTERNAL_SERVICES_FILE, array( $this->dbSetup, 'install' ) );

		# Add all menu links, JS and CSS
		$this->_addRegisterActions();

		//$this->_checkForDbUpdates();

		# Include all other scripts
		//add_action('plugins_loaded', array($this, 'requireFiles'));

	}

	/**
	 * Singleton for class
	 *
	 * @static
	 */
	public static function init() {
		if ( ! self::$instance ) {
			self::$instance = new ES_init();
		}

		return self::$instance;
	}

	/**
	 * Initialize classes that are needed to call
	 */
	private function _initClasses() {
		$this->views          = new View_Controller();
		$this->ajaxController = new Ajax_Controller;
		$this->dbSetup        = new Db_Setup();
		$this->helper         = new Helper();
		$this->formController = new Form_Controller();
	}

	/**
	 * Custom class autoloader that uses my custom namespace convention
	 */
	private function _requireFiles() {
		# Have to call the source table and db upgrade class manually
		require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		spl_autoload_register( function ( $class ) {
			# Explode the namespace, then unset the first two parameters, since we just want the
			# class name
			$structure = explode( '\\', $class );

			unset( $structure[0], $structure[1] );

			# Set the prefix of 'external-services.' as the start of the file name
			$file = self::EXTERNAL_SERVICES_AUTOLOAD_PREFIX;

			# If the class is in another directory, then we need to get the last index,
			# otherwise just get the first
			if ( count( $structure ) > 1 ) {
				# Set the file name from the last index, then unset it as we don't need it anymore
				$fileName = end( $structure );
				$keys     = array_keys( $structure );
				$last     = end( $keys );
				unset( $structure[ $last ] );

				# Loop through any additional directories the class resides in, then store the path
				$extraDir = '';

				foreach ( $structure as $dir ) {
					$extraDir .= $dir . DIRECTORY_SEPARATOR;
				}
			} else {
				# Just get the first index
				$fileName = reset( $structure );
			}

			# Set name of the file name to {class-name}.php
			$file .= strtolower( str_replace( '_', '-', $fileName ) . '.php' );

			# Set start to 'Classes/'
			$filePath = self::EXTERNAL_SERVICES_CLASSES_DIR;

			# Then either append the file name or subdirectories + filename
			$filePath .= ( isset( $extraDir ) ) ? $extraDir . $file : $file;

			# If there is actually a class with the correct convention and structure, then use it!
			if ( file_exists( $filePath ) ) {
				require_once $filePath;

				return true;
			}
		} );
	}


	/**
	 * Add predefined actions and filters (using WordPress, have to add custom ones immediately and only access within the class you pass in)
	 */
	private function _addRegisterActions() {
		add_action( 'admin_enqueue_scripts', array( $this, 'external_services_load_js' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'external_services_load_css' ) );
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'wp_ajax_call_view', array( $this->ajaxController, 'callView' ) );
		add_action( 'wp_ajax_check_form_nonce', array( $this->ajaxController, 'checkFormNonce' ) );
		add_action( 'wp_ajax_delete_data', array( $this->dbSetup, 'uninstall' ) );
		add_action('admin_post_add_service_submit', array($this->formController, 'processAddServicePostData'));
		add_action( 'submenu_file', array( $this, 'hideConfigureServiceMenuItem' ) );
	}

	/**
	 * Adds admin menu links and routes
	 */
	public function add_admin_menu() {
		// Visible items
		add_menu_page(
			'External Services',
			'External Services',
			'manage_options',
			'external-services-menu',
			function () {
				$this->views->returnView( 'viewServices', new Services_Table(), true );
			},
			'dashicons-admin-links',
			9
		);

		add_submenu_page(
			'external-services-menu',
			'View Services',
			'View Services',
			'manage_options',
			'external-services-view',
			function () {
				$this->views->returnView( 'viewServices', new Services_Table(), true );
			}
		);

		add_submenu_page(
			'external-services-menu',
			'Add Services',
			'Add Services',
			'manage_options',
			'external-services-add',
			function () {
				$this->views->returnView( 'addService', new Add_Service_Block(), true );
			}
		);

		add_submenu_page(
			'external-services-menu'
			, 'Completed Jobs',
			'Completed Jobs',
			'manage_options',
			'external-services-completed',
			function () {
				$this->views->returnView( 'completedServices', new Completed_Jobs(), true );
			}
		);

		add_submenu_page(
			'external-services-menu',
			'Archived Jobs',
			'Archived Jobs',
			'manage_options',
			'external-services-archived',
			function () {
				$this->views->returnView( 'archivedServices', new Archived_Services(), true );
			}
		);

		// Not visible items
		add_submenu_page(
			'external-services-menu',
			'Configure Service',
			'Configure Service',
			'manage_options',
			'external-services-configure',
			function () {
				$this->views->returnView( 'configureService', new Configure_Service_Block(), true );
			}
		);
	}

	/**
	 * Remove the configure service link from the submenu, should only be accessed after
	 */
	public static function hideConfigureServiceMenuItem() {
		remove_submenu_page( 'external-services-menu', 'external-services-configure' );
	}

	/**
	 * Loads custom JS files + external JS libraries
	 */
	public static function external_services_load_js() {
		# Re-add jquery to stop none conflict mode
		wp_enqueue_script( 'jquery' );

		# re-add jquery ui dialog js (for deactivation modal)
		wp_enqueue_script( 'jquery-ui-dialog' );

		# Add root JS file
		wp_register_script(
			'external-services',
			plugins_url( 'external-services/js/external-services.js' ),
			'jquery',
			1.0,
			false
		);

		wp_enqueue_script( 'external-services' );

		# Add service connect script
		wp_register_script(
			'external-services-form-validation',
			plugins_url( 'external-services/js/external-services-form-validation.js' ),
			array(
				'jquery',
				'external-services'
			),
			1.0,
			false
		);

		wp_enqueue_script( 'external-services-form-validation' );

		# Add service connect script
		wp_register_script(
			'external-services-service-connect',
			plugins_url( 'external-services/js/external-services-service-connect.js' ),
			array(
				'jquery',
				'external-services'
			),
			1.0,
			false
		);

		wp_enqueue_script( 'external-services-service-connect' );

		# Add helper class
		wp_register_script(
			'external-services-helper',
			plugins_url( 'external-services/js/external-services-helper.js' ),
			array(
				'jquery',
				'external-services'
			),
			1.0,
			false
		);

		wp_enqueue_script( 'external-services-helper' );

		# Pass wp admin url to use when making ajax calls
		wp_localize_script( 'external-services-service-connect', 'serviceConnect', array( 'wp_ajax_url' => admin_url( 'admin-ajax.php' ) ) );

		# Add jQuery validation cdn
		wp_register_script(
			'jQ-validation',
			'https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js',
			'jquery'
		);

		wp_enqueue_script( 'jQ-validation' );
	}

	/**
	 * Add main css file, all other css scripts are initialized in here
	 */
	public static function external_services_load_css() {
		# Add default jquery ui dialog css (needed for deactivate modal)
		wp_enqueue_style( 'wp-jquery-ui-dialog' );

		#  Add custom css script
		wp_register_style(
			'external-services',
			plugins_url( 'external-services/css/external-services.css' ),
			array(),
			1.0
		);

		wp_enqueue_style( 'external-services' );
	}
}