jQuery(document).ready(function($) {
    $('#post_categories').select2({
        placeholder: 'Válassz kategóriákat',
        allowClear: true
    });

    $('#product_categories').select2({
        placeholder: 'Válassz kategóriákat',
        allowClear: true
    });
});