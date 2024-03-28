<?php

class UserReviewModel
{

    public static function urr_prepare_user_data($results): array
    {
        $response = [];

        foreach ($results as $key => $res) {
            $data = $res->meta_value;

            $resReview = json_decode($data);

            $response[$res->ID]['user_login'] = esc_html($res->user_login);
            $response[$res->ID]['user_email'] = esc_html($res->user_email);
            $response[$res->ID]['review'] = $resReview->review;
            $response[$res->ID]['rating'] = $resReview->rating;

        }

        return $response;
    }

    public static function urr_fetch_reviews_from_database($currentPage = 0, $rating = 0): array
    {

        global $wpdb;
        $table_name = $wpdb->prefix . 'users';

        $query = "
		SELECT
			    wp_users.user_login,
			    wp_users.user_email,
			    wp_usermeta.meta_key,
			    wp_users.ID,
			    wp_usermeta.meta_value
			FROM wp_users
			INNER join wp_usermeta on wp_users.id = wp_usermeta.user_id
			WHERE wp_usermeta.meta_key = 'review_rating'";
        if ($rating) {
            $query .= "
            AND wp_usermeta.meta_value LIKE '%\"rating\":\"$rating\"%'
            ";
        }
        $query .= "
           ORDER BY wp_users.user_registered
                LIMIT 5 OFFSET $currentPage
       ";

        $reviews = $wpdb->get_results($query);
        return self::urr_prepare_user_data($reviews);
    }

    public static function urr_fetch_pagination_data_from_database($rating = 0): int
    {

        global $wpdb;
        $query = "
            SELECT
                  COUNT(*) as total
                FROM wp_users
                         INNER join wp_usermeta on wp_users.id = wp_usermeta.user_id
                WHERE wp_usermeta.meta_key = 'review_rating'
        ";
        if ($rating) {
            $query .= "
            AND wp_usermeta.meta_value LIKE '%\"rating\":\"$rating\"%'
            ";
        }
        $reviews = $wpdb->get_results($query);
        return $reviews[0]->total;
    }
}
