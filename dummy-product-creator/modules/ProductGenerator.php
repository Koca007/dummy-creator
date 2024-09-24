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
    protected $name_prefix; // Név prefix

    public function __construct($logger, $featured_images = '', $product_count = 0, $product_categories = [], $number_of_categories = 1, $full_description = '', $short_description = '', $min_price = 0, $name_prefix = '') {
        $this->logger = $logger;
        $this->product_count = $product_count;

        // A kiemelt képek vesszővel elválasztott listáját feldolgozzuk tömbbé
        $this->featured_images = !empty($featured_images) ? array_map('trim', explode(',', $featured_images)) : [];

        // A termékkategóriák beállítása
        $this->product_categories = $product_categories;
        $this->number_of_categories = $number_of_categories;

        // Leírások beállítása
        $this->full_description = $full_description;
        $this->short_description = $short_description;

        // Minimális ár beállítása
        $this->min_price = $min_price > 0 ? $min_price : 0;

        // Név prefix beállítása
        $this->name_prefix = $name_prefix;
    }

    public function create_dummy_products() {
        for ($i = 0; $i < $this->product_count; $i++) {
            $this->create_product($i + 1);
        }

        // Visszaigazoló üzenet
        return $this->product_count . ' termék sikeresen létrehozva!';
    }

    protected function create_product($product_number) {
        // Véletlenszerű normál ár generálása, figyelembe véve a minimális árat
        $regular_price = rand($this->min_price > 0 ? $this->min_price : 50, 200); // Normál ár a minimum ár felett
        $sale_price = rand(10, $regular_price - 1); // Kedvezményes ár
        
        // WooCommerce egyszerű termék létrehozása
        $product = new WC_Product_Simple();
    
        // Név prefix alkalmazása, ha meg van adva
        $random_name = wp_generate_password(4, false);
        $product_name = !empty($this->name_prefix) 
            ? $this->name_prefix . ' Dummy Product ' . $random_name // Prefix hozzáadása
            : 'Dummy Product ' . $random_name; // Alapértelmezett név
        $product->set_name($product_name); // Termék neve
    
        $product->set_regular_price($regular_price); // Normál ár beállítása
        $product->set_sale_price($sale_price); // Kedvezményes ár beállítása
        
        // Leírások beállítása
        $product->set_description($this->full_description);
        $product->set_short_description($this->short_description);
        
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
        
        // Kiemelt kép beállítása
        if (!empty($this->featured_images)) {
            $random_image_id = $this->featured_images[array_rand($this->featured_images)];
            $product->set_image_id($random_image_id);
        }
    
        // Kategóriák beállítása
        if (!empty($this->product_categories)) {
            $number_to_select = min($this->number_of_categories, count($this->product_categories));
            $selected_categories = (array) array_rand($this->product_categories, $number_to_select);
            
            // Biztosítsuk, hogy a kiválasztott kategóriák ID-jai helyesen vannak kezelve
            $category_ids = [];
            foreach ($selected_categories as $key) {
                $category_ids[] = $this->product_categories[$key];
            }
            
            $product->set_category_ids($category_ids); // Kategória ID-k beállítása
        }
    
        // Súly beállítása
        $product->set_weight(rand(1, 50));
    
        // Dimenziók beállítása külön-külön
        $length = rand(10, 100); // Hossz 10 és 100 cm között
        $width = rand(10, 100); // Szélesség 10 és 100 cm között
        $height = rand(10, 100); // Magasság 10 és 100 cm között
    
        $product->set_length($length);  // Hossz beállítása
        $product->set_width($width);    // Szélesség beállítása
        $product->set_height($height);  // Magasság beállítása
    
        // Logolás a dimenziókról
        $this->logger->log_info('Dimenziók: Hossz: ' . $length . ' cm, Szélesség: ' . $width . ' cm, Magasság: ' . $height . ' cm');
    
        // Termék mentése WooCommerce-be
        $result = $product->save();
        
        if (!$result) {
            $this->logger->log_error('Hiba történt a termék mentése közben.');
        } else {
            $this->logger->log_info('Dummy product létrehozva: ' . $product->get_name() . ', Normál ár: ' . $regular_price . ', Kedvezményes ár: ' . $sale_price);
        }
    }
}    
