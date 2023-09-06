<?php

namespace Multi_Emails_WooCommerce\Admin;

use Multi_Emails_WooCommerce\Vendor;

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

        $items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->table_multi_emails_vendor LIMIT %d, %d", $first_item_no, $this->per_page));
        $this->items = array_map(function ($item) {
            return new Vendor($item);
        }, $items);

        $total_items = $wpdb->get_var("SELECT count(*) FROM $wpdb->table_multi_emails_vendor");

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
            'company_name' => __('Company Name', 'multi-emails-woocommerce'),
            'category' => __('Category', 'multi-emails-woocommerce'),
            'emails' => __('Emails', 'multi-emails-woocommerce'),
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
        return sprintf('<input type="checkbox" name="items[]" value="%d" />', $item->get_id());
    }

    /**
     * Company Name column 
     * @since 1.0.0
     */
    function column_company_name($item) {
        $edit_url = add_query_arg('id', $item->get_id(), menu_page_url('multi-emails-woocommerce', false));

        printf('<strong><a href="%s">%s</a></strong>', $edit_url, esc_html($item->name));


        $delete_url = add_query_arg(array('id' => $item->get_id(), '_nonce' => wp_create_nonce('_nonce_delete_multi_emails_vendor_' . $item->get_id())), menu_page_url('multi-emails-woocommerce', false));

        $row_actions[] = sprintf('<a href="%s" >%s</a>', $edit_url, __('Edit', 'multi-emails-woocommerce'));
        $row_actions[] = sprintf('<a href="%s" class="delete-vendor">%s</a>', $delete_url, __('Delete', 'multi-emails-woocommerce'));

        echo '<div class="row-actions">' . implode(' | ', $row_actions) . '</div>';
    }

    /**
     * Category column 
     * @since 1.0.0
     */
    function column_category($item) {
        $term = get_term($item->category);
        if ($term) {
            echo esc_html($term->name);
        }
    }

    /**
     * Emails column 
     * @since 1.0.0
     */
    function column_emails($item) {
        echo implode(', ', $item->emails);
    }
}
