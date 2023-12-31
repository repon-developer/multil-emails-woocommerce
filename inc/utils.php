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
	 * 
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
	 * Get multi recipient
	 * 
	 * @return array
	 */
	public static function get_multi_recipient_settings() {
		$email_recipients = get_option('multi-emails-woocommerce-recipients');
		if (!is_array($email_recipients)) {
			$email_recipients = [];
		}

		$email_recipients = array_map(function ($recipient_item) {
			return Utils::sanitize_recipient($recipient_item);
		}, $email_recipients);

		return $email_recipients;
	}

	/**
	 * Get addtional email key
	 * 
	 * @since 1.0.0
	 * @return string
	 */
	public static function get_additional_email_key($number) {
		return 'mew_billing_email_' . $number;
	}

	/** 
	 * Get additional email fields
	 * 
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

			if (empty($field_label)) {
				$field_label = __('Email address', 'multi-emails-woocommerce') . ' ' . ( $start + 1 );
			}


			$addtional_emails[$key] = $field_label;
		}

		return $addtional_emails;
	}

	/**
	 * Get additional email page settings
	 * 
	 * @since 1.0.0
	 * @return array
	 */

	public static function get_additional_email_pages() {
		$additional_email_pages = get_option('multi_email_woocommerce_additional_email_pages', ['account', 'checkout']);
		if (!is_array($additional_email_pages)) {
			$additional_email_pages = [];
		}

		return $additional_email_pages;
	}

	/**
	 * Get company from product ID
	 * 
	 * @since 1.0.0
	 * @return false|array
	 */
	public static function get_company_from_product_id($product_id) {
		$email_recipients = self::get_multi_recipient_settings();

		$terms = get_the_terms($product_id, 'product_cat');

		$term_ids = [];
		if ($terms) {
			$term_ids = wp_list_pluck($terms, 'term_id');
		}

		$company = false;

		foreach ($email_recipients as $recipient_item) {
			$matched_items = array_intersect($term_ids, $recipient_item['categories']);
			if (count($matched_items) > 0 || in_array($product_id, $recipient_item['products'])) {
				$company = $recipient_item;
				break;
			}
		}

		return $company;
	}
}
