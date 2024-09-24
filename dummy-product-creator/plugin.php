<?php
// A WordPress plugin fájl
/*
Plugin Name: Dummy Product Creator
Description: A WordPress plugin for creating dummy products and posts.
Version: 1.1
Author: Koca
*/

// Helper fájlok betöltése
require_once plugin_dir_path(__FILE__) . 'includes/woocommerce-check.php';
require_once plugin_dir_path(__FILE__) . 'helper/log.php';
require_once plugin_dir_path(__FILE__) . 'modules/ProductGenerator.php'; // ProductGenerator osztály

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
        'Dummy Creator',     // Oldal neve
        'Dummy Creator',     // Menüpont neve
        'manage_options',    // Jogosultság szükséges
        'dummy-creator',     // Slug
        'dummy_creator_page' // Funkció a lap tartalmának megjelenítéséhez
    );
}

function dummy_creator_page() {
    global $logger;
    $feedback = ''; // Visszaigazoló üzenet változó

    // Tabfülek létrehozása
    $tabs = [
        'post_generator' => 'Post Generator',
        'product_generator' => 'Product Generator'
    ];

    // Aktív tab meghatározása, alapértelmezett: post_generator
    $current_tab = isset($_GET['tab']) ? $_GET['tab'] : 'post_generator';

    echo '<h1>Dummy Product Creator</h1>';
    echo '<h2 class="nav-tab-wrapper">';
    foreach ($tabs as $tab => $name) {
        $active_class = ($current_tab == $tab) ? ' nav-tab-active' : '';
        echo '<a href="?page=dummy-creator&tab=' . esc_attr($tab) . '" class="nav-tab' . esc_attr($active_class) . '">' . esc_html($name) . '</a>';
    }
    echo '</h2>';

    // Tabfülek alapján tartalom betöltése
    if ($current_tab == 'post_generator') {
        include plugin_dir_path(__FILE__) . 'templates/post-form.php'; // Post Generator tab tartalma
    } elseif ($current_tab == 'product_generator') {
        if (is_woocommerce_active()) {
            include plugin_dir_path(__FILE__) . 'templates/product-form.php'; // Product Generator tab tartalma
        } else {
            echo '<div class="notice notice-error"><p>A WooCommerce nincs aktiválva, a termék generátor nem használható.</p></div>';
        }
    }

    if (isset($_POST['dummy_product_count'])) {
        $product_count = intval($_POST['dummy_product_count']);
        $featured_images = isset($_POST['featured_images']) ? $_POST['featured_images'] : '';
        $product_categories = isset($_POST['product_categories']) ? $_POST['product_categories'] : [];
        $full_description = isset($_POST['full_description']) ? $_POST['full_description'] : '';
        $short_description = isset($_POST['short_description']) ? $_POST['short_description'] : '';
        $number_of_categories = isset($_POST['number_of_categories']) ? intval($_POST['number_of_categories']) : 1;
        $min_price = isset($_POST['dummy_product_min_price']) ? floatval($_POST['dummy_product_min_price']) : 0; // Minimális ár
        $name_prefix = isset($_POST['name_prefix']) ? sanitize_text_field($_POST['name_prefix']) : ''; // Név prefix
    
        $product_generator = new ProductGenerator(
            $logger,
            $featured_images,
            $product_count,
            $product_categories,
            $number_of_categories,
            $full_description,
            $short_description,
            $min_price,
            $name_prefix // Név prefix átadása
        );
        $feedback = $product_generator->create_dummy_products(); // Visszaigazolás elmentése
    
        // Visszajelző üzenet megjelenítése
        if (!empty($feedback)) {
            echo '<div id="product_creation_feedback" class="notice notice-success is-dismissible"><p>' . esc_html($feedback) . '</p></div>';
        }
    }
    
    

// Extra link a bővítmény oldalára
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'dummy_creator_action_links');
function dummy_creator_action_links($links) {
    $settings_link = '<a href="' . admin_url('admin.php?page=dummy-creator&tab=post_generator') . '">Settings</a>';
    array_unshift($links, $settings_link); // A "Settings" link a lista elejére kerül
    return $links;
}
}