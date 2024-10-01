<?php
if (!function_exists('dummy_check_woocommerce')) {
    function dummy_check_woocommerce() {
        return class_exists('WooCommerce');
    }
}
