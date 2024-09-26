<?php
/*
Plugin Name: Dummy Product Creator
Description: A WordPress plugin for creating dummy products, posts, and users.
Version: 1.2
Author: Koca
*/

// Helper files
require_once plugin_dir_path(__FILE__) . 'includes/woocommerce-check.php';
require_once plugin_dir_path(__FILE__) . 'helper/log.php';
require_once plugin_dir_path(__FILE__) . 'helper/woocommerce-helper.php';
require_once plugin_dir_path(__FILE__) . 'helper/admin-menu-helper.php'; // Admin menü kiszervezve
require_once plugin_dir_path(__FILE__) . 'helper/scripts-helper.php';
require_once plugin_dir_path(__FILE__) . 'helper/generator-handler.php';
require_once plugin_dir_path(__FILE__) . 'modules/ProductGenerator.php'; 
require_once plugin_dir_path(__FILE__) . 'modules/PostGenerator.php';    
require_once plugin_dir_path(__FILE__) . 'modules/UserGenerator.php';    
require_once plugin_dir_path(__FILE__) . 'helper/CategoryHelper.php';
require_once plugin_dir_path(__FILE__) . 'helper/plugin-settings-link.php'; // Settings link kiszervezve

// Log class instance
$logger = new Logger();

// WooCommerce ellenőrzés a plugin aktiválásakor
if (!function_exists('dummy_check_woocommerce')) {
    function dummy_check_woocommerce() {
        global $logger;

        if (is_woocommerce_active()) {
            $logger->log_info('WooCommerce aktív, a Dummy Product Creator inicializálása folyamatban.');

            // Scripts és admin menü regisztrálása
            add_action('admin_enqueue_scripts', 'dummy_creator_enqueue_scripts');
        } else {
            // WooCommerce nincs aktiválva
            add_action('admin_notices', function() {
                echo '<div class="notice notice-error"><p>A Dummy Product Creator plugin használatához a WooCommerce telepítése és aktiválása szükséges.</p></div>';
            });

            $logger->log_error('WooCommerce nincs aktiválva, a plugin nem inicializálható.');
        }
    }
}
?>
