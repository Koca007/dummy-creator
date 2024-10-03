<?php
function dummy_creator_page() {
    global $logger;

    // Define the available tabs
    $tabs = [
        'post_generator' => 'Post Generator',
        'user_generator' => 'User Generator'
    ];

    // Check if WooCommerce is active and add the Product Generator tab
    if (is_woocommerce_active()) {
        $tabs['product_generator'] = 'Product Generator';
    }

    $current_tab = isset($_GET['tab']) ? $_GET['tab'] : 'post_generator';

    // Display the tab navigation
    echo '<h1>Dummy Product Creator</h1>';
    echo '<h2 class="nav-tab-wrapper">';
    foreach ($tabs as $tab => $name) {
        $active_class = ($current_tab == $tab) ? ' nav-tab-active' : '';
        echo '<a href="?page=dummy-creator&tab=' . esc_attr($tab) . '" class="nav-tab' . esc_attr($active_class) . '">' . esc_html($name) . '</a>';
    }
    echo '</h2>';

    // Include the appropriate form based on the selected tab
    if ($current_tab == 'post_generator') {
        include plugin_dir_path(__FILE__) . '../templates/post-form.php';
    } elseif ($current_tab == 'product_generator') {
        include plugin_dir_path(__FILE__) . '../templates/product-form.php';
    } elseif ($current_tab == 'user_generator') {
        include plugin_dir_path(__FILE__) . '../templates/user-form.php';
    }

    // Handle product generation if the product form is submitted
    if (isset($_POST['dummy_product_count']) && is_woocommerce_active()) {
        handle_product_generation($logger);
    }

    // Handle post generation if the post form is submitted
    if (isset($_POST['dummy_post_count'])) {
        handle_post_generation($logger);
    }

    // Handle user generation if the user form is submitted
    if (isset($_POST['dummy_user_count'])) {
        handle_user_generation($logger);
    }
}

function handle_product_generation($logger) {
    // Ensure all required fields are properly received from the form
    $product_count = isset($_POST['dummy_product_count']) ? intval($_POST['dummy_product_count']) : 0;
    if ($product_count <= 0) {
        $logger->log_error("Invalid product count: " . $product_count);
        return;
    }

    // Fetch data from the form
    $featured_images = sanitize_text_field($_POST['featured_images'] ?? '');
    $product_categories = isset($_POST['product_categories']) ? array_map('intval', $_POST['product_categories']) : [];
    $product_tags = isset($_POST['product_tags']) ? array_map('intval', $_POST['product_tags']) : [];
    $full_description = sanitize_textarea_field($_POST['full_description'] ?? '');
    $short_description = sanitize_textarea_field($_POST['short_description'] ?? '');
    $min_price = floatval($_POST['dummy_product_min_price'] ?? 0);
    $name_prefix = sanitize_text_field($_POST['name_prefix'] ?? '');
    $gallery_images = sanitize_text_field($_POST['gallery_images'] ?? '');
    $product_type = sanitize_text_field($_POST['product_type'] ?? 'simple');
    $manage_stock = isset($_POST['manage_stock']) && $_POST['manage_stock'] == 1;
    $digital_product = isset($_POST['digital_product']) && $_POST['digital_product'] == 1;
    $featured_product = isset($_POST['featured_product']) && $_POST['featured_product'] == 1;
    $variation_images = sanitize_text_field($_POST['variation_images'] ?? '');
    $variation_description = sanitize_textarea_field($_POST['variation_description'] ?? '');
    $number_of_categories = intval($_POST['number_of_categories'] ?? 1);
    $selected_attributes = isset($_POST['product_attributes']) ? array_map('sanitize_text_field', $_POST['product_attributes']) : [];

    // Log the form data for debugging
    $logger->log_info("Form data received: " . print_r($_POST, true));

    // Create a new instance of the ProductGenerator class with the form data
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
        $gallery_images,
        $product_type,
        $manage_stock,
        $product_tags,
        $digital_product,
        $featured_product,
        $selected_attributes, // Pass selected attributes
        $variation_images,
        $variation_description
    );

    // Generate the products and display a success message
    echo '<div class="notice notice-success is-dismissible"><p>' . esc_html($product_generator->create_dummy_products()) . '</p></div>';
}

function handle_post_generation($logger) {
    // Fetch post generation data from the form
    $post_count = intval($_POST['dummy_post_count']);
    $featured_image = intval($_POST['featured_image']);
    $post_categories = $_POST['post_categories'] ?? [];
    $name_prefix = sanitize_text_field($_POST['name_prefix']);
    $post_content = sanitize_textarea_field($_POST['post_content']);
    $post_type = sanitize_text_field($_POST['post_type']);
    $post_status = sanitize_text_field($_POST['dummy_post_status']);

    // Create a new instance of the PostGenerator class with the form data
    $post_generator = new PostGenerator(
        $logger,
        $featured_image,
        $post_count,
        $post_categories,
        $name_prefix,
        $post_content,
        $post_type,
        $post_status
    );

    // Generate the posts and display a success message
    echo '<div class="notice notice-success is-dismissible"><p>' . esc_html($post_generator->create_dummy_posts()) . '</p></div>';
}

function handle_user_generation($logger) {
    // Fetch user generation data from the form
    if (isset($_POST['dummy_user_count'], $_POST['user_role'], $_POST['email_domain'])) {
        $user_count = intval($_POST['dummy_user_count']);
        $user_role = sanitize_text_field($_POST['user_role']);
        $email_domain = sanitize_text_field($_POST['email_domain']);

        // Create a new instance of the UserGenerator class with the form data
        $user_generator = new UserGenerator($logger, $user_count, $user_role, $email_domain);
        $result = $user_generator->create_dummy_users();

        // Display a success message
        echo "<div class='updated'><p>{$result}</p></div>";
    }
}
