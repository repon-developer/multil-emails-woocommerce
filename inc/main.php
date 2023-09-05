<?php

namespace Multi_Emails_WooCommerce;

if (!defined('ABSPATH')) {
    exit;
}


/**
 * Main class plugin
 */
final class Main {

    /**
     * Throw error on object clone
     *
     * The whole idea of the singleton design pattern is that there is a single
     * object therefore, we don't want the object to be cloned.
     *
     * @since 1.0
     * @return void
     */
    public function __clone() {
        // Cloning instances of the class is forbidden
        _doing_it_wrong(__FUNCTION__, esc_html__('Cheating huh?', 'multi-emails-woocommerce'), '1.0.0');
    }

    /**
     * Disable unserializing of the class
     *
     * @since 1.0
     * @return void
     */
    public function __wakeup() {
        // Unserializing instances of the class is forbidden
        _doing_it_wrong(__FUNCTION__, esc_html__('Cheating huh?', 'multi-emails-woocommerce'), '1.0.0');
    }

    /**
     * Constructor
     */
    function __construct() {
        if (is_admin()) {
            require_once  MULTI_EMAILS_WOOCOMMERCE_PATH . '/inc/admin/admin.php';
        }
    }
}

new Main();
