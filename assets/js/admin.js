(function ($) {
    $('.btn-vendor-add-new-email').on('click', function (e) {
        e.preventDefault();
        const list_item = $('ul.vendor-email-address-list-field');
        list_item.append('<li><input class="regular-text" type="text" name="emails[]" /><span class="dashicons dashicons-remove remove"></span></li>')
    })

    $('ul.vendor-email-address-list-field').on('click', 'span.remove', function () {
        $(this).closest('li').remove();
    })

    $('table.multi_emails_woocommerce_table tr a.delete-vendor').on('click', function (e) {
        const is_confirm = confirm(multi_emails_woocommerce.i10n.vendor_delete_confirm)
        if (!is_confirm) {
            e.preventDefault()
        }
    })
})(jQuery)

