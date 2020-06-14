<?php

namespace ExternalServices\Classes\Tables; 

use ExternalServices\Classes\Views;

class viewServicesTables extends \WP_List_Table
{


    public function prepare_items() {
        global $wpdb;

        $orderBy = isset($_GET['orderby']) ? trim($_GET['orderby']) : "";
        $order = isset($_GET['order']) ? trim($_GET['order']) : "";

        $search_term = isset($_POST['s']) ? sanitize_text_field($_POST['s']) : "";

        if ($orderBy != "" && $order != "") {
            $this->items = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}external_services ORDER BY $orderBy $order", ARRAY_A);
        } else {
            $this->items = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}external_services", ARRAY_A);
        }

        if ($search_term != "") {
            $this->items = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}external_services WHERE service_name LIKE '%$search_term%'", ARRAY_A);
        }

        $columns = $this->get_columns();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, array(), $sortable);
    }

    public function get_sortable_columns() {

        return array(
            "service_name" => array("service_name", true),
            "id" => array("id", true),
            "date_created" => array("date_created", true),
        );
    }

    public function get_columns() {

        return array(
            "cb" => "<input type='checkbox' />",
            "service_name" => "Service Name",
            "id" => "ID",
            "service_url" => "URL",
            "authorization_key" => "Key",
            "cron_run" => "Called Every",
            "date_created" => "Creation Date",
            "action" => "Action"
        );
    }

    public function column_cb($item) {
        return sprintf("<input type='checkbox' name='post[]' value='%s'/>", $item['id']);
    }

    public function column_default($item, $column_name) {

        switch ($column_name) {
            case 'service_name':
            case 'id':
            case 'service_url':
            case 'authorization_key':
            case 'cron_run':
            case 'date_created':
                return $item[$column_name];
            case 'action':
                return '<a href="?page=' . $_GET['page'] . '&action=editService&id=' . $item['id'] . '">Edit</a> | <a class="deleteService" style="color:red;" id="' . $item['id'] . '" href="">Delete</a>';
            default:
                return "No Value";
        }
    }

    /**
     * Generates content for a single row of the table
     *
     * @since 3.1.0
     *
     * @param object $item The current item
     */
    public function single_row($item) {
        echo '<tr>';
        $this->single_row_columns($item);
        echo '</tr>';
    }

    /**
     * Displays the search box.
     *
     * @since 3.1.0
     *
     * @param string $text     The 'submit' button label.
     * @param string $input_id ID attribute value for the search input field.
     */
    public function search_box($text, $input_id) {
        if (empty($_REQUEST['s']) && !$this->has_items()) {
            return;
        }

        $input_id = $input_id . '-search-input';

        if (!empty($_REQUEST['orderby'])) {
            echo '<input type="hidden" name="orderby" value="' . esc_attr($_REQUEST['orderby']) . '" />';
        }
        if (!empty($_REQUEST['order'])) {
            echo '<input type="hidden" name="order" value="' . esc_attr($_REQUEST['order']) . '" />';
        }
        if (!empty($_REQUEST['post_mime_type'])) {
            echo '<input type="hidden" name="post_mime_type" value="' . esc_attr($_REQUEST['post_mime_type']) . '" />';
        }
        if (!empty($_REQUEST['detached'])) {
            echo '<input type="hidden" name="detached" value="' . esc_attr($_REQUEST['detached']) . '" />';
        }
        ?>
        <form method="post" name="services_search" action="<?php echo $_SERVER['PHP_SELF'] ?>?page=view-services" >
            <p class="search-box">
                <label class="screen-reader-text" for="<?php echo esc_attr($input_id); ?>"><?php echo $text; ?>:</label>
                <input type="search" id="<?php echo esc_attr($input_id); ?>" placeholder="Service Name..." name="s" value="<?php _admin_search_query(); ?>" />
                <?php submit_button($text, '', '', false, array('id' => 'search-submit')); ?>
            </p>
        </form>
        <?php
    }

    /**
     * Get an associative array ( option_name => option_title ) with the list
     * of bulk actions available on this table.
     *
     * @since 3.1.0
     *
     * @return array
     */
    protected function get_bulk_actions() {
        $actions = array(
            "edit" => "Edit",
            "delete" => "Delete"
        );

        return $actions;
    }

    /**
     * Generates the columns for a single row of the table
     *
     * @since 3.1.0
     *
     * @param object $item The current item
     */
    protected function single_row_columns($item) {

        list( $columns, $hidden, $sortable, $primary ) = $this->get_column_info();

        foreach ($columns as $column_name => $column_display_name) {

            $classes = "$column_name column-$column_name";
            if ($primary === $column_name) {
                $classes .= ' has-row-actions column-primary';
            }

            if (in_array($column_name, $hidden)) {
                $classes .= ' hidden';
            }

            // Comments column uses HTML in the display name with screen reader text.
            // Instead of using esc_attr(), we strip tags to get closer to a user-friendly string.
            $data = 'data-colname="' . wp_strip_all_tags($column_display_name) . '"';

            $attributes = "class='$classes' $data";

            if ('cb' === $column_name) {
                echo '<th scope="row" class="check-column">';
                echo $this->column_cb($item);
                echo '</th>';
            } elseif (method_exists($this, '_column_' . $column_name)) {
                echo call_user_func(
                    array($this, '_column_' . $column_name),
                    $item,
                    $classes,
                    $data,
                    $primary
                );
            } elseif (method_exists($this, 'column_' . $column_name)) {
                echo "<td $attributes>";
                echo call_user_func(array($this, 'column_' . $column_name), $item);
                echo $this->handle_row_actions($item, $column_name, $primary);
                echo '</td>';
            } else {
                echo "<td $attributes>";
                echo $this->column_default($item, $column_name);
                echo $this->handle_row_actions($item, $column_name, $primary);
                echo '</td>';
            }
        }
    }
}