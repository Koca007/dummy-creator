<?php

class ProductGenerator {
    protected $logger;
    protected $featured_images;
    protected $product_count;
    protected $product_categories;
    protected $number_of_categories;
    protected $full_description;
    protected $short_description;

    public function __construct($logger, $featured_images = '', $product_count = 0, $product_categories = [], $number_of_categories = 1, $full_description = '', $short_description = '') {
        $this->logger = $logger;
        $this->product_count = $product_count;

        // A kiemelt képek vesszővel elválasztott listáját feldolgozzuk tömbbé
        if (!empty($featured_images)) {
            $this->featured_images = array_map('trim', explode(',', $featured_images));
        } else {
            $this->featured_images = [];
        }

        // A termékkategóriák beállítása
        $this->product_categories = $product_categories;
        $this->number_of_categories = $number_of_categories;

        // Leírások beállítása
        $this->full_description = $full_description;
        $this->short_description = $short_description;
    }

    public function create_dummy_products() {
        for ($i = 0; $i < $this->product_count; $i++) {
            $this->create_product();
        }

        // Visszaigazoló üzenet
        return $this->product_count . ' termék sikeresen létrehozva!';
    }

    protected function create_product() {
        // Véletlenszerű normál ár generálása
        $regular_price = rand(50, 200); // Normál ár 50 és 200 között
    
        // Véletlenszerű kedvezményes ár generálása
        $sale_price = rand(10, $regular_price - 1); // Kedvezményes ár kisebb, mint a normál ár
    
        // Ha valamiért az árak egyenlők lennének vagy a kedvezményes ár nagyobb lenne, újra generáljuk
        while ($sale_price >= $regular_price) {
            $sale_price = rand(10, $regular_price - 1);
        }
    
        // Termék létrehozása
        $product = new WC_Product_Simple(); // Egyszerű termék létrehozása
        $product->set_name('Dummy Product ' . wp_generate_password(4, false));
        $product->set_regular_price($regular_price); // Normál ár beállítása
        $product->set_sale_price($sale_price); // Kedvezményes ár beállítása
    
        // Leírások beállítása
        $product->set_description($this->full_description); // Teljes leírás
        $product->set_short_description($this->short_description); // Rövid leírás
        $product->set_status('publish');
    
        // Véletlenszerű termék típus beállítása (fizikai, virtuális, letölthető)
        $product_types = ['physical', 'virtual', 'downloadable'];
        $selected_type = $product_types[array_rand($product_types)];
    
        if ($selected_type === 'virtual') {
            $product->set_virtual(true);
        } elseif ($selected_type === 'downloadable') {
            $product->set_downloadable(true);
        }
        
        $this->logger->log_info('Termék típusa: ' . ucfirst($selected_type));
    
        // Kiemelt kép randomizálása, ha meg van adva
        if (!empty($this->featured_images)) {
            $random_image_id = $this->featured_images[array_rand($this->featured_images)];
            $product->set_image_id($random_image_id); // Kép beállítása ID alapján
            $this->logger->log_info('Kiemelt kép beállítva a termékhez: ' . $random_image_id);
        }
    
        // Kategória randomizálása, ha vannak megadva
        if (!empty($this->product_categories)) {
            $random_categories = (array) $this->product_categories;
            $total_categories = count($random_categories);
            $number_to_select = min($this->number_of_categories, $total_categories);
    
            // Véletlenszerű kategóriák kiválasztása
            $selected_categories = [];
            $keys = array_rand($random_categories, $number_to_select);
    
            if ($number_to_select === 1) {
                $selected_categories[] = $random_categories[$keys];
            } else {
                foreach ($keys as $key) {
                    $selected_categories[] = $random_categories[$key];
                }
            }
    
            $product->set_category_ids($selected_categories); // Kategória beállítása
            $this->logger->log_info('Kategória beállítva a termékhez: ' . implode(', ', $selected_categories));
        }
    
        // Véletlenszerű súly és dimenziók beállítása
        $weight = rand(1, 50); // Súly 1 és 50 kg között
        $length = rand(10, 100); // Hossz 10 és 100 cm között
        $width = rand(10, 100); // Szélesség 10 és 100 cm között
        $height = rand(10, 100); // Magasság 10 és 100 cm között
    
        $product->set_weight($weight); // Súly beállítása
        $product->set_length($length); // Hossz beállítása
        $product->set_width($width); // Szélesség beállítása
        $product->set_height($height); // Magasság beállítása
        $this->logger->log_info('Súly: ' . $weight . ' kg, Dimenziók: ' . $length . ' x ' . $width . ' x ' . $height . ' cm');
    
        $product->save();
    
        // Logoljuk a létrehozott terméket és az árakat
        $this->logger->log_info('Dummy product létrehozva: ' . $product->get_name() . ', Normál ár: ' . $regular_price . ', Kedvezményes ár: ' . $sale_price);
    }
}