<?php

namespace Multi_Emails_WooCommerce\Admin;

use Multi_Emails_WooCommerce\Vendor;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Admin class
 */
final class Admin {

    /**
     * Hold error object
     * @var WP_Error
     */
    var $error = null;

    public function __construct() {
        $this->error = new \WP_Error();

        add_action('init', array($this, 'handle_submission_form'));
        add_action('admin_menu', array($this, 'admin_menu'), 200);
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    /**
     * Handle submission form
     * @since 1.0.0
     */
    public function handle_submission_form() {
        if (!isset($_POST['_wpnonce'])) {
            return;
        }

        if (!wp_verify_nonce($_POST['_wpnonce'], '_nonce_multi_emails_woocommerce_settings')) {
            return;
        }

        $post_data = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);

        $email_recipients = [];
        if (isset($post_data['email-recipients']) && is_array($post_data['email-recipients'])) {
            $email_recipients = $post_data['email-recipients'];
        }

        update_option('multi-emails-woocommerce-recipients', $email_recipients);

        $customer_emails = [];
        if (isset($post_data['customer-emails']) && is_array($post_data['customer-emails'])) {
            $customer_emails = $post_data['customer-emails'];
        }

        update_option('multi-emails-woocommerce-customer-emails', $customer_emails);
    }

    /**
     * Admin menu
     * @since 1.0.0
     */
    public function admin_menu() {
        add_submenu_page('woocommerce', __('Multi-Emails', 'multi-emails-woocommerce'), __('Multi-Emails', 'multi-emails-woocommerce'), 'manage_woocommerce', 'multi-emails-woocommerce', array($this, 'output'), 200);
    }

    /**
     * Enqueue scripts
     * @since 1.0.0
     */
    public function enqueue_scripts() {
        preg_match('/multi-emails-woocommerce/', get_current_screen()->id, $matches);
        if (sizeof($matches) == 0) {
            return;
        };

        wp_register_style('select2', MULTI_EMAILS_WOOCOMMERCE_URL . '/assets/css/select2.css', [], '4.0.3');
        wp_register_script('select2', MULTI_EMAILS_WOOCOMMERCE_URL . 'assets/js/select2.js', array('jquery'), '4.0.3', true);

        wp_enqueue_style('multi-emails-woocommerce', MULTI_EMAILS_WOOCOMMERCE_URL . '/assets/css/admin.css', ['select2']);
        wp_enqueue_script('multi-emails-woocommerce', MULTI_EMAILS_WOOCOMMERCE_URL . '/assets/js/admin.js', ['jquery', 'select2', 'wp-util'], MULTI_EMAILS_WOOCOMMERCE_VERSION, true);
        wp_localize_script('multi-emails-woocommerce', 'multi_emails_woocommerce', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'i10n' => array(
                'customer_field_title' => __('Field title', 'multi-emails-woocommerce'),
                'customer_remove_email_notice' => __('Do you want to remove this field?', 'multi-emails-woocommerce'),
                'search_category' => __('Search category', 'multi-emails-woocommerce'),
                'search_product' => __('Search product', 'multi-emails-woocommerce'),
                'vendor_delete_confirm' => __('Do you want to delete this item?', 'multi-emails-woocommerce')
            )
        ));
    }

    /**
     * Get woocommerce categories
     * @since 1.0.0
     * @return string
     */
    public function get_categories($selected_categories = []) {
        $product_categories = get_terms(array(
            'taxonomy' => 'product_cat',
            'hide_empty' => false,
        ));

        $html = array_map(function ($term) use ($selected_categories) {
            $option_selected = in_array($term->term_id, $selected_categories);
            return sprintf('<option value="%d" %s>%s</option>', $term->term_id, selected(true, $option_selected, false), esc_html($term->name));
        }, $product_categories);

        return implode("\n", $html);
    }

    /**
     * Get woocommerce products
     * @since 1.0.0
     * @return string
     */
    public function get_products($selected_products = []) {
        $products = wc_get_products(array(
            'limit' => -1,
        ));

        $html = array_map(function ($product) use ($selected_products) {
            $option_selected = in_array($product->get_id(), $selected_products);
            return sprintf('<option value="%d" %s>%s</option>', $product->get_id(), selected(true, $option_selected, false), esc_html($product->name));
        }, $products);

        return implode("\n", $html);
    }

    /**
     * Output settings
     * @since 1.0.0
     */
    public function output() {
        echo '<div class="wrap multi-emails-woocommerce-wrap">';
        echo '<h1 class="wp-heading-inline">' . __('Multiple email recipients', 'multi-emails-woocommerce') . '</h1>';
        echo '<hr class="wp-header-end">';
        require_once MULTI_EMAILS_WOOCOMMERCE_PATH . '/inc/admin/settings.php';
        echo '</div>';
    }
}


new Admin();
