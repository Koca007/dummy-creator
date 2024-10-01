<?php

class MenuSettings {
    public function __construct() {
        add_action('admin_menu', [$this, 'add_menu']);
    }

    public function add_menu() {
        add_menu_page(
            'Generator Tools',        // Oldal neve
            'Generators',             // Menüpont neve
            'manage_options',         // Képesség szükséges
            'generator-tools',        // Slug
            [$this, 'display_tabs'],  // Funkció a tabfülek megjelenítésére
            'dashicons-admin-tools',  // Ikon
            20                        // Pozíció
        );
    }

    public function display_tabs() {
        // Tabfülek meghatározása
        $tabs = [
            'post_generator' => 'Post Generator',
            'product_generator' => 'Product Generator'
        ];

        // Aktív tabfül meghatározása
        $current_tab = isset($_GET['tab']) ? $_GET['tab'] : 'post_generator';

        echo '<h2 class="nav-tab-wrapper">';
        foreach ($tabs as $tab => $name) {
            $class = ($tab == $current_tab) ? ' nav-tab-active' : '';
            echo '<a class="nav-tab' . esc_attr($class) . '" href="?page=generator-tools&tab=' . esc_attr($tab) . '">' . esc_html($name) . '</a>';
        }
        echo '</h2>';

        // Aktív tabfül tartalmának betöltése
        if ($current_tab == 'post_generator') {
            include plugin_dir_path(__FILE__) . '../templates/post-form.php'; // Post Generator tab
        } elseif ($current_tab == 'product_generator' && is_woocommerce_active()) {
            include plugin_dir_path(__FILE__) . '../templates/product-form.php'; // Product Generator tab
        } else {
            echo '<div class="notice notice-error"><p>WooCommerce nem aktív, a termék generátor nem használható.</p></div>';
        }
    }
}
