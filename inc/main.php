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
     * The whole idea of the singleton design pattern is that there is a single
     * object therefore, we don't want the object to be cloned.
     * @since 1.0
     * @return void
     */
    public function __clone() {
        _doing_it_wrong(__FUNCTION__, esc_html__('Cheating huh?', 'multi-emails-woocommerce'), '1.0.0');
    }

    /**
     * Disable unserializing of the class
     * @since 1.0
     * @return void
     */
    public function __wakeup() {
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
        add_action('woocommerce_new_order', array($this, 'save_additional_emails'), 10, 2);
        add_filter('woocommerce_billing_fields', array($this, 'add_checkout_fields'));
        add_filter('woocommerce_mail_callback_params', array($this, 'add_additional_emails'), 10, 2);
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

    /**
     * Add addtional email field at checkout form
     * @since 1.0.0
     * @return array
     */
    public function add_checkout_fields($fields) {
        $billing_email_priority = $fields['billing_email']['priority'];

        $start = 0;

        $additional_email_fields = Utils::get_additional_email_fields();
        foreach ($additional_email_fields as $field_key => $email_label) {
            $start++;
            $fields[$field_key] = array(
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

    /**
     * Save additional email at order
     * @since 1.0.0
     */
    public function save_additional_emails($order_id, $order) {
        $user_id     = $order->get_customer_id();

        $additional_email_keys = array_keys(Utils::get_additional_email_fields());

        foreach ($additional_email_keys as $field_key) {
            $email = !empty($_POST[$field_key]) ? sanitize_email($_POST[$field_key]) : false;
            if (!is_email($email)) {
                continue;
            }

            if ($user_id && is_checkout()) {
                update_user_meta($user_id, $field_key, $email);
            }

            update_post_meta($order_id, $field_key, $email);
        }
    }

    /**
     * Add addtional recipients
     * @since 1.0.0
     * @return array
     */
    public function add_additional_emails($params, \WC_Email $email) {
        if (!$email->is_customer_email()) {
            return $params;
        }

        $order = $email->object;
        if (!is_a($order, 'WC_Order')) {
            return $params;
        }

        $additional_recipients = array_map(function ($key) use ($order) {
            return sanitize_email(get_post_meta($order->get_id(), $key, true));
        }, array_keys(Utils::get_additional_email_fields()));

        $additional_recipients = array_unique(array_filter($additional_recipients));

        array_unshift($additional_recipients, $params[0]);

        $params[0] = implode(',', $additional_recipients);
        return $params;
    }
}

Main::get_instance();
