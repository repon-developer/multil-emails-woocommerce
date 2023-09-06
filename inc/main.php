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
        if ( is_null(self::$instance) ) {
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

        add_filter('woocommerce_email_classes', array($this, 'register_email_classes'));
        add_action('woocommerce_checkout_order_processed', array($this, 'handle_checkout_order_processed'), 10, 3);

        add_action('initd', function () {
            $order = wc_get_order(62);
            $this->handle_checkout_order_processed(62, [], $order);
        });
    }

    /**
     * Register email classess
     * @since 1.0.0
     */
    public function register_email_classes($email_classes) {
        require_once MULTI_EMAILS_WOOCOMMERCE_PATH . 'inc/emails/new-order.php';
        $email_classes['multi-emails-woocommerce-new-order'] = new Emails\New_Order();
        return $email_classes;
    }

    /**
     * Add company field under shipping class field
     * @since 1.0.0
     */
    public function add_company_field() {
        global $wpdb;

        $vendors = $wpdb->get_results("SELECT * FROM $wpdb->table_multi_emails_vendor");

        $options = array(
            '' => __('Select Company', 'multi-emails-woocommerce')
        );

        foreach ($vendors as $vendor) {
            $options[$vendor->id] = $vendor->name;
        }

        $product_vendor = get_post_meta(get_the_ID(), 'product_vendor', true);

        echo '<div class="options_group">';
        woocommerce_wp_select(
            array(
                'id'          => 'product_vendor',
                'value'       => $product_vendor,
                'label'       => __('Company', 'multi-emails-woocommerce'),
                'options'     => $options,
            )
        );

        echo '</div>';
    }

    /**
     * Handle after creating an order
     * @since 1.0.0
     */
    public function handle_checkout_order_processed($order_id, $posted_data, $order) {
        $order_temrs = [];

        foreach ($order->get_items() as $item) {
            $product_id = $item->get_product_id();
            $terms = get_the_terms($product_id, 'product_cat');
            if ($terms === false) {
                continue;
            }

            foreach ($terms as $term) {
                $order_temrs[] = $term->term_id;
            }
        }

        $order_temrs = array_unique($order_temrs);

        global $wpdb;
        $vendors = $wpdb->get_results(sprintf("SELECT * FROM $wpdb->table_multi_emails_vendor WHERE category IN(%s);", implode(',', $order_temrs)));
        array_walk($vendors, function (&$item) {
            $item = new Vendor($item);
        });

        $wc_emails = WC()->mailer()->get_emails();
        foreach ($vendors as $vendor) {
            if (!$vendor->has_email()) {
                continue;
            }

            $vendor_emails = $vendor->get_emails();
            foreach ($vendor_emails as $email) {
                $wc_emails['multi-emails-woocommerce-new-order']->trigger($email, $vendor);
            }
        }
    }
}

Main::get_instance();
