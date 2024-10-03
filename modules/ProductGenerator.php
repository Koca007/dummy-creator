<?php
class ProductGenerator {
    protected $logger;
    protected $featured_images;
    protected $product_count;
    protected $product_categories;
    protected $number_of_categories;
    protected $full_description;
    protected $short_description;
    protected $min_price;
    protected $name_prefix;
    protected $gallery_images; // Gallery images
    protected $product_type;
    protected $manage_stock;
    protected $product_tags;
    protected $digital_product;
    protected $featured_product;
    protected $selected_attributes;
    protected $variation_images;
    protected $variation_description;

    public function __construct(
        $logger,
        $featured_images = '',
        $product_count = 0,
        $product_categories = [],
        $number_of_categories = 1,
        $full_description = '',
        $short_description = '',
        $min_price = 0,
        $name_prefix = '',
        $gallery_images = '', // Receive gallery images from form
        $product_type = 'simple',
        $manage_stock = false,
        $product_tags = [],
        $digital_product = false,
        $featured_product = false,
        $selected_attributes = [],
        $variation_images = '',
        $variation_description = ''
    ) {
        $this->logger = $logger;
        $this->product_count = $product_count;
        $this->featured_images = is_string($featured_images) && !empty($featured_images) ? array_map('trim', explode(',', $featured_images)) : [];
        $this->gallery_images = is_string($gallery_images) && !empty($gallery_images) ? array_map('trim', explode(',', $gallery_images)) : []; // Parse gallery images
        $this->variation_images = is_string($variation_images) && !empty($variation_images) ? array_map('trim', explode(',', $variation_images)) : [];
        $this->product_categories = is_array($product_categories) ? $product_categories : [];
        $this->number_of_categories = $number_of_categories;
        $this->full_description = $full_description;
        $this->short_description = $short_description;
        $this->min_price = $min_price > 0 ? $min_price : 50;
        $this->name_prefix = $name_prefix;
        $this->product_type = $product_type;
        $this->manage_stock = $manage_stock;
        $this->product_tags = is_array($product_tags) ? $product_tags : [];
        $this->digital_product = $digital_product;
        $this->featured_product = $featured_product;
        $this->selected_attributes = $selected_attributes;
        $this->variation_description = $variation_description;
    }

    public function create_dummy_products() {
        $this->logger->log_info("Starting product creation. Total products: " . $this->product_count);

        for ($i = 0; $i < $this->product_count; $i++) {
            $this->logger->log_info("Creating product #" . ($i + 1));
            $this->create_product($i + 1);
            $this->logger->log_info("Product #" . ($i + 1) . " creation completed.");
        }

        return $this->product_count . ' products successfully created!';
    }

    protected function create_product($product_number) {
        $regular_price = rand($this->min_price, 200);
        $sale_price = rand(10, $regular_price - 1);

        $this->logger->log_info("Product $product_number: Setting price. Regular: $regular_price, Sale: $sale_price");

        // Create a new product, either simple or variable
        if ($this->product_type === 'simple') {
            $product = new WC_Product_Simple();
        } elseif ($this->product_type === 'variable') {
            $product = new WC_Product_Variable();
        }

        // Generate product name with optional prefix
        $random_name = wp_generate_password(4, false);
        $product_name = !empty($this->name_prefix)
            ? $this->name_prefix . ' Dummy Product ' . $random_name
            : 'Dummy Product ' . $random_name;
        $product->set_name($product_name);

        $product->set_regular_price($regular_price);
        $product->set_sale_price($sale_price);

        // Set product descriptions
        $product->set_description($this->full_description);

        // Handle short description:
        if ($this->product_type === 'simple') {
            $product->set_short_description($this->short_description); // For simple products
        }

        $product->set_status('publish');

        // Handle stock management
        if ($this->manage_stock) {
            $stock_quantity = rand(1, 100);
            $product->set_manage_stock(true);
            $product->set_stock_quantity($stock_quantity);
            $this->logger->log_info("Product $product_number: Stock management enabled, quantity: $stock_quantity");
        }

        // Handle featured image
        if (!empty($this->featured_images)) {
            $random_image_id = $this->featured_images[array_rand($this->featured_images)];
            $product->set_image_id($random_image_id);
        }

        // Handle gallery images (for all products including variable ones)
        if (!empty($this->gallery_images)) {
            $product->set_gallery_image_ids($this->gallery_images);
            $this->logger->log_info("Product $product_number: Gallery images set.");
        }

        // Handle product categories
        if (!empty($this->product_categories)) {
            $product->set_category_ids($this->select_random_categories());
        }

        if (!empty($this->product_tags)) {
            $product->set_tag_ids($this->product_tags);
        }

        // Randomize featured product status
        if ($this->featured_product) {
            // Set 30% chance for the product to be featured
            if (rand(1, 100) <= 30) {
                $product->set_featured(true);
                $this->logger->log_info("Product $product_number: Set as featured.");
            } else {
                $product->set_featured(false);
                $this->logger->log_info("Product $product_number: Not set as featured.");
            }
        }

        $product->set_weight(rand(1, 50));
        $product->set_length(rand(10, 100));
        $product->set_width(rand(10, 100));
        $product->set_height(rand(10, 100));

        // Save the product
        $product_id = $product->save();

        if ($product_id) {
            $this->logger->log_info("Product $product_number: Created successfully, ID: $product_id");
        } else {
            $this->logger->log_error("Product $product_number: Error saving the product.");
        }

        // If this is a variable product, handle the variations
        if ($this->product_type === 'variable' && $product_id) {
            $this->assign_selected_attributes($product);
        }
    }

    // Select random categories based on number_of_categories input
    protected function select_random_categories() {
        $number_to_select = min($this->number_of_categories, count($this->product_categories));

        if ($number_to_select == 1) {
            return (array) $this->product_categories[array_rand($this->product_categories)];
        } else {
            $keys = array_rand($this->product_categories, $number_to_select);
            return is_array($keys) ? array_intersect_key($this->product_categories, array_flip($keys)) : [$this->product_categories[$keys]];
        }
    }

    // Assign selected attributes to a variable product and create variations
    protected function assign_selected_attributes($product) {
        $selected_attributes = $this->selected_attributes;

        if (!empty($selected_attributes)) {
            $attributes_with_terms = CategoryHelper::get_product_attributes_and_terms();
            $product_attributes = [];
            $attribute_values = [];

            foreach ($attributes_with_terms as $attribute_data) {
                if (in_array($attribute_data['attribute_name'], $selected_attributes)) {
                    $terms = wp_list_pluck($attribute_data['terms'], 'slug');
                    if (!empty($terms)) {
                        $taxonomy_name = $attribute_data['attribute_name'];
                        $product_attribute = new WC_Product_Attribute();
                        $product_attribute->set_name($taxonomy_name);
                        $product_attribute->set_options($terms);
                        $product_attribute->set_position(0);
                        $product_attribute->set_visible(true);
                        $product_attribute->set_variation(true);
                        $product_attributes[] = $product_attribute;

                        $attribute_values[$taxonomy_name] = $terms;
                    }
                }
            }

            if (!empty($product_attributes)) {
                $product->set_attributes($product_attributes);
                $product->save();

                $variation_combinations = $this->generate_variation_combinations($attribute_values);

                foreach ($variation_combinations as $variation_attributes) {
                    $variation = new WC_Product_Variation();
                    $variation->set_parent_id($product->get_id());
                    $variation->set_attributes($variation_attributes);

                    $regular_price = rand($this->min_price, 200);
                    $sale_price = rand(10, $regular_price - 1);
                    $variation->set_regular_price($regular_price);
                    $variation->set_sale_price($sale_price);

                    // Optionally set random variation images from gallery
                    if (!empty($this->gallery_images)) {
                        $random_image_id = $this->gallery_images[array_rand($this->gallery_images)];
                        $variation->set_image_id($random_image_id);
                        $this->logger->log_info("Variation: Image set to $random_image_id.");
                    }

                    // Set the short description for each variation
                    $variation->set_description($this->short_description);

                    $variation->save();
                    $this->logger->log_info("Variation created with attributes: " . json_encode($variation_attributes));
                }
            } else {
                $this->logger->log_info("No selected attributes found for variations.");
            }
        }
    }

    // Generate all combinations of selected attribute values
    protected function generate_variation_combinations($attributes) {
        $combinations = [[]];

        foreach ($attributes as $attribute_name => $attribute_values) {
            $temp_combinations = [];

            foreach ($combinations as $combination) {
                foreach ($attribute_values as $attribute_value) {
                    $temp_combinations[] = array_merge($combination, [$attribute_name => $attribute_value]);
                }
            }

            $combinations = $temp_combinations;
        }

        return $combinations;
    }
}
