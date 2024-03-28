<?php

class AjaxHandler
{
    public function __construct()
    {
        add_action('wp_ajax_urr_handle_user_registration_review', [$this, 'urr_handle_user_registration_review']);
        add_action('wp_ajax_nopriv_urr_handle_user_registration_review', [$this, 'urr_handle_user_registration_review']);
        add_action('wp_ajax_urr_load_review_data', [$this, 'urr_load_review_data']);
        add_action(USER_REGISTRATION_REVIEW_EMAIL_HOOK, [$this, 'urr_send_registration_email']);
    }

    function urr_handle_user_registration_review(): void
    {
        global $wpdb;
        check_ajax_referer('user_registration_review_nonce', '_wpnonce');


        // Extract form data
        parse_str($_POST['data'], $form_data);

        $form_data = array_map('sanitize_text_field', $form_data);
        // Extract username from email
        $username = sanitize_user(explode('@', $form_data['user_email'])[0]);
        if(email_exists($form_data['user_email'])) {
            $response = [
                'success' => false,
                'message' => 'Sorry Email already exists',
            ];
            wp_send_json($response, 422);

        }
        // Insert user data into the database
        $user_data = [
            'user_email' => $form_data['user_email'],
            'password' => $form_data['password'],
            'first_name' => $form_data['first_name'],
            'last_name' => $form_data['last_name'],
            'user_login' => $username, // Insert extracted username
        ];
        $user_meta_data = json_encode([
            'review' => $form_data['review'],
            'rating' => $form_data['rating'],
        ]);
        $wpdb->query('START TRANSACTION');
        $user_id = wp_insert_user($user_data);
        if (!is_wp_error($user_id)) {
            update_user_meta($user_id, 'review_rating', $user_meta_data);

            $wpdb->query('COMMIT');
            $response = [
                'success' => true,
                'message' => 'User registered successfully!',
            ];
            $code = 200;
        } else {
            $response = [
                'success' => false,
                'message' => $user_id->get_error_message(),
            ];
            $code = $user_id->get_error_code();
            $wpdb->query('ROLLBACK');
        }
        do_action(USER_REGISTRATION_REVIEW_EMAIL_HOOK, $form_data['user_email']);
        wp_send_json($response, $code);
    }

    function urr_load_review_data(): void
    {
        // Check if the AJAX request is valid
        check_ajax_referer('user_registration_review_nonce', '_wpnonce');

        if (!isset($_GET['action']) || $_GET['action'] !== 'urr_load_review_data') {
            wp_send_json_error('Invalid AJAX request');
        }
        $rating = $_GET['data']['selectedRating'] ?? intval($_GET['data']['selectedRating']);

        $page = $_GET['data']['currentPage'] ?? intval($_GET['data']['currentPage']);
        $page = ($page != 1) ? (($page - 1) * 5) : 0;

        $userReviewModel = new UserReviewModel();

        $reviews = $userReviewModel::urr_fetch_reviews_from_database($page, $rating);

        $pagination_data = $userReviewModel::urr_fetch_pagination_data_from_database($rating);
        // Send JSON response
        $response = [
            'reviews' => $reviews,
            'pagination_data' => $pagination_data
        ];
        wp_send_json_success($response);
    }

    function urr_send_registration_email($user_email): void
    {
        $email_template = file_get_contents(USER_REGISTRATION_REVIEW_PLUGIN_URL . 'templates/welcome-email-template.php');
        $email_subject = 'Welcome! ' .$user_email;
        $email_content = sprintf($email_template, $user_email);
        $headers = array('Content-Type: text/html; charset=UTF-8');

        wp_mail($user_email, $email_subject, $email_content, $headers);
    }
}


