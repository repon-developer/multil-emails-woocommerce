<?php

namespace Multi_Emails_WooCommerce;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Admin class
 */
final class Admin {

    public function __construct() {
        add_action('admin_menu', array($this, 'admin_menu'), 200);
    }

    /**
     * Admin menu
     * @since 1.0.0
     */
    public function admin_menu() {
        add_submenu_page('woocommerce', __('Multi-Emails', 'multi-emails-woocommerce'), __('Multi-Emails', 'multi-emails-woocommerce'), 'manage_woocommerce', 'multi-emails-woocmmerce', array($this, 'company_list'), 200);
    }

    /**
     * Company list table
     * @since 1.0.0
     */
    public function company_list() {
        echo '<div class="wrap multi-emails-woocommerce-wrap">';
        echo '<h1 class="wp-heading-inline">' . __('Multi Emails', 'multi-emails-woocommerce') . '</h1>';
        echo '<hr class="wp-header-end">';

        echo '<form method="post">';


        echo '</form>';
        
        echo '</div>';
    }
}


new Admin();
