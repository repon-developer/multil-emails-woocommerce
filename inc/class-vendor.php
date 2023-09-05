<?php

namespace Multi_Emails_WooCommerce;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Multi email class item
 */
final class Vendor {

    /**
     * Get Multi email item from ID
     * @since 1.0.0
     */
    public static function get($id) {
        global $wpdb;
        $vendor = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->table_multi_emails_vendor WHERE id = %d", $id), ARRAY_A);
        return new self($vendor);
    }

    /**
     * Hold ID of this item
     * @since 1.0.0
     * @var int
     */
    private $id = 0;

    /**
     * Company name
     * @since 1.0.0
     * @var string
     */
    var $name = '';

    /**
     * Product Category
     * @since 1.0.0
     * @var int
     */
    var $category = 0;

    /**
     * Hold all email
     * @since 1.0.0
     * @var array
     */
    var $emails = [];

    /**
     * Dirty data
     * @since 1.0.0
     * @var array
     */
    var $dirty_data = [];

    /**
     * Constructor
     * @since 1.0.0
     */
    public function __construct($data) {
        if (is_object($data)) {
            $data = (array) $data;
        }

        if (!is_array($data)) {
            return;
        }

        foreach ($data as $key => $value) {
            $this->$key = $value;
        }

        if (!is_array($this->emails)) {
            $this->emails = [];
        }
    }

    /**
     * Check if exists or not
     * @since 1.0.0
     * @return boolean
     */
    public function exists() {
        return absint($this->id) > 0;
    }

    /**
     * Get ID of current item
     * @since 1.0.0
     * @return int
     */
    public function get_id() {
        return absint($this->id);
    }

    /**
     * Store extra data to dirty variable.
     * @since 1.0.0
     */
    public function __set($key, $value) {
        $this->dirty_data[$key] = $value;
    }

    /**
     * Save the vendor
     * @since 1.0.0
     * @return int on success
     */
    public function save() {
        return 34;
        global $wpdb;
        $data = get_object_vars($this);
        unset($data['dirty_data']);
        $wpdb->replace($wpdb->table_multi_emails_vendor, $data);
        return $wpdb->insert_id;
    }
}
