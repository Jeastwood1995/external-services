<?php

class ES_init
{
    /**
     * Holds the singleton instance of this class
     *
     * @var ES_init
     */
    static $instance = false;

    /**
     * @var $servicesTable viewServicesTables init
     */
    protected $servicesTable;

    /**
     * @var views initializer
     */
    private $views;

    /**
     * ES_init constructor.
     */
    protected function __construct()
    {
        # Set up db tables
        register_activation_hook(EXTERNAL_SERVICES_FILE, array($this, 'db_init'));

        # Include all other scripts
        $this->includeScripts();

        # Set up menu pages
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );

        # Add Javascript files, function down below
        add_action('admin_enqueue_scripts', array($this, 'external_services_load_js'));

        # Add Custom CSS/SASS files
        add_action('admin_enqueue_scripts', array($this, 'external_services_load_css'));
    }

    /**
     * Singleton
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
        add_menu_page('External Suppliers', 'External Suppliers', 'manage_options', 'external-services-menu', array($this->views, 'servicesView'), 'dashicons-admin-links', 9);
        /*
        add_submenu_page('external-services-menu', 'View Services', 'View Services', 'manage_options', 'external-services-view', array($this, 'external_services_menu_view'));
        add_submenu_page('external-services-menu', 'Add Services', 'Add Services', 'manage_options', 'external-services-add', array($this, 'external_services_menu_add'));
        add_submenu_page('external-services-menu', 'Completed Jobs', 'Completed Jobs', 'manage_options', 'external-services-completed', array($this, 'external_services_menu_jobs'));
        add_submenu_page('external-services-menu', 'Archived Jobs', 'Archived Jobs', 'manage_options', 'external-services-archived', array($this, 'external_services_menu_jobs'));
        */
    }

    public static function db_init() {
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

    }

    private function includeScripts() {
        require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
        require_once(EXTERNAL_SERVICES_DIR . 'Classes/external-services.services-view.php');
        require_once(EXTERNAL_SERVICES_DIR . 'Classes/Tables/external-services.services-table.php');

        $this->initClasses();
    }

    private function initClasses() {
        $this->views = \ExternalServices\Classes\Views::init();
        //$this->servicesTable = \ExternalServices\Classes\Tables\viewServicesTables::init;
    }
}