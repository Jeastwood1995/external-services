<?php
namespace ExternalServices\Classes;

use ExternalServices\Classes\Ajax\Ajax_Connection;
use ExternalServices\Classes\Controllers\abstractController;
use ExternalServices\Classes\Controllers\AddServiceController;
use ExternalServices\Classes\Controllers\Form_Controller;
use ExternalServices\Classes\Tables\Completed_Jobs;
use ExternalServices\Classes\Tables\Services_Table;

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
     * @var AddServiceController
     */
    protected $controller;

    /**
     * ES_init constructor.
     */
    public function __construct()
    {
        # Set up db tables
        //$this->_dbInit();

        # Get required files, only if composer is not installed on the webserver
        //if (!exec('composer -v')) {
            $this->_requireFiles();
        //}

        # Init views and loader classes
        $this->_initClasses();

        //register_activation_hook(EXTERNAL_SERVICES_FILE, array($this, 'activateHook'));

        # Add all menu links, JS and CSS
        $this->_addRegisterActions();

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

    public function add_admin_menu() {
        add_menu_page(
            'External Suppliers',
            'External Suppliers',
            'manage_options',
            'external-services-menu',
            function() {
                $this->views->returnView('viewServices', new Services_Table());
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
                $this->views->returnView('viewServices', new Services_Table());
            }
        );

        add_submenu_page(
            'external-services-menu',
            'Add Services',
            'Add Services',
            'manage_options',
            'external-services-add',
            function() {
                $this->views->returnView('addService');
            }
        );

        add_submenu_page(
            'external-services-menu'
            , 'Completed Jobs',
            'Completed Jobs',
            'manage_options',
            'external-services-completed',
            function () {
                $this->views->returnView('completedJobs', new Completed_Jobs());
            }
        );

        add_submenu_page(
            'external-services-menu',
            'Archived Jobs',
            'Archived Jobs',
            'manage_options',
            'external-services-archived',
            array($this, 'external_services_menu_jobs')
        );
    }

    public static function external_services_load_js() {
        # Re-add jquery to stop none conflict mode
        wp_enqueue_script('jquery');

        # Add custom JS file
        wp_register_script(
            'external-services-js',
             plugins_url('external-services/js/external-services.js'),
            'jquery',
            1.0,
            false
        );

        wp_enqueue_script('external-services-js');

        # Pass ajax post script to use with the test connection function
        wp_localize_script('external-services-js', 'external_services', array('ajax_post' => admin_url( 'admin-ajax.php' )));

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

    protected function _initClasses() {
        $this->views = new Views();
        $this->loader = new Loader();
    }

    protected function _requireFiles() {
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

            if (file_exists($filePath)) {
                require_once $filePath;
                return true;
            }
        });
        /*
        require_once(EXTERNAL_SERVICES_DIR . 'Classes/external-services.views-interface.php');
        require_once(EXTERNAL_SERVICES_DIR . 'Classes/external-services.loader.php');
        require_once(EXTERNAL_SERVICES_DIR . 'Classes/external-services.views.php');
        require_once(EXTERNAL_SERVICES_DIR . 'Classes/Tables/external-services.services-table.php');
        require_once(EXTERNAL_SERVICES_DIR . 'Classes/Controllers/external-services.form-controller.php');
        require_once(EXTERNAL_SERVICES_DIR . 'Classes/Ajax/external-services.ajax-connection.php');
        */
        /*
        if (is_dir(self::EXTERNAL_SERVICES_CLASSES_DIR)) {
            $dir = scandir(self::EXTERNAL_SERVICES_CLASSES_DIR);

            foreach ($dir as $result) {
                    $extension = pathinfo($result)['extension'];

                    if ($extension == 'php') {
                        $file = self::EXTERNAL_SERVICES_CLASSES_DIR . $result;
                        require_once(self::EXTERNAL_SERVICES_CLASSES_DIR . $result);
                        return true;
                    }
            }
        }
        */
    }

    protected static function _dbInit() {
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
    }

    protected function _addRegisterActions()
    {
        $this->loader->add_action('admin_enqueue_scripts', $this, 'external_services_load_js');
        $this->loader->add_action('admin_enqueue_scripts', $this, 'external_services_load_css');
        $this->loader->add_action('admin_menu', $this, 'add_admin_menu');
        $this->loader->add_action('admin_post_form_submit', new Form_Controller(), 'controllerFormSubmit');
        $this->loader->add_action('wp_ajax_test_connection', new Ajax_Connection(), 'getConnection');
        $this->loader->run();
    }
}