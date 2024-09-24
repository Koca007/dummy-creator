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
    </table>

    <?php
    // Visszajelző üzenet elem
    if (isset($feedback_message)) {
        echo '<div id="post_creation_feedback" class="' . esc_attr($feedback_class) . '">' . esc_html($feedback_message) . '</div>';
    }
    ?>

    <p class="submit">
        <input type="submit" class="button button-primary" value="Posztok létrehozása" />
    </p>
</form>
