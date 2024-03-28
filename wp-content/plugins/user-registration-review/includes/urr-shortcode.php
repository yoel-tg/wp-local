<?php
require_once plugin_dir_path(__FILE__) . 'urr-db-queries.php';

class Shortcodes
{
    public function __construct()
    {
        add_shortcode('urr_reviews', array($this, 'urr_display_reviews'));
        add_shortcode('urr_registration_forms', array($this, 'urr_display_registration_forms'));
    }

    public function urr_display_reviews()
    {
        if (is_user_logged_in()) {
            ob_start();
            $userReviewModel = new UserReviewModel();
            $reviews = $userReviewModel::urr_fetch_reviews_from_database();
            $totalCount = $userReviewModel::urr_fetch_pagination_data_from_database();

            require_once USER_REGISTRATION_REVIEW_PLUGIN_DIR . 'templates/urr-review-template.php';

            return ob_get_clean();

        }
    }

    public function urr_display_registration_forms()
    {
        ob_start();

        require_once USER_REGISTRATION_REVIEW_PLUGIN_DIR . 'templates/urr-registration-template.php';

        return ob_get_clean();
    }
}

