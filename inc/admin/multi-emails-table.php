<?php

namespace Multi_Emails_WooCommerce\Admin;


if (!defined('ABSPATH')) {
    exit;
}

/**
 * WP List table of multi emails
 * @since 1.0.0
 */
class Multi_Emails_Table extends \WP_List_Table {
    /**
     * Entry per page
     * @since 1.0.0
     */
    var $per_page = 15;

    /**
     * Constructor.
     * @since 1.0.0
     */
    public function __construct() {
        $this->per_page = $this->get_items_per_page('items_per_page', 15);
        parent::__construct(array('singular' => 'multi_email_woocommerce_table', 'plural' => 'multi_emails_woocommerce_table', 'ajax' => false));
    }

    /**
     * Prepare the items for the table to process
     * @since 1.0.0
     */
    public function prepare_items() {
        global $wpdb;

        $this->_column_headers = array($this->get_columns());

        $page_number = $this->get_pagenum();

        $first_item_no = ($page_number - 1) * $this->per_page;

        $items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->table_multi_emails LIMIT %d, %d", $first_item_no, $this->per_page));
        $this->items = array_map(function ($item) {
            return new Quote($item);
        }, $items);

        $total_items = $wpdb->get_var("SELECT count(*) FROM $wpdb->table_multi_emails");

        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'per_page'    => $this->per_page
        ));
    }

    /**
     * set bulk action for table
     * @since 1.0.0
     */
    public function get_bulk_actions() {
        $actions = [
            'bulk-delete' => __('Delete', 'multi-emails-woocommerce'),
        ];

        return $actions;
    }

    /**
     * Get all available column of table
     * @since 1.0.0
     * @return array
     */
    function get_columns() {
        return [
            'cb' => '<input type="checkbox" />',
            'category' => __('Category', 'multi-emails-woocommerce'),
        ];
    }

    /**
     * Define what data to show on each column of the table
     * @param  String $column_name - Current column name
     * @since 1.0.0
     */
    public function column_default($item, $column_name) {
        //return print_r($item->$column_name, true);
    }

    /**
     * Checkbox column 
     * @since 1.0.0
     */
    function column_cb($item) {
        return sprintf('<input type="checkbox" name="items[]" value="%d" />', $item->id);
    }

    /**
     * Category column 
     * @since 1.0.0
     */
    function column_category($item) {
        
    }

}
