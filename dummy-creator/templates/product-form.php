<form method="post" action="" class="wrap">
    <table class="form-table">
        <tr>
            <th scope="row">
                <label for="dummy_product_count">Hány terméket szeretnél létrehozni?</label>
            </th>
            <td>
                <input type="number" id="dummy_product_count" class="regular-text" name="dummy_product_count" min="1" required />
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="featured_images">Kiemelt képek (ID-k, vesszővel elválasztva):</label>
            </th>
            <td>
                <input type="text" id="featured_images" class="regular-text" name="featured_images" />
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="product_categories">Kategóriák:</label>
            </th>
            <td>
                <select id="product_categories" name="product_categories[]" multiple="multiple" class="regular-text" style="width: 300px;">
                    <?php
                    // Kategóriák betöltése a helper függvénnyel
                    $categories = CategoryHelper::get_product_categories();
                    if (is_array($categories) && !empty($categories)) {
                        foreach ($categories as $category) {
                            if (isset($category->term_id) && isset($category->name)) {
                                echo '<option value="' . esc_attr($category->term_id) . '">' . esc_html($category->name) . '</option>';
                            }
                        }
                    } else {
                        echo '<option value="">Nincsenek elérhető kategóriák</option>';
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="number_of_categories">Hány kategória legyen társítva a termékhez?</label>
            </th>
            <td>
                <input type="number" id="number_of_categories" class="regular-text" name="number_of_categories" min="1" required />
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="full_description">Teljes leírás:</label>
            </th>
            <td>
                <textarea id="full_description" class="regular-text" name="full_description" required></textarea>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="short_description">Rövid leírás:</label>
            </th>
            <td>
                <textarea id="short_description" class="regular-text" name="short_description" required></textarea>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="dummy_product_min_price">Minimális ár (opcionális):</label>
            </th>
            <td>
                <input type="number" id="dummy_product_min_price" class="regular-text" name="dummy_product_min_price" min="0" step="0.01" />
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="name_prefix">Név prefix (opcionális):</label>
            </th>
            <td>
                <input type="text" id="name_prefix" class="regular-text" name="name_prefix" />
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="gallery_images">Galéria képek (ID-k, vesszővel elválasztva):</label>
            </th>
            <td>
                <input type="text" id="gallery_images" class="regular-text" name="gallery_images" />
            </td>
        </tr>
    </table>

    <p class="submit">
        <input type="submit" class="button button-primary" value="Termékek létrehozása" />
    </p>
</form>

<script>
jQuery(document).ready(function($) {
    $('#product_categories').select2({
        placeholder: 'Válassz kategóriákat',
        allowClear: true
    });
});
</script>
