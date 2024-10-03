<?php
// Admin menu registration
if (!function_exists('dummy_creator_register_menu')) {
    function dummy_creator_register_menu() {
        add_menu_page(
            'Dummy Creator',        // Oldal neve
            'Dummy Creator',        // Menüpont neve
            'edit_posts',           // Jogosultság szükséges (edit_posts az editor jogosultsága)
            'dummy-creator',        // Slug
            'dummy_creator_page'    // Funkció a lap tartalmának megjelenítéséhez
        );
    }

    // Admin menü hozzáadása
    add_action('admin_menu', 'dummy_creator_register_menu');
}
