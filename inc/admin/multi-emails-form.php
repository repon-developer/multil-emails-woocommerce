<?php

$product_categories = get_terms(array(
    'taxonomy'   => 'product_cat',
    'hide_empty' => false,
));

if (is_wp_error($product_categories)) {
    $product_categories = [];
}

$product_category_options = array_map(function ($term) use ($vendor) {
    return sprintf('<option value="%d" %s>%s</option>', $term->term_id, selected($term->term_id, $vendor->category, false), $term->name);
}, $product_categories);

$vendor_emails_html = [];
foreach ($vendor->emails as $email) {
    $vendor_emails_html[] = sprintf('<li><input class="regular-text" type="text" name="emails[]" value="%s" /> <span class="dashicons dashicons-remove remove"></span><li>', $email);
}
?>

<div class="wrap multi-emails-woocommerce-wrap">
    <h1 class="wp-heading-inline"><?php _e('Multi Emails Vendor', 'multi-emails-woocommerce'); ?></h1>
    <hr class="wp-header-end">

    <?php
    if ($error->has_errors()) {
        printf('<div class="notice notice-error"><p>%1$s</p></div>', esc_html($error->get_error_message()));
    }

    ?>

    <form method="post">
        <?php wp_nonce_field('_nonce_multi_emails_submission_form') ?>
        <input type="hidden" name="vendor-id" value="<?php echo $vendor->get_id() ?>">
        <table class="form-table">
            <tr>
                <th>
                    <label for="company-name"><?php _e('Company Name', 'multi-emails-woocommerce'); ?></label>
                </th>
                <td>
                    <input class="regular-text" id="company-name" name="company-name" value="<?php echo $vendor->name ?>">
                </td>
            </tr>

            <tr>
                <th>
                    <label for="product-category"><?php _e('Product Category', 'multi-emails-woocommerce'); ?></label>
                </th>
                <td>
                    <select name="product-category" id="product-category">
                        <?php echo implode('', $product_category_options); ?>
                    </select>
                </td>
            </tr>

            <tr>
                <th>
                    <label><?php _e('Emails', 'multi-emails-woocommerce'); ?></label>
                </th>
                <td>
                    <ul class="vendor-email-address-list-field"><?php echo implode('', $vendor_emails_html) ?></ul>
                    <a class="button btn-vendor-add-new-email" href="#"><?php _e('Add Email', 'multi-emails-woocommerce') ?></a>
                </td>
            </tr>
        </table>

        <?php submit_button()  ?>

    </form>

</div>