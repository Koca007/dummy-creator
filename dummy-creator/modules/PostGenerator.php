<?php

class PostGenerator {
    protected $logger;
    protected $featured_image;
    protected $post_count;
    protected $post_categories;
    protected $name_prefix;
    protected $post_content;
    protected $post_type; // Post type attribute

    public function __construct($logger, $featured_image, $post_count, $post_categories, $name_prefix, $post_content, $post_type = 'post') {
        $this->logger = $logger;
        $this->featured_image = $featured_image;
        $this->post_count = $post_count;
        $this->post_categories = $post_categories;
        $this->name_prefix = $name_prefix;
        $this->post_content = $post_content;
        $this->post_type = $post_type; // Initialize post type
    }

    public function create_dummy_posts() {
        $created_posts = 0;

        // Log the detected post type before the creation loop starts
        $this->logger->log_info("Post creation started. Post type: {$this->post_type}");

        for ($i = 0; $i < $this->post_count; $i++) {
            $post_title = $this->name_prefix ? $this->name_prefix . ' ' . ($i + 1) : 'Dummy Post ' . ($i + 1);

            // Log that we are handling a post or page
            $this->logger->log_info("Creating Post/Page: {$post_title}");

            // For Posts and Pages
            $new_post = [
                'post_title'    => $post_title,
                'post_content'  => $this->post_content,
                'post_status'   => 'publish',
                'post_category' => $this->post_categories,
                'post_type'     => $this->post_type // Set the correct post type
            ];

            $post_id = wp_insert_post($new_post);

            if ($post_id && !is_wp_error($post_id)) {
                if ($this->featured_image) {
                    set_post_thumbnail($post_id, $this->featured_image);
                }

                $this->logger->log_info("Post/Page created: {$post_id} - {$post_title}");
                $created_posts++;
            } else {
                // Log if there was an error creating the post/page
                $this->logger->log_info("Error creating post/page: {$post_title}");
            }
        }

        $this->logger->log_info("Post creation completed. Total posts created: {$created_posts}");

        return "{$created_posts} dummy posts created!";
    }
}
