<?php
namespace ExternalServices\Classes;

use ExternalServices\Classes\Ajax\Ajax_Connection;
use ExternalServices\Classes\Models\Configure_Service;
use ExternalServices\Classes\Setup\Db_Setup;
use ExternalServices\Classes\Tables\Archived_Services;
use ExternalServices\Classes\Tables\Completed_Jobs;
use ExternalServices\Classes\Tables\Services_Table;
use ExternalServices\Classes\Utilities\Notices;

class ES_init
{
    /** @var string  */
    CONST EXTERNAL_SERVICES_CLASSES_DIR = EXTERNAL_SERVICES_DIR . 'Classes' . DIRECTORY_SEPARATOR;

    /** @var string */
    CONST EXTERNAL_SERVICES_AUTOLOAD_PREFIX = 'external-services.';

    /**
     * Holds the singleton instance of this class
     *
     * @var ES_init
     */
    private static $instance = false;

    /**
     * @var \ExternalServices\Classes\Views
     */
    protected $views;

    /**
     * @var \ExternalServices\Classes\Loader
     */
    protected $loader;

	/**
	 * @var \ExternalServices\Classes\Ajax\Ajax_Connection
	 */
    protected $ajaxConnector;

    /**
     * @var \ExternalServices\Classes\Setup\Db_Setup;
     */
    protected $dbSetup;

	/**
	 * @var \ExternalServices\Classes\Utilities\Notices;
	 */
	protected $notices;

    /**
     * ES_init constructor.
     */
    public function __construct()
    {
        # Get required files, only if composer is not installed on the webserver
        //if (!exec('composer -v')) {
            $this->_requireFiles();
        //}

        # Init views and loader classes
        $this->_initClasses();

	    # Install up db tables and any upgrade scripts
	    $this->_dbInit();

        //register_activation_hook(EXTERNAL_SERVICES_FILE, array($this, 'activateHook'));

        # Add all menu links, JS and CSS
        $this->_addRegisterActions();

        //$this->_checkForDbUpdates();

        # Include all other scripts
        //add_action('plugins_loaded', array($this, 'requireFiles'));

        # Set up menu pages
        //add_action('admin_menu', array( $this, 'add_admin_menu' ) );

        # Add Javascript files, function down below
        //add_action('admin_enqueue_scripts', array($this, 'external_services_load_js'));

        # Add Custom CSS/SASS files, function down below
        //add_action('admin_enqueue_scripts', array($this, 'external_services_load_css'));

        //add_action('admin_post_addService_submit', array(new AddServiceController(), 'addService_submit'));
    }

    /**
     * Singleton for class
     *
     * @static
     */
    public static function init()
    {
        if ( ! self::$instance ) {
            self::$instance = new ES_init();
        }

        return self::$instance;
    }

    /**
     * Adds admin menu links and routes
     */
    public function add_admin_menu() {
        add_menu_page(
            'External Suppliers',
            'External Suppliers',
            'manage_options',
            'external-services-menu',
            function() {
                $this->views->returnView('viewServices', new Services_Table(), true);
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
            function() {
                $this->views->returnView('viewServices', new Services_Table(), true);
            }
        );

        add_submenu_page(
            'external-services-menu',
            'Add Services',
            'Add Services',
            'manage_options',
            'external-services-add',
            function() {
                $this->views->returnView('addService', null, true);
            }
        );

        add_submenu_page(
            'external-services-menu'
            , 'Completed Jobs',
            'Completed Jobs',
            'manage_options',
            'external-services-completed',
            function () {
                $this->views->returnView('completedJobs', new Completed_Jobs(), true);
            }
        );

        add_submenu_page(
            'external-services-menu',
            'Archived Jobs',
            'Archived Jobs',
            'manage_options',
            'external-services-archived',
            function () {
            	$this->views->returnView('archivedJobs', new Archived_Services(), true);
            }
        );
    }

    /**
     * Loads custom JS files + external JS libraries
     */
    public static function external_services_load_js() {
        # Re-add jquery to stop none conflict mode
        wp_enqueue_script('jquery');

        # Add root JS file
        wp_register_script(
            'external-services',
             plugins_url('external-services/js/external-services.js'),
            'jquery',
            1.0,
            false
        );

        wp_enqueue_script('external-services');

        # Pass ajax post script to use with the test connection function
        wp_localize_script('external-services', 'external_services', array('ajax_post' => admin_url( 'admin-ajax.php' )));

        # Add service connect script
        wp_register_script(
            'external-services-form-validation',
            plugins_url('external-services/js/external-services-form-validation.js'),
            array(
                'jquery',
                'external-services'
            ),
            1.0,
            false
        );

        wp_enqueue_script('external-services-form-validation');

        # Add service connect script
        wp_register_script(
            'external-services-service-connect',
            plugins_url('external-services/js/external-services-service-connect.js'),
            array(
                'jquery',
                'external-services'
            ),
            1.0,
            false
        );

        wp_enqueue_script('external-services-service-connect');

        # Add jQuery validation cdn
        wp_register_script(
            'jQ-validation',
            'https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.min.js',
            'jquery'
        );

        wp_enqueue_script('jQ-validation');

        /*
        # Add full jquery-ui CDN
        wp_register_script('jquery-ui-full', 'https://code.jquery.com/ui/1.12.1/jquery-ui.js', 'jquery');
        wp_enqueue_script('jquery-ui-full');

        # Add jquery form validation
        wp_register_script('jquery-form', 'http://malsup.github.io/min/jquery.form.min.js', 'jquery');
        wp_enqueue_script('jquery-form');
        */
    }

    /**
     * Add main css file, all other css scripts are initialized in here
     */
    public static function external_services_load_css() {
        # Add custom css script
        wp_register_style(
            'external-services-css',
            plugins_url('external-services/css/external-services.css'),
            array(),
            1.0
        );

        wp_enqueue_style('external-services-css');

    }

    /**
     * Initialize classes that are needed to call
     */
    private function _initClasses() {
        $this->views = new Views();
        $this->loader = new Loader();
        $this->ajaxConnector = new Ajax_Connection();
        $this->dbSetup = new Db_Setup();
    }

    /**
     * Custom class autoloader that uses my custom namespace convention
     */
    private function _requireFiles() {
        # Have to call the source table class manually
        require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');

        spl_autoload_register(function ($class) {
            # Explode the namespace, then unset the first two parameters, since we just want the
            # class name
            $structure = explode('\\', $class);

            unset($structure[0], $structure[1]);

            # Set the prefix of 'external-services.' as the start of the file name
            $file = self::EXTERNAL_SERVICES_AUTOLOAD_PREFIX;

            # If the class is in another directory, then we need to get the last index,
            # otherwise just get the first
            if (count($structure) > 1) {
                # Set the file name from the last index, then unset it as we don't need it anymore
                $fileName = end($structure);
                $keys = array_keys($structure);
                $last = end($keys);
                unset($structure[$last]);

                # Loop through any additional directories the class resides in, then store the path
                $extraDir = '';

                foreach ($structure as $dir) {
                    $extraDir .= $dir . DIRECTORY_SEPARATOR;
                }
            } else {
                # Just get the first index
                $fileName = reset($structure);
            }

            # Set name of the file name to {class-name}.php
            $file .= strtolower(str_replace('_', '-', $fileName) . '.php');

            # Set start to 'Classes/'
            $filePath = self::EXTERNAL_SERVICES_CLASSES_DIR;

            # Then either append the file name or sub directories + filename
            $filePath .= (isset($extraDir)) ? $extraDir . $file : $file;

            # If there is actually a class with the correct convention and structure, then use it!
            if (file_exists($filePath)) {
                require_once $filePath;
                return true;
            }
        });
    }

	/**
	 * Try to install the database, otherwise show admin error notice
	 */
    private function _dbInit() {
    	if (!$this->dbSetup->checkForInstall()) {
    		try {
    			$this->dbSetup->install();
		    } catch (\Exception $e) {
    			$this->loader->add_action('admin_notices', new Notices($e->getMessage()), 'dbInstallError');
            }
	    }

    	/*
        global $wpdb;

        if (!$wpdb->query("DESCRIBE {$wpdb->prefix}external_services")) {
            $createtable = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}external_services("
                . "id INT(6) AUTO_INCREMENT PRIMARY KEY,"
                . "service_name VARCHAR(250) NOT NULL,"
                . "service_url VARCHAR (250) NOT NULL,"
                . "authorization_key VARCHAR(250) NOT NULL,"
                . "cron_run VARCHAR(250) NOT NULL,"
                . "date_created DATETIME DEFAULT CURRENT_TIMESTAMP,"
                . "date_modified DATETIME ON UPDATE CURRENT_TIMESTAMP"
                . ")";

            $wpdb->query($createtable);
        }

        if (!$wpdb->query("DESCRIBE {$wpdb->prefix}external_services_log")) {

            $createtable = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}external_services_log("
                . "log_id INT(6) AUTO_INCREMENT PRIMARY KEY,"
                . "service_id INT(6) NOT NULL,"
                . "service_name VARCHAR(255) NOT NULL,"
                . "post_id BIGINT(20) UNSIGNED NOT NULL,"
                . "status VARCHAR(255) NOT NULL,"
                . "date_created DATETIME DEFAULT CURRENT_TIMESTAMP,"
                . "product_modified_date DATETIME,"
                . "previous_data TEXT,"
                . "new_data TEXT,"
                . "archived INT DEFAULT 0,"
                . "FOREIGN KEY(service_id) REFERENCES wp_external_services(id),"
                . "FOREIGN KEY(post_id) REFERENCES wp_posts(ID)"
                . ")";

            $wpdb->query($createtable);
        }
    	*/
    }

	/**
	 * Add predefined actions and filters (using wordpress, have to add custom ones immediately and only access within the class you pass in)
	 */
    private function _addRegisterActions()
    {
        $this->loader->add_action('admin_enqueue_scripts', $this, 'external_services_load_js');
        $this->loader->add_action('admin_enqueue_scripts', $this, 'external_services_load_css');
        $this->loader->add_action('admin_menu', $this, 'add_admin_menu');
        $this->loader->add_action('wp_ajax_test_connection', $this->ajaxConnector, 'getConnection');
        $this->loader->add_action('wp_ajax_call_view', $this->ajaxConnector, 'callView');
        $this->loader->add_action('wp_ajax_download_data_file', new Configure_Service(), 'downloadDataFile');
        # TODO: create custom file upload class and functionality
        //$this->loader->add_filter('upload_dir', null, 'uploadDataFile');
        $this->loader->run();
    }

    private function _checkForDbUpdates() {
        if( ! function_exists('get_plugin_data') ){
            require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        }

        $plugin_data = get_plugin_data( EXTERNAL_SERVICES_FILE, false );
        $hi = 'hi';
    }
}