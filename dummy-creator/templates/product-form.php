<?php
// Check if WooCommerce is active
if (!is_woocommerce_active()) {
    return; // Exit if WooCommerce is not active
}
?>

<form method="post" action="">
    <label for="dummy_product_count">Hány terméket szeretnél létrehozni?</label>
    <input type="number" id="dummy_product_count" name="dummy_product_count" min="1" required /><br />

    <label for="featured_images">Kiemelt képek (ID-k, vesszővel elválasztva):</label>
    <input type="text" id="featured_images" name="featured_images" /><br />

    <label for="product_categories">Kategóriák:</label>
    <select id="product_categories" name="product_categories[]" multiple="multiple" required>
        <?php
        // Kategóriák betöltése
        $categories = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => false]);
        if (is_array($categories) && !empty($categories)) {
            foreach ($categories as $category) {
                // Check if the category is a valid term object
                if (isset($category->term_id) && isset($category->name)) {
                    echo '<option value="' . esc_attr($category->term_id) . '">' . esc_html($category->name) . '</option>';
                }
            }
        } else {
            echo '<option value="">Nincsenek elérhető kategóriák</option>';
        }
        ?>
    </select><br />

    <label for="number_of_categories">Hány kategória legyen társítva a termékhez?</label>
    <input type="number" id="number_of_categories" name="number_of_categories" min="1" required /><br />

    <label for="full_description">Teljes leírás:</label>
    <textarea id="full_description" name="full_description" required></textarea><br />

    <label for="short_description">Rövid leírás:</label>
    <textarea id="short_description" name="short_description" required></textarea><br />

    <input type="submit" value="Termékek létrehozása" />
</form>

<div id="product_creation_feedback"></div> <!-- Visszajelző elem -->

<script>
jQuery(document).ready(function($) {
    $('#product_categories').select2({
        placeholder: 'Válassz kategóriákat',
        allowClear: true
    });
});
</script>
