<?php
// helper/log.php

class Logger {
    protected $log_file;

    public function __construct() {
        $upload_dir = wp_upload_dir(); // WordPress upload könyvtárának elérése
        $this->log_file = $upload_dir['basedir'] . '/dummy_product_creator.log'; // Log fájl elérési útvonala
    }

    // Logolás írása
    public function log($message) {
        $time = date('Y-m-d H:i:s'); // Dátum és idő hozzáadása
        $log_message = "[{$time}] - {$message}" . PHP_EOL;
        file_put_contents($this->log_file, $log_message, FILE_APPEND);
    }

    // Hiba logolása
    public function log_error($message) {
        $this->log("ERROR: {$message}");
    }

    // Információs üzenet logolása
    public function log_info($message) {
        $this->log("INFO: {$message}");
    }
}
