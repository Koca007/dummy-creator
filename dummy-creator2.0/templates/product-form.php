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
                    $categories = CategoryHelper::get_product_categories();
                    if (is_array($categories) && !empty($categories)) {
                        foreach ($categories as $category) {
                            echo '<option value="' . esc_attr($category->term_id) . '">' . esc_html($category->name) . '</option>';
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
                <label for="product_tags">Termék címkék:</label>
            </th>
            <td>
                <select id="product_tags" name="product_tags[]" multiple="multiple" class="regular-text" style="width: 300px;">
                    <?php
                    $tags = get_terms(['taxonomy' => 'product_tag', 'hide_empty' => false]);
                    if (is_array($tags) && !empty($tags)) {
                        foreach ($tags as $tag) {
                            echo '<option value="' . esc_attr($tag->term_id) . '">' . esc_html($tag->name) . '</option>';
                        }
                    } else {
                        echo '<option value="">Nincsenek elérhető címkék</option>';
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
        <tr>
            <th scope="row">
                <label for="product_type">Termék típus:</label>
            </th>
            <td>
                <select id="product_type" name="product_type" class="regular-text" style="width: 300px;">
                    <option value="simple" selected>Egyszerű</option>
                    <option value="variable">Variációs</option>
                </select>
            </td>
        </tr>

        <!-- Attribute selection, shown only for variable products -->
        <tr id="attributes_row" style="display:none;">
            <th scope="row">Választható attribútumok:</th>
            <td>
                <select id="product_attributes" name="product_attributes[]" multiple="multiple" class="regular-text" style="width: 300px;">
                    <?php
                    $attributes = CategoryHelper::get_product_attributes_and_terms();
                    if (!empty($attributes)) {
                        foreach ($attributes as $attribute_data) {
                            $attribute_label = esc_html($attribute_data['attribute_label']);
                            $attribute_name = esc_attr($attribute_data['attribute_name']);
                            echo '<option value="' . $attribute_name . '">' . $attribute_label . '</option>';
                        }
                    } else {
                        echo '<option value="">Nincsenek elérhető attribútumok</option>';
                    }
                    ?>
                </select>
            </td>
        </tr>

        <tr>
            <th scope="row">
                <label for="manage_stock">Készletkezelés:</label>
            </th>
            <td>
                <label>
                    <input type="checkbox" id="manage_stock" name="manage_stock" value="1" />
                    Random készletkezelés bekapcsolása
                </label>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="featured_product">Kiemelt termékek:</label>
            </th>
            <td>
                <label>
                    <input type="checkbox" id="featured_product" name="featured_product" value="1" />
                    Véletlenszerű kiemelt termékek létrehozása
                </label>
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

    $('#product_tags').select2({
        placeholder: 'Válassz címkéket',
        allowClear: true
    });

    // Initialize Select2 for the attributes field
    $('#product_attributes').select2({
        placeholder: 'Válassz attribútumokat',
        allowClear: true
    });

    // Show/hide attribute section based on product type
    $('#product_type').change(function() {
        if ($(this).val() == 'variable') {
            $('#attributes_row').show(); // Show attributes for variable products
        } else {
            $('#attributes_row').hide(); // Hide for simple products
        }
    }).trigger('change');
});
</script>
