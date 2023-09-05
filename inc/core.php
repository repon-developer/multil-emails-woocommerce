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
        $wpdb->table_multi_emails = $wpdb->prefix . 'woocommerce_multi_emails';
    }
}

new Core();
