<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Core class
 */
final class Core {

    public function __construct() {
        global $wpdb;
        $wpdb->table_multi_emails_vendor = $wpdb->prefix . 'multi_emails_vendor';
        
        require_once MULTI_EMAILS_WOOCOMMERCE_PATH . '/inc/class-vendor.php';
    }
}

new Core();
