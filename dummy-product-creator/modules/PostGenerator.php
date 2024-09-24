<?php
if (isset($_POST['dummy_post_count'])) {
    $post_count = intval($_POST['dummy_post_count']);
    $feedback_message = '';
    $feedback_class = 'notice'; // Alapértelmezett osztály

    // Ellenőrizd, hogy a megadott posztok száma érvényes-e
    if ($post_count > 0) {
        for ($i = 0; $i < $post_count; $i++) {
            // Poszt adatok előkészítése
            $post_data = [
                'post_title'    => 'Dummy Post ' . wp_generate_password(4, false),
                'post_content'  => 'Ez egy dummy tartalom.',
                'post_status'   => 'publish',
                'post_author'   => get_current_user_id(),
                'post_type'     => 'post'
            ];

            // Poszt létrehozása
            $post_id = wp_insert_post($post_data);

            // Ellenőrzés, hogy sikeres volt-e a poszt létrehozása
            if (!is_wp_error($post_id)) {
                $feedback_message .= 'Poszt ' . ($i + 1) . ' sikeresen létrehozva!<br />';
                $feedback_class = 'notice notice-success'; // Zöld osztály sikeres üzenethez
            } else {
                $feedback_message .= 'Hiba történt a poszt létrehozásakor: ' . $post_id->get_error_message() . '<br />';
                $feedback_class = 'notice notice-error'; // Piros osztály hibaüzenethez
            }
        }

        $feedback_message .= $post_count . ' poszt sikeresen létrehozva!';
    } else {
        $feedback_message = 'Érvénytelen posztszám!';
        $feedback_class = 'notice notice-error';
    }
}


