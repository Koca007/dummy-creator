<?php

class UserGenerator {
    protected $logger;
    protected $user_count;
    protected $user_role;
    protected $email_domain;

    public function __construct($logger, $user_count, $user_role, $email_domain) {
        $this->logger = $logger;
        $this->user_count = $user_count;
        $this->user_role = $user_role;
        $this->email_domain = $email_domain;
    }

    public function create_dummy_users() {
        $created_users = 0;

        for ($i = 0; $i < $this->user_count; $i++) {
            $random_username = 'dummyuser_' . wp_generate_password(4, false);
            $random_password = wp_generate_password();  // Generate a random password
            $random_email = $random_username . '@' . $this->email_domain;

            $user_id = wp_create_user($random_username, $random_password, $random_email);

            if (!is_wp_error($user_id)) {
                wp_update_user([
                    'ID' => $user_id,
                    'role' => $this->user_role
                ]);

                // Log the creation, username, and the random password for debugging
                $this->logger->log_info("User created: {$user_id} - {$random_username}, Password: {$random_password}");
                $created_users++;
            } else {
                // Log the error if the user creation fails
                $this->logger->log_error('User creation failed: ' . $user_id->get_error_message());
            }
        }

        return "{$created_users} dummy users have been created!";
    }
}
