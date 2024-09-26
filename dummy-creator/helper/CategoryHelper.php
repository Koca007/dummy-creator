<?php
class CategoryHelper {
    
    // Termékkategóriák lekérése WooCommerce-hez
    public static function get_product_categories() {
        return get_terms(['taxonomy' => 'product_cat', 'hide_empty' => false]);
    }

    // Poszt kategóriák lekérése
    public static function get_post_categories() {
        return get_terms(['taxonomy' => 'category', 'hide_empty' => false]);
    }

    // Poszt típusok lekérése, beleértve a beépített és a custom post típusokat is
    public static function get_post_types() {
        $args = [
            'public'   => true,
            '_builtin' => false,
        ];

        $post_types = get_post_types($args, 'objects');
        $builtin_post_types = get_post_types(['public' => true, '_builtin' => true], 'objects');
        return array_merge($post_types, $builtin_post_types);
    }

    // HTML kód generálása poszt kategóriákhoz (kiszervezve a formból)
    public static function render_post_categories_select($selected_categories = []) {
        $categories = self::get_post_categories();
        
        if (!empty($categories) && !is_wp_error($categories)) {
            foreach ($categories as $category) {
                $selected = in_array($category->term_id, $selected_categories) ? 'selected' : '';
                echo '<option value="' . esc_attr($category->term_id) . '" ' . $selected . '>' . esc_html($category->name) . '</option>';
            }
        } else {
            echo '<option value="">Nincsenek elérhető kategóriák</option>';
        }
    }
}
