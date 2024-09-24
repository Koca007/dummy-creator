<?php
// A WordPress plugin fájl
/*
Plugin Name: Dummy Product Creator
Description: A WordPress plugin for creating dummy products.
Version: 1.0
Author: Koca 
*/

// Helper fájlok behívása
require_once plugin_dir_path(__FILE__) . 'helper/woocommerce-check.php';
require_once plugin_dir_path(__FILE__) . 'helper/log.php';
require_once plugin_dir_path(__FILE__) . 'helper/ProductGenerator.php';
require_once plugin_dir_path(__FILE__) . 'templates/product-form.php';

// Log osztály példányosítása
$logger = new Logger();

// WooCommerce ellenőrzése a plugins_loaded hookban
add_action('plugins_loaded', 'dummy_check_woocommerce');

function dummy_check_woocommerce() {
    global $logger;

    if (is_woocommerce_active()) {
        $logger->log_info('WooCommerce aktív, a Dummy Product Creator inicializálása folyamatban.');

        // Admin menüpont regisztrálása
        add_action('admin_menu', 'dummy_creator_register_menu');

        // Stílusok és JS betöltése
        add_action('admin_enqueue_scripts', 'dummy_creator_enqueue_scripts');

    } else {
        // WooCommerce nincs telepítve
        add_action('admin_notices', function() {
            echo '<div class="notice notice-error"><p>A Dummy Product Creator plugin használatához a WooCommerce telepítése és aktiválása szükséges.</p></div>';
        });

        $logger->log_error('WooCommerce nincs aktiválva, a plugin nem inicializálható.');
    }
}

function dummy_creator_enqueue_scripts() {
    wp_enqueue_style('select2-css', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css');
    wp_enqueue_script('select2-js', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js', ['jquery'], null, true);
    wp_enqueue_script('dummy-select2', plugin_dir_url(__FILE__) . 'assets/select2.js', ['select2-js'], null, true);
    
    // Admin stílusok
    wp_add_inline_style('select2-css', '
        .notice {
            display: block; /* Az üzenetek megjelenítése */
            opacity: 1; /* Teljes átlátszóság */
            transition: none; /* Nincs animáció */
        }
    ');
}

// Admin menüpont regisztrálása
function dummy_creator_register_menu() {
    add_menu_page(
        'Dummy Creator',     
        'Dummy Creator',     
        'manage_options',    
        'dummy-creator',     
        'dummy_creator_page' // Itt hivatkozunk a függvényre
    );
}

// Extra link a bővítmény oldalára
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'dummy_creator_action_links');

function dummy_creator_action_links($links) {
    // Link a bővítmény oldalára
    $settings_link = '<a href="' . admin_url('admin.php?page=dummy-creator') . '">Settings</a>';
    array_unshift($links, $settings_link); // A "Settings" link a lista elejére kerül
    return $links;
}

function dummy_creator_page() {
    global $logger;
    $feedback = ''; // Visszaigazoló üzenet változó

    echo '<h1>Dummy Product Creator</h1>';
    echo '<p>Itt tudsz dummy termékeket létrehozni.</p>';

    // Ellenőrizzük, hogy a formot elküldték-e
    if (isset($_POST['dummy_product_count'])) {
        $product_count = intval($_POST['dummy_product_count']);
        $featured_images = isset($_POST['featured_images']) ? $_POST['featured_images'] : '';
        $product_categories = isset($_POST['product_categories']) ? $_POST['product_categories'] : [];
        $full_description = isset($_POST['full_description']) ? $_POST['full_description'] : '';
        $short_description = isset($_POST['short_description']) ? $_POST['short_description'] : '';
        $number_of_categories = isset($_POST['number_of_categories']) ? intval($_POST['number_of_categories']) : 1;

        $product_generator = new ProductGenerator($logger, $featured_images, $product_count, $product_categories, $number_of_categories, $full_description, $short_description);
        $feedback = $product_generator->create_dummy_products(); // Visszaigazolás elmentése
    }

    // Visszaigazoló üzenet megjelenítése
    if (!empty($feedback)) {
        echo '<div id="product_creation_feedback">' . esc_html($feedback) . '</div>';
    }

    // Include the product form
    include plugin_dir_path(__FILE__) . 'templates/product-form.php';
}
