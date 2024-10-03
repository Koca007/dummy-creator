<?php
class CategoryHelper {
    // Fetch product categories
    public static function get_product_categories() {
        return get_terms(['taxonomy' => 'product_cat', 'hide_empty' => false]);
    }

    // Fetch product attributes and their terms
    public static function get_product_attributes_and_terms() {
        global $wpdb;

        // Fetch all WooCommerce attribute taxonomies
        $attributes = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}woocommerce_attribute_taxonomies");

        $result = [];
        foreach ($attributes as $attribute) {
            // Generate taxonomy name (e.g., 'pa_size')
            $taxonomy_name = wc_attribute_taxonomy_name($attribute->attribute_name);
            $terms = get_terms(['taxonomy' => $taxonomy_name, 'hide_empty' => false]);

            if (!empty($terms) && !is_wp_error($terms)) {
                $result[] = [
                    'attribute_label' => $attribute->attribute_label,
                    'attribute_name' => $taxonomy_name,
                    'terms' => $terms,
                ];
            }
        }

        return $result;
    }

    // Fetch product tags
    public static function get_product_tags() {
        return get_terms(['taxonomy' => 'product_tag', 'hide_empty' => false]);
    }

    // Fetch post categories
    public static function get_post_categories() {
        return get_terms(['taxonomy' => 'category', 'hide_empty' => false]);
    }

    // Fetch post types, including built-in and custom post types
    public static function get_post_types() {
        $args = [
            'public'   => true,
            '_builtin' => false,
        ];

        $post_types = get_post_types($args, 'objects');
        $builtin_post_types = get_post_types(['public' => true, '_builtin' => true], 'objects');
        return array_merge($post_types, $builtin_post_types);
    }

    // Render HTML for post categories (moved out of the form)
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
