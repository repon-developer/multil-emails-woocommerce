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
        add_action('init', array($this, 'handle_delete_vendor'));
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

        if (!wp_verify_nonce($_POST['_wpnonce'], '_nonce_multi_emails_submission_form')) {
            return;
        }

        $post_data = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);


        if (empty($post_data['company-name'])) {
            $this->error->add('company_name', __('Please enter company name.', 'multi-emails-woocommerce'));
        }

        if (empty($post_data['product-category'])) {
            $this->error->add('product_category', __('Please select a category from the list.', 'multi-emails-woocommerce'));
        }

        $emails = empty($post_data['emails']) || !is_array($post_data['emails']) ? [] : $post_data['emails'];
        $emails = array_filter(array_map('sanitize_email', $emails));

        if (empty($emails)) {
            $this->error->add('email', __('Please enter at least one email.', 'multi-emails-woocommerce'));
        }

        if ($this->error->has_errors()) {
            return;
        }

        $vendor_id = isset($post_data['vendor-id']) ? $post_data['vendor-id'] : null;
        $vendor = Vendor::get($vendor_id);

        $vendor->name = $post_data['company-name'];
        $vendor->category = $post_data['product-category'];
        $vendor->emails = $emails;
        $vendor_id = $vendor->save();


        exit(wp_safe_redirect(add_query_arg(array('id' => $vendor_id, 'page' => 'multi-emails-woocommerce'))));
    }

    /**
     * Delete vendor
     * @since 1.0.0
     */
    public function handle_delete_vendor() {
        if (!isset($_GET['id'], $_GET['_nonce'])) {
            return;
        }

        if (!wp_verify_nonce($_GET['_nonce'], '_nonce_delete_multi_emails_vendor_' . $_GET['id'])) {
            return;
        }

        $vendor = Vendor::get($_GET['id']);
        if (!$vendor->exists()) {
            wp_die(__('Invalid vendor', 'multi-emails-woocommerce'));
        }

        $vendor->delete();

        exit(wp_safe_redirect(remove_query_arg(array('id', '_nonce'))));
    }

    /**
     * Admin menu
     * @since 1.0.0
     */
    public function admin_menu() {
        add_submenu_page('woocommerce', __('Multi-Emails', 'multi-emails-woocommerce'), __('Multi-Emails', 'multi-emails-woocommerce'), 'manage_woocommerce', 'multi-emails-woocommerce', array($this, 'company_list'), 200);
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

        wp_enqueue_style('multi-emails-woocommerce', MULTI_EMAILS_WOOCOMMERCE_URL . '/assets/css/admin.css');
        wp_enqueue_script('multi-emails-woocommerce', MULTI_EMAILS_WOOCOMMERCE_URL . '/assets/js/admin.js', ['jquery'], MULTI_EMAILS_WOOCOMMERCE_VERSION, true);
        wp_localize_script('multi-emails-woocommerce', 'multi_emails_woocommerce', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'i10n' => array(
                'vendor_delete_confirm' => __('Do you want to delete this item?', 'multi-emails-woocommerce')
            )
        ));
    }

    /**
     * Company list table
     * @since 1.0.0
     */
    public function company_list() {
        if (isset($_GET['id'])) {
            $vendor = Vendor::get($_GET['id']);

            $post_data = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);
            if (is_array($post_data)) {
                $vendor = new Vendor(array(
                    'name' => $post_data['company-name'],
                    'category' => $post_data['product-category'],
                    'emails' => isset($post_data['emails']) ? $post_data['emails'] : [],
                ));
            }

            $error = $this->error;
            return require_once MULTI_EMAILS_WOOCOMMERCE_PATH . '/inc/admin/multi-emails-form.php';
        }


        require_once MULTI_EMAILS_WOOCOMMERCE_PATH . '/inc/admin/multi-emails-table.php';

        $multi_emails_table = new Multi_Emails_Table();
        $multi_emails_table->prepare_items();

        echo '<div class="wrap multi-emails-woocommerce-wrap">';
        echo '<h1 class="wp-heading-inline">' . __('Multi Emails', 'multi-emails-woocommerce') . '</h1>';
        echo ' <a href="' . add_query_arg('id', 'new', menu_page_url('multi-emails-woocommerce', false)) . '" class="page-title-action">' . __('Add New', 'multi-emails-woocommerce') . '</a>';

        echo '<hr class="wp-header-end">';

        echo '<form method="post">';
        $multi_emails_table->display();
        echo '</form>';

        echo '</div>';
    }
}


new Admin();
