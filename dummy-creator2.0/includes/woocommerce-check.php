<?php
// helper/woocommerce-check.php

function is_woocommerce_active() {
    // Ellenőrzi, hogy a WooCommerce bővítmény aktív-e
    return class_exists('WooCommerce');
}
?>