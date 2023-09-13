<?php

use Multi_Emails_WooCommerce\Utils;

$email_recipients = Utils::get_multi_recipient_settings();
if (isset($_POST['email-recipients'])) {
    $email_recipients = $_POST['email-recipients'];
}

$email_recipients = array_map(function ($recipient_item) {
    return Utils::sanitize_recipient($recipient_item);
}, $email_recipients);


$customer_emails = get_option('multi-emails-woocommerce-customer-emails');
if (!is_array($customer_emails)) {
    $customer_emails = [];
}

$customer_emails_items = array_map(function ($item) {
    return sprintf(
        '<li>
            <input placeholder="%s" class="regular-text" type="text" name="customer-emails[]" value="%s" /> 
            <span class="dashicons dashicons-remove remove-email"></span>
        </li>',
        __('Field title', 'multi-emails-woocommerce'),
        esc_attr($item)
    );
}, $customer_emails);


$enable_addtional_email_notifications = get_option('enable_addtional_email_notifications', 'yes');

$additional_email_pages = Utils::get_additional_email_pages(); ?>

<div id="poststuff">

    <form method="post">
        <div class="postbox">

            <div class="postbox-header">
                <h2 class="hndle ui-sortable-handle"><?php _e('Multiple Email Settings', 'multi-emails-woocommerce') ?> </h2>
            </div>

            <div class="inside">

                <?php wp_nonce_field('_nonce_multi_emails_woocommerce_settings') ?>

                <table class="form-table">
                    <tr>
                        <th>
                            <label><?php _e('Company Recipient(s)', 'multi-emails-woocommerce') ?></label>
                            <span class="multi-emails-woocommerce-tooltip">
                                <span class="dashicons dashicons-editor-help"></span>
                                <span class="tooltiptext"><?php _e('Enter any email to deliver cart order for fullfillment using any category or product.', 'multi-emails-woocommerce') ?></span>
                            </span>
                        </th>
                        <td>

                            <?php

                            foreach ($email_recipients as $index_no => $recipient_item) {
                                $item_no = $index_no + 1;

                                printf('<fieldset class="woocommerce-multi-emails-fieldset" data-no="%d">', $item_no);
                                echo '<span class="dashicons dashicons-remove remove-recipient"></span>';

                                printf(
                                    '<div class="field-row"><input placeholder="%s" name="email-recipients[%d][emails]" type="email" value="%s"></div>',
                                    __('Email address(es)', 'multi-emails-woocommerce'),
                                    $item_no,
                                    esc_attr($recipient_item['emails'])
                                );

                                echo '<div class="field-row">';
                                printf('<select class="multil-emails-woocommerce-recipient-categoires" name="email-recipients[%s][categories][]" multiple>', $item_no);
                                echo $this->get_categories($recipient_item['categories']);
                                echo '</select>';
                                echo '</div>';

                                echo '<div class="field-row">';
                                printf('<select class="multi-emails-woocommerce-search-product" name="email-recipients[%d][products][]" multiple>', $item_no);
                                echo $this->get_products($recipient_item['products']);
                                echo '</select>';
                                echo '</div>';

                                echo '</fieldset>';
                            }

                            ?>

                            <button class="button" id="woocommerce-multi-emails-add-recipient"><?php _e('Add Recipient', 'multi-emails-woocommerce') ?></button>
                        </td>
                    </tr>

                    <tr>
                        <th>
                            <label>
                                <?php _e('Additional Email Notifications', 'multi-emails-woocommerce') ?>
                            </label>
                        </th>

                        <td>
                            <label>
                                <input type="checkbox" name="enable_addtional_email_notifications" value="yes" <?php checked('yes', $enable_addtional_email_notifications) ?>>
                                <?php _e('Enable this option for additional email notifications.', 'multi-emails-woocommerce') ?>
                            </label>
                        </td>
                    </tr>

                    <tr>
                        <th>
                            <label>
                                <?php _e('Customer Email Labels', 'multi-emails-woocommerce') ?>
                            </label>

                            <span class="multi-emails-woocommerce-tooltip">
                                <span class="dashicons dashicons-editor-help"></span>
                                <span class="tooltiptext"><?php _e('Please enter label of email field. It will display on user front end.', 'multi-emails-woocommerce') ?></span>
                            </span>
                        </th>

                        <td>
                            <ul id="multi-emails-woocommerce-customer-emails"><?php echo implode("\n", $customer_emails_items) ?></ul>
                            <a id="multi-emails-woocommerce-add-customer-email" href="#" class="button"><?php _e('Add email field', 'multi-emails-woocommerce') ?></a>
                        </td>
                    </tr>

                    <tr>
                        <th>
                            <label>
                                <?php _e('Display Additional Email Fields', 'multi-emails-woocommerce') ?>
                            </label>
                        </th>

                        <td>

                            <div class="input-inline-row">
                                <label>
                                    <input type="checkbox" name="additional_email_pages[]" value="account" <?php checked(true, in_array('account', $additional_email_pages)) ?>>
                                    <?php _e('Account page', 'multi-emails-woocommerce') ?>
                                </label>

                                <label>
                                    <input type="checkbox" name="additional_email_pages[]" value="checkout" <?php checked(true, in_array('checkout', $additional_email_pages)) ?>>
                                    <?php _e('Checkout', 'multi-emails-woocommerce') ?>
                                </label>
                            </div>
                        </td>
                    </tr>


                </table>
            </div>
        </div>

        <?php submit_button()  ?>
    </form>

</div>

<script type="text/html" id="tmpl-woocommerce-multi-emails-recipient">
    <fieldset class="woocommerce-multi-emails-fieldset" data-no="{{data.index_no}}">

        <span class="dashicons dashicons-remove remove-recipient"></span>

        <div class="field-row">
            <input placeholder="<?php _e('Email address(es)') ?>" name="email-recipients[{{data.index_no}}][emails]" type="email">
        </div>

        <div class="field-row">
            <select class="multil-emails-woocommerce-recipient-categoires" name="email-recipients[{{data.index_no}}][categories][]" multiple placeholder="<?php _e('Search for category') ?>">
                <?php echo $this->get_categories() ?>
            </select>
        </div>

        <div class="field-row">
            <select class="multi-emails-woocommerce-search-product" name="email-recipients[{{data.index_no}}][products][]" multiple>
                <?php echo $this->get_products() ?>
            </select>
        </div>

    </fieldset>
</script>