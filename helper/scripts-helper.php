<?php

// Enqueue styles and scripts
function dummy_creator_enqueue_scripts() {
    // Enqueue Select2 CSS
    wp_enqueue_style('select2-css', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css');

    // Enqueue Select2 JS
    wp_enqueue_script('select2-js', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js', ['jquery'], null, true);

    // Enqueue custom Select2 initialization script
    wp_enqueue_script('dummy-select2', plugin_dir_url(__FILE__) . '../assets/select2.js', ['select2-js'], null, true);

    // Inline styles for admin
    wp_add_inline_style('select2-css', '
        .notice {
            display: block;
            opacity: 1;
            transition: none;
        }
    ');
}

// Hook to enqueue the scripts on admin pages
add_action('admin_enqueue_scripts', 'dummy_creator_enqueue_scripts');
