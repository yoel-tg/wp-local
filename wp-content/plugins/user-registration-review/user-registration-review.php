<?php
/*
* Plugin Name:       User Registration Review Plugin
* Plugin URI:        https://example.com/plugins/the-basics/
* Description:       Allow user to create custom registration forms.
* Version:           0.0.1
* Requires PHP:      8.2
* Author:            Yoyal Limbu
* Author URI:        https://yoyallimbu.com/
* Text Domain:       user-registration-review
* Domain Path:       /languages
*/

class UserRegistrationReview
{

    public function __construct()
    {

        $this->init_hooks();

        $this->define_constants();

        $this->includes();

    }

    public function define_constants(): void
    {
        define('USER_REGISTRATION_REVIEW_PLUGIN_DIR', plugin_dir_path(__FILE__));
        define('USER_REGISTRATION_REVIEW_PLUGIN_URL', plugin_dir_url(__FILE__));
        define("USER_REGISTRATION_REVIEW_EMAIL_HOOK", 'user_registration_email_sent');
    }


    public function user_registration_review_enqueue_styles(): void
    {
        wp_enqueue_style('user-registration-review-style', USER_REGISTRATION_REVIEW_PLUGIN_URL . 'css/user-registration-review-style.css');
        wp_enqueue_style('toast-style', USER_REGISTRATION_REVIEW_PLUGIN_URL . 'css/toastify.css');
    }


    public function init_hooks(): void
    {

        add_action('init', array($this, 'initialize'));
        add_action('wp_enqueue_scripts', array($this, 'user_registration_review_enqueue_styles'));
        add_action('wp_enqueue_scripts', array($this, 'user_registration_review_enqueue_scripts'));

    }

    function user_registration_review_enqueue_scripts(): void
    {
        // Enqueue JavaScript file for AJAX handling
        wp_enqueue_script('user-registration-review-script', USER_REGISTRATION_REVIEW_PLUGIN_URL . 'js/user-registration-review-script.js', array('jquery'), false, true);
        wp_enqueue_script('toast-script', USER_REGISTRATION_REVIEW_PLUGIN_URL . 'js/toastify.js'); //js toast library
        wp_localize_script('user-registration-review-script', 'user_registration_review', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('user_registration_review_nonce')
        ));
    }

    public function initialize(): void
    {
        new Shortcodes();
        new AjaxHandler();
    }

    public function includes(): void
    {
        require_once(USER_REGISTRATION_REVIEW_PLUGIN_DIR . 'includes/urr-ajax-handlers.php');
        require_once(USER_REGISTRATION_REVIEW_PLUGIN_DIR . 'includes/urr-shortcode.php');
        require_once(USER_REGISTRATION_REVIEW_PLUGIN_DIR . 'includes/urr-db-queries.php');
    }


}

new UserRegistrationReview();


