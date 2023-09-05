(function ($) {
    $('.btn-vendor-add-new-email').on('click', function (e) {
        e.preventDefault();
        const list_item = $('ul.vendor-email-address-list-field');
        list_item.append('<li><input class="regular-text" type="text" name="emails[]" /><span class="dashicons dashicons-remove remove"></span></li>')
    })

    $('ul.vendor-email-address-list-field').on('click', 'span.remove', function () {
        $(this).closest('li').remove();
    })
})(jQuery)

