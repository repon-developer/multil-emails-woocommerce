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

        add_action('woocommerce_checkout_order_processed', array($this, 'order_send_email'), 10, 3);
        add_filter('woocommerce_billing_fields', array($this, 'add_checkout_fields'));
    }


    public function update_recipient($recipient) {
        return $this->recipient;
    }

    /**
     * Send email after order
     * @since 1.0.0
     */
    public function order_send_email($order_id, $posted_data, $order) {
        $email_recipients = get_option('multi-emails-woocommerce-recipients');
        $email_recipients = array_map(function ($recipient_item) {
            return Utils::sanitize_recipient($recipient_item);
        }, $email_recipients);

        $email_recipients = array_filter($email_recipients, function ($item) {
            return !empty($item['emails']);
        });


        $order_recipient_emails = [];

        foreach ($order->get_items() as $item) {
            $product_id = $item->get_product_id();
            $terms = get_the_terms($product_id, 'product_cat');

            $term_ids = [];
            if ($terms) {
                $term_ids = wp_list_pluck($terms, 'term_id');
            }

            foreach ($email_recipients as $recipient_item) {
                $matched_items = array_intersect($term_ids, $recipient_item['categories']);
                if (sizeof($matched_items) > 0 || in_array($product_id, $recipient_item['products'])) {
                    $order_recipient_emails[] = $recipient_item['emails'];
                }
            }
        }

        $order_recipient_emails = array_unique($order_recipient_emails);

        add_filter('woocommerce_new_order_email_allows_resend', '__return_true');

        $wc_emails = WC()->mailer()->get_emails();


        foreach ($order_recipient_emails as $emails) {
            $this->recipient = $emails;
            add_filter('woocommerce_email_recipient_new_order', array($this, 'update_recipient'));
            WC()->mailer()->emails['WC_Email_New_Order']->trigger($order->get_id(), $order, true);
            remove_filter('woocommerce_email_recipient_new_order', array($this, 'update_recipient'));
        }

        remove_filter('woocommerce_new_order_email_allows_resend', '__return_true');

        delete_post_meta($order_id, '_new_order_email_sent');
    }

    public function add_checkout_fields($fields) {
        $customer_emails = get_option('multi-emails-woocommerce-customer-emails');
        if (!is_array($customer_emails) || empty($customer_emails)) {
            return $fields;
        }

        $billing_email_priority = $fields['billing_email']['priority'];

        $start = 1;

        foreach ($customer_emails as $key => $email_label) {
            $start++;

            $filed_name = Utils::customer_email_name($start);

            $fields[$filed_name] = array(
                'label'        => $email_label,
                'required'     => false,
                'class'        => ['form-row-wide'],
                'validate'     => ['email'],
                'type'         => 'email',
                'priority'     => $billing_email_priority + $start,
            );
        }

        return $fields;
    }
}

Main::get_instance();
