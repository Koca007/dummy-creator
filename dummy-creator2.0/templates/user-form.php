<form method="post" action="" class="wrap">
    <table class="form-table">
        <tr>
            <th scope="row">
                <label for="dummy_user_count">Hány felhasználót szeretnél létrehozni?</label>
            </th>
            <td>
                <input type="number" id="dummy_user_count" class="regular-text" name="dummy_user_count" min="1" required />
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="user_role">Felhasználói szerep:</label>
            </th>
            <td>
                <select id="user_role" name="user_role" class="regular-text">
                    <?php
                    $roles = wp_roles()->roles;
                    foreach ($roles as $role_slug => $role) {
                        // Exclude Administrator role from the dropdown
                        if ($role_slug !== 'administrator') {
                            echo '<option value="' . esc_attr($role_slug) . '">' . esc_html($role['name']) . '</option>';
                        }
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="email_domain">Email domain (pl: example.com):</label>
            </th>
            <td>
                <input type="text" id="email_domain" class="regular-text" name="email_domain" required placeholder="example.com" />
            </td>
        </tr>
    </table>

    <p class="submit">
        <input type="submit" class="button button-primary" value="Felhasználók létrehozása" />
    </p>
</form>
