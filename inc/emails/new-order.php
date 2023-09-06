<?php

namespace Multi_Emails_WooCommerce\Emails;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class for email for new order
 */
class New_Order extends \WC_Email {

    /**
     * Set email default
     * @since 1.0.0
     */
    public function __construct() {
        $this->id = 'multi-emails-woocommerce-new-order';
        $this->customer_email = false;
        $this->title = __('Multi Emails WooCommerce - New Order', 'multi-emails-woocommerce');
        $this->placeholders   = array(
            '{site_title}'   => $this->get_blogname(),
        );

        $this->template_base = MULTI_EMAILS_WOOCOMMERCE_PATH . 'templates/';
        $this->template_html  = 'emails/new-order.php';

        parent::__construct();
    }

    /**
     * Force HTML type.
     */
    public function get_email_type() {
        return 'html';
    }

    /**
     * Prepares email content and triggers the email
     * @since 1.0.0
     */
    public function trigger($email, $vendor) {
        $this->recipient = $email;
        if (!$this->is_enabled() || !$this->get_recipient()) {
            return;
        }

        error_log($this->get_recipient());
        error_log($this->get_content());
        $this->send($this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments());
    }

    /**
     * Get HTML content of email template
     * @since 1.0.0
     */
    public function get_content_html() {
        return wc_get_template_html($this->template_html, array(
            'email_heading' => $this->get_heading(),
            'sent_to_admin' => false,
            'plain_text'    => false,
            'email'         => $this,
            'site_title' => $this->get_blogname(),
        ), '', $this->template_base);
    }

    /**
     * Get subject of email
     * @since 1.0.0
     * @return string
     */
    public function get_default_subject() {
        return __('New order available', 'multi-emails-woocommerce');
    }

    /**
     * Get heading of email
     * @since 1.0.0
     * @return string
     */
    public function get_default_heading() {
        return __('New order', 'multi-emails-woocommerce');
    }

    /**
     * Init settings fields.
     * @since 1.0.0
     */
    public function init_form_fields() {
        $this->form_fields = array(
            'enabled'    => array(
                'type'    => 'checkbox',
                'default' => 'yes',
                'title'   => __('Enable/Disable', 'multi-emails-woocommerce'),
                'label'   => __('Enable this email notification', 'multi-emails-woocommerce'),
            ),
            'subject'    => array(
                'type'        => 'text',
                'title'       => __('Subject', 'multi-emails-woocommerce'),
                'desc_tip'    => true,
                'description' => sprintf(__('Available placeholders: %s', 'multi-emails-woocommerce'), '<code>{site_title}</code>'),
                'placeholder' => $this->get_default_subject(),
            ),
            'heading'    => array(
                'title'       => __('Email heading', 'multi-emails-woocommerce'),
                'type'        => 'text',
                'desc_tip'    => true,
                'description' => sprintf(__('Available placeholders: %s', 'multi-emails-woocommerce'), '<code>{site_title}</code>'),
                'placeholder' => $this->get_default_heading()
            ),
        );
    }
}
