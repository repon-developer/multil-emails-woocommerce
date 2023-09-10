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

    /**
     * Get addtional email key
     * @since 1.0.0
     * @return string
     */
    public static function get_additional_email_key($number) {
        return 'billing_email_' . $number;
    }

    /** 
     * Get additional email fields
     * @since 1.0.0
     * @return array
     */
    public static function get_additional_email_fields() {
        $customer_emails = get_option('multi-emails-woocommerce-customer-emails');
        if (!is_array($customer_emails) || empty($customer_emails)) {
            $customer_emails = [];
        }

        $addtional_emails = [];

        $start = 0;
        foreach ($customer_emails as $field_label) {
            $start++;
            $key = self::get_additional_email_key($start);
            $addtional_emails[$key] = $field_label;
        }

        return $addtional_emails;
    }

    
}
