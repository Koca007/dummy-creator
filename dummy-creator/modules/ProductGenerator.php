<?php

class ProductGenerator {
    protected $logger;
    protected $featured_images;
    protected $product_count;
    protected $product_categories;
    protected $number_of_categories;
    protected $full_description;
    protected $short_description;
    protected $min_price;
    protected $name_prefix;
    protected $gallery_images; // Galéria képek ID-k

    public function __construct($logger, $featured_images = '', $product_count = 0, $product_categories = [], $number_of_categories = 1, $full_description = '', $short_description = '', $min_price = 0, $name_prefix = '', $gallery_images = '') {
        $this->logger = $logger;
        $this->product_count = $product_count;

        // A kiemelt képek vesszővel elválasztott listáját feldolgozzuk tömbbé
        $this->featured_images = !empty($featured_images) ? array_map('trim', explode(',', $featured_images)) : [];

        // A galéria képek vesszővel elválasztott listáját feldolgozzuk tömbbé
        $this->gallery_images = !empty($gallery_images) ? array_map('trim', explode(',', $gallery_images)) : [];

        // A termékkategóriák beállítása
        $this->product_categories = is_array($product_categories) ? $product_categories : [];
        $this->number_of_categories = $number_of_categories;

        // Leírások beállítása
        $this->full_description = $full_description;
        $this->short_description = $short_description;

        // Minimális ár beállítása
        $this->min_price = $min_price > 0 ? $min_price : 50;

        // Név prefix beállítása
        $this->name_prefix = $name_prefix;
    }

    public function create_dummy_products() {
        $this->logger->log_info("Termék generálás elkezdve. Termékek száma: " . $this->product_count);

        for ($i = 0; $i < $this->product_count; $i++) {
            $this->logger->log_info("Létrehozás elkezdve a(z) " . ($i + 1) . ". termékhez.");
            $this->create_product($i + 1);
            $this->logger->log_info("Létrehozás befejezve a(z) " . ($i + 1) . ". termékhez.");
        }

        // Visszaigazoló üzenet
        return $this->product_count . ' termék sikeresen létrehozva!';
    }

    protected function create_product($product_number) {
        // Véletlenszerű normál ár generálása, figyelembe véve a minimális árat
        $regular_price = rand($this->min_price, 200); // Normál ár legalább a minimum ár
        $sale_price = rand(10, $regular_price - 1); // Kedvezményes ár, de kevesebb, mint a normál ár

        $this->logger->log_info("Termék $product_number: Ár beállítva. Normál ár: $regular_price, Kedvezményes ár: $sale_price");

        // WooCommerce egyszerű termék létrehozása
        $product = new WC_Product_Simple();

        // Név prefix alkalmazása, ha meg van adva
        $random_name = wp_generate_password(4, false);
        $product_name = !empty($this->name_prefix) 
            ? $this->name_prefix . ' Dummy Product ' . $random_name // Prefix hozzáadása
            : 'Dummy Product ' . $random_name; // Alapértelmezett név
        $product->set_name($product_name);

        $this->logger->log_info("Termék $product_number: Név: $product_name");

        $product->set_regular_price($regular_price); // Normál ár beállítása
        $product->set_sale_price($sale_price); // Kedvezményes ár beállítása

        // Leírások beállítása
        $product->set_description($this->full_description);
        $product->set_short_description($this->short_description);
        $this->logger->log_info("Termék $product_number: Leírás beállítva.");

        // Termék állapotának beállítása
        $product->set_status('publish');

        // Véletlenszerű termék típus (fizikai, virtuális, letölthető)
        $product_types = ['physical', 'virtual', 'downloadable'];
        $selected_type = $product_types[array_rand($product_types)];

        if ($selected_type === 'virtual') {
            $product->set_virtual(true);
        } elseif ($selected_type === 'downloadable') {
            $product->set_downloadable(true);
        }
        $this->logger->log_info("Termék $product_number: Termék típus: $selected_type");

        // Kiemelt kép beállítása
        if (!empty($this->featured_images)) {
            $random_image_id = $this->featured_images[array_rand($this->featured_images)];
            $product->set_image_id($random_image_id);
            $this->logger->log_info("Termék $product_number: Kiemelt kép ID: $random_image_id");
        }

        // Galéria képek beállítása
        if (!empty($this->gallery_images)) {
            // A galéria képek meta adatként való beállítása
            $gallery_ids = implode(',', $this->gallery_images);
            $product->update_meta_data('_product_image_gallery', $gallery_ids);
            $this->logger->log_info("Termék $product_number: Galéria képek ID-k: " . implode(', ', $this->gallery_images));
        }

        // Kategóriák beállítása
        if (!empty($this->product_categories)) {
            $number_to_select = min($this->number_of_categories, count($this->product_categories));
            $selected_categories = (array) array_rand($this->product_categories, $number_to_select);

            $this->logger->log_info("Termék $product_number: Kiválasztott kategóriák száma: $number_to_select, Kiválasztott kategóriák: " . implode(', ', $selected_categories));

            $product->set_category_ids(array_map(function($key) {
                return $this->product_categories[$key];
            }, $selected_categories));
        }

        // Súly beállítása
        $product->set_weight(rand(1, 50));

        // Dimenziók beállítása külön-külön
        $length = rand(10, 100);
        $width = rand(10, 100);
        $height = rand(10, 100);

        $product->set_length($length);
        $product->set_width($width);
        $product->set_height($height);

        $this->logger->log_info("Termék $product_number: Dimenziók: Hossz: $length cm, Szélesség: $width cm, Magasság: $height cm");

        // Termék mentése WooCommerce-be
        $result = $product->save();

        if (!$result) {
            $this->logger->log_error("Termék $product_number: Hiba történt a termék mentése közben.");
        } else {
            $this->logger->log_info("Dummy product létrehozva: " . $product->get_name() . ", Normál ár: " . $regular_price . ", Kedvezményes ár: " . $sale_price);
        }
    }
}
