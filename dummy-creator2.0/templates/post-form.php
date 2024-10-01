<form method="post" action="" class="wrap">
    <table class="form-table">
        <tr>
            <th scope="row">
                <label for="dummy_post_count">Hány posztot szeretnél létrehozni?</label>
            </th>
            <td>
                <input type="number" id="dummy_post_count" class="regular-text" name="dummy_post_count" min="1" required />
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="featured_image">Kiemelt kép (ID):</label>
            </th>
            <td>
                <input type="text" id="featured_image" class="regular-text" name="featured_image" />
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="post_categories">Kategóriák:</label>
            </th>
            <td>
                <select id="post_categories" name="post_categories[]" class="regular-text" multiple>
                    <?php
                    // Poszt kategóriák lekérése a kiszervezett CategoryHelper osztályból
                    CategoryHelper::render_post_categories_select();
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="post_type">Poszt típus:</label>
            </th>
            <td>
                <select id="post_type" name="post_type" class="regular-text">
                    <?php
                    // Poszt típusok lekérése és megjelenítése
                    $post_types = CategoryHelper::get_post_types();
                    if (!empty($post_types)) {
                        foreach ($post_types as $post_type) {
                            echo '<option value="' . esc_attr($post_type->name) . '">' . esc_html($post_type->label) . '</option>';
                        }
                    } else {
                        echo '<option value="">Nincsenek elérhető poszt típusok</option>';
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="dummy_post_status">Poszt státusz:</label>
            </th>
            <td>
                <select id="dummy_post_status" name="dummy_post_status" class="regular-text">
                    <?php
                    // Elérhető poszt státuszok lekérése
                    $post_statuses = get_post_statuses();
                    foreach ($post_statuses as $status_value => $status_label) {
                        // Beállítjuk a "published" státuszt alapértelmezettként
                        $selected = ($status_value === 'publish') ? 'selected' : '';
                        echo '<option value="' . esc_attr($status_value) . '" ' . $selected . '>' . esc_html($status_label) . '</option>';
                    }
                    ?>
                </select>
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
                <label for="post_content">Poszt tartalma:</label>
            </th>
            <td>
                <textarea id="post_content" class="regular-text" name="post_content" rows="5"></textarea>
            </td>
        </tr>
    </table>

    <p class="submit">
        <input type="submit" class="button button-primary" value="Posztok létrehozása" />
    </p>
</form>

<script>
jQuery(document).ready(function($) {
    $('#post_categories').select2({
        placeholder: 'Válassz kategóriákat',
        allowClear: true
    });
});
</script>
