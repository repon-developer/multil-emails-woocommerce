<?php

namespace Multi_Emails_WooCommerce;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Core class
 */
final class Core {

    /**
     * Hold the current instance
     * @var Main
     */
    private static $instance = null;

    /**
     * Get the instance of plugin
     * @since 1.0.0
     * @return Main
     */
    public static function get_instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function __construct() {
        global $wpdb;
        $wpdb->table_multi_emails_vendor = $wpdb->prefix . 'multi_emails_vendor';

        $this->include_files();

        register_activation_hook(MULTI_EMAILS_WOOCOMMERCE_FILE, array($this, 'activation'));
    }

    /**
     * Include core files
     * @since 1.0.0
     */
    public function include_files() {
        require_once MULTI_EMAILS_WOOCOMMERCE_PATH . '/inc/class-vendor.php';
    }

    /**
     * Plugin activation
     * @since 1.0.0
     */
    public function activation() {
        global $wpdb;

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        maybe_create_table( $wpdb->table_multi_emails_vendor, "CREATE TABLE $wpdb->table_multi_emails_vendor (
            `id` INT NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(100) NULL,
            `category` INT NULL,
            `emails` LONGTEXT NULL,
            `status` VARCHAR(10) NOT NULL DEFAULT 'acitve',
            `created_on` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        );");
    }
}

Core::get_instance();