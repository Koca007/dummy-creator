<?php
// Handle form submissions for generating products, posts, and users
function dummy_creator_page() {
    global $logger;

    $tabs = [
        'post_generator' => 'Post Generator',
        'user_generator' => 'User Generator'
    ];

    // Check if WooCommerce is active
    if (is_woocommerce_active()) {
        $tabs['product_generator'] = 'Product Generator';
    }

    $current_tab = isset($_GET['tab']) ? $_GET['tab'] : 'post_generator';

    echo '<h1>Dummy Product Creator</h1>';
    echo '<h2 class="nav-tab-wrapper">';
    foreach ($tabs as $tab => $name) {
        $active_class = ($current_tab == $tab) ? ' nav-tab-active' : '';
        echo '<a href="?page=dummy-creator&tab=' . esc_attr($tab) . '" class="nav-tab' . esc_attr($active_class) . '">' . esc_html($name) . '</a>';
    }
    echo '</h2>';

    // Include the appropriate template based on the current tab
    if ($current_tab == 'post_generator') {
        include plugin_dir_path(__FILE__) . '../templates/post-form.php';
    } elseif ($current_tab == 'product_generator') {
        include plugin_dir_path(__FILE__) . '../templates/product-form.php';
    } elseif ($current_tab == 'user_generator') {
        include plugin_dir_path(__FILE__) . '../templates/user-form.php';
    }

    // Handle form submissions
    if (isset($_POST['dummy_product_count']) && is_woocommerce_active()) {
        handle_product_generation($logger);
    }

    if (isset($_POST['dummy_post_count'])) {
        handle_post_generation($logger);
    }

    if (isset($_POST['dummy_user_count'])) {
        handle_user_generation($logger);
    }
}


function handle_product_generation() {
    global $logger; // Make sure $logger is accessible

    $product_count = intval($_POST['dummy_product_count']);
    $featured_images = sanitize_text_field($_POST['featured_images']);
    $product_categories = $_POST['product_categories'] ?? [];
    $full_description = sanitize_textarea_field($_POST['full_description']);
    $short_description = sanitize_textarea_field($_POST['short_description']);
    $min_price = floatval($_POST['dummy_product_min_price']);
    $name_prefix = sanitize_text_field($_POST['name_prefix']);
    $gallery_images = sanitize_text_field($_POST['gallery_images']);
    $number_of_categories = intval($_POST['number_of_categories']); // Kategóriák száma

    $product_generator = new ProductGenerator(
        $logger,
        $featured_images,
        $product_count,
        $product_categories,
        $number_of_categories,
        $full_description,
        $short_description,
        $min_price,
        $name_prefix,
        $gallery_images
    );

    echo '<div class="notice notice-success is-dismissible"><p>' . esc_html($product_generator->create_dummy_products()) . '</p></div>';
}

function handle_post_generation() {
    global $logger; // Make sure $logger is accessible

    $post_count = intval($_POST['dummy_post_count']);
    $featured_image = intval($_POST['featured_image']);
    $post_categories = $_POST['post_categories'] ?? [];
    $name_prefix = sanitize_text_field($_POST['name_prefix']);
    $post_content = sanitize_textarea_field($_POST['post_content']);
    $post_type = sanitize_text_field($_POST['post_type']); // Get the selected post type

    $post_generator = new PostGenerator(
        $logger,
        $featured_image,
        $post_count,
        $post_categories,
        $name_prefix,
        $post_content,
        $post_type // Pass the post type to the constructor
    );

    echo '<div class="notice notice-success is-dismissible"><p>' . esc_html($post_generator->create_dummy_posts()) . '</p></div>';
}

function handle_user_generation() {
    global $logger; // Ensure $logger is accessible

    if (isset($_POST['dummy_user_count'], $_POST['user_role'], $_POST['email_domain'])) {
        $user_count = intval($_POST['dummy_user_count']);
        $user_role = sanitize_text_field($_POST['user_role']);
        $email_domain = sanitize_text_field($_POST['email_domain']);

        $user_generator = new UserGenerator($logger, $user_count, $user_role, $email_domain);
        $result = $user_generator->create_dummy_users();

        echo "<div class='updated'><p>{$result}</p></div>";
    }
}
