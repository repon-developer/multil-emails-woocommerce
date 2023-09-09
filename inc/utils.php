<?php

namespace Multi_Emails_WooCommerce;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Utilities class
 */
class Utils {

    /**
     * Sanitize recipient data
     * @since 1.0.0
     */
    public static function sanitize_recipient($item) {
        if (!isset($item['emails'])) {
            $item['emails'] = '';
        }

        if (!isset($item['categories']) || !is_array($item['categories'])) {
            $item['categories'] = [];
        }

        if (!isset($item['products']) || !is_array($item['products'])) {
            $item['products'] = [];
        }

        $item['emails'] = trim($item['emails']);

        return $item;
    }

    public static function customer_email_name($number) {
        return 'billing_email_' . $number;
    }
}
