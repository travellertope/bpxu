<?php
/**
 * Plugin Name: BPU Headless Connector
 * Plugin URI: https://blackprofessionals.uk
 * Description: Custom Headless API connector for Black Professionals United (BPU). Provides Cross-Subdomain SSO verification, SSO Token Relay for PAIRED, headless Job Board Click Tracking, headless Tutor LMS progress triggers, Gemini Pro AI CV parsing, CV Clinic manual reviews dashboard, Mentor Directory endpoints, and Mentorship Booking system.
 * Version: 2.0.0
 * Author: Antigravity AI & BPU Tech Team
 * Author URI: https://blackprofessionals.uk
 * License: GPL2
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class BPU_Headless_Connector {

    private $namespace = 'bpu/v1';

    /**
     * Allowed redirect origins for SSO handoff.
     */
    private $allowed_sso_origins = array(
        'https://app.blackprofessionals.uk',
        'https://pairedbybpu.uk',
        'https://www.pairedbybpu.uk',
    );

    public function __construct() {
        // Register custom post types (CV Reviews + Mentorship Bookings)
        add_action( 'init', array( $this, 'register_cv_review_post_type' ) );
        add_action( 'init', array( $this, 'register_mentorship_booking_post_type' ) );
        add_action( 'init', array( $this, 'register_paired_session_post_type' ) );
        add_action( 'init', array( $this, 'register_mentor_experience_post_type' ) );
        add_action( 'init', array( $this, 'register_mentor_education_post_type' ) );
        add_action( 'init', array( $this, 'register_mentor_review_post_type' ) );
        add_action( 'init', array( $this, 'register_paired_message_post_type' ) );
        add_action( 'init', array( $this, 'register_paired_notification_post_type' ) );
        add_action( 'init', array( $this, 'register_paired_referral_post_type' ) );
        add_action( 'init', array( $this, 'register_bpu_coupon_post_type' ) );

        // Register bpu_pro role
        add_action( 'init', array( $this, 'register_pro_role' ) );

        // Register mentor role
        add_action( 'init', array( $this, 'register_mentor_role' ) );

        // SSO token relay handoff (runs on every page load, checks for handoff query param)
        add_action( 'init', array( $this, 'handle_sso_handoff' ) );

        // Register API routes
        add_action( 'rest_api_init', array( $this, 'register_api_routes' ) );

        // CORS headers for REST API responses
        add_filter( 'rest_pre_serve_request', array( $this, 'add_cors_headers' ), 10, 4 );

        // CORS and Cookie domain reminders in Admin Panel
        add_action( 'admin_notices', array( $this, 'display_cookie_domain_check' ) );

        // Hook when manual CV review is published to trigger alerts
        add_action( 'publish_cv_clinic_review', array( $this, 'notify_candidate_of_cv_review' ), 10, 2 );

        // Notify pro members when a new job is published
        add_action( 'publish_job_listing', array( $this, 'notify_matched_members_of_new_job' ), 10, 2 );

        // Notify matched mentees when a user is promoted to the mentor role
        add_action( 'set_user_role', array( $this, 'notify_matched_mentees_of_new_mentor' ), 10, 3 );

        // Weekly job digest (WP Cron)
        add_filter( 'cron_schedules', array( $this, 'add_weekly_cron_schedule' ) );
        add_action( 'bpu_weekly_job_digest', array( $this, 'send_weekly_job_digests' ) );

        // Sync bpu_pro role with WooCommerce Subscription status
        add_action( 'woocommerce_subscription_status_active',     array( $this, 'on_subscription_activated' ) );
        add_action( 'woocommerce_subscription_status_cancelled',  array( $this, 'on_subscription_deactivated' ) );
        add_action( 'woocommerce_subscription_status_expired',    array( $this, 'on_subscription_deactivated' ) );
        add_action( 'woocommerce_subscription_status_on-hold',    array( $this, 'on_subscription_deactivated' ) );

        // Job platform CPTs and roles
        add_action( 'init', array( $this, 'register_job_post_type' ) );
        add_action( 'init', array( $this, 'register_job_application_post_type' ) );
        add_action( 'init', array( $this, 'register_employer_role' ) );
        add_action( 'init', array( $this, 'register_employer_taxonomy' ) );
        add_action( 'add_meta_boxes', array( $this, 'register_job_meta_boxes' ) );
        add_action( 'save_post_bpu_job', array( $this, 'save_job_meta_boxes' ), 10, 2 );
        add_action( 'admin_menu', array( $this, 'register_employer_link_admin_page' ) );
        add_action( 'admin_menu', array( $this, 'register_job_reports_admin_page' ) );
        add_action( 'admin_menu', array( $this, 'register_spam_cleanup_admin_page' ) );
        add_filter( 'manage_bpu_job_posts_columns',       array( $this, 'job_list_columns' ) );
        add_action( 'manage_bpu_job_posts_custom_column', array( $this, 'job_list_column_content' ), 10, 2 );
        add_action( 'wp_ajax_bpu_reports_export_csv', array( $this, 'ajax_reports_export_csv' ) );
        add_action( 'wp_ajax_bpu_delete_spam_users', array( $this, 'ajax_delete_spam_users' ) );
        add_action( 'wp_ajax_bpu_employer_search_users', array( $this, 'ajax_employer_search_users' ) );
        add_action( 'wp_ajax_bpu_employer_link_user',   array( $this, 'ajax_employer_link_user' ) );
        add_action( 'wp_ajax_bpu_employer_unlink_user', array( $this, 'ajax_employer_unlink_user' ) );
    }

    /**
     * Register Custom Post Type for Manual CV Reviews
     */
    public function register_cv_review_post_type() {
        $labels = array(
            'name'               => _x( 'CV Reviews', 'post type general name', 'bpu' ),
            'singular_name'      => _x( 'CV Review', 'post type singular name', 'bpu' ),
            'menu_name'          => _x( 'CV Clinic Reviews', 'admin menu', 'bpu' ),
            'add_new'            => _x( 'Add New Review', 'review', 'bpu' ),
            'add_new_item'       => __( 'Add New CV Critique Review', 'bpu' ),
            'edit_item'          => __( 'Edit CV Review', 'bpu' ),
            'new_item'           => __( 'New CV Review', 'bpu' ),
            'all_items'          => __( 'All Reviews', 'bpu' ),
            'view_item'          => __( 'View Review', 'bpu' ),
            'search_items'       => __( 'Search Reviews', 'bpu' ),
            'not_found'          => __( 'No reviews found', 'bpu' ),
            'not_found_in_trash' => __( 'No reviews found in Trash', 'bpu' ),
        );

        $args = array(
            'labels'             => $labels,
            'public'             => false, // Internal backend only
            'publicly_queryable' => false,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array( 'slug' => 'cv-clinic-review' ),
            'capability_type'    => 'post',
            'has_archive'        => false,
            'hierarchical'       => false,
            'menu_position'      => 28,
            'menu_icon'          => 'dashicons-welcome-learn-more',
            'supports'           => array( 'title', 'editor', 'custom-fields' ),
        );

        register_post_type( 'cv_clinic_review', $args );
    }

    /**
     * Register Custom Post Type for Mentorship Bookings
     */
    public function register_mentorship_booking_post_type() {
        $labels = array(
            'name'               => _x( 'Mentorship Bookings', 'post type general name', 'bpu' ),
            'singular_name'      => _x( 'Mentorship Booking', 'post type singular name', 'bpu' ),
            'menu_name'          => _x( 'Mentorship Bookings', 'admin menu', 'bpu' ),
            'add_new'            => _x( 'Add New Booking', 'booking', 'bpu' ),
            'add_new_item'       => __( 'Add New Mentorship Booking', 'bpu' ),
            'edit_item'          => __( 'Edit Booking', 'bpu' ),
            'new_item'           => __( 'New Booking', 'bpu' ),
            'all_items'          => __( 'All Bookings', 'bpu' ),
            'view_item'          => __( 'View Booking', 'bpu' ),
            'search_items'       => __( 'Search Bookings', 'bpu' ),
            'not_found'          => __( 'No bookings found', 'bpu' ),
            'not_found_in_trash' => __( 'No bookings found in Trash', 'bpu' ),
        );

        $args = array(
            'labels'             => $labels,
            'public'             => false,
            'publicly_queryable' => false,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array( 'slug' => 'mentorship-booking' ),
            'capability_type'    => 'post',
            'has_archive'        => false,
            'hierarchical'       => false,
            'menu_position'      => 29,
            'menu_icon'          => 'dashicons-calendar-alt',
            'supports'           => array( 'title', 'custom-fields' ),
        );

        register_post_type( 'mentorship_booking', $args );
    }

    public function register_paired_session_post_type() {
        register_post_type( 'paired_session', array(
            'labels'              => array(
                'name'          => _x( 'Mentor Sessions', 'post type general name', 'bpu' ),
                'singular_name' => _x( 'Mentor Session', 'post type singular name', 'bpu' ),
                'menu_name'     => _x( 'Mentor Sessions', 'admin menu', 'bpu' ),
            ),
            'public'              => false,
            'publicly_queryable'  => false,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'query_var'           => true,
            'rewrite'             => array( 'slug' => 'paired-session' ),
            'capability_type'     => 'post',
            'has_archive'         => false,
            'hierarchical'        => false,
            'menu_position'       => 30,
            'menu_icon'           => 'dashicons-welcome-learn-more',
            'supports'            => array( 'title', 'custom-fields' ),
        ) );
    }

    public function register_mentor_experience_post_type() {
        register_post_type( 'mentor_experience', array(
            'labels'              => array(
                'name'          => _x( 'Mentor Experiences', 'post type general name', 'bpu' ),
                'singular_name' => _x( 'Mentor Experience', 'post type singular name', 'bpu' ),
            ),
            'public'              => false,
            'publicly_queryable'  => false,
            'show_ui'             => false,
            'capability_type'     => 'post',
            'has_archive'         => false,
            'hierarchical'        => false,
            'supports'            => array( 'title', 'custom-fields' ),
        ) );
    }

    public function register_mentor_education_post_type() {
        register_post_type( 'mentor_education', array(
            'labels'              => array(
                'name'          => _x( 'Mentor Education', 'post type general name', 'bpu' ),
                'singular_name' => _x( 'Mentor Education Entry', 'post type singular name', 'bpu' ),
            ),
            'public'              => false,
            'publicly_queryable'  => false,
            'show_ui'             => false,
            'capability_type'     => 'post',
            'has_archive'         => false,
            'hierarchical'        => false,
            'supports'            => array( 'title', 'custom-fields' ),
        ) );
    }

    public function register_mentor_review_post_type() {
        register_post_type( 'mentor_review', array(
            'labels'              => array(
                'name'          => _x( 'Mentor Reviews', 'post type general name', 'bpu' ),
                'singular_name' => _x( 'Mentor Review', 'post type singular name', 'bpu' ),
            ),
            'public'              => false,
            'publicly_queryable'  => false,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'capability_type'     => 'post',
            'has_archive'         => false,
            'hierarchical'        => false,
            'menu_position'       => 31,
            'menu_icon'           => 'dashicons-star-filled',
            'supports'            => array( 'title', 'custom-fields' ),
        ) );
    }

    public function register_paired_message_post_type() {
        register_post_type( 'paired_message', array(
            'labels'              => array(
                'name'          => _x( 'Messages', 'post type general name', 'bpu' ),
                'singular_name' => _x( 'Message', 'post type singular name', 'bpu' ),
            ),
            'public'              => false,
            'publicly_queryable'  => false,
            'show_ui'             => false,
            'capability_type'     => 'post',
            'has_archive'         => false,
            'hierarchical'        => false,
            'supports'            => array( 'title', 'custom-fields' ),
        ) );
    }

    public function register_paired_notification_post_type() {
        register_post_type( 'paired_notification', array(
            'labels'              => array(
                'name'          => _x( 'Notifications', 'post type general name', 'bpu' ),
                'singular_name' => _x( 'Notification', 'post type singular name', 'bpu' ),
            ),
            'public'              => false,
            'publicly_queryable'  => false,
            'show_ui'             => false,
            'capability_type'     => 'post',
            'has_archive'         => false,
            'hierarchical'        => false,
            'supports'            => array( 'title', 'custom-fields' ),
        ) );
    }

    public function register_paired_referral_post_type() {
        register_post_type( 'paired_referral', array(
            'labels'              => array(
                'name'          => _x( 'Referrals', 'post type general name', 'bpu' ),
                'singular_name' => _x( 'Referral', 'post type singular name', 'bpu' ),
            ),
            'public'              => false,
            'publicly_queryable'  => false,
            'show_ui'             => false,
            'capability_type'     => 'post',
            'has_archive'         => false,
            'hierarchical'        => false,
            'supports'            => array( 'title', 'custom-fields' ),
        ) );
    }

    public function register_bpu_coupon_post_type() {
        register_post_type( 'bpu_coupon', array(
            'labels'              => array(
                'name'          => _x( 'Coupons', 'post type general name', 'bpu' ),
                'singular_name' => _x( 'Coupon', 'post type singular name', 'bpu' ),
            ),
            'public'              => false,
            'publicly_queryable'  => false,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'capability_type'     => 'post',
            'has_archive'         => false,
            'hierarchical'        => false,
            'menu_position'       => 30,
            'menu_icon'           => 'dashicons-tag',
            'supports'            => array( 'title', 'custom-fields' ),
        ) );
    }

    /**
     * Register the bpu_pro role (idempotent).
     */
    public function register_pro_role() {
        if ( ! get_role( 'bpu_pro' ) ) {
            add_role( 'bpu_pro', __( 'BPU Pro Member', 'bpu' ), array( 'read' => true ) );
        }
    }

    /**
     * Register the mentor role (idempotent).
     */
    public function register_mentor_role() {
        if ( ! get_role( 'mentor' ) ) {
            add_role( 'mentor', __( 'Mentor', 'bpu' ), array( 'read' => true ) );
        }
    }

    /**
     * Returns true if user_id is a Pro member.
     * Checks (in order): administrator role, bpu_pro role, active WooCommerce Subscription.
     */
    private function is_pro_member( int $user_id ): bool {
        $user = get_userdata( $user_id );
        if ( ! $user ) {
            return false;
        }
        $roles = (array) $user->roles;
        if ( in_array( 'administrator', $roles, true ) ) {
            return true;
        }
        if ( in_array( 'bpu_pro', $roles, true ) ) {
            return true;
        }
        // Check WooCommerce Subscriptions if available
        if ( function_exists( 'wcs_user_has_subscription' ) ) {
            return (bool) wcs_user_has_subscription( $user_id, '', 'active' );
        }
        return false;
    }

    /** Auto-assign bpu_pro role when a WooCommerce Subscription becomes active. */
    public function on_subscription_activated( $subscription ) {
        $user_id = is_callable( array( $subscription, 'get_user_id' ) )
            ? $subscription->get_user_id()
            : ( $subscription->user_id ?? 0 );
        if ( $user_id ) {
            $user = new WP_User( $user_id );
            $user->add_role( 'bpu_pro' );
        }
    }

    /** Remove bpu_pro role when all WooCommerce Subscriptions are inactive. */
    public function on_subscription_deactivated( $subscription ) {
        $user_id = is_callable( array( $subscription, 'get_user_id' ) )
            ? $subscription->get_user_id()
            : ( $subscription->user_id ?? 0 );
        if ( ! $user_id ) {
            return;
        }
        // Only strip role if no remaining active subscriptions
        if ( function_exists( 'wcs_user_has_subscription' ) && ! wcs_user_has_subscription( $user_id, '', 'active' ) ) {
            $user = new WP_User( $user_id );
            $user->remove_role( 'bpu_pro' );
        }
    }

    /**
     * Register Custom REST API routes
     */
    public function register_api_routes() {
        
        // 1. SSO Session Verification
        register_rest_route( $this->namespace, '/sso/validate', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( $this, 'validate_sso_session' ),
            'permission_callback' => '__return_true', 
        ) );

        // 2. Job Click Tracking (For guest and authenticated members)
        register_rest_route( $this->namespace, '/track-click', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array( $this, 'track_job_click' ),
            'permission_callback' => '__return_true',
            'args'                => array(
                'job_id' => array(
                    'required'          => true,
                    'sanitize_callback' => 'absint',
                ),
                'user_id' => array(
                    'required'          => false,
                    'sanitize_callback' => 'absint',
                ),
            ),
        ) );

        // 3. Tutor LMS Course Progress Trigger
        register_rest_route( $this->namespace, '/courses/progress', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array( $this, 'track_course_progress' ),
            'permission_callback' => array( $this, 'check_auth_permissions' ),
            'args'                => array(
                'course_id' => array(
                    'required'          => true,
                    'sanitize_callback' => 'absint',
                ),
            ),
        ) );

        // 3b. Course listing (always-public, works with or without Tutor LMS REST API)
        register_rest_route( $this->namespace, '/courses', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( $this, 'get_courses' ),
            'permission_callback' => '__return_true',
            'args'                => array(
                'per_page' => array( 'default' => 50, 'sanitize_callback' => 'absint' ),
                'page'     => array( 'default' => 1,  'sanitize_callback' => 'absint' ),
                'search'   => array( 'default' => '', 'sanitize_callback' => 'sanitize_text_field' ),
                'category' => array( 'default' => '', 'sanitize_callback' => 'sanitize_text_field' ),
                'tag'      => array( 'default' => '', 'sanitize_callback' => 'sanitize_text_field' ),
            ),
        ) );

        // 3c. Enrolled courses for the authenticated member (JWT Bearer)
        register_rest_route( $this->namespace, '/member/enrolled-courses', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( $this, 'get_enrolled_courses' ),
            'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
        ) );

        // 4. CV Upload & Gemini Pro Autofill parser (Pro — JWT Bearer)
        register_rest_route( $this->namespace, '/member/cv-upload', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array( $this, 'handle_member_cv_upload' ),
            'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
        ) );

        // 4b. CV Analyzer — instant ATS feedback (Free — JWT Bearer)
        register_rest_route( $this->namespace, '/member/cv-analyze', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array( $this, 'handle_member_cv_analyze' ),
            'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
        ) );

        // 5. Get Member CV Reviews List
        register_rest_route( $this->namespace, '/member/cv-reviews', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( $this, 'get_member_cv_reviews' ),
            'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
        ) );

        // ──────────────────────────────────────────────────────
        // 6. SSO Token Exchange (for PAIRED mentorship app)
        // ──────────────────────────────────────────────────────
        register_rest_route( $this->namespace, '/sso/exchange', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array( $this, 'exchange_sso_token' ),
            'permission_callback' => '__return_true',
            'args'                => array(
                'token' => array(
                    'required'          => true,
                    'type'              => 'string',
                    'sanitize_callback' => 'sanitize_text_field',
                    'validate_callback' => function ( $value ) {
                        return is_string( $value ) && preg_match( '/^[a-f0-9]{64}$/', $value );
                    },
                ),
            ),
        ) );

        // ──────────────────────────────────────────────────────
        // 6c. Member Profile Update (JWT Bearer auth)
        // ──────────────────────────────────────────────────────
        register_rest_route( $this->namespace, '/member/profile', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array( $this, 'update_member_profile' ),
            'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
        ) );

        // ──────────────────────────────────────────────────────
        // 6d. In-app Login (public — returns JWT)
        // ──────────────────────────────────────────────────────
        register_rest_route( $this->namespace, '/auth/login', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array( $this, 'handle_login' ),
            'permission_callback' => '__return_true',
        ) );

        // ──────────────────────────────────────────────────────
        // 6e. In-app Register (public — creates account, returns JWT)
        // ──────────────────────────────────────────────────────
        register_rest_route( $this->namespace, '/auth/register', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array( $this, 'handle_register' ),
            'permission_callback' => '__return_true',
        ) );

        // ──────────────────────────────────────────────────────
        // 6f. Forgot Password (public — sends reset email)
        // ──────────────────────────────────────────────────────
        register_rest_route( $this->namespace, '/auth/forgot-password', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array( $this, 'handle_forgot_password' ),
            'permission_callback' => '__return_true',
        ) );

        // ──────────────────────────────────────────────────────
        // 6g. Reset Password (public — validates token, updates password)
        // ──────────────────────────────────────────────────────
        register_rest_route( $this->namespace, '/auth/reset-password', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array( $this, 'handle_reset_password' ),
            'permission_callback' => '__return_true',
        ) );

        // ──────────────────────────────────────────────────────
        // 6b. SSO Profile (JWT Bearer auth — for Next.js profile refresh)
        // ──────────────────────────────────────────────────────
        register_rest_route( $this->namespace, '/sso/profile', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( $this, 'get_sso_profile' ),
            'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
        ) );

        // ──────────────────────────────────────────────────────
        // 7. Mentor Directory (public)
        // ──────────────────────────────────────────────────────
        register_rest_route( $this->namespace, '/mentors', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( $this, 'get_mentors' ),
            'permission_callback' => '__return_true',
            'args'                => array(
                'page' => array(
                    'default'           => 1,
                    'sanitize_callback' => 'absint',
                ),
                'per_page' => array(
                    'default'           => 12,
                    'sanitize_callback' => 'absint',
                ),
                'industry' => array(
                    'default'           => '',
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                'search' => array(
                    'default'           => '',
                    'sanitize_callback' => 'sanitize_text_field',
                ),
            ),
        ) );

        // 7b. Single Mentor Profile (public)
        register_rest_route( $this->namespace, '/mentors/(?P<id>\d+)', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( $this, 'get_single_mentor' ),
            'permission_callback' => '__return_true',
            'args'                => array(
                'id' => array(
                    'required'          => true,
                    'sanitize_callback' => 'absint',
                    'validate_callback' => function ( $value ) {
                        return is_numeric( $value ) && intval( $value ) > 0;
                    },
                ),
            ),
        ) );

        // ──────────────────────────────────────────────────────
        // 8. Mentorship Bookings (authenticated)
        // ──────────────────────────────────────────────────────
        register_rest_route( $this->namespace, '/bookings', array(
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array( $this, 'create_booking' ),
                'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
                'args'                => array(
                    'mentor_id' => array(
                        'required'          => true,
                        'sanitize_callback' => 'absint',
                        'validate_callback' => function ( $value ) {
                            return is_numeric( $value ) && intval( $value ) > 0;
                        },
                    ),
                    'date' => array(
                        'required'          => true,
                        'sanitize_callback' => 'sanitize_text_field',
                        'validate_callback' => function ( $value ) {
                            // Accepts YYYY-MM-DD, must be today or in the future
                            $d = DateTime::createFromFormat( 'Y-m-d', $value );
                            return $d && $d->format( 'Y-m-d' ) === $value;
                        },
                    ),
                    'time_slot' => array(
                        'required'          => true,
                        'sanitize_callback' => 'sanitize_text_field',
                        'validate_callback' => function ( $value ) {
                            // Accepts HH:MM-HH:MM format
                            return (bool) preg_match( '/^\d{2}:\d{2}-\d{2}:\d{2}$/', $value );
                        },
                    ),
                    'session_id' => array(
                        'required'          => false,
                        'default'           => 0,
                        'sanitize_callback' => 'absint',
                    ),
                    'notes' => array(
                        'required'          => false,
                        'default'           => '',
                        'sanitize_callback' => 'sanitize_textarea_field',
                    ),
                ),
            ),
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_bookings' ),
                'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
                'args'                => array(
                    'page' => array(
                        'default'           => 1,
                        'sanitize_callback' => 'absint',
                    ),
                    'per_page' => array(
                        'default'           => 20,
                        'sanitize_callback' => 'absint',
                    ),
                ),
            ),
        ) );

        // 8b. Single Booking
        register_rest_route( $this->namespace, '/bookings/(?P<id>\d+)', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( $this, 'get_single_booking' ),
            'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
            'args'                => array(
                'id' => array(
                    'required'          => true,
                    'sanitize_callback' => 'absint',
                ),
            ),
        ) );

        // ──────────────────────────────────────────────────────
        // 9. Events (The Events Calendar — public)
        // ──────────────────────────────────────────────────────
        register_rest_route( $this->namespace, '/events', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( $this, 'get_events' ),
            'permission_callback' => '__return_true',
            'args'                => array(
                'page'     => array( 'default' => 1,  'sanitize_callback' => 'absint' ),
                'per_page' => array( 'default' => 12, 'sanitize_callback' => 'absint' ),
            ),
        ) );

        // ──────────────────────────────────────────────────────
        // 9b. Member Registered Events (JWT Bearer)
        // ──────────────────────────────────────────────────────
        register_rest_route( $this->namespace, '/member/registered-events', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( $this, 'get_registered_events' ),
            'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
        ) );

        // ──────────────────────────────────────────────────────
        // 10. Member Preferences (Pro — JWT Bearer)
        // ──────────────────────────────────────────────────────
        register_rest_route( $this->namespace, '/member/preferences', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array( $this, 'update_member_preferences' ),
            'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
        ) );

        // ──────────────────────────────────────────────────────
        // 11. Request CV Review (Pro — JWT Bearer)
        // ──────────────────────────────────────────────────────
        register_rest_route( $this->namespace, '/member/request-cv-review', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array( $this, 'handle_request_cv_review' ),
            'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
        ) );

        // ──────────────────────────────────────────────────────
        // 12. Job Platform
        // ──────────────────────────────────────────────────────
        register_rest_route( $this->namespace, '/jobs', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_jobs' ),
                'permission_callback' => '__return_true',
                'args'                => array(
                    'page'     => array( 'default' => 1,  'sanitize_callback' => 'absint' ),
                    'per_page' => array( 'default' => 20, 'sanitize_callback' => 'absint' ),
                    'job_type' => array( 'default' => '',  'sanitize_callback' => 'sanitize_text_field' ),
                    'industry' => array( 'default' => '',  'sanitize_callback' => 'sanitize_text_field' ),
                    'search'   => array( 'default' => '',  'sanitize_callback' => 'sanitize_text_field' ),
                ),
            ),
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array( $this, 'create_job' ),
                'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
            ),
        ) );

        register_rest_route( $this->namespace, '/jobs/(?P<id>\d+)', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_single_job' ),
                'permission_callback' => '__return_true',
                'args'                => array( 'id' => array( 'required' => true, 'sanitize_callback' => 'absint' ) ),
            ),
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => array( $this, 'update_job' ),
                'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
            ),
            array(
                'methods'             => WP_REST_Server::DELETABLE,
                'callback'            => array( $this, 'delete_job' ),
                'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
            ),
        ) );

        register_rest_route( $this->namespace, '/jobs/(?P<id>\d+)/click', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array( $this, 'track_job_outbound_click' ),
            'permission_callback' => '__return_true',
        ) );

        register_rest_route( $this->namespace, '/jobs/(?P<id>\d+)/apply', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array( $this, 'submit_job_application' ),
            'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
        ) );

        register_rest_route( $this->namespace, '/jobs/(?P<id>\d+)/applications', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( $this, 'get_job_applications' ),
            'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
        ) );

        register_rest_route( $this->namespace, '/employer/register', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array( $this, 'handle_employer_register' ),
            'permission_callback' => '__return_true',
        ) );

        register_rest_route( $this->namespace, '/employer/jobs', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( $this, 'get_employer_jobs' ),
            'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
        ) );

        register_rest_route( $this->namespace, '/employer/profile', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( $this, 'get_employer_profile' ),
            'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
        ) );

        register_rest_route( $this->namespace, '/employer/profile', array(
            'methods'             => WP_REST_Server::EDITABLE,
            'callback'            => array( $this, 'update_employer_profile' ),
            'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
        ) );

        register_rest_route( $this->namespace, '/employer/logo', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array( $this, 'upload_employer_logo' ),
            'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
        ) );

        register_rest_route( $this->namespace, '/paired/mentor-apply', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array( $this, 'submit_mentor_application' ),
            'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
        ) );

        register_rest_route( $this->namespace, '/paired/mentor-approve/(?P<user_id>\d+)', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array( $this, 'approve_mentor_application' ),
            'permission_callback' => array( $this, 'check_admin_jwt_auth' ),
        ) );

        register_rest_route( $this->namespace, '/paired/mentor-reject/(?P<user_id>\d+)', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array( $this, 'reject_mentor_application' ),
            'permission_callback' => array( $this, 'check_admin_jwt_auth' ),
        ) );

        register_rest_route( $this->namespace, '/paired/mentor-applications', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( $this, 'list_mentor_applications' ),
            'permission_callback' => function ( $request ) {
                return $this->check_jwt_bearer_auth( $request ) && current_user_can( 'promote_users' );
            },
        ) );

        // ── Mentor self-service endpoints ─────────────────────────

        // Mentor own profile (GET / PUT)
        register_rest_route( $this->namespace, '/paired/mentor/profile', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_mentor_own_profile' ),
                'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
            ),
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => array( $this, 'update_mentor_profile' ),
                'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
            ),
        ) );

        // Mentor's mentees list
        register_rest_route( $this->namespace, '/paired/mentor/mentees', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( $this, 'get_mentor_mentees' ),
            'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
        ) );

        // Mentor stats
        register_rest_route( $this->namespace, '/paired/mentor/stats', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( $this, 'get_mentor_stats' ),
            'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
        ) );

        // Update booking status (mentor only)
        register_rest_route( $this->namespace, '/bookings/(?P<id>\d+)/status', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array( $this, 'update_booking_status' ),
            'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
            'args'                => array(
                'id'     => array( 'required' => true, 'sanitize_callback' => 'absint' ),
                'status' => array( 'required' => true, 'sanitize_callback' => 'sanitize_text_field' ),
            ),
        ) );

        // ── Mentor session types ─────────────────────────────
        register_rest_route( $this->namespace, '/paired/mentor/sessions', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_mentor_sessions' ),
                'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
            ),
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array( $this, 'create_mentor_session' ),
                'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
            ),
        ) );

        register_rest_route( $this->namespace, '/paired/mentor/sessions/(?P<id>\d+)', array(
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => array( $this, 'update_mentor_session' ),
                'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
                'args'                => array( 'id' => array( 'required' => true, 'sanitize_callback' => 'absint' ) ),
            ),
            array(
                'methods'             => WP_REST_Server::DELETABLE,
                'callback'            => array( $this, 'delete_mentor_session' ),
                'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
                'args'                => array( 'id' => array( 'required' => true, 'sanitize_callback' => 'absint' ) ),
            ),
        ) );

        // Public: list mentor's visible sessions
        register_rest_route( $this->namespace, '/paired/mentors/(?P<id>\d+)/sessions', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( $this, 'get_public_mentor_sessions' ),
            'permission_callback' => '__return_true',
            'args'                => array( 'id' => array( 'required' => true, 'sanitize_callback' => 'absint' ) ),
        ) );

        // ── Session-specific custom hours ───────────────────
        register_rest_route( $this->namespace, '/paired/mentor/sessions/(?P<id>\d+)/hours', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_session_custom_hours' ),
                'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
                'args'                => array( 'id' => array( 'required' => true, 'sanitize_callback' => 'absint' ) ),
            ),
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => array( $this, 'set_session_custom_hours' ),
                'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
                'args'                => array( 'id' => array( 'required' => true, 'sanitize_callback' => 'absint' ) ),
            ),
        ) );

        // ── Work experience CRUD ─────────────────────────────
        register_rest_route( $this->namespace, '/paired/mentor/experiences', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_mentor_experiences' ),
                'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
            ),
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array( $this, 'create_mentor_experience' ),
                'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
            ),
        ) );

        register_rest_route( $this->namespace, '/paired/mentor/experiences/(?P<id>\d+)', array(
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => array( $this, 'update_mentor_experience' ),
                'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
                'args'                => array( 'id' => array( 'required' => true, 'sanitize_callback' => 'absint' ) ),
            ),
            array(
                'methods'             => WP_REST_Server::DELETABLE,
                'callback'            => array( $this, 'delete_mentor_experience' ),
                'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
                'args'                => array( 'id' => array( 'required' => true, 'sanitize_callback' => 'absint' ) ),
            ),
        ) );

        // ── Education CRUD ───────────────────────────────────
        register_rest_route( $this->namespace, '/paired/mentor/education', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_mentor_education' ),
                'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
            ),
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array( $this, 'create_mentor_education' ),
                'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
            ),
        ) );

        register_rest_route( $this->namespace, '/paired/mentor/education/(?P<id>\d+)', array(
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => array( $this, 'update_mentor_education' ),
                'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
                'args'                => array( 'id' => array( 'required' => true, 'sanitize_callback' => 'absint' ) ),
            ),
            array(
                'methods'             => WP_REST_Server::DELETABLE,
                'callback'            => array( $this, 'delete_mentor_education' ),
                'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
                'args'                => array( 'id' => array( 'required' => true, 'sanitize_callback' => 'absint' ) ),
            ),
        ) );

        // ── Availability schedule ────────────────────────────
        register_rest_route( $this->namespace, '/paired/mentor/availability', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_mentor_availability' ),
                'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
            ),
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => array( $this, 'update_mentor_availability' ),
                'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
            ),
        ) );

        // Public: available slots for a mentor
        register_rest_route( $this->namespace, '/paired/mentors/(?P<id>\d+)/slots', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( $this, 'get_mentor_available_slots' ),
            'permission_callback' => '__return_true',
            'args'                => array(
                'id'       => array( 'required' => true, 'sanitize_callback' => 'absint' ),
                'date'     => array( 'sanitize_callback' => 'sanitize_text_field' ),
                'duration' => array( 'sanitize_callback' => 'absint' ),
            ),
        ) );

        // ── Skills list (hardcoded) ──────────────────────────
        register_rest_route( $this->namespace, '/paired/skills', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( $this, 'get_paired_skills' ),
            'permission_callback' => '__return_true',
        ) );

        // ── Profile photo upload ─────────────────────────────
        register_rest_route( $this->namespace, '/paired/mentor/photo', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array( $this, 'upload_mentor_photo' ),
            'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
        ) );

        // ── Phase 2: Reviews & Ratings ──────────────────────────
        register_rest_route( $this->namespace, '/paired/reviews', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array( $this, 'submit_mentor_review' ),
            'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
        ) );

        register_rest_route( $this->namespace, '/paired/mentors/(?P<id>\d+)/reviews', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( $this, 'get_mentor_reviews' ),
            'permission_callback' => '__return_true',
        ) );

        // ── Phase 2: In-App Messaging ───────────────────────────
        register_rest_route( $this->namespace, '/paired/messages', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_message_conversations' ),
                'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
            ),
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array( $this, 'send_message' ),
                'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
            ),
        ) );

        register_rest_route( $this->namespace, '/paired/messages/(?P<user_id>\d+)', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( $this, 'get_message_thread' ),
            'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
        ) );

        // ── Phase 2: In-App Notifications ───────────────────────
        register_rest_route( $this->namespace, '/paired/notifications', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( $this, 'get_notifications' ),
            'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
        ) );

        register_rest_route( $this->namespace, '/paired/notifications/(?P<id>\d+)/read', array(
            'methods'             => 'PUT',
            'callback'            => array( $this, 'mark_notification_read' ),
            'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
        ) );

        register_rest_route( $this->namespace, '/paired/notifications/read-all', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array( $this, 'mark_all_notifications_read' ),
            'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
        ) );

        // ── Phase 2: Favourite Mentors ──────────────────────────
        register_rest_route( $this->namespace, '/paired/favourites', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_favourites' ),
                'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
            ),
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array( $this, 'add_favourite' ),
                'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
            ),
        ) );

        register_rest_route( $this->namespace, '/paired/favourites/(?P<mentor_id>\d+)', array(
            'methods'             => WP_REST_Server::DELETABLE,
            'callback'            => array( $this, 'remove_favourite' ),
            'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
        ) );

        // ── Phase 2: Mentee Profile ─────────────────────────────
        register_rest_route( $this->namespace, '/paired/mentee/profile', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_mentee_profile' ),
                'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
            ),
            array(
                'methods'             => 'PUT',
                'callback'            => array( $this, 'update_mentee_profile' ),
                'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
            ),
        ) );

        // ── Change Password ──────────────────────────────────────
        register_rest_route( $this->namespace, '/paired/account/change-password', array(
            'methods'             => 'POST',
            'callback'            => array( $this, 'change_password' ),
            'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
        ) );

        // ── Phase 2: Admin Mentor Management ────────────────────
        register_rest_route( $this->namespace, '/paired/admin/mentors', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( $this, 'admin_list_mentors' ),
            'permission_callback' => array( $this, 'check_admin_jwt_auth' ),
        ) );

        register_rest_route( $this->namespace, '/paired/admin/mentors/(?P<id>\d+)', array(
            'methods'             => 'PUT',
            'callback'            => array( $this, 'admin_update_mentor' ),
            'permission_callback' => array( $this, 'check_admin_jwt_auth' ),
        ) );

        register_rest_route( $this->namespace, '/paired/admin/mentors/(?P<id>\d+)/deactivate', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array( $this, 'admin_deactivate_mentor' ),
            'permission_callback' => array( $this, 'check_admin_jwt_auth' ),
        ) );

        register_rest_route( $this->namespace, '/paired/admin/mentors/(?P<id>\d+)/activate', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array( $this, 'admin_activate_mentor' ),
            'permission_callback' => array( $this, 'check_admin_jwt_auth' ),
        ) );

        // ── Phase 2: Admin Platform Analytics ───────────────────
        register_rest_route( $this->namespace, '/paired/admin/stats', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( $this, 'admin_get_platform_stats' ),
            'permission_callback' => array( $this, 'check_admin_jwt_auth' ),
        ) );

        // ═══════════════════════════════════════════════════════════
        //  PHASE 3 ROUTES
        // ═══════════════════════════════════════════════════════════

        // ── Stripe: Checkout ────────────────────────────────────────
        register_rest_route( $this->namespace, '/paired/checkout/create-session', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array( $this, 'stripe_create_checkout' ),
            'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
        ) );

        register_rest_route( $this->namespace, '/paired/stripe/webhook', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array( $this, 'stripe_webhook' ),
            'permission_callback' => '__return_true',
        ) );

        register_rest_route( $this->namespace, '/paired/mentor/payout-settings', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_payout_settings' ),
                'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
            ),
            array(
                'methods'             => 'PUT',
                'callback'            => array( $this, 'update_payout_settings' ),
                'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
            ),
        ) );

        register_rest_route( $this->namespace, '/paired/admin/payouts', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( $this, 'admin_get_payouts' ),
            'permission_callback' => array( $this, 'check_admin_jwt_auth' ),
        ) );

        // ── Meeting Settings ────────────────────────────────────────
        register_rest_route( $this->namespace, '/paired/mentor/meeting-settings', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_meeting_settings' ),
                'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
            ),
            array(
                'methods'             => 'PUT',
                'callback'            => array( $this, 'update_meeting_settings' ),
                'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
            ),
        ) );

        register_rest_route( $this->namespace, '/paired/mentor/calendar-connect', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array( $this, 'toggle_calendar_sync' ),
            'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
        ) );

        // ── Mentor Onboarding ───────────────────────────────────────
        register_rest_route( $this->namespace, '/paired/mentor/onboarding', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( $this, 'get_onboarding_status' ),
            'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
        ) );

        register_rest_route( $this->namespace, '/paired/mentor/onboarding/(?P<step>[a-z_]+)/complete', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array( $this, 'complete_onboarding_step' ),
            'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
        ) );

        // ── Referral Programme ──────────────────────────────────────
        register_rest_route( $this->namespace, '/paired/referral/code', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( $this, 'get_referral_code' ),
            'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
        ) );

        register_rest_route( $this->namespace, '/paired/referral/stats', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( $this, 'get_referral_stats' ),
            'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
        ) );

        register_rest_route( $this->namespace, '/paired/referral/apply', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array( $this, 'apply_referral_code' ),
            'permission_callback' => '__return_true',
        ) );

        register_rest_route( $this->namespace, '/paired/admin/referrals', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( $this, 'admin_get_referrals' ),
            'permission_callback' => array( $this, 'check_admin_jwt_auth' ),
        ) );

        // ── KYC Verification ────────────────────────────────────────
        register_rest_route( $this->namespace, '/paired/mentor/kyc/submit', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array( $this, 'kyc_submit' ),
            'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
        ) );

        register_rest_route( $this->namespace, '/paired/mentor/kyc/status', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( $this, 'kyc_get_status' ),
            'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
        ) );

        register_rest_route( $this->namespace, '/paired/admin/kyc', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( $this, 'admin_list_kyc' ),
            'permission_callback' => array( $this, 'check_admin_jwt_auth' ),
        ) );

        register_rest_route( $this->namespace, '/paired/admin/kyc/(?P<id>\d+)', array(
            'methods'             => 'PUT',
            'callback'            => array( $this, 'admin_review_kyc' ),
            'permission_callback' => array( $this, 'check_admin_jwt_auth' ),
        ) );

        // Admin: Bookings
        register_rest_route( $this->namespace, '/paired/admin/bookings', array(
            'methods'             => 'GET',
            'callback'            => array( $this, 'admin_list_bookings' ),
            'permission_callback' => array( $this, 'check_admin_jwt_auth' ),
        ) );
        register_rest_route( $this->namespace, '/paired/admin/bookings/(?P<id>\d+)/status', array(
            'methods'             => 'POST',
            'callback'            => array( $this, 'admin_update_booking_status' ),
            'permission_callback' => array( $this, 'check_admin_jwt_auth' ),
        ) );

        // Admin: Mentees
        register_rest_route( $this->namespace, '/paired/admin/mentees', array(
            'methods'             => 'GET',
            'callback'            => array( $this, 'admin_list_mentees' ),
            'permission_callback' => array( $this, 'check_admin_jwt_auth' ),
        ) );
        register_rest_route( $this->namespace, '/paired/admin/mentees/(?P<id>\d+)/deactivate', array(
            'methods'             => 'POST',
            'callback'            => array( $this, 'admin_deactivate_mentee' ),
            'permission_callback' => array( $this, 'check_admin_jwt_auth' ),
        ) );
        register_rest_route( $this->namespace, '/paired/admin/mentees/(?P<id>\d+)/activate', array(
            'methods'             => 'POST',
            'callback'            => array( $this, 'admin_activate_mentee' ),
            'permission_callback' => array( $this, 'check_admin_jwt_auth' ),
        ) );

        // Admin: Transaction History
        register_rest_route( $this->namespace, '/paired/admin/transactions', array(
            'methods'             => 'GET',
            'callback'            => array( $this, 'admin_list_transactions' ),
            'permission_callback' => array( $this, 'check_admin_jwt_auth' ),
        ) );

        // Admin: Financial Reports
        register_rest_route( $this->namespace, '/paired/admin/reports', array(
            'methods'             => 'GET',
            'callback'            => array( $this, 'admin_financial_reports' ),
            'permission_callback' => array( $this, 'check_admin_jwt_auth' ),
        ) );

        // Admin: Coupon Management
        register_rest_route( $this->namespace, '/paired/admin/coupons', array(
            'methods'             => 'GET',
            'callback'            => array( $this, 'admin_list_coupons' ),
            'permission_callback' => array( $this, 'check_admin_jwt_auth' ),
        ) );
        register_rest_route( $this->namespace, '/paired/admin/coupons', array(
            'methods'             => 'POST',
            'callback'            => array( $this, 'admin_create_coupon' ),
            'permission_callback' => array( $this, 'check_admin_jwt_auth' ),
        ) );
        register_rest_route( $this->namespace, '/paired/admin/coupons/(?P<id>\d+)', array(
            'methods'             => 'PUT',
            'callback'            => array( $this, 'admin_update_coupon' ),
            'permission_callback' => array( $this, 'check_admin_jwt_auth' ),
        ) );
        register_rest_route( $this->namespace, '/paired/admin/coupons/(?P<id>\d+)', array(
            'methods'             => 'DELETE',
            'callback'            => array( $this, 'admin_delete_coupon' ),
            'permission_callback' => array( $this, 'check_admin_jwt_auth' ),
        ) );

        // Admin: Platform Settings
        register_rest_route( $this->namespace, '/paired/admin/settings', array(
            'methods'             => 'GET',
            'callback'            => array( $this, 'admin_get_settings' ),
            'permission_callback' => array( $this, 'check_admin_jwt_auth' ),
        ) );
        register_rest_route( $this->namespace, '/paired/admin/settings', array(
            'methods'             => 'POST',
            'callback'            => array( $this, 'admin_update_settings' ),
            'permission_callback' => array( $this, 'check_admin_jwt_auth' ),
        ) );

        // Admin: Email Templates
        register_rest_route( $this->namespace, '/paired/admin/email-templates', array(
            'methods'             => 'GET',
            'callback'            => array( $this, 'admin_get_email_templates' ),
            'permission_callback' => array( $this, 'check_admin_jwt_auth' ),
        ) );
        register_rest_route( $this->namespace, '/paired/admin/email-templates', array(
            'methods'             => 'POST',
            'callback'            => array( $this, 'admin_update_email_template' ),
            'permission_callback' => array( $this, 'check_admin_jwt_auth' ),
        ) );

        // Admin: Category/Skill Management
        register_rest_route( $this->namespace, '/paired/admin/skills', array(
            'methods'             => 'GET',
            'callback'            => array( $this, 'admin_get_skills' ),
            'permission_callback' => array( $this, 'check_admin_jwt_auth' ),
        ) );
        register_rest_route( $this->namespace, '/paired/admin/skills', array(
            'methods'             => 'POST',
            'callback'            => array( $this, 'admin_update_skills' ),
            'permission_callback' => array( $this, 'check_admin_jwt_auth' ),
        ) );
        register_rest_route( $this->namespace, '/paired/admin/skills/reset', array(
            'methods'             => 'POST',
            'callback'            => array( $this, 'admin_reset_skills' ),
            'permission_callback' => array( $this, 'check_admin_jwt_auth' ),
        ) );

        // Admin: Referral Settings
        register_rest_route( $this->namespace, '/paired/admin/referral-settings', array(
            'methods'             => 'GET',
            'callback'            => array( $this, 'admin_get_referral_settings' ),
            'permission_callback' => array( $this, 'check_admin_jwt_auth' ),
        ) );
        register_rest_route( $this->namespace, '/paired/admin/referral-settings', array(
            'methods'             => 'POST',
            'callback'            => array( $this, 'admin_update_referral_settings' ),
            'permission_callback' => array( $this, 'check_admin_jwt_auth' ),
        ) );

        // Admin: Job Management
        register_rest_route( $this->namespace, '/admin/jobs', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( $this, 'admin_get_jobs' ),
            'permission_callback' => array( $this, 'check_admin_jwt_auth' ),
            'args'                => array(
                'page'     => array( 'default' => 1,  'sanitize_callback' => 'absint' ),
                'per_page' => array( 'default' => 20, 'sanitize_callback' => 'absint' ),
                'status'   => array( 'default' => '',  'sanitize_callback' => 'sanitize_text_field' ),
                'search'   => array( 'default' => '',  'sanitize_callback' => 'sanitize_text_field' ),
            ),
        ) );

        register_rest_route( $this->namespace, '/admin/jobs/(?P<id>\d+)/status', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array( $this, 'admin_update_job_status' ),
            'permission_callback' => array( $this, 'check_admin_jwt_auth' ),
        ) );

        register_rest_route( $this->namespace, '/admin/jobs/(?P<id>\d+)', array(
            'methods'             => WP_REST_Server::DELETABLE,
            'callback'            => array( $this, 'admin_delete_job' ),
            'permission_callback' => array( $this, 'check_admin_jwt_auth' ),
        ) );
    }

    /**
     * 1. Validate SSO Session
     */
    public function validate_sso_session( WP_REST_Request $request ) {
        $current_user = wp_get_current_user();

        if ( ! $current_user->exists() ) {
            return new WP_Error(
                'rest_not_logged_in',
                __( 'No active session found. Please log in.', 'bpu' ),
                array( 'status' => 401 )
            );
        }

        $user_id = $current_user->ID;
        $acf_profile = array();

        if ( function_exists( 'get_fields' ) ) {
            $fields = get_fields( 'user_' . $user_id );
            if ( $fields ) {
                $acf_profile = $fields;
            }
        }

        // Fetch CV upload metadata
        $cv_id = get_user_meta( $user_id, '_bpu_member_cv_id', true );
        $cv_url = $cv_id ? wp_get_attachment_url( $cv_id ) : '';

        return new WP_REST_Response( array(
            'authenticated' => true,
            'user'          => array(
                'id'           => $user_id,
                'username'     => $current_user->user_login,
                'email'        => $current_user->user_email,
                'display_name' => $current_user->display_name,
                'roles'        => $current_user->roles,
                'profile'      => $acf_profile,
                'cv_url'       => $cv_url,
            )
        ), 200 );
    }

    /**
     * 2. Headless Job Click Tracker
     */
    public function track_job_click( WP_REST_Request $request ) {
        global $wpdb;
        $job_id  = $request->get_param( 'job_id' );
        $user_id = $request->get_param( 'user_id' );
        $ip_addr = $_SERVER['REMOTE_ADDR'];

        $post_type = get_post_type( $job_id );
        if ( 'job_listing' !== $post_type && 'post' !== $post_type ) {
            return new WP_Error(
                'rest_invalid_job_id',
                __( 'Invalid Job Listing ID.', 'bpu' ),
                array( 'status' => 400 )
            );
        }

        $current_clicks = (int) get_post_meta( $job_id, '_click_count', true );
        update_post_meta( $job_id, '_click_count', $current_clicks + 1 );

        $clicks_table = $wpdb->prefix . 'job_manager_clicks'; 
        $alt_table    = $wpdb->prefix . 'job_clicks';          
        
        $inserted = false;
        
        if ( $wpdb->get_var( "SHOW TABLES LIKE '$clicks_table'" ) === $clicks_table ) {
            $wpdb->insert(
                $clicks_table,
                array(
                    'job_id'     => $job_id,
                    'user_id'    => $user_id ? $user_id : 0,
                    'click_time' => current_time( 'mysql' ),
                    'ip_address' => $ip_addr,
                ),
                array( '%d', '%d', '%s', '%s' )
            );
            $inserted = true;
        } elseif ( $wpdb->get_var( "SHOW TABLES LIKE '$alt_table'" ) === $alt_table ) {
            $wpdb->insert(
                $alt_table,
                array(
                    'job_id'     => $job_id,
                    'user_id'    => $user_id ? $user_id : 0,
                    'clicked_at' => current_time( 'mysql' ),
                ),
                array( '%d', '%d', '%s' )
            );
            $inserted = true;
        }

        return new WP_REST_Response( array(
            'success'      => true,
            'job_id'       => $job_id,
            'total_meta_clicks' => $current_clicks + 1,
            'legacy_db_logged'  => $inserted,
        ), 200 );
    }

    /**
     * GET /member/enrolled-courses — returns courses the JWT user is enrolled in, with progress.
     */
    public function get_enrolled_courses( WP_REST_Request $request ) {
        $user_id = get_current_user_id();

        // Get all course IDs this user is enrolled in via Tutor LMS enrolment records.
        $enrolled_ids = array();
        if ( function_exists( 'tutor_utils' ) ) {
            $enrolled_courses = tutor_utils()->get_enrolled_courses_ids_by_user( $user_id );
            if ( is_array( $enrolled_courses ) ) {
                $enrolled_ids = array_map( 'intval', $enrolled_courses );
            }
        }

        // Fallback: query tutor_enrolled post type directly.
        if ( empty( $enrolled_ids ) ) {
            $enrol_posts = get_posts( array(
                'post_type'      => 'tutor_enrolled',
                'post_status'    => 'completed',
                'author'         => $user_id,
                'posts_per_page' => -1,
                'fields'         => 'ids',
            ) );
            foreach ( $enrol_posts as $ep_id ) {
                $cid = (int) wp_get_post_parent_id( $ep_id );
                if ( $cid ) $enrolled_ids[] = $cid;
            }
            $enrolled_ids = array_unique( $enrolled_ids );
        }

        if ( empty( $enrolled_ids ) ) {
            return new WP_REST_Response( array( 'success' => true, 'courses' => array() ), 200 );
        }

        $query = new WP_Query( array(
            'post_type'      => 'courses',
            'post_status'    => 'publish',
            'post__in'       => $enrolled_ids,
            'posts_per_page' => -1,
            'orderby'        => 'date',
            'order'          => 'DESC',
        ) );

        $courses = array();
        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $post_id  = get_the_ID();
                $percent  = function_exists( 'tutor_utils' )
                    ? (int) tutor_utils()->get_course_completed_percent( $post_id, $user_id )
                    : 0;
                $completed = function_exists( 'tutor_utils' )
                    ? (bool) tutor_utils()->is_completed_course( $post_id, $user_id )
                    : false;
                $status = $completed ? 'Completed' : ( $percent > 0 ? 'In Progress' : 'Enrolled' );
                $courses[] = array(
                    'id'             => $post_id,
                    'title'          => get_the_title(),
                    'excerpt'        => wp_strip_all_tags( get_the_excerpt() ),
                    'provider'       => get_post_meta( $post_id, '_tutor_course_instructor', true )
                        ? ( get_userdata( (int) get_post_meta( $post_id, '_tutor_course_instructor', true ) )->display_name ?? 'BPU Partner' )
                        : 'BPU Partner',
                    'category'       => wp_get_post_terms( $post_id, 'course-category', array( 'fields' => 'names' ) )[0] ?? 'Professional Development',
                    'learn_more_url' => get_the_permalink(),
                    'image'          => get_the_post_thumbnail_url( $post_id, 'medium' ) ?: '',
                    'duration'       => get_post_meta( $post_id, '_course_duration', true ) ?: '',
                    'level'          => get_post_meta( $post_id, '_tutor_course_level', true ) ?: '',
                    'status'         => $status,
                    'progress'       => $percent,
                );
            }
            wp_reset_postdata();
        }

        return new WP_REST_Response( array( 'success' => true, 'courses' => $courses ), 200 );
    }

    /**
     * GET /courses — Paginated course listing sourced directly from Tutor LMS post type.
     * Works regardless of whether Tutor LMS exposes its own REST routes.
     */
    public function get_courses( WP_REST_Request $request ) {
        $per_page = min( 50, max( 1, $request->get_param( 'per_page' ) ) );
        $page     = max( 1, $request->get_param( 'page' ) );
        $search   = $request->get_param( 'search' );
        $category = $request->get_param( 'category' );
        $tag      = $request->get_param( 'tag' );

        $query_args = array(
            'post_type'      => 'courses',
            'post_status'    => 'publish',
            'posts_per_page' => $per_page,
            'paged'          => $page,
            'orderby'        => 'date',
            'order'          => 'DESC',
        );

        if ( ! empty( $search ) ) {
            $query_args['s'] = $search;
        }

        $tax_query = array();

        if ( ! empty( $category ) ) {
            $tax_query[] = array(
                'taxonomy' => 'course-category',
                'field'    => 'name',
                'terms'    => $category,
            );
        }

        if ( ! empty( $tag ) ) {
            $tax_query[] = array(
                'taxonomy' => 'course-tag',
                'field'    => 'name',
                'terms'    => $tag,
            );
        }

        if ( ! empty( $tax_query ) ) {
            $tax_query['relation']       = 'AND';
            $query_args['tax_query']     = $tax_query;
        }

        $query = new WP_Query( $query_args );

        $courses = array();

        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $post_id    = get_the_ID();
                $categories = wp_get_post_terms( $post_id, 'course-category', array( 'fields' => 'names' ) );
                $tags       = wp_get_post_terms( $post_id, 'course-tag',      array( 'fields' => 'names' ) );
                $courses[]  = array(
                    'id'             => $post_id,
                    'title'          => get_the_title(),
                    'excerpt'        => wp_strip_all_tags( get_the_excerpt() ),
                    'provider'       => get_post_meta( $post_id, '_tutor_course_instructor', true )
                        ? get_userdata( (int) get_post_meta( $post_id, '_tutor_course_instructor', true ) )->display_name ?? 'BPU Partner'
                        : 'BPU Partner',
                    'category'       => ! empty( $categories ) && ! is_wp_error( $categories ) ? $categories[0] : 'Professional Development',
                    'categories'     => ! is_wp_error( $categories ) ? array_values( $categories ) : array(),
                    'tags'           => ! is_wp_error( $tags )       ? array_values( $tags )       : array(),
                    'learn_more_url' => get_the_permalink(),
                    'image'          => get_the_post_thumbnail_url( $post_id, 'medium' ) ?: '',
                    'duration'       => get_post_meta( $post_id, '_course_duration', true ) ?: '',
                    'level'          => get_post_meta( $post_id, '_tutor_course_level', true ) ?: '',
                );
            }
            wp_reset_postdata();
        }

        $total       = $query->found_posts;
        $total_pages = (int) ceil( $total / $per_page );

        $response = new WP_REST_Response( array(
            'success'     => true,
            'courses'     => $courses,
            'total'       => $total,
            'page'        => $page,
            'per_page'    => $per_page,
            'total_pages' => $total_pages,
        ), 200 );

        $response->header( 'X-WP-Total',      $total );
        $response->header( 'X-WP-TotalPages', $total_pages );

        return $response;
    }

    /**
     * 3. Tutor LMS Course Progress Trigger
     */
    public function track_course_progress( WP_REST_Request $request ) {
        $course_id = $request->get_param( 'course_id' );
        $user_id   = wp_get_current_user()->ID;

        if ( ! function_exists( 'tutor' ) || ! function_exists( 'tutor_utils' ) ) {
            return new WP_Error(
                'tutor_lms_inactive',
                __( 'Tutor LMS is not active on the WordPress backend.', 'bpu' ),
                array( 'status' => 500 )
            );
        }

        $is_enrolled = tutor_utils()->is_enrolled_to_course( $course_id, $user_id );
        
        if ( ! $is_enrolled ) {
            tutor_utils()->do_enroll( $course_id, $user_id );
        }

        do_action( 'tutor_course/enrolled', $course_id, $user_id );

        return new WP_REST_Response( array(
            'success'     => true,
            'course_id'   => $course_id,
            'user_id'     => $user_id,
            'status'      => 'Enrolled & Started',
            'is_enrolled_before' => $is_enrolled,
        ), 200 );
    }

    /**
     * 4. CV Upload & Gemini Pro Parser — Pro members only, JWT Bearer auth.
     */
    public function handle_member_cv_upload( WP_REST_Request $request ) {
        $payload = $this->verify_jwt_bearer( $request );
        if ( ! $payload || empty( $payload['user_id'] ) ) {
            return new WP_Error( 'bpu_unauthorized', __( 'Invalid or missing token.', 'bpu' ), array( 'status' => 401 ) );
        }

        $user_id = intval( $payload['user_id'] );

        if ( ! $this->is_pro_member( $user_id ) ) {
            return new WP_Error( 'bpu_not_pro', __( 'CV upload requires a BPU Pro membership.', 'bpu' ), array( 'status' => 403 ) );
        }

        // Ensure files are present in the request
        $files = $request->get_file_params();
        if ( empty( $files ) || ! isset( $files['cv_file'] ) ) {
            return new WP_Error(
                'rest_upload_no_file',
                __( 'No file was uploaded. Please attach a "cv_file" field.', 'bpu' ),
                array( 'status' => 400 )
            );
        }

        $cv_file = $files['cv_file'];

        // Validate file type (allow only PDFs for reliable parsing)
        $file_type = wp_check_filetype( $cv_file['name'] );
        if ( 'pdf' !== $file_type['ext'] ) {
            return new WP_Error(
                'rest_invalid_file_type',
                __( 'Only PDF formats are supported for automatic CV parsing.', 'bpu' ),
                array( 'status' => 400 )
            );
        }

        // Include WordPress media uploading libraries
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
        require_once( ABSPATH . 'wp-admin/includes/media.php' );
        require_once( ABSPATH . 'wp-admin/includes/image.php' );

        // Upload and attach the CV to the database
        $attachment_id = media_handle_upload( 'cv_file', 0 ); // 0 links it globally
        if ( is_wp_error( $attachment_id ) ) {
            return $attachment_id;
        }

        // Save CV Attachment ID in member metavalues
        update_user_meta( $user_id, '_bpu_member_cv_id', $attachment_id );

        // Extract PDF content and feed to Gemini Pro
        $file_path = get_attached_file( $attachment_id );
        $file_data = file_get_contents( $file_path );
        $base64_pdf = base64_encode( $file_data );

        // Trigger Google Cloud Vertex AI / Gemini API
        $parsed_profile = $this->parse_cv_with_gemini( $base64_pdf );

        if ( is_wp_error( $parsed_profile ) ) {
            return $parsed_profile; // Return API failure gracefully
        }

        // Populate Member ACF profile fields programmatically based on the parsed results
        $this->update_member_acf_profile( $user_id, $parsed_profile );

        return new WP_REST_Response( array(
            'success'       => true,
            'attachment_id' => $attachment_id,
            'cv_url'        => wp_get_attachment_url( $attachment_id ),
            'parsed_data'   => $parsed_profile,
        ), 200 );
    }

    /**
     * POST /bpu/v1/member/cv-analyze — instant ATS analysis, free for all authenticated users.
     */
    public function handle_member_cv_analyze( WP_REST_Request $request ) {
        $payload = $this->verify_jwt_bearer( $request );
        if ( ! $payload || empty( $payload['user_id'] ) ) {
            return new WP_Error( 'bpu_unauthorized', __( 'Invalid or missing token.', 'bpu' ), array( 'status' => 401 ) );
        }

        $user_id     = intval( $payload['user_id'] );
        $body        = $request->get_body_params();
        $target_role = isset( $body['target_role'] )     ? sanitize_text_field( $body['target_role'] )         : '';
        $job_desc    = isset( $body['job_description'] ) ? sanitize_textarea_field( $body['job_description'] ) : '';

        if ( empty( $target_role ) ) {
            return new WP_Error( 'bpu_missing_field', __( 'target_role is required.', 'bpu' ), array( 'status' => 400 ) );
        }

        // Determine the PDF to analyze: new upload takes precedence, else stored CV.
        $files   = $request->get_file_params();
        $base64_pdf = '';

        if ( ! empty( $files['cv_file'] ) && $files['cv_file']['error'] === UPLOAD_ERR_OK ) {
            $file_type = wp_check_filetype( $files['cv_file']['name'] );
            if ( 'pdf' !== $file_type['ext'] ) {
                return new WP_Error( 'bpu_invalid_file', __( 'Only PDF files are supported.', 'bpu' ), array( 'status' => 400 ) );
            }
            $file_data  = file_get_contents( $files['cv_file']['tmp_name'] );
            $base64_pdf = base64_encode( $file_data );
        } else {
            $cv_id = get_user_meta( $user_id, '_bpu_member_cv_id', true );
            if ( $cv_id ) {
                $cv_path = get_attached_file( $cv_id );
                if ( $cv_path && file_exists( $cv_path ) ) {
                    $base64_pdf = base64_encode( file_get_contents( $cv_path ) );
                }
            }
        }

        if ( empty( $base64_pdf ) ) {
            return new WP_Error( 'bpu_no_cv', __( 'No CV found. Please upload a PDF.', 'bpu' ), array( 'status' => 400 ) );
        }

        $results = $this->analyze_cv_with_gemini( $base64_pdf, $target_role, $job_desc );
        if ( is_wp_error( $results ) ) {
            return $results;
        }

        return new WP_REST_Response( array_merge( array( 'success' => true ), $results ), 200 );
    }

    /**
     * Call Gemini API with automatic fallback: tries gemini-2.5-flash first, then gemini-2.0-flash.
     * Returns decoded response array on success, WP_Error on failure.
     */
    private function gemini_request( array $body, string $api_key, int $timeout = 90 ) {
        $models = array( 'gemini-2.5-flash', 'gemini-2.0-flash' );
        $base   = 'https://generativelanguage.googleapis.com/v1beta/models/';

        foreach ( $models as $model ) {
            $url      = $base . $model . ':generateContent?key=' . $api_key;
            $response = wp_remote_post( $url, array(
                'headers' => array( 'Content-Type' => 'application/json' ),
                'body'    => wp_json_encode( $body ),
                'timeout' => $timeout,
            ) );

            if ( is_wp_error( $response ) ) {
                return new WP_Error( 'gemini_request_failed', $response->get_error_message(), array( 'status' => 502 ) );
            }

            $status = wp_remote_retrieve_response_code( $response );
            $raw    = wp_remote_retrieve_body( $response );

            if ( 200 === $status ) {
                return json_decode( $raw, true );
            }

            // 503 = overloaded, 429 = rate limit — try fallback model
            if ( in_array( $status, array( 429, 503 ), true ) ) {
                continue;
            }

            // Any other error code is not retryable
            $decoded = json_decode( $raw, true );
            $message = $decoded['error']['message'] ?? "Gemini returned status {$status}";
            return new WP_Error( 'gemini_api_error', $message, array( 'status' => 502 ) );
        }

        return new WP_Error(
            'gemini_unavailable',
            __( 'The AI service is currently experiencing high demand. Please try again in a few minutes.', 'bpu' ),
            array( 'status' => 503 )
        );
    }

    /**
     * ATS-style CV analysis via Gemini (returns score, strengths, weaknesses, recommendation).
     */
    private function analyze_cv_with_gemini( $base64_pdf, $target_role, $job_description = '' ) {
        $api_key = defined( 'GEMINI_API_KEY' ) ? GEMINI_API_KEY : get_option( 'bpu_gemini_api_key', '' );

        if ( empty( $api_key ) ) {
            return new WP_Error( 'gemini_missing_api_key', __( 'Gemini API Key is not configured.', 'bpu' ), array( 'status' => 500 ) );
        }

        $prompt = sprintf(
            'You are an expert ATS system and Senior HR Recruiter. Review the candidate\'s CV for the role of "%s".%s

Provide a critical and constructive evaluation. Return a single valid JSON object with these exact keys:
- score: integer 0-100 representing how well the CV matches the target role
- strengths: array of 3-4 strings describing key strengths of this CV for the role
- weaknesses: array of 3-4 strings describing gaps or improvements needed
- recommendation: single string with the most important action the candidate should take

Return only the JSON object, no markdown.',
            esc_attr( $target_role ),
            $job_description ? "\n\nJob Description:\n" . mb_substr( $job_description, 0, 5000 ) : ''
        );

        $body = array(
            'contents' => array(
                array(
                    'parts' => array(
                        array( 'text' => $prompt ),
                        array(
                            'inline_data' => array(
                                'mime_type' => 'application/pdf',
                                'data'      => $base64_pdf,
                            ),
                        ),
                    ),
                ),
            ),
            'generationConfig' => array( 'responseMimeType' => 'application/json' ),
        );

        $data = $this->gemini_request( $body, $api_key, 90 );

        if ( is_wp_error( $data ) ) {
            return $data;
        }

        $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

        if ( empty( $text ) ) {
            return new WP_Error( 'gemini_empty', __( 'Empty response from AI.', 'bpu' ), array( 'status' => 502 ) );
        }

        $result = json_decode( trim( $text ), true );
        if ( ! $result || ! isset( $result['score'] ) ) {
            return new WP_Error( 'gemini_invalid_json', __( 'Could not parse AI response.', 'bpu' ), array( 'status' => 502 ) );
        }

        return array(
            'score'          => intval( $result['score'] ),
            'strengths'      => array_slice( (array) ( $result['strengths'] ?? [] ), 0, 4 ),
            'weaknesses'     => array_slice( (array) ( $result['weaknesses'] ?? [] ), 0, 4 ),
            'recommendation' => sanitize_textarea_field( $result['recommendation'] ?? '' ),
        );
    }

    /**
     * Parse base64 PDF CV via Google Gemini Multimodal API.
     * Tries gemini-2.5-flash then falls back to gemini-2.0-flash on 429/503.
     */
    private function parse_cv_with_gemini( $base64_pdf ) {
        $api_key = defined( 'GEMINI_API_KEY' ) ? GEMINI_API_KEY : get_option( 'bpu_gemini_api_key', '' );

        if ( empty( $api_key ) ) {
            return new WP_Error(
                'gemini_missing_api_key',
                __( 'Gemini API Key is not configured on the WordPress host.', 'bpu' ),
                array( 'status' => 500 )
            );
        }

        $prompt = 'You are a professional CV parser. Extract all information from this PDF CV and return a single valid JSON object with the following keys.

Flat profile fields (strings):
- first_name
- last_name
- phone_number
- current_employment_status: one of [Employed Full-Time, Employed Part-Time, Self-employed, Not employed but looking for work, Student]
- level_of_education: one of [High School, Bachelor\'s Degree, Masters Degree, PhD, Other]
- industry: one of [Information Technology, Legal, Banking & Financial Services, Engineering, Healthcare, Sales & Retail]
- industryfield_of_expertise: one of [Technology, Finance and Accounting, Legal, Human Resources, Engineering, Sciences (STEM)]
- years_of_experience: one of [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11-15, 16-20, 20+]
- skills_separate: comma-separated list of key technical and professional skills
- user_bio: a one-paragraph professional summary written in third person based on the CV
- residence: city and country of the candidate if present (e.g. "Edinburgh, UK")
- linkedin_profile: LinkedIn URL if present, else empty string

Structured arrays (output as JSON arrays — include ALL entries found):
- work_experiences: array of objects, each with keys: title (job title), company, start_date (e.g. "Jan 2020"), end_date (e.g. "Mar 2023" or "" if current), is_current (true/false), description (one sentence summary of role)
- education_history: array of objects, each with keys: institution, degree, field_of_study, start_year (4-digit string), end_year (4-digit string or "" if ongoing)
- certifications: array of objects, each with keys: name, issuer, year (4-digit string or "")
- languages: array of language name strings (e.g. ["English", "French"])

Rules:
- Output ONLY the raw JSON object. No markdown, no backticks, no explanation.
- If a field cannot be determined, use an empty string "" or empty array [] as appropriate.
- Never invent information not present in the CV.';

        $body = array(
            'contents' => array(
                array(
                    'parts' => array(
                        array( 'text' => $prompt ),
                        array(
                            'inlineData' => array(
                                'mimeType' => 'application/pdf',
                                'data'     => $base64_pdf,
                            ),
                        ),
                    ),
                ),
            ),
            'generationConfig' => array( 'responseMimeType' => 'application/json' ),
        );

        $data = $this->gemini_request( $body, $api_key, 60 );

        if ( is_wp_error( $data ) ) {
            return $data;
        }

        if ( ! isset( $data['candidates'][0]['content']['parts'][0]['text'] ) ) {
            return new WP_Error(
                'gemini_invalid_response',
                __( 'Invalid response format returned by AI engine.', 'bpu' ),
                array( 'status' => 500 )
            );
        }

        $raw_json_text = $data['candidates'][0]['content']['parts'][0]['text'];
        $parsed_data   = json_decode( trim( $raw_json_text ), true );

        if ( empty( $parsed_data ) ) {
            return new WP_Error(
                'gemini_json_parse_error',
                __( 'Failed to decode structured resume JSON.', 'bpu' ),
                array( 'status' => 500 )
            );
        }

        return $parsed_data;
    }

    /**
     * Store all CV-parsed data into WordPress user meta.
     * Always overwrites on re-upload — the CV is the source of truth.
     */
    private function update_member_acf_profile( $user_id, $data ) {
        $selector = 'user_' . $user_id;

        // ── Flat scalar fields ────────────────────────────────
        $flat_fields = [
            'first_name'               => 'sanitize_text_field',
            'last_name'                => 'sanitize_text_field',
            'phone_number'             => 'sanitize_text_field',
            'current_employment_status'=> 'sanitize_text_field',
            'level_of_education'       => 'sanitize_text_field',
            'industry'                 => 'sanitize_text_field',
            'industryfield_of_expertise' => 'sanitize_text_field',
            'years_of_experience'      => 'sanitize_text_field',
            'skills_separate'          => 'sanitize_text_field',
            'user_bio'                 => 'sanitize_textarea_field',
            'residence'                => 'sanitize_text_field',
            'linkedin_profile'         => 'sanitize_url',
        ];

        foreach ( $flat_fields as $key => $sanitizer ) {
            if ( ! isset( $data[ $key ] ) || $data[ $key ] === '' ) {
                continue;
            }
            $value = call_user_func( $sanitizer, $data[ $key ] );
            // Write via ACF if available, always write via user_meta as fallback/mirror
            if ( function_exists( 'update_field' ) ) {
                update_field( $key, $value, $selector );
            }
            update_user_meta( $user_id, $key, $value );
        }

        // ── Structured array fields ───────────────────────────
        // work_experiences → bpu_experiences
        if ( ! empty( $data['work_experiences'] ) && is_array( $data['work_experiences'] ) ) {
            $experiences = array_map( function( $e ) {
                return [
                    'title'       => sanitize_text_field( $e['title']       ?? '' ),
                    'company'     => sanitize_text_field( $e['company']     ?? '' ),
                    'start_date'  => sanitize_text_field( $e['start_date']  ?? '' ),
                    'end_date'    => sanitize_text_field( $e['end_date']    ?? '' ),
                    'is_current'  => ! empty( $e['is_current'] ),
                    'description' => sanitize_textarea_field( $e['description'] ?? '' ),
                ];
            }, $data['work_experiences'] );
            update_user_meta( $user_id, 'bpu_experiences', $experiences );
        }

        // education_history → bpu_educations (same key used by the PAIRED importer)
        if ( ! empty( $data['education_history'] ) && is_array( $data['education_history'] ) ) {
            $educations = array_map( function( $e ) {
                return [
                    'institute'  => sanitize_text_field( $e['institution']    ?? '' ),
                    'degree'     => sanitize_text_field( $e['degree']         ?? '' ),
                    'field'      => sanitize_text_field( $e['field_of_study'] ?? '' ),
                    'start_year' => sanitize_text_field( $e['start_year']     ?? '' ),
                    'end_year'   => sanitize_text_field( $e['end_year']       ?? '' ),
                ];
            }, $data['education_history'] );
            update_user_meta( $user_id, 'bpu_educations', $educations );
        }

        // certifications
        if ( ! empty( $data['certifications'] ) && is_array( $data['certifications'] ) ) {
            $certs = array_map( function( $c ) {
                return [
                    'name'   => sanitize_text_field( $c['name']   ?? '' ),
                    'issuer' => sanitize_text_field( $c['issuer'] ?? '' ),
                    'year'   => sanitize_text_field( $c['year']   ?? '' ),
                ];
            }, $data['certifications'] );
            update_user_meta( $user_id, 'bpu_certifications', $certs );
        }

        // languages
        if ( ! empty( $data['languages'] ) && is_array( $data['languages'] ) ) {
            $languages = implode( ', ', array_map( 'sanitize_text_field', $data['languages'] ) );
            update_user_meta( $user_id, 'bpu_languages', $languages );
        }

        // Timestamp of last CV parse
        update_user_meta( $user_id, 'bpu_cv_parsed_at', current_time( 'mysql' ) );
    }

    /**
     * 5. Get Member CV Reviews List
     * Retrieves all manual critique reviews published by BPU professionals for this user.
     */
    public function get_member_cv_reviews( WP_REST_Request $request ) {
        $payload = $this->verify_jwt_bearer( $request );
        if ( ! $payload || empty( $payload['user_id'] ) ) {
            return new WP_Error( 'sso_invalid_token', __( 'Invalid or missing token.', 'bpu' ), array( 'status' => 401 ) );
        }
        $user_id = intval( $payload['user_id'] );

        $query = new WP_Query( array(
            'post_type'      => 'cv_clinic_review',
            'post_status'    => 'publish',
            'posts_per_page' => 15,
            'meta_query'     => array(
                array(
                    'key'     => '_bpu_candidate_id',
                    'value'   => $user_id,
                    'compare' => '=',
                )
            )
        ) );

        $reviews = array();

        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                
                // Get review custom meta values
                $score    = get_post_meta( get_the_ID(), '_bpu_review_score', true );
                $cv_link  = get_post_meta( get_the_ID(), '_bpu_cv_attachment_url', true );
                
                $reviews[] = array(
                    'id'         => get_the_ID(),
                    'title'      => get_the_title(),
                    'critique'   => get_the_content(),
                    'score'      => $score ? (int) $score : null,
                    'cv_link'    => $cv_link,
                    'date'       => get_the_date( 'c' ),
                    'reviewer'   => get_the_author(),
                );
            }
            wp_reset_postdata();
        }

        // Clear dashboard unread reviews counter
        update_user_meta( $user_id, '_bpu_unread_reviews', 0 );

        return new WP_REST_Response( array(
            'success' => true,
            'reviews' => $reviews,
        ), 200 );
    }

    /**
     * Hook to notify candidate via email & dashboard alert when a manual review is published
     */
    public function notify_candidate_of_cv_review( $post_id, $post ) {
        $candidate_id = get_post_meta( $post_id, '_bpu_candidate_id', true );
        if ( ! $candidate_id ) {
            return;
        }

        // 1. Log unread alert counter in user metadata
        $unread = (int) get_user_meta( $candidate_id, '_bpu_unread_reviews', true );
        update_user_meta( $candidate_id, '_bpu_unread_reviews', $unread + 1 );

        // 2. Send transaction email notification via wp_mail
        $candidate = get_userdata( $candidate_id );
        if ( $candidate ) {
            $to      = $candidate->user_email;
            $subject = __( 'Your CV Clinic Review is Ready! - Black Professionals UK', 'bpu' );
            
            $message  = sprintf( __( 'Hello %s,', 'bpu' ), $candidate->display_name ) . "\r\n\r\n";
            $message .= __( 'Good news! A BPU career professional has manually reviewed your CV and posted detailed critiques and strategy suggestions to help you stand out.', 'bpu' ) . "\r\n\r\n";
            $message .= sprintf( __( 'Review Title: %s', 'bpu' ), $post->post_title ) . "\r\n";
            $message .= __( 'Log in to your member portal to view the full details of your review:', 'bpu' ) . "\r\n";
            $message .= 'https://app.blackprofessionals.uk/dashboard/cv-clinic' . "\r\n\r\n";
            $message .= __( 'To your career success,', 'bpu' ) . "\r\n";
            $message .= __( 'The BPU CV Clinic Team', 'bpu' );

            $headers = array( 'Content-Type: text/plain; charset=UTF-8' );

            wp_mail( $to, $subject, $message, $headers );
        }
    }

    // ══════════════════════════════════════════════════════════════
    //  MATCH-BASED EMAIL NOTIFICATIONS
    // ══════════════════════════════════════════════════════════════

    /**
     * Fires when a job_listing post transitions to 'publish'.
     * Scores every member profile against the new job and emails those
     * whose match score is >= 70. Deduped via post meta flag.
     */
    public function notify_matched_members_of_new_job( $post_id, $post ) {
        if ( get_post_meta( $post_id, '_bpu_job_notified', true ) ) {
            return;
        }

        $job_title = $post->post_title;
        $job_type  = get_post_meta( $post_id, '_job_type', true );
        $company   = get_post_meta( $post_id, '_company_name', true ) ?: 'A partner organisation';
        $location  = get_post_meta( $post_id, '_job_location', true ) ?: 'United Kingdom';

        $members  = $this->get_pro_members_with_profiles( 150 );
        $notified = 0;

        foreach ( $members as $member ) {
            $profile = function_exists( 'get_fields' )
                ? ( get_fields( 'user_' . $member->ID ) ?: array() )
                : array();

            $score = $this->score_job_match( $profile, $job_title, $job_type );
            if ( $score < 70 ) {
                continue;
            }

            $subject  = sprintf( __( 'New job match (%d%%): %s — BPU', 'bpu' ), $score, $job_title );
            $message  = sprintf( __( 'Hi %s,', 'bpu' ), $member->display_name ) . "\r\n\r\n";
            $message .= __( 'A new job has been posted that matches your BPU profile.', 'bpu' ) . "\r\n\r\n";
            $message .= sprintf( __( 'Role:     %s', 'bpu' ), $job_title ) . "\r\n";
            $message .= sprintf( __( 'Company:  %s', 'bpu' ), $company ) . "\r\n";
            $message .= sprintf( __( 'Location: %s', 'bpu' ), $location ) . "\r\n";
            $message .= sprintf( __( 'Match:    %d%%', 'bpu' ), $score ) . "\r\n\r\n";
            $message .= __( 'View and apply in your member portal:', 'bpu' ) . "\r\n";
            $message .= 'https://app.blackprofessionals.uk' . "\r\n\r\n";
            $message .= __( 'To your career success,', 'bpu' ) . "\r\n";
            $message .= __( 'The BPU Team', 'bpu' );

            wp_mail( $member->user_email, $subject, $message, array( 'Content-Type: text/plain; charset=UTF-8' ) );
            $notified++;
        }

        update_post_meta( $post_id, '_bpu_job_notified', 1 );
        update_post_meta( $post_id, '_bpu_job_notified_count', $notified );
    }

    /**
     * Fires when a WordPress user's role is changed via WP_User::set_role().
     * If the new role is 'mentor', scores every member profile against the
     * mentor's profile and emails those with a match score >= 60.
     * Deduped via user meta flag so the mentor only triggers one round.
     */
    public function notify_matched_mentees_of_new_mentor( $user_id, $new_role, $old_roles ) {
        if ( $new_role !== 'mentor' ) {
            return;
        }
        if ( get_user_meta( $user_id, '_bpu_mentor_notified', true ) ) {
            return;
        }

        $mentor = get_userdata( $user_id );
        if ( ! $mentor ) {
            return;
        }

        $mentor_profile = function_exists( 'get_fields' )
            ? ( get_fields( 'user_' . $user_id ) ?: array() )
            : array();

        $mentor_industry = $mentor_profile['industry'] ?? '';
        $mentor_field    = $mentor_profile['industryfield_of_expertise'] ?? '';

        $members  = $this->get_pro_members_with_profiles( 150 );
        $notified = 0;

        foreach ( $members as $member ) {
            if ( $member->ID === $user_id ) {
                continue;
            }

            $member_profile = function_exists( 'get_fields' )
                ? ( get_fields( 'user_' . $member->ID ) ?: array() )
                : array();

            $score = $this->score_mentor_match( $member_profile, $mentor_profile );
            if ( $score < 60 ) {
                continue;
            }

            $subject  = sprintf( __( 'New mentor match on PAIRED: %s', 'bpu' ), $mentor->display_name );
            $message  = sprintf( __( 'Hi %s,', 'bpu' ), $member->display_name ) . "\r\n\r\n";
            $message .= __( 'A new mentor has joined PAIRED who looks like a great fit for your career goals.', 'bpu' ) . "\r\n\r\n";
            $message .= sprintf( __( 'Mentor:   %s', 'bpu' ), $mentor->display_name ) . "\r\n";
            if ( $mentor_industry ) {
                $message .= sprintf( __( 'Industry: %s', 'bpu' ), $mentor_industry ) . "\r\n";
            }
            if ( $mentor_field ) {
                $message .= sprintf( __( 'Field:    %s', 'bpu' ), $mentor_field ) . "\r\n";
            }
            $message .= "\r\n" . __( 'View their profile and book a free session:', 'bpu' ) . "\r\n";
            $message .= 'https://pairedbybpu.uk/mentors/' . $user_id . "\r\n\r\n";
            $message .= __( 'The PAIRED Team', 'bpu' );

            wp_mail( $member->user_email, $subject, $message, array( 'Content-Type: text/plain; charset=UTF-8' ) );
            $notified++;
        }

        update_user_meta( $user_id, '_bpu_mentor_notified', 1 );
    }

    /**
     * Query members who have at least a partial profile (industry or skills set).
     * Excludes admins, editors, and mentors. Capped at $limit to avoid timeouts.
     */
    private function get_members_with_profiles( int $limit = 150 ): array {
        return ( new WP_User_Query( array(
            'role__not_in' => array( 'administrator', 'editor', 'author', 'contributor', 'mentor' ),
            'number'       => $limit,
            'orderby'      => 'registered',
            'order'        => 'DESC',
            'meta_query'   => array(
                'relation' => 'OR',
                array( 'key' => 'industry',        'value' => '', 'compare' => '!=' ),
                array( 'key' => 'skills_separate', 'value' => '', 'compare' => '!=' ),
            ),
        ) ) )->get_results();
    }

    /** Pro members who have at least a partial profile — used for job/mentor notifications. */
    private function get_pro_members_with_profiles( int $limit = 150 ): array {
        return ( new WP_User_Query( array(
            'role'    => 'bpu_pro',
            'number'  => $limit,
            'orderby' => 'registered',
            'order'   => 'DESC',
            'meta_query' => array(
                'relation' => 'OR',
                array( 'key' => 'industry',        'value' => '', 'compare' => '!=' ),
                array( 'key' => 'skills_separate', 'value' => '', 'compare' => '!=' ),
            ),
        ) ) )->get_results();
    }

    /** Pro members who have opted in to the weekly digest. */
    private function get_pro_members_with_digest( int $limit = 200 ): array {
        return ( new WP_User_Query( array(
            'role'    => 'bpu_pro',
            'number'  => $limit,
            'orderby' => 'registered',
            'order'   => 'DESC',
            'meta_query' => array(
                array( 'key' => '_bpu_weekly_emails', 'value' => '1', 'compare' => '=' ),
            ),
        ) ) )->get_results();
    }

    /**
     * Score a member's ACF profile against a job title and type.
     * Returns an integer from 50–99. Mirrors the scoring logic in lib/api.ts.
     */
    private function score_job_match( array $profile, string $job_title, string $job_type ): int {
        $score       = 60;
        $u_industry  = strtolower( $profile['industry'] ?? '' );
        $u_field     = strtolower( $profile['industryfield_of_expertise'] ?? '' );
        $u_skills    = strtolower( $profile['skills_separate'] ?? '' );
        $u_status    = strtolower( $profile['current_employment_status'] ?? '' );
        $u_exp       = intval( $profile['years_of_experience'] ?? 0 );
        $target      = strtolower( "$job_type $job_title" );

        foreach ( (array) preg_split( '/\W+/', $u_industry ) as $w ) {
            if ( strlen( $w ) > 3 && strpos( $target, $w ) !== false ) $score += 8;
        }
        foreach ( (array) preg_split( '/\W+/', $u_field ) as $w ) {
            if ( strlen( $w ) > 3 && strpos( $target, $w ) !== false ) $score += 6;
        }
        foreach ( (array) preg_split( '/[,\s]+/', $u_skills ) as $w ) {
            if ( strlen( $w ) > 2 && strpos( $target, $w ) !== false ) $score += 4;
        }

        if ( $u_exp >= 5 )  $score += 6;
        if ( $u_exp >= 10 ) $score += 4;
        if ( strpos( $u_status, 'looking' ) !== false || strpos( $u_status, 'student' ) !== false ) {
            $score += 5;
        }

        return min( 99, max( 50, $score ) );
    }

    /**
     * Score a member's profile against a mentor's profile.
     * Returns an integer from 0–99. Shared industry = +30, shared field = +20,
     * overlapping skills = up to +20 (5 pts per shared skill).
     */
    private function score_mentor_match( array $member_profile, array $mentor_profile ): int {
        $score    = 40;
        $m_ind    = strtolower( $member_profile['industry'] ?? '' );
        $m_field  = strtolower( $member_profile['industryfield_of_expertise'] ?? '' );
        $m_skills = array_filter( array_map( 'trim', explode( ',', strtolower( $member_profile['skills_separate'] ?? '' ) ) ) );
        $t_ind    = strtolower( $mentor_profile['industry'] ?? '' );
        $t_field  = strtolower( $mentor_profile['industryfield_of_expertise'] ?? '' );
        $t_skills = array_filter( array_map( 'trim', explode( ',', strtolower( $mentor_profile['skills_separate'] ?? '' ) ) ) );

        if ( $m_ind   && $t_ind   && $m_ind   === $t_ind   ) $score += 30;
        if ( $m_field && $t_field && $m_field === $t_field ) $score += 20;

        $overlap = count( array_intersect( $m_skills, $t_skills ) );
        $score  += min( 20, $overlap * 5 );

        return min( 99, $score );
    }

    /**
     * Send a welcome email to a newly registered member.
     */
    private function send_welcome_email( WP_User $user ) {
        $to      = $user->user_email;
        $subject = __( 'Welcome to Black Professionals United!', 'bpu' );

        $message  = sprintf( __( 'Hi %s,', 'bpu' ), $user->display_name ) . "\r\n\r\n";
        $message .= __( 'Welcome to the Black Professionals United community — we are thrilled to have you.', 'bpu' ) . "\r\n\r\n";
        $message .= __( 'Here is what you can do next:', 'bpu' ) . "\r\n";
        $message .= '• ' . __( 'Complete your profile so we can match you with the right jobs and mentors', 'bpu' ) . "\r\n";
        $message .= '• ' . __( 'Upload your CV to our AI-powered CV Clinic for personalised feedback', 'bpu' ) . "\r\n";
        $message .= '• ' . __( 'Browse PAIRED — our free 1-on-1 mentorship platform', 'bpu' ) . "\r\n\r\n";
        $message .= __( 'Your member portal:', 'bpu' ) . ' https://app.blackprofessionals.uk' . "\r\n";
        $message .= __( 'Find a mentor:', 'bpu' ) . ' https://pairedbybpu.uk/mentors' . "\r\n\r\n";
        $message .= __( 'To your career success,', 'bpu' ) . "\r\n";
        $message .= __( 'The BPU Team', 'bpu' );

        wp_mail( $to, $subject, $message, array( 'Content-Type: text/plain; charset=UTF-8' ) );
    }

    /**
     * Send confirmation email to mentee and notification email to mentor on booking creation.
     */
    private function send_booking_emails( WP_User $mentee, WP_User $mentor, string $date, string $time_slot, string $notes ) {
        $headers  = array( 'Content-Type: text/plain; charset=UTF-8' );
        $portal   = 'https://pairedbybpu.uk/dashboard';
        $readable_date = date_i18n( get_option( 'date_format' ), strtotime( $date ) );
        $readable_time = str_replace( '-', ' – ', $time_slot ) . ' GMT';

        // 1. Mentee confirmation
        $mentee_subject = sprintf(
            __( 'Booking requested with %s — PAIRED by BPU', 'bpu' ),
            $mentor->display_name
        );
        $mentee_msg  = sprintf( __( 'Hi %s,', 'bpu' ), $mentee->display_name ) . "\r\n\r\n";
        $mentee_msg .= sprintf(
            __( 'Your session request with %s has been sent. They will confirm shortly.', 'bpu' ),
            $mentor->display_name
        ) . "\r\n\r\n";
        $mentee_msg .= sprintf( __( 'Date:      %s', 'bpu' ), $readable_date ) . "\r\n";
        $mentee_msg .= sprintf( __( 'Time:      %s', 'bpu' ), $readable_time ) . "\r\n";
        if ( ! empty( $notes ) ) {
            $mentee_msg .= sprintf( __( 'Your notes: %s', 'bpu' ), $notes ) . "\r\n";
        }
        $mentee_msg .= "\r\n" . __( 'View your sessions:', 'bpu' ) . ' ' . $portal . "\r\n\r\n";
        $mentee_msg .= __( 'The PAIRED Team', 'bpu' );

        wp_mail( $mentee->user_email, $mentee_subject, $mentee_msg, $headers );

        // 2. Mentor notification
        $mentor_subject = sprintf(
            __( 'New session request from %s — PAIRED by BPU', 'bpu' ),
            $mentee->display_name
        );
        $mentor_msg  = sprintf( __( 'Hi %s,', 'bpu' ), $mentor->display_name ) . "\r\n\r\n";
        $mentor_msg .= sprintf(
            __( '%s has requested a 1-on-1 session with you.', 'bpu' ),
            $mentee->display_name
        ) . "\r\n\r\n";
        $mentor_msg .= sprintf( __( 'Date:      %s', 'bpu' ), $readable_date ) . "\r\n";
        $mentor_msg .= sprintf( __( 'Time:      %s', 'bpu' ), $readable_time ) . "\r\n";
        if ( ! empty( $notes ) ) {
            $mentor_msg .= sprintf( __( 'Their notes: %s', 'bpu' ), $notes ) . "\r\n";
        }
        $mentor_msg .= "\r\n" . __( 'Log in to confirm or reschedule:', 'bpu' ) . ' ' . $portal . "\r\n\r\n";
        $mentor_msg .= __( 'The PAIRED Team', 'bpu' );

        wp_mail( $mentor->user_email, $mentor_subject, $mentor_msg, $headers );
    }

    /**
     * Helper permission check for authenticated API requests
     */
    public function check_auth_permissions() {
        return wp_get_current_user()->exists();
    }

    // ══════════════════════════════════════════════════════════════
    //  SSO TOKEN RELAY SYSTEM
    // ══════════════════════════════════════════════════════════════

    /**
     * SSO Handoff — runs on every `init`.
     * When ?bpu_sso_handoff=1&redirect_to=<url> is detected:
     *   • Logged-in  → generate one-time token, redirect to target with ?token=XYZ
     *   • Logged-out → redirect to /login?redirect_to=<handoff url>
     */
    public function handle_sso_handoff() {
        if ( empty( $_GET['bpu_sso_handoff'] ) ) {
            return;
        }

        $redirect_to = isset( $_GET['redirect_to'] ) ? esc_url_raw( wp_unslash( $_GET['redirect_to'] ) ) : '';

        // Validate the redirect target against allowed origins
        if ( ! $this->is_allowed_sso_origin( $redirect_to ) ) {
            wp_die(
                esc_html__( 'Invalid redirect target. Only authorised BPU applications are allowed.', 'bpu' ),
                esc_html__( 'SSO Error', 'bpu' ),
                array( 'response' => 403 )
            );
        }

        if ( is_user_logged_in() ) {
            // Generate a one-time token
            $token   = bin2hex( random_bytes( 32 ) ); // 64-char hex
            $user_id = get_current_user_id();

            // Store with 5-minute TTL
            $option_key = 'bpu_sso_token_' . $token;
            update_option( $option_key, array(
                'user_id'    => $user_id,
                'expires_at' => time() + 300, // 5 minutes
            ), false ); // autoload = false

            // Append token to the redirect URL.
            // wp_redirect() is intentional — wp_safe_redirect() only allows
            // redirects to the WordPress host and would block the SSO callback
            // on app.blackprofessionals.uk / pairedbybpu.uk. The target has
            // already been validated against $this->allowed_sso_origins above.
            $target_url = add_query_arg( 'token', $token, $redirect_to );
            wp_redirect( $target_url );
            exit;
        } else {
            // Build the handoff URL to return to after login
            $handoff_url = add_query_arg( array(
                'bpu_sso_handoff' => '1',
                'redirect_to'     => rawurlencode( $redirect_to ),
            ), home_url( '/' ) );

            $login_url = add_query_arg( 'redirect_to', rawurlencode( $handoff_url ), wp_login_url() );

            wp_redirect( $login_url );
            exit;
        }
    }

    /**
     * Validate a URL belongs to an allowed SSO origin.
     */
    private function is_allowed_sso_origin( $url ) {
        if ( empty( $url ) ) {
            return false;
        }

        $parsed = wp_parse_url( $url );
        if ( empty( $parsed['host'] ) || empty( $parsed['scheme'] ) ) {
            return false;
        }

        $origin = $parsed['scheme'] . '://' . $parsed['host'];

        foreach ( $this->allowed_sso_origins as $allowed ) {
            if ( strcasecmp( $origin, $allowed ) === 0 ) {
                return true;
            }
        }

        return false;
    }

    /**
     * POST /sso/exchange — Exchange a one-time SSO token for user data + JWT.
     */
    public function exchange_sso_token( WP_REST_Request $request ) {
        $token      = $request->get_param( 'token' );
        $option_key = 'bpu_sso_token_' . $token;

        $stored = get_option( $option_key );

        if ( empty( $stored ) || ! is_array( $stored ) ) {
            return new WP_Error(
                'sso_invalid_token',
                __( 'Invalid or expired SSO token.', 'bpu' ),
                array( 'status' => 401 )
            );
        }

        // Check TTL
        if ( time() > intval( $stored['expires_at'] ) ) {
            delete_option( $option_key );
            return new WP_Error(
                'sso_token_expired',
                __( 'SSO token has expired. Please initiate login again.', 'bpu' ),
                array( 'status' => 401 )
            );
        }

        // Consume the token (one-time use)
        delete_option( $option_key );

        $user_id = intval( $stored['user_id'] );
        $user    = get_userdata( $user_id );

        if ( ! $user ) {
            return new WP_Error(
                'sso_user_not_found',
                __( 'User associated with this token no longer exists.', 'bpu' ),
                array( 'status' => 404 )
            );
        }

        // Build user profile payload — ACF fields with user meta fallback
        $acf_profile = array();
        if ( function_exists( 'get_fields' ) ) {
            $fields = get_fields( 'user_' . $user_id );
            if ( $fields ) {
                $acf_profile = $fields;
            }
        }

        $flat_keys = array(
            'first_name', 'last_name', 'phone_number', 'current_employment_status',
            'level_of_education', 'industry', 'industryfield_of_expertise',
            'years_of_experience', 'skills_separate', 'user_bio', 'residence', 'linkedin_profile',
        );
        foreach ( $flat_keys as $key ) {
            if ( empty( $acf_profile[ $key ] ) ) {
                $meta = get_user_meta( $user_id, $key, true );
                if ( $meta !== '' && $meta !== false ) {
                    $acf_profile[ $key ] = $meta;
                }
            }
        }

        $cv_id  = get_user_meta( $user_id, '_bpu_member_cv_id', true );
        $cv_url = $cv_id ? wp_get_attachment_url( $cv_id ) : '';

        // Generate JWT — embed profile so the Next.js app needs no extra API call
        $now = time();
        $jwt_payload = array(
            'user_id'      => $user_id,
            'username'     => $user->user_login,
            'email'        => $user->user_email,
            'display_name' => $user->display_name,
            'roles'        => $user->roles,
            'profile'      => $acf_profile ?: null,
            'cv_url'       => $cv_url ?: null,
            'iat'          => $now,
            'exp'          => $now + DAY_IN_SECONDS, // 24 hours
        );

        $jwt = $this->generate_jwt( $jwt_payload );

        return new WP_REST_Response( array(
            'success' => true,
            'user'    => array(
                'id'           => $user_id,
                'username'     => $user->user_login,
                'email'        => $user->user_email,
                'display_name' => $user->display_name,
                'roles'        => $user->roles,
                'profile'      => $acf_profile,
                'cv_url'       => $cv_url,
            ),
            'jwt'     => $jwt,
        ), 200 );
    }

    // ══════════════════════════════════════════════════════════════
    //  MEMBER PROFILE UPDATE
    // ══════════════════════════════════════════════════════════════

    // ══════════════════════════════════════════════════════════════
    //  IN-APP AUTH ENDPOINTS
    // ══════════════════════════════════════════════════════════════

    /**
     * POST /bpu/v1/auth/login — authenticate with username/password, return JWT.
     *
     * Rate-limited: 5 attempts per IP per 5 minutes.
     */
    public function handle_login( WP_REST_Request $request ) {
        $ip       = sanitize_text_field( $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0' );
        $rate_key = 'bpu_login_rate_' . md5( $ip );
        $attempts = (int) get_transient( $rate_key );

        if ( $attempts >= 5 ) {
            return new WP_Error( 'too_many_attempts', __( 'Too many login attempts. Please wait 5 minutes.', 'bpu' ), array( 'status' => 429 ) );
        }

        $body     = $request->get_json_params();
        $username = sanitize_text_field( $body['username'] ?? '' );
        $password = $body['password'] ?? '';

        if ( empty( $username ) || empty( $password ) ) {
            return new WP_Error( 'missing_fields', __( 'Username and password are required.', 'bpu' ), array( 'status' => 400 ) );
        }

        $user = wp_authenticate( $username, $password );

        if ( is_wp_error( $user ) ) {
            set_transient( $rate_key, $attempts + 1, 5 * MINUTE_IN_SECONDS );
            return new WP_Error( 'invalid_credentials', __( 'Invalid username or password.', 'bpu' ), array( 'status' => 401 ) );
        }

        // Clear rate-limit counter on success.
        delete_transient( $rate_key );

        $jwt = $this->generate_jwt( array(
            'user_id'      => $user->ID,
            'username'     => $user->user_login,
            'email'        => $user->user_email,
            'display_name' => $user->display_name,
            'roles'        => array_values( (array) $user->roles ),
            'profile'      => get_field( 'profile_photo', 'user_' . $user->ID ) ?: '',
            'cv_url'       => get_field( 'cv_file', 'user_' . $user->ID ) ?: '',
            'iat'          => time(),
            'exp'          => time() + DAY_IN_SECONDS,
        ) );

        return rest_ensure_response( array(
            'jwt'          => $jwt,
            'display_name' => $user->display_name,
            'email'        => $user->user_email,
            'roles'        => array_values( (array) $user->roles ),
        ) );
    }

    /**
     * POST /bpu/v1/auth/register — create a WP user, save ACF fields, return JWT.
     */
    public function handle_register( WP_REST_Request $request ) {
        $body = $request->get_json_params();

        $username = sanitize_user( $body['username'] ?? '' );
        $email    = sanitize_email( $body['email'] ?? '' );
        $password = $body['password'] ?? '';

        if ( empty( $username ) || empty( $email ) || empty( $password ) ) {
            return new WP_Error( 'missing_fields', __( 'Username, email, and password are required.', 'bpu' ), array( 'status' => 400 ) );
        }

        // ── Honeypot: bots fill hidden fields, humans don't ──
        if ( ! empty( $body['website'] ) || ! empty( $body['_confirm_email'] ) ) {
            return new WP_Error( 'invalid_request', __( 'Registration failed.', 'bpu' ), array( 'status' => 400 ) );
        }

        // ── Rate limit: max 5 registrations per IP per hour ──
        $ip       = sanitize_text_field( $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0' );
        $reg_key  = 'bpu_reg_rate_' . md5( $ip );
        $reg_hits = (int) get_transient( $reg_key );
        if ( $reg_hits >= 5 ) {
            return new WP_Error( 'too_many_attempts', __( 'Too many registration attempts. Please try again later.', 'bpu' ), array( 'status' => 429 ) );
        }
        set_transient( $reg_key, $reg_hits + 1, HOUR_IN_SECONDS );

        // ── Block URL-as-username (e.g. www.something.com) ──
        if ( preg_match( '/\.(com|uk|net|org|io|in|co|info|biz|xyz|top|site|online|store|shop|blogspot|wordpress)\b/i', $username ) ) {
            return new WP_Error( 'invalid_username', __( 'That username is not allowed.', 'bpu' ), array( 'status' => 400 ) );
        }
        if ( preg_match( '#https?://#i', $username ) ) {
            return new WP_Error( 'invalid_username', __( 'That username is not allowed.', 'bpu' ), array( 'status' => 400 ) );
        }

        // ── Block disposable / known spam email domains ──
        $disposable_domains = array(
            'mailinator.com', 'guerrillamail.com', 'throwam.com', 'tempmail.com',
            'yopmail.com', 'sharklasers.com', 'spam4.me', 'trashmail.com',
            'dispostable.com', 'maildrop.cc', 'fakeinbox.com', 'getnada.com',
            'discard.email', 'mailnull.com', 'spamgourmet.com', 'mytemp.email',
        );
        $email_domain = strtolower( substr( strrchr( $email, '@' ), 1 ) );
        if ( in_array( $email_domain, $disposable_domains, true ) ) {
            return new WP_Error( 'invalid_email', __( 'Please use a permanent email address to register.', 'bpu' ), array( 'status' => 400 ) );
        }

        if ( username_exists( $username ) ) {
            return new WP_Error( 'username_taken', __( 'That username is already taken.', 'bpu' ), array( 'status' => 409 ) );
        }

        if ( email_exists( $email ) ) {
            return new WP_Error( 'email_taken', __( 'An account with that email already exists.', 'bpu' ), array( 'status' => 409 ) );
        }

        $user_id = wp_create_user( $username, $password, $email );

        if ( is_wp_error( $user_id ) ) {
            return new WP_Error( 'register_failed', $user_id->get_error_message(), array( 'status' => 500 ) );
        }

        // Core WP display name fields.
        $first = sanitize_text_field( $body['first_name'] ?? '' );
        $last  = sanitize_text_field( $body['last_name'] ?? '' );
        wp_update_user( array(
            'ID'           => $user_id,
            'first_name'   => $first,
            'last_name'    => $last,
            'display_name' => trim( "$first $last" ) ?: $username,
        ) );

        // ACF field mapping: form key => ACF field key.
        $acf_map = array(
            'phone_number'         => 'phone_number',
            'birthday'             => 'birthday',
            'gender'               => 'what_is_your_gender',
            'sexuality'            => 'sexuality',
            'education'            => 'level_of_education',
            'ethnicity'            => 'ethnicity',
            'first_gen_immigrant'  => 'first_generation_uk_immigrant',
            'disability'           => 'do_you_have_a_disability',
            'other_disability'     => 'if_yes_please_specify',
            'employment_status'    => 'current_employment_status',
            'industry'             => 'industry',
            'field_of_expertise'   => 'industryfield_of_expertise',
            'expertise_not_listed' => 'if_expertise_not_listed_please_enter_it_here',
            'years_experience'     => 'years_of_experience',
            'skills'               => 'skills_separate',
            'country'              => 'country_location',
            'where_in_uk'          => 'where_in_the_uk',
            'city'                 => 'location_city',
            'user_bio'             => 'user_bio',
        );

        foreach ( $acf_map as $form_key => $acf_key ) {
            if ( isset( $body[ $form_key ] ) && $body[ $form_key ] !== '' ) {
                update_field( $acf_key, sanitize_text_field( $body[ $form_key ] ), 'user_' . $user_id );
            }
        }

        $user = get_userdata( $user_id );

        $jwt = $this->generate_jwt( array(
            'user_id'      => $user_id,
            'username'     => $user->user_login,
            'email'        => $user->user_email,
            'display_name' => $user->display_name,
            'roles'        => array_values( (array) $user->roles ),
            'profile'      => '',
            'cv_url'       => '',
            'iat'          => time(),
            'exp'          => time() + DAY_IN_SECONDS,
        ) );

        $this->send_welcome_email( $user );

        return new WP_REST_Response( array(
            'jwt'          => $jwt,
            'display_name' => $user->display_name,
            'email'        => $user->user_email,
            'roles'        => array_values( (array) $user->roles ),
        ), 201 );
    }

    /**
     * POST /bpu/v1/auth/forgot-password — send password reset email.
     *
     * Accepts email or username. Always returns 200 to prevent user enumeration.
     * Rate-limited to 3 requests per IP per 15 minutes.
     */
    public function handle_forgot_password( WP_REST_Request $request ) {
        $ip       = sanitize_text_field( $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0' );
        $rate_key = 'bpu_forgot_rate_' . md5( $ip );
        $attempts = (int) get_transient( $rate_key );
        if ( $attempts >= 3 ) {
            return new WP_REST_Response( array(
                'success' => true,
                'message' => 'If an account exists with that email, a reset link has been sent.',
            ), 200 );
        }
        set_transient( $rate_key, $attempts + 1, 15 * MINUTE_IN_SECONDS );

        $body  = $request->get_json_params();
        $login = sanitize_text_field( $body['email'] ?? '' );

        if ( empty( $login ) ) {
            return new WP_Error( 'bpu_missing', __( 'Email is required.', 'bpu' ), array( 'status' => 400 ) );
        }

        // Always respond with success to prevent user enumeration
        $user = is_email( $login )
            ? get_user_by( 'email', $login )
            : get_user_by( 'login', $login );

        if ( $user ) {
            $token   = bin2hex( random_bytes( 32 ) ); // 64 hex chars
            $expires = time() + HOUR_IN_SECONDS;

            update_user_meta( $user->ID, '_bpu_password_reset_token',   $token );
            update_user_meta( $user->ID, '_bpu_password_reset_expires', $expires );

            $app_url   = defined( 'BPU_APP_URL' ) ? BPU_APP_URL : 'https://app.blackprofessionals.uk';
            $reset_url = $app_url . '/reset-password?token=' . $token;

            $reset_html = $this->build_email_html(
                'Reset your password',
                '<p style="margin:0 0 16px;font-size:15px;line-height:1.6;color:#555;">Hi ' . esc_html( $user->display_name ) . ',</p>'
                . '<p style="margin:0 0 16px;font-size:15px;line-height:1.6;color:#555;">We received a request to reset your password. Click the button below to set a new one. This link expires in 1 hour.</p>'
                . '<p style="margin:24px 0;text-align:center;">'
                . '<a href="' . esc_url( $reset_url ) . '" style="display:inline-block;padding:12px 32px;background:#C8102E;color:#ffffff;text-decoration:none;border-radius:8px;font-size:15px;font-weight:600;">Set new password</a>'
                . '</p>'
                . '<p style="margin:0 0 8px;font-size:13px;line-height:1.5;color:#999;">Or copy and paste this link into your browser:</p>'
                . '<p style="margin:0 0 24px;font-size:13px;line-height:1.5;color:#C8102E;word-break:break-all;">' . esc_url( $reset_url ) . '</p>'
                . '<p style="margin:0;font-size:13px;line-height:1.5;color:#999;">If you did not request this, you can safely ignore this email &mdash; your password will not change.</p>'
            );

            wp_mail(
                $user->user_email,
                'Reset your BPU password',
                $reset_html,
                array( 'Content-Type: text/html; charset=UTF-8' )
            );
        }

        return new WP_REST_Response( array(
            'success' => true,
            'message' => 'If an account exists with that email, a reset link has been sent.',
        ), 200 );
    }

    /**
     * POST /bpu/v1/auth/reset-password — validate token and set new password.
     */
    public function handle_reset_password( WP_REST_Request $request ) {
        $body     = $request->get_json_params();
        $token    = sanitize_text_field( $body['token'] ?? '' );
        $password = $body['password'] ?? '';

        if ( empty( $token ) || empty( $password ) ) {
            return new WP_Error( 'bpu_missing', __( 'Token and password are required.', 'bpu' ), array( 'status' => 400 ) );
        }

        if ( strlen( $password ) < 8 ) {
            return new WP_Error( 'bpu_weak_password', __( 'Password must be at least 8 characters.', 'bpu' ), array( 'status' => 400 ) );
        }

        // Find user with this token
        $users = get_users( array(
            'meta_key'   => '_bpu_password_reset_token',
            'meta_value' => $token,
            'number'     => 1,
        ) );

        if ( empty( $users ) ) {
            return new WP_Error( 'bpu_invalid_token', __( 'Invalid or expired reset link.', 'bpu' ), array( 'status' => 400 ) );
        }

        $user    = $users[0];
        $expires = (int) get_user_meta( $user->ID, '_bpu_password_reset_expires', true );

        if ( time() > $expires ) {
            delete_user_meta( $user->ID, '_bpu_password_reset_token' );
            delete_user_meta( $user->ID, '_bpu_password_reset_expires' );
            return new WP_Error( 'bpu_token_expired', __( 'This reset link has expired. Please request a new one.', 'bpu' ), array( 'status' => 400 ) );
        }

        wp_set_password( $password, $user->ID );
        delete_user_meta( $user->ID, '_bpu_password_reset_token' );
        delete_user_meta( $user->ID, '_bpu_password_reset_expires' );

        return new WP_REST_Response( array(
            'success' => true,
            'message' => 'Your password has been updated. You can now sign in.',
        ), 200 );
    }

    /**
     * POST /bpu/v1/member/profile — update ACF profile fields for the JWT holder.
     */
    public function update_member_profile( WP_REST_Request $request ) {
        $payload = $this->verify_jwt_bearer( $request );
        if ( ! $payload || empty( $payload['user_id'] ) ) {
            return new WP_Error( 'sso_invalid_token', __( 'Invalid token.', 'bpu' ), array( 'status' => 401 ) );
        }

        $user_id = intval( $payload['user_id'] );
        $body    = $request->get_json_params();

        $allowed = array(
            'first_name', 'last_name', 'phone_number', 'age_range',
            'birthday',
            'what_is_your_gender',
            'your_sexuality',
            'level_of_education', 'industry',
            'country_location', 'where_in_the_uk', 'location_city',
            'current_employment_status', 'industryfield_of_expertise',
            'expertise_not_listed',
            'years_of_experience', 'skills_separate', 'user_bio',
            'how_would_you_best_describe_your_ethnicity',
            'first-generation_immigrant',
            'do_you_have_any_disabilities_or_accessibility_needs_we_should_be_aware_of',
            'other_disability',
        );

        if ( ! function_exists( 'update_field' ) ) {
            return new WP_Error(
                'acf_missing',
                __( 'Advanced Custom Fields is not active.', 'bpu' ),
                array( 'status' => 500 )
            );
        }

        foreach ( $allowed as $field ) {
            if ( isset( $body[ $field ] ) ) {
                update_field( $field, sanitize_textarea_field( (string) $body[ $field ] ), 'user_' . $user_id );
            }
        }

        return new WP_REST_Response( array( 'success' => true ), 200 );
    }

    // ══════════════════════════════════════════════════════════════
    //  SSO PROFILE ENDPOINT
    // ══════════════════════════════════════════════════════════════

    /**
     * Verify a BPU JWT from the Authorization: Bearer header.
     * Returns the decoded payload array on success, WP_Error on failure.
     */
    private function verify_jwt_bearer( WP_REST_Request $request ) {
        $auth = $request->get_header( 'Authorization' );
        if ( ! $auth || ! preg_match( '/^Bearer\s+(.+)$/i', $auth, $m ) ) {
            return false;
        }

        $parts = explode( '.', trim( $m[1] ) );
        if ( count( $parts ) !== 3 ) {
            return false;
        }

        $secret   = defined( 'BPU_JWT_SECRET' ) ? BPU_JWT_SECRET : AUTH_SALT;
        $expected = $this->base64url_encode(
            hash_hmac( 'sha256', $parts[0] . '.' . $parts[1], $secret, true )
        );

        if ( ! hash_equals( $expected, $parts[2] ) ) {
            return false;
        }

        $payload = json_decode( $this->base64url_decode( $parts[1] ), true );
        if ( ! $payload || empty( $payload['exp'] ) || time() > intval( $payload['exp'] ) ) {
            return false;
        }

        return $payload;
    }

    /** Permission callback for JWT-authenticated routes. */
    public function check_jwt_bearer_auth( WP_REST_Request $request ) {
        return $this->verify_jwt_bearer( $request ) !== false;
    }

    /** Permission callback for admin-only JWT-authenticated routes. */
    public function check_admin_jwt_auth( WP_REST_Request $request ) {
        $payload = $this->verify_jwt_bearer( $request );
        if ( ! $payload || empty( $payload['user_id'] ) ) {
            return false;
        }
        $user = get_userdata( intval( $payload['user_id'] ) );
        return $user && $user->has_cap( 'promote_users' );
    }

    /** GET /bpu/v1/sso/profile — returns fresh profile data for a JWT holder. */
    public function get_sso_profile( WP_REST_Request $request ) {
        $payload = $this->verify_jwt_bearer( $request );
        if ( ! $payload || empty( $payload['user_id'] ) ) {
            return new WP_Error( 'sso_invalid_token', __( 'Invalid token.', 'bpu' ), array( 'status' => 401 ) );
        }

        $user_id = intval( $payload['user_id'] );
        $user    = get_userdata( $user_id );
        if ( ! $user ) {
            return new WP_Error( 'sso_user_not_found', __( 'User not found.', 'bpu' ), array( 'status' => 404 ) );
        }

        // Read ACF fields, then fill any missing flat fields from raw user meta as fallback.
        $acf_profile = array();
        if ( function_exists( 'get_fields' ) ) {
            $fields = get_fields( 'user_' . $user_id );
            if ( $fields ) {
                $acf_profile = $fields;
            }
        }

        $flat_keys = array(
            'first_name', 'last_name', 'phone_number', 'current_employment_status',
            'level_of_education', 'industry', 'industryfield_of_expertise',
            'years_of_experience', 'skills_separate', 'user_bio', 'residence', 'linkedin_profile',
        );
        foreach ( $flat_keys as $key ) {
            if ( empty( $acf_profile[ $key ] ) ) {
                $meta = get_user_meta( $user_id, $key, true );
                if ( $meta !== '' && $meta !== false ) {
                    $acf_profile[ $key ] = $meta;
                }
            }
        }

        $cv_id  = get_user_meta( $user_id, '_bpu_member_cv_id', true );
        $cv_url = $cv_id ? wp_get_attachment_url( $cv_id ) : '';

        $experiences    = get_user_meta( $user_id, 'bpu_experiences', true );
        $educations     = get_user_meta( $user_id, 'bpu_educations', true );
        $certifications = get_user_meta( $user_id, 'bpu_certifications', true );
        $languages      = get_user_meta( $user_id, 'bpu_languages', true );
        $cv_parsed_at   = get_user_meta( $user_id, 'bpu_cv_parsed_at', true );

        return new WP_REST_Response( array(
            'profile'        => $acf_profile,
            'cv_url'         => $cv_url,
            'experiences'    => is_array( $experiences )    ? $experiences    : array(),
            'educations'     => is_array( $educations )     ? $educations     : array(),
            'certifications' => is_array( $certifications ) ? $certifications : array(),
            'languages'      => is_string( $languages )     ? $languages      : '',
            'cv_parsed_at'   => $cv_parsed_at ?: '',
        ), 200 );
    }

    /**
     * POST /bpu/v1/member/preferences — save weekly_emails opt-in + target_role (Pro only).
     */
    public function update_member_preferences( WP_REST_Request $request ) {
        $payload = $this->verify_jwt_bearer( $request );
        if ( ! $payload || empty( $payload['user_id'] ) ) {
            return new WP_Error( 'sso_invalid_token', __( 'Invalid token.', 'bpu' ), array( 'status' => 401 ) );
        }

        $user_id = intval( $payload['user_id'] );
        if ( ! $this->is_pro_member( $user_id ) ) {
            return new WP_Error( 'bpu_not_pro', __( 'Email preferences require a BPU Pro membership.', 'bpu' ), array( 'status' => 403 ) );
        }

        $body = $request->get_json_params();

        if ( isset( $body['weekly_emails'] ) ) {
            update_user_meta( $user_id, '_bpu_weekly_emails', $body['weekly_emails'] ? '1' : '0' );
        }
        if ( isset( $body['target_role'] ) ) {
            update_user_meta( $user_id, '_bpu_target_role', sanitize_text_field( $body['target_role'] ) );
        }

        return new WP_REST_Response( array( 'success' => true ), 200 );
    }

    /**
     * POST /bpu/v1/member/request-cv-review — create a pending cv_clinic_review (Pro only).
     */
    public function handle_request_cv_review( WP_REST_Request $request ) {
        $payload = $this->verify_jwt_bearer( $request );
        if ( ! $payload || empty( $payload['user_id'] ) ) {
            return new WP_Error( 'sso_invalid_token', __( 'Invalid token.', 'bpu' ), array( 'status' => 401 ) );
        }

        $user_id = intval( $payload['user_id'] );
        if ( ! $this->is_pro_member( $user_id ) ) {
            return new WP_Error( 'bpu_not_pro', __( 'CV review requests require a BPU Pro membership.', 'bpu' ), array( 'status' => 403 ) );
        }

        $user = get_userdata( $user_id );

        // Prevent duplicate pending requests.
        $existing = get_posts( array(
            'post_type'      => 'cv_clinic_review',
            'post_status'    => 'pending',
            'posts_per_page' => 1,
            'meta_query'     => array(
                array( 'key' => '_bpu_member_id', 'value' => $user_id, 'compare' => '=' ),
            ),
        ) );
        if ( ! empty( $existing ) ) {
            return new WP_Error( 'duplicate_request', __( 'You already have a pending CV review request.', 'bpu' ), array( 'status' => 409 ) );
        }

        $cv_id  = get_user_meta( $user_id, '_bpu_member_cv_id', true );
        $cv_url = $cv_id ? wp_get_attachment_url( $cv_id ) : '';

        $post_id = wp_insert_post( array(
            'post_type'   => 'cv_clinic_review',
            'post_status' => 'pending',
            'post_title'  => sprintf( __( 'CV Review Request — %s', 'bpu' ), $user->display_name ),
            'post_author' => $user_id,
            'meta_input'  => array(
                '_bpu_member_id'    => $user_id,
                '_bpu_member_email' => $user->user_email,
                '_bpu_cv_url'       => $cv_url,
            ),
        ) );

        if ( is_wp_error( $post_id ) ) {
            return new WP_Error( 'request_failed', __( 'Could not submit CV review request.', 'bpu' ), array( 'status' => 500 ) );
        }

        wp_mail(
            get_option( 'admin_email' ),
            sprintf( __( 'New CV Review Request from %s', 'bpu' ), $user->display_name ),
            sprintf(
                "A BPU Pro member has requested a CV review.\n\nName: %s\nEmail: %s\nCV: %s\n\nReview in WP Admin:\n%s",
                $user->display_name,
                $user->user_email,
                $cv_url ?: 'No CV uploaded',
                admin_url( 'post.php?post=' . $post_id . '&action=edit' )
            ),
            array( 'Content-Type: text/plain; charset=UTF-8' )
        );

        return new WP_REST_Response( array( 'success' => true, 'request_id' => $post_id ), 201 );
    }

    /**
     * WP Cron handler — sends weekly job digests to opted-in Pro members.
     */
    public function send_weekly_job_digests() {
        $members = $this->get_pro_members_with_digest( 200 );
        if ( empty( $members ) ) {
            return;
        }

        $jobs = get_posts( array(
            'post_type'      => 'job_listing',
            'post_status'    => 'publish',
            'posts_per_page' => 10,
            'orderby'        => 'date',
            'order'          => 'DESC',
        ) );

        if ( empty( $jobs ) ) {
            return;
        }

        foreach ( $members as $member ) {
            $profile = function_exists( 'get_fields' )
                ? ( get_fields( 'user_' . $member->ID ) ?: array() )
                : array();

            $matched = array();
            foreach ( $jobs as $job ) {
                $job_type = get_post_meta( $job->ID, '_job_type', true );
                $score    = $this->score_job_match( $profile, $job->post_title, $job_type );
                if ( $score >= 60 ) {
                    $matched[] = array(
                        'title'   => $job->post_title,
                        'company' => get_post_meta( $job->ID, '_company_name', true ) ?: 'Partner Organisation',
                        'score'   => $score,
                    );
                }
            }

            if ( empty( $matched ) ) {
                continue;
            }

            usort( $matched, function ( $a, $b ) { return $b['score'] - $a['score']; } );
            $top = array_slice( $matched, 0, 5 );

            $message  = sprintf( __( 'Hi %s,', 'bpu' ), $member->display_name ) . "\r\n\r\n";
            $message .= __( 'Your top job matches this week from BPU:', 'bpu' ) . "\r\n\r\n";
            foreach ( $top as $i => $j ) {
                $message .= sprintf( "%d. %s — %s (%d%% match)\r\n", $i + 1, $j['title'], $j['company'], $j['score'] );
            }
            $message .= "\r\n" . __( 'View all in your member portal:', 'bpu' ) . "\r\n";
            $message .= "https://app.blackprofessionals.uk\r\n\r\n";
            $message .= __( 'To unsubscribe, visit My Profile › Email Preferences.', 'bpu' ) . "\r\n";
            $message .= __( 'The BPU Team', 'bpu' );

            wp_mail(
                $member->user_email,
                __( 'Your Weekly BPU Job Digest', 'bpu' ),
                $message,
                array( 'Content-Type: text/plain; charset=UTF-8' )
            );
        }
    }

    /** Add a "weekly" interval to WP Cron schedules. */
    public function add_weekly_cron_schedule( $schedules ) {
        if ( ! isset( $schedules['weekly'] ) ) {
            $schedules['weekly'] = array(
                'interval' => 7 * DAY_IN_SECONDS,
                'display'  => __( 'Once Weekly', 'bpu' ),
            );
        }
        return $schedules;
    }

    /** Base64url-decode (RFC 7515). */
    private function base64url_decode( $data ) {
        return base64_decode( strtr( $data, '-_', '+/' ) . str_repeat( '=', 3 - ( 3 + strlen( $data ) ) % 4 ) );
    }

    // ══════════════════════════════════════════════════════════════
    //  JWT HELPERS (HMAC-SHA256, no external library)
    // ══════════════════════════════════════════════════════════════

    /**
     * Base64url-encode (RFC 7515).
     */
    private function base64url_encode( $data ) {
        return rtrim( strtr( base64_encode( $data ), '+/', '-_' ), '=' );
    }

    /**
     * Generate a signed JWT (HS256).
     */
    private function generate_jwt( array $payload ) {
        $secret = defined( 'BPU_JWT_SECRET' ) ? BPU_JWT_SECRET : AUTH_SALT;

        $header = $this->base64url_encode( wp_json_encode( array(
            'alg' => 'HS256',
            'typ' => 'JWT',
        ) ) );

        $body = $this->base64url_encode( wp_json_encode( $payload ) );

        $signature = $this->base64url_encode(
            hash_hmac( 'sha256', $header . '.' . $body, $secret, true )
        );

        return $header . '.' . $body . '.' . $signature;
    }

    // ══════════════════════════════════════════════════════════════
    //  HTML EMAIL HELPER
    // ══════════════════════════════════════════════════════════════

    private function build_email_html( $heading, $body_html ) {
        return '<!DOCTYPE html><html><head><meta charset="utf-8"></head>'
            . '<body style="margin:0;padding:0;background:#f5f5f5;font-family:-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica Neue,Arial,sans-serif;">'
            . '<table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f5f5;padding:40px 20px;">'
            . '<tr><td align="center">'
            . '<table width="100%" cellpadding="0" cellspacing="0" style="max-width:520px;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.1);">'
            . '<tr><td style="background:#C8102E;padding:24px 32px;">'
            . '<img src="https://blackprofessionals.uk/wp-content/uploads/2025/03/bpu_logo-.png" alt="BPU" height="28" style="display:block;filter:brightness(0) invert(1);">'
            . '</td></tr>'
            . '<tr><td style="padding:32px;">'
            . '<h1 style="margin:0 0 16px;font-size:22px;color:#1a1a1a;">' . $heading . '</h1>'
            . $body_html
            . '</td></tr>'
            . '<tr><td style="padding:20px 32px;background:#fafafa;border-top:1px solid #eee;">'
            . '<p style="margin:0;font-size:12px;color:#999;text-align:center;">&copy; ' . date( 'Y' ) . ' Black Professionals United. All rights reserved.</p>'
            . '</td></tr>'
            . '</table>'
            . '</td></tr></table></body></html>';
    }

    // ══════════════════════════════════════════════════════════════
    //  MENTOR DIRECTORY ENDPOINTS
    // ══════════════════════════════════════════════════════════════

    /**
     * GET /mentors — Paginated, filterable list of mentors.
     */
    public function get_mentors( WP_REST_Request $request ) {
        $page     = max( 1, $request->get_param( 'page' ) );
        $per_page = min( 100, max( 1, $request->get_param( 'per_page' ) ) );
        $industry = $request->get_param( 'industry' );
        $search   = $request->get_param( 'search' );

        $user_args = array(
            'role'   => 'mentor',
            'number' => $per_page,
            'paged'  => $page,
            'orderby' => 'display_name',
            'order'   => 'ASC',
        );

        if ( ! empty( $search ) ) {
            $user_args['search']         = '*' . $search . '*';
            $user_args['search_columns'] = array( 'display_name', 'user_login', 'user_email' );
        }

        // If filtering by industry via ACF meta
        if ( ! empty( $industry ) ) {
            $user_args['meta_query'] = array(
                array(
                    'key'     => 'industry',
                    'value'   => $industry,
                    'compare' => '=',
                ),
            );
        }

        $user_query = new WP_User_Query( $user_args );
        $mentors    = array();

        foreach ( $user_query->get_results() as $user ) {
            $mentors[] = $this->format_mentor_summary( $user );
        }

        $total       = $user_query->get_total();
        $total_pages = (int) ceil( $total / $per_page );

        $response = new WP_REST_Response( array(
            'success'     => true,
            'mentors'     => $mentors,
            'total'       => $total,
            'page'        => $page,
            'per_page'    => $per_page,
            'total_pages' => $total_pages,
        ), 200 );

        // Standard pagination headers
        $response->header( 'X-WP-Total', $total );
        $response->header( 'X-WP-TotalPages', $total_pages );

        return $response;
    }

    /**
     * GET /mentors/{id} — Single mentor profile.
     */
    public function get_single_mentor( WP_REST_Request $request ) {
        $user_id = $request->get_param( 'id' );
        $user    = get_userdata( $user_id );

        if ( ! $user || ! in_array( 'mentor', (array) $user->roles, true ) ) {
            return new WP_Error(
                'mentor_not_found',
                __( 'Mentor not found.', 'bpu' ),
                array( 'status' => 404 )
            );
        }

        $profile = $this->format_mentor_detail( $user );

        return new WP_REST_Response( array(
            'success' => true,
            'mentor'  => $profile,
        ), 200 );
    }

    /**
     * Compute average rating and review count for a mentor.
     */
    private function get_mentor_review_stats( $mentor_id ) {
        $query = new WP_Query( array(
            'post_type'      => 'mentor_review',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'meta_query'     => array(
                array( 'key' => '_review_mentor_id', 'value' => $mentor_id, 'compare' => '=' ),
            ),
        ) );

        $total_rating = 0;
        $review_count = 0;

        if ( $query->have_posts() ) {
            foreach ( $query->posts as $pid ) {
                $total_rating += (int) get_post_meta( $pid, '_review_rating', true );
                $review_count++;
            }
            wp_reset_postdata();
        }

        return array(
            'average_rating' => $review_count > 0 ? round( $total_rating / $review_count, 2 ) : 0,
            'review_count'   => $review_count,
        );
    }

    /**
     * Format a mentor user for list responses (summary).
     */
    private function format_mentor_summary( WP_User $user ) {
        $acf          = $this->get_mentor_acf_fields( $user->ID );
        $review_stats = $this->get_mentor_review_stats( $user->ID );

        return array(
            'id'                        => $user->ID,
            'display_name'              => $user->display_name,
            'avatar_url'                => get_avatar_url( $user->ID, array( 'size' => 256 ) ),
            'industry'                  => isset( $acf['industry'] ) ? $acf['industry'] : '',
            'years_of_experience'       => isset( $acf['years_of_experience'] ) ? $acf['years_of_experience'] : '',
            'skills_separate'           => isset( $acf['skills_separate'] ) ? $acf['skills_separate'] : '',
            'user_bio'                  => isset( $acf['user_bio'] ) ? $acf['user_bio'] : '',
            'industryfield_of_expertise' => isset( $acf['industryfield_of_expertise'] ) ? $acf['industryfield_of_expertise'] : '',
            'company'                   => isset( $acf['company'] ) ? $acf['company'] : '',
            'current_role'              => isset( $acf['current_role'] ) ? $acf['current_role'] : '',
            'average_rating'            => $review_stats['average_rating'],
            'review_count'              => $review_stats['review_count'],
        );
    }

    /**
     * Format a mentor user for detail responses (full profile).
     */
    private function format_mentor_detail( WP_User $user ) {
        $acf = $this->get_mentor_acf_fields( $user->ID );

        // Availability data (stored as user meta)
        $availability = get_user_meta( $user->ID, '_bpu_mentor_availability', true );

        // Work experience
        $exp_query = new WP_Query( array(
            'post_type'      => 'mentor_experience',
            'post_status'    => 'any',
            'posts_per_page' => 50,
            'meta_query'     => array(
                array( 'key' => '_mentor_exp_user_id', 'value' => $user->ID, 'compare' => '=' ),
            ),
            'orderby'        => 'meta_value',
            'meta_key'       => '_mentor_exp_start_date',
            'order'          => 'DESC',
        ) );
        $experiences = array();
        if ( $exp_query->have_posts() ) {
            while ( $exp_query->have_posts() ) {
                $exp_query->the_post();
                $experiences[] = $this->format_experience( get_the_ID() );
            }
            wp_reset_postdata();
        }

        // Education
        $edu_query = new WP_Query( array(
            'post_type'      => 'mentor_education',
            'post_status'    => 'any',
            'posts_per_page' => 50,
            'meta_query'     => array(
                array( 'key' => '_mentor_edu_user_id', 'value' => $user->ID, 'compare' => '=' ),
            ),
            'orderby'        => 'meta_value',
            'meta_key'       => '_mentor_edu_start_year',
            'order'          => 'DESC',
        ) );
        $education = array();
        if ( $edu_query->have_posts() ) {
            while ( $edu_query->have_posts() ) {
                $edu_query->the_post();
                $education[] = $this->format_education( get_the_ID() );
            }
            wp_reset_postdata();
        }

        $review_stats = $this->get_mentor_review_stats( $user->ID );

        return array(
            'id'                        => $user->ID,
            'display_name'              => $user->display_name,
            'username'                  => $user->user_login,
            'avatar_url'                => get_avatar_url( $user->ID, array( 'size' => 512 ) ),
            'profile'                   => $acf,
            'availability'              => ! empty( $availability ) ? $availability : array(),
            'experiences'               => $experiences,
            'education'                 => $education,
            'registered_date'           => $user->user_registered,
            'average_rating'            => $review_stats['average_rating'],
            'review_count'              => $review_stats['review_count'],
        );
    }

    /**
     * Retrieve relevant ACF fields for a mentor.
     */
    private function get_mentor_acf_fields( $user_id ) {
        if ( ! function_exists( 'get_fields' ) ) {
            // Fallback: return raw usermeta for known keys
            $keys = array(
                'industry',
                'years_of_experience',
                'skills_separate',
                'user_bio',
                'industryfield_of_expertise',
                'first_name',
                'last_name',
                'phone_number',
                'current_employment_status',
                'current_role',
                'company',
                'level_of_education',
                'linkedin_profile',
                'facebook_profile',
                'instagram_profile',
                'x_profile',
                'residence',
                'mentorship_availability',
                'mentorship_requirements',
                'mentees_at_once',
                'gender',
                'employment_status',
            );
            $fields = array();
            foreach ( $keys as $key ) {
                $val = get_user_meta( $user_id, $key, true );
                if ( '' !== $val ) {
                    $fields[ $key ] = $val;
                }
            }
            return $fields;
        }

        $all_fields = get_fields( 'user_' . $user_id );
        return is_array( $all_fields ) ? $all_fields : array();
    }

    // ══════════════════════════════════════════════════════════════
    //  MENTORSHIP BOOKING ENDPOINTS
    // ══════════════════════════════════════════════════════════════

    /**
     * POST /bookings — Create a new mentorship booking.
     */
    public function create_booking( WP_REST_Request $request ) {
        $payload = $this->verify_jwt_bearer( $request );
        if ( ! $payload || empty( $payload['user_id'] ) ) {
            return new WP_Error( 'sso_invalid_token', __( 'Invalid or missing token.', 'bpu' ), array( 'status' => 401 ) );
        }
        $user_id    = intval( $payload['user_id'] );
        $mentor_id  = $request->get_param( 'mentor_id' );
        $session_id = absint( $request->get_param( 'session_id' ) );
        $date       = $request->get_param( 'date' );
        $time_slot  = $request->get_param( 'time_slot' );
        $notes      = $request->get_param( 'notes' );

        // Verify the mentor exists and has the mentor role
        $mentor = get_userdata( $mentor_id );
        if ( ! $mentor || ! in_array( 'mentor', (array) $mentor->roles, true ) ) {
            return new WP_Error(
                'booking_invalid_mentor',
                __( 'The specified mentor does not exist.', 'bpu' ),
                array( 'status' => 400 )
            );
        }

        // Prevent booking yourself
        if ( $user_id === $mentor_id ) {
            return new WP_Error(
                'booking_self',
                __( 'You cannot book a session with yourself.', 'bpu' ),
                array( 'status' => 400 )
            );
        }

        // Check group booking capacity
        $is_group  = false;
        $slot_cap  = 1;
        if ( $session_id ) {
            $is_group = (bool) get_post_meta( $session_id, '_session_group_booking', true );
            $slot_cap = (int) get_post_meta( $session_id, '_session_slot_capacity', true ) ?: 1;
        }

        // Check for duplicate bookings (same mentee + mentor + date + time)
        $slot_bookings = new WP_Query( array(
            'post_type'   => 'mentorship_booking',
            'post_status' => array( 'publish', 'pending' ),
            'posts_per_page' => -1,
            'meta_query'  => array(
                'relation' => 'AND',
                array( 'key' => '_bpu_booking_mentor_id', 'value' => $mentor_id, 'compare' => '=' ),
                array( 'key' => '_bpu_booking_date', 'value' => $date, 'compare' => '=' ),
                array( 'key' => '_bpu_booking_time_slot', 'value' => $time_slot, 'compare' => '=' ),
                array( 'key' => '_bpu_booking_status', 'value' => array( 'pending', 'confirmed' ), 'compare' => 'IN' ),
            ),
        ) );

        $existing_count = $slot_bookings->found_posts;

        // Check if this user already booked this exact slot
        foreach ( $slot_bookings->posts as $existing_post ) {
            $existing_mentee = (int) get_post_meta( $existing_post->ID, '_bpu_booking_mentee_id', true );
            if ( $existing_mentee === $user_id ) {
                wp_reset_postdata();
                return new WP_Error(
                    'booking_duplicate',
                    __( 'You already have a booking for this slot.', 'bpu' ),
                    array( 'status' => 409 )
                );
            }
        }
        wp_reset_postdata();

        // For non-group sessions, only 1 booking per slot
        if ( ! $is_group && $existing_count >= 1 ) {
            return new WP_Error( 'slot_full', __( 'This time slot is no longer available.', 'bpu' ), array( 'status' => 409 ) );
        }

        // For group sessions, check capacity
        if ( $is_group && $existing_count >= $slot_cap ) {
            return new WP_Error( 'slot_full', __( 'This group session is full.', 'bpu' ), array( 'status' => 409 ) );
        }

        // Create the booking post
        $mentee = get_userdata( $user_id );
        $title  = sprintf(
            '%s → %s | %s %s',
            $mentee->display_name,
            $mentor->display_name,
            $date,
            $time_slot
        );

        $post_id = wp_insert_post( array(
            'post_type'   => 'mentorship_booking',
            'post_title'  => sanitize_text_field( $title ),
            'post_status' => 'pending', // Admin can approve / auto-publish
            'post_author' => $user_id,
        ), true );

        if ( is_wp_error( $post_id ) ) {
            return $post_id;
        }

        // Save booking metadata
        update_post_meta( $post_id, '_bpu_booking_mentee_id', $user_id );
        update_post_meta( $post_id, '_bpu_booking_mentor_id', $mentor_id );
        update_post_meta( $post_id, '_bpu_booking_session_id', $session_id );
        update_post_meta( $post_id, '_bpu_booking_date', $date );
        update_post_meta( $post_id, '_bpu_booking_time_slot', $time_slot );
        update_post_meta( $post_id, '_bpu_booking_notes', $notes );
        update_post_meta( $post_id, '_bpu_booking_status', 'pending' );
        update_post_meta( $post_id, '_bpu_booking_created_at', current_time( 'mysql' ) );
        if ( $is_group ) {
            update_post_meta( $post_id, '_bpu_booking_is_group', 1 );
        }

        $this->send_booking_emails( $mentee, $mentor, $date, $time_slot, $notes );

        // Create in-app notification for the mentor about the new booking
        $mentee_name = $mentee ? $mentee->display_name : 'A mentee';
        $this->create_notification(
            $mentor_id,
            'new_booking',
            __( 'New Booking Request', 'bpu' ),
            sprintf( '%s has requested a session on %s.', esc_html( $mentee_name ), esc_html( $date ) ),
            '/paired/dashboard'
        );

        return new WP_REST_Response( array(
            'success' => true,
            'booking' => $this->format_booking( $post_id, $user_id ),
        ), 201 );
    }

    /**
     * GET /bookings — List the current user's bookings.
     */
    public function get_bookings( WP_REST_Request $request ) {
        $payload = $this->verify_jwt_bearer( $request );
        if ( ! $payload || empty( $payload['user_id'] ) ) {
            return new WP_Error( 'sso_invalid_token', __( 'Invalid or missing token.', 'bpu' ), array( 'status' => 401 ) );
        }
        $user_id  = intval( $payload['user_id'] );
        $page     = max( 1, $request->get_param( 'page' ) );
        $per_page = min( 100, max( 1, $request->get_param( 'per_page' ) ) );

        $query = new WP_Query( array(
            'post_type'      => 'mentorship_booking',
            'post_status'    => array( 'publish', 'pending', 'draft' ),
            'posts_per_page' => $per_page,
            'paged'          => $page,
            'orderby'        => 'meta_value',
            'meta_key'       => '_bpu_booking_date',
            'order'          => 'ASC',
            'meta_query'     => array(
                'relation' => 'OR',
                array( 'key' => '_bpu_booking_mentee_id', 'value' => $user_id, 'compare' => '=' ),
                array( 'key' => '_bpu_booking_mentor_id', 'value' => $user_id, 'compare' => '=' ),
            ),
        ) );

        $bookings = array();
        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $bookings[] = $this->format_booking( get_the_ID(), $user_id );
            }
            wp_reset_postdata();
        }

        $total       = $query->found_posts;
        $total_pages = (int) ceil( $total / $per_page );

        $response = new WP_REST_Response( array(
            'success'     => true,
            'bookings'    => $bookings,
            'total'       => $total,
            'page'        => $page,
            'per_page'    => $per_page,
            'total_pages' => $total_pages,
        ), 200 );

        $response->header( 'X-WP-Total', $total );
        $response->header( 'X-WP-TotalPages', $total_pages );

        return $response;
    }

    /**
     * GET /bookings/{id} — Single booking detail.
     */
    public function get_single_booking( WP_REST_Request $request ) {
        $payload = $this->verify_jwt_bearer( $request );
        if ( ! $payload || empty( $payload['user_id'] ) ) {
            return new WP_Error( 'sso_invalid_token', __( 'Invalid or missing token.', 'bpu' ), array( 'status' => 401 ) );
        }
        $user_id = intval( $payload['user_id'] );
        $post_id = $request->get_param( 'id' );

        $post = get_post( $post_id );
        if ( ! $post || 'mentorship_booking' !== $post->post_type ) {
            return new WP_Error(
                'booking_not_found',
                __( 'Booking not found.', 'bpu' ),
                array( 'status' => 404 )
            );
        }

        // Ensure the current user is either the mentee or the mentor
        $mentee_id = (int) get_post_meta( $post_id, '_bpu_booking_mentee_id', true );
        $mentor_id = (int) get_post_meta( $post_id, '_bpu_booking_mentor_id', true );

        if ( $user_id !== $mentee_id && $user_id !== $mentor_id ) {
            return new WP_Error(
                'booking_forbidden',
                __( 'You do not have permission to view this booking.', 'bpu' ),
                array( 'status' => 403 )
            );
        }

        return new WP_REST_Response( array(
            'success' => true,
            'booking' => $this->format_booking( $post_id, $user_id ),
        ), 200 );
    }

    // ══════════════════════════════════════════════════════════════
    //  EVENTS (THE EVENTS CALENDAR)
    // ══════════════════════════════════════════════════════════════

    /**
     * GET /events — Paginated upcoming event list using The Events Calendar.
     */
    public function get_registered_events( WP_REST_Request $request ) {
        $user_id = get_current_user_id();

        // Query Tribe attendee records belonging to this WP user.
        // Covers both RSVP (tribe_rsvp_attendees) and ticket (tribe_attendee) post types.
        $attendees = get_posts( array(
            'post_type'      => array( 'tribe_rsvp_attendees', 'tribe_attendee' ),
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'meta_query'     => array(
                array(
                    'key'   => '_tribe_rsvp_attendee_user_id',
                    'value' => $user_id,
                ),
            ),
        ) );

        $event_ids = array();
        foreach ( $attendees as $attendee ) {
            $event_id = get_post_meta( $attendee->ID, '_tribe_rsvp_event', true )
                ?: get_post_meta( $attendee->ID, '_tribe_tpp_event', true );
            if ( $event_id ) {
                $event_ids[] = (int) $event_id;
            }
        }
        $event_ids = array_unique( $event_ids );

        if ( empty( $event_ids ) ) {
            return new WP_REST_Response( array( 'success' => true, 'events' => array() ), 200 );
        }

        $query = new WP_Query( array(
            'post_type'      => 'tribe_events',
            'post_status'    => 'publish',
            'post__in'       => $event_ids,
            'posts_per_page' => -1,
            'orderby'        => 'meta_value',
            'meta_key'       => '_EventStartDate',
            'order'          => 'ASC',
        ) );

        $events = array();
        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $post_id  = get_the_ID();
                $events[] = array(
                    'id'           => $post_id,
                    'title'        => get_the_title(),
                    'description'  => wp_strip_all_tags( get_the_excerpt() ),
                    'start_date'   => get_post_meta( $post_id, '_EventStartDate', true ),
                    'end_date'     => get_post_meta( $post_id, '_EventEndDate',   true ),
                    'venue'        => get_post_meta( $post_id, '_EventVenueID', true )
                        ? tribe_get_venue( $post_id ) : '',
                    'cost'         => get_post_meta( $post_id, '_EventCost', true ) ?: 'Free',
                    'url'          => get_the_permalink(),
                    'image'        => get_the_post_thumbnail_url( $post_id, 'medium' ) ?: '',
                    'is_virtual'   => (bool) get_post_meta( $post_id, '_tribe_events_venue_show_map', true ),
                    'register_url' => function_exists( 'tribe_get_tickets_link' )
                        ? tribe_get_tickets_link( $post_id ) : get_the_permalink(),
                );
            }
            wp_reset_postdata();
        }

        return new WP_REST_Response( array( 'success' => true, 'events' => $events ), 200 );
    }

    public function get_events( WP_REST_Request $request ) {
        $page     = max( 1, $request->get_param( 'page' ) );
        $per_page = min( 50, max( 1, $request->get_param( 'per_page' ) ) );

        // The Events Calendar stores events as the 'tribe_events' post type.
        $today = date( 'Y-m-d' );

        $query_args = array(
            'post_type'      => 'tribe_events',
            'post_status'    => 'publish',
            'posts_per_page' => $per_page,
            'paged'          => $page,
            'orderby'        => 'meta_value',
            'meta_key'       => '_EventStartDate',
            'order'          => 'ASC',
            'meta_query'     => array(
                array(
                    'key'     => '_EventStartDate',
                    'value'   => $today,
                    'compare' => '>=',
                    'type'    => 'DATETIME',
                ),
            ),
        );

        $query  = new WP_Query( $query_args );
        $events = array();

        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $post_id = get_the_ID();
                $events[] = array(
                    'id'          => $post_id,
                    'title'       => get_the_title(),
                    'description' => wp_strip_all_tags( get_the_excerpt() ),
                    'start_date'  => get_post_meta( $post_id, '_EventStartDate',    true ),
                    'end_date'    => get_post_meta( $post_id, '_EventEndDate',      true ),
                    'venue'       => get_post_meta( $post_id, '_EventVenueID',      true )
                        ? tribe_get_venue( $post_id )
                        : '',
                    'cost'        => get_post_meta( $post_id, '_EventCost',         true ) ?: 'Free',
                    'url'         => get_the_permalink(),
                    'image'       => get_the_post_thumbnail_url( $post_id, 'medium' ) ?: '',
                    'is_virtual'  => (bool) get_post_meta( $post_id, '_tribe_events_venue_show_map', true ),
                    'register_url' => function_exists( 'tribe_get_tickets_link' )
                        ? tribe_get_tickets_link( $post_id )
                        : get_the_permalink(),
                );
            }
            wp_reset_postdata();
        }

        $total       = $query->found_posts;
        $total_pages = (int) ceil( $total / $per_page );

        $response = new WP_REST_Response( array(
            'success'     => true,
            'events'      => $events,
            'total'       => $total,
            'page'        => $page,
            'per_page'    => $per_page,
            'total_pages' => $total_pages,
        ), 200 );

        $response->header( 'X-WP-Total',      $total );
        $response->header( 'X-WP-TotalPages', $total_pages );

        return $response;
    }

    /**
     * Format a booking post into a structured response array.
     */
    private function format_booking( $post_id, $current_user_id ) {
        $mentee_id = (int) get_post_meta( $post_id, '_bpu_booking_mentee_id', true );
        $mentor_id = (int) get_post_meta( $post_id, '_bpu_booking_mentor_id', true );

        $mentee = get_userdata( $mentee_id );
        $mentor = get_userdata( $mentor_id );

        return array(
            'id'         => $post_id,
            'date'       => get_post_meta( $post_id, '_bpu_booking_date', true ),
            'time_slot'  => get_post_meta( $post_id, '_bpu_booking_time_slot', true ),
            'notes'      => get_post_meta( $post_id, '_bpu_booking_notes', true ),
            'status'     => get_post_meta( $post_id, '_bpu_booking_status', true ),
            'created_at' => get_post_meta( $post_id, '_bpu_booking_created_at', true ),
            'role'       => ( $current_user_id === $mentee_id ) ? 'mentee' : 'mentor',
            'mentor'     => $mentor ? array(
                'id'           => $mentor->ID,
                'display_name' => $mentor->display_name,
                'avatar_url'   => get_avatar_url( $mentor->ID, array( 'size' => 128 ) ),
            ) : null,
            'mentee'     => $mentee ? array(
                'id'           => $mentee->ID,
                'display_name' => $mentee->display_name,
                'avatar_url'   => get_avatar_url( $mentee->ID, array( 'size' => 128 ) ),
            ) : null,
            'mentee_profile' => $mentee ? array(
                'career_goals'      => get_user_meta( $mentee->ID, '_paired_career_goals', true ) ?: '',
                'skills_to_develop' => get_user_meta( $mentee->ID, '_paired_skills_to_develop', true ) ?: '',
                'industry'          => get_user_meta( $mentee->ID, '_paired_industry', true ) ?: '',
                'bio'               => get_user_meta( $mentee->ID, '_paired_bio', true ) ?: '',
            ) : null,
            'payment_status'  => get_post_meta( $post_id, '_bpu_booking_payment_status', true ) ?: 'not_required',
            'payment_amount'  => (float) get_post_meta( $post_id, '_bpu_booking_payment_amount', true ),
            'meet_link'       => get_post_meta( $post_id, '_bpu_booking_meet_link', true ) ?: '',
            'is_group_session' => (bool) get_post_meta( $post_id, '_bpu_booking_is_group', true ),
            'session_id'      => (int) get_post_meta( $post_id, '_bpu_booking_session_id', true ),
        );
    }

    // ══════════════════════════════════════════════════════════════
    //  MENTOR SELF-SERVICE ENDPOINTS
    // ══════════════════════════════════════════════════════════════

    /**
     * GET /paired/mentor/profile — Return the authenticated mentor's own full profile.
     */
    public function get_mentor_own_profile( WP_REST_Request $request ) {
        $payload = $this->verify_jwt_bearer( $request );
        if ( ! $payload || empty( $payload['user_id'] ) ) {
            return new WP_Error( 'sso_invalid_token', __( 'Invalid or missing token.', 'bpu' ), array( 'status' => 401 ) );
        }

        $user_id = intval( $payload['user_id'] );
        $user    = get_userdata( $user_id );

        if ( ! $user ) {
            return new WP_Error( 'user_not_found', __( 'User not found.', 'bpu' ), array( 'status' => 404 ) );
        }

        if ( ! in_array( 'mentor', (array) $user->roles, true ) ) {
            return new WP_Error( 'not_a_mentor', __( 'You do not have the mentor role.', 'bpu' ), array( 'status' => 403 ) );
        }

        // All profile meta keys we expose
        $keys = array(
            'first_name',
            'last_name',
            'phone_number',
            'residence',
            'gender',
            'bp_network',
            'employment_status',
            'industry',
            'industryfield_of_expertise',
            'current_role',
            'company',
            'years_of_experience',
            'skills_separate',
            'user_bio',
            'mentorship_availability',
            'mentorship_requirements',
            'mentees_at_once',
            'linkedin_profile',
            'facebook_profile',
            'instagram_profile',
            'x_profile',
            'paired_image_path',
            // Legacy keys also returned for backwards compat
            'current_employment_status',
            'level_of_education',
        );

        $profile = array();

        // Prefer ACF fields if available, fall back to raw usermeta
        if ( function_exists( 'get_fields' ) ) {
            $acf = get_fields( 'user_' . $user_id );
            if ( is_array( $acf ) ) {
                $profile = $acf;
            }
        }

        // Always merge the explicit keys from raw usermeta so nothing is missed
        foreach ( $keys as $key ) {
            if ( ! isset( $profile[ $key ] ) ) {
                $val = get_user_meta( $user_id, $key, true );
                if ( '' !== $val ) {
                    $profile[ $key ] = $val;
                }
            }
        }

        return new WP_REST_Response( array(
            'success'      => true,
            'profile'      => $profile,
            'display_name' => $user->display_name,
            'email'        => $user->user_email,
            'avatar_url'   => get_avatar_url( $user_id, array( 'size' => 256 ) ),
        ), 200 );
    }

    /**
     * PUT /paired/mentor/profile — Update the authenticated mentor's profile fields.
     */
    public function update_mentor_profile( WP_REST_Request $request ) {
        $payload = $this->verify_jwt_bearer( $request );
        if ( ! $payload || empty( $payload['user_id'] ) ) {
            return new WP_Error( 'sso_invalid_token', __( 'Invalid or missing token.', 'bpu' ), array( 'status' => 401 ) );
        }

        $user_id = intval( $payload['user_id'] );
        $user    = get_userdata( $user_id );

        if ( ! $user ) {
            return new WP_Error( 'user_not_found', __( 'User not found.', 'bpu' ), array( 'status' => 404 ) );
        }

        if ( ! in_array( 'mentor', (array) $user->roles, true ) ) {
            return new WP_Error( 'not_a_mentor', __( 'You do not have the mentor role.', 'bpu' ), array( 'status' => 403 ) );
        }

        $body = $request->get_json_params();
        if ( ! is_array( $body ) ) {
            $body = array();
        }

        $allowed_keys = array(
            'bio'                        => 'user_bio',
            'industry'                   => 'industry',
            'industryfield_of_expertise' => 'industryfield_of_expertise',
            'current_role'               => 'current_role',
            'company'                    => 'company',
            'years_of_experience'        => 'years_of_experience',
            'skills_separate'            => 'skills_separate',
            'employment_status'          => 'employment_status',
            'mentorship_availability'    => 'mentorship_availability',
            'mentorship_requirements'    => 'mentorship_requirements',
            'mentees_at_once'            => 'mentees_at_once',
            'linkedin_profile'           => 'linkedin_profile',
            'facebook_profile'           => 'facebook_profile',
            'instagram_profile'          => 'instagram_profile',
            'x_profile'                  => 'x_profile',
            'residence'                  => 'residence',
            'first_name'                 => 'first_name',
            'last_name'                  => 'last_name',
            'phone_number'               => 'phone_number',
            'bp_network'                 => 'bp_network',
        );

        foreach ( $allowed_keys as $request_key => $meta_key ) {
            if ( array_key_exists( $request_key, $body ) ) {
                $val = sanitize_text_field( (string) $body[ $request_key ] );
                update_user_meta( $user_id, $meta_key, $val );
            }
        }

        // Handle bio separately to preserve line breaks (use sanitize_textarea_field)
        if ( array_key_exists( 'bio', $body ) ) {
            update_user_meta( $user_id, 'user_bio', sanitize_textarea_field( (string) $body['bio'] ) );
        }
        if ( array_key_exists( 'mentorship_requirements', $body ) ) {
            update_user_meta( $user_id, 'mentorship_requirements', sanitize_textarea_field( (string) $body['mentorship_requirements'] ) );
        }

        return new WP_REST_Response( array( 'success' => true ), 200 );
    }

    /**
     * GET /paired/mentor/mentees — List mentees who have booked the authenticated mentor.
     */
    public function get_mentor_mentees( WP_REST_Request $request ) {
        $payload = $this->verify_jwt_bearer( $request );
        if ( ! $payload || empty( $payload['user_id'] ) ) {
            return new WP_Error( 'sso_invalid_token', __( 'Invalid or missing token.', 'bpu' ), array( 'status' => 401 ) );
        }

        $user_id = intval( $payload['user_id'] );
        $user    = get_userdata( $user_id );

        if ( ! $user || ! in_array( 'mentor', (array) $user->roles, true ) ) {
            return new WP_Error( 'not_a_mentor', __( 'You do not have the mentor role.', 'bpu' ), array( 'status' => 403 ) );
        }

        $query = new WP_Query( array(
            'post_type'      => 'mentorship_booking',
            'post_status'    => array( 'publish', 'pending', 'draft' ),
            'posts_per_page' => -1,
            'meta_query'     => array(
                array(
                    'key'     => '_bpu_booking_mentor_id',
                    'value'   => $user_id,
                    'compare' => '=',
                ),
            ),
        ) );

        // Collect booking counts and last booking dates per mentee
        $mentee_data = array();

        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $post_id   = get_the_ID();
                $mentee_id = (int) get_post_meta( $post_id, '_bpu_booking_mentee_id', true );
                if ( ! $mentee_id ) {
                    continue;
                }
                if ( ! isset( $mentee_data[ $mentee_id ] ) ) {
                    $mentee_data[ $mentee_id ] = array(
                        'booking_count'     => 0,
                        'last_booking_date' => '',
                    );
                }
                $mentee_data[ $mentee_id ]['booking_count']++;
                $booking_date = get_post_meta( $post_id, '_bpu_booking_date', true );
                if ( $booking_date > $mentee_data[ $mentee_id ]['last_booking_date'] ) {
                    $mentee_data[ $mentee_id ]['last_booking_date'] = $booking_date;
                }
            }
            wp_reset_postdata();
        }

        $mentees = array();
        foreach ( $mentee_data as $mentee_id => $data ) {
            $mentee_user = get_userdata( $mentee_id );
            if ( ! $mentee_user ) {
                continue;
            }
            $mentees[] = array(
                'id'                => $mentee_id,
                'display_name'      => $mentee_user->display_name,
                'email'             => $mentee_user->user_email,
                'avatar_url'        => get_avatar_url( $mentee_id, array( 'size' => 128 ) ),
                'booking_count'     => $data['booking_count'],
                'last_booking_date' => $data['last_booking_date'],
            );
        }

        // Sort by last booking date descending
        usort( $mentees, function( $a, $b ) {
            return strcmp( $b['last_booking_date'], $a['last_booking_date'] );
        } );

        return new WP_REST_Response( array(
            'success' => true,
            'mentees' => $mentees,
        ), 200 );
    }

    /**
     * GET /paired/mentor/stats — Return aggregate stats for the authenticated mentor.
     */
    public function get_mentor_stats( WP_REST_Request $request ) {
        $payload = $this->verify_jwt_bearer( $request );
        if ( ! $payload || empty( $payload['user_id'] ) ) {
            return new WP_Error( 'sso_invalid_token', __( 'Invalid or missing token.', 'bpu' ), array( 'status' => 401 ) );
        }

        $user_id = intval( $payload['user_id'] );
        $user    = get_userdata( $user_id );

        if ( ! $user || ! in_array( 'mentor', (array) $user->roles, true ) ) {
            return new WP_Error( 'not_a_mentor', __( 'You do not have the mentor role.', 'bpu' ), array( 'status' => 403 ) );
        }

        $query = new WP_Query( array(
            'post_type'      => 'mentorship_booking',
            'post_status'    => array( 'publish', 'pending', 'draft' ),
            'posts_per_page' => -1,
            'meta_query'     => array(
                array(
                    'key'     => '_bpu_booking_mentor_id',
                    'value'   => $user_id,
                    'compare' => '=',
                ),
            ),
        ) );

        $stats = array(
            'total_bookings' => 0,
            'pending'        => 0,
            'confirmed'      => 0,
            'completed'      => 0,
            'cancelled'      => 0,
            'unique_mentees' => 0,
            'total_minutes'  => 0,
        );

        $mentee_ids = array();

        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $post_id  = get_the_ID();
                $status   = get_post_meta( $post_id, '_bpu_booking_status', true ) ?: 'pending';
                $mentee_id = (int) get_post_meta( $post_id, '_bpu_booking_mentee_id', true );

                $stats['total_bookings']++;
                $stats['total_minutes'] += 60; // each session = 60 minutes

                if ( isset( $stats[ $status ] ) ) {
                    $stats[ $status ]++;
                }

                if ( $mentee_id ) {
                    $mentee_ids[ $mentee_id ] = true;
                }
            }
            wp_reset_postdata();
        }

        $stats['unique_mentees'] = count( $mentee_ids );

        return new WP_REST_Response( array(
            'success' => true,
            'stats'   => $stats,
        ), 200 );
    }

    /**
     * POST /bookings/{id}/status — Allow a mentor to confirm, cancel, or complete a booking.
     */
    public function update_booking_status( WP_REST_Request $request ) {
        $payload = $this->verify_jwt_bearer( $request );
        if ( ! $payload || empty( $payload['user_id'] ) ) {
            return new WP_Error( 'sso_invalid_token', __( 'Invalid or missing token.', 'bpu' ), array( 'status' => 401 ) );
        }

        $user_id = intval( $payload['user_id'] );
        $post_id = absint( $request->get_param( 'id' ) );
        $status  = sanitize_text_field( $request->get_param( 'status' ) );

        $valid_statuses = array( 'confirmed', 'cancelled', 'completed' );
        if ( ! in_array( $status, $valid_statuses, true ) ) {
            return new WP_Error(
                'booking_invalid_status',
                __( 'Invalid status. Use: confirmed, cancelled, or completed.', 'bpu' ),
                array( 'status' => 400 )
            );
        }

        $post = get_post( $post_id );
        if ( ! $post || 'mentorship_booking' !== $post->post_type ) {
            return new WP_Error( 'booking_not_found', __( 'Booking not found.', 'bpu' ), array( 'status' => 404 ) );
        }

        $mentor_id = (int) get_post_meta( $post_id, '_bpu_booking_mentor_id', true );
        if ( $user_id !== $mentor_id ) {
            return new WP_Error(
                'booking_forbidden',
                __( 'Only the mentor of this booking can update its status.', 'bpu' ),
                array( 'status' => 403 )
            );
        }

        update_post_meta( $post_id, '_bpu_booking_status', $status );

        // Generate meeting link on confirmation
        $meet_link = '';
        if ( 'confirmed' === $status ) {
            $meet_link = $this->generate_meeting_link( $post_id, $mentor_id );
        }

        // Delete calendar event on cancellation
        if ( 'cancelled' === $status ) {
            $this->delete_google_calendar_event( $post_id );
        }

        // Send email notification to mentee
        $mentee_id   = (int) get_post_meta( $post_id, '_bpu_booking_mentee_id', true );
        $mentee      = get_userdata( $mentee_id );
        $mentor      = get_userdata( $mentor_id );
        $booking_date = get_post_meta( $post_id, '_bpu_booking_date', true );
        $time_slot    = get_post_meta( $post_id, '_bpu_booking_time_slot', true );
        $mentor_name  = $mentor ? $mentor->display_name : 'your mentor';

        if ( $mentee ) {
            if ( 'confirmed' === $status ) {
                $heading    = 'Your session has been confirmed!';
                $body_html  = '<p style="color:#333;font-size:15px;line-height:1.6;">Great news! Your session with <strong>' . esc_html( $mentor_name ) . '</strong>';
                if ( $booking_date ) {
                    $body_html .= ' on <strong>' . esc_html( $booking_date ) . '</strong>';
                }
                if ( $time_slot ) {
                    $body_html .= ' at <strong>' . esc_html( str_replace( '-', ' – ', $time_slot ) ) . ' GMT</strong>';
                }
                $body_html .= ' has been confirmed.</p>';
                if ( $meet_link ) {
                    $body_html .= '<p style="color:#333;font-size:15px;line-height:1.6;">Join the meeting: <a href="' . esc_url( $meet_link ) . '" style="color:#7c3aed;font-weight:600;">' . esc_html( $meet_link ) . '</a></p>';
                } else {
                    $body_html .= '<p style="color:#555;font-size:14px;">Your mentor will be in touch about the video call link.</p>';
                }
                $body_html .= '<p style="margin-top:24px;"><a href="https://pairedbybpu.uk/paired/dashboard" style="display:inline-block;background:#7c3aed;color:#fff;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:600;">View my sessions</a></p>';
            } elseif ( 'cancelled' === $status ) {
                $heading    = 'Session request declined';
                $body_html  = '<p style="color:#333;font-size:15px;line-height:1.6;">Unfortunately your session request with <strong>' . esc_html( $mentor_name ) . '</strong> has been declined.</p>';
                $body_html .= '<p style="color:#555;font-size:14px;">Don\'t be discouraged — there are many great mentors on PAIRED. Browse the directory and book another session.</p>';
                $body_html .= '<p style="margin-top:24px;"><a href="https://pairedbybpu.uk/paired/mentors" style="display:inline-block;background:#C8102E;color:#fff;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:600;">Find another mentor</a></p>';
            } else {
                $heading   = 'Session marked as completed';
                $body_html = '<p style="color:#333;font-size:15px;line-height:1.6;">Your session with <strong>' . esc_html( $mentor_name ) . '</strong> has been marked as completed. We hope it was valuable!</p>';
                $body_html .= '<p style="margin-top:24px;"><a href="https://pairedbybpu.uk/paired/mentors/' . intval( $mentor_id ) . '/review" style="display:inline-block;background:#7c3aed;color:#fff;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:600;">Leave a review</a></p>';
                $body_html .= '<p style="margin-top:12px;"><a href="https://pairedbybpu.uk/paired/mentors" style="color:#7c3aed;font-size:14px;text-decoration:underline;">Or browse more mentors</a></p>';
            }

            wp_mail(
                $mentee->user_email,
                $heading . ' — PAIRED by BPU',
                $this->build_email_html( $heading, $body_html ),
                array( 'Content-Type: text/html; charset=UTF-8' )
            );

            // Create in-app notification for mentee
            if ( 'confirmed' === $status ) {
                $notif_date = $booking_date ? $booking_date : 'your scheduled date';
                $this->create_notification(
                    $mentee_id,
                    'booking_status',
                    __( 'Session Confirmed', 'bpu' ),
                    sprintf( '%s confirmed your session on %s.', esc_html( $mentor_name ), esc_html( $notif_date ) ),
                    '/paired/dashboard'
                );
            } elseif ( 'cancelled' === $status ) {
                $this->create_notification(
                    $mentee_id,
                    'booking_status',
                    __( 'Session Declined', 'bpu' ),
                    sprintf( 'Your session request with %s has been declined.', esc_html( $mentor_name ) ),
                    '/paired/mentors'
                );
            } elseif ( 'completed' === $status ) {
                $this->create_notification(
                    $mentee_id,
                    'booking_status',
                    __( 'Session Completed', 'bpu' ),
                    sprintf( 'Your session with %s has been marked as completed. Leave a review!', esc_html( $mentor_name ) ),
                    '/paired/mentors/' . $mentor_id . '/review'
                );
            }
        }

        return new WP_REST_Response( array(
            'success' => true,
            'booking' => $this->format_booking( $post_id, $user_id ),
        ), 200 );
    }

    // ══════════════════════════════════════════════════════════════
    //  PAIRED SESSION TYPE CRUD
    // ══════════════════════════════════════════════════════════════

    private function verify_mentor( WP_REST_Request $request ) {
        $payload = $this->verify_jwt_bearer( $request );
        if ( ! $payload || empty( $payload['user_id'] ) ) {
            return new WP_Error( 'sso_invalid_token', __( 'Invalid or missing token.', 'bpu' ), array( 'status' => 401 ) );
        }
        $user_id = intval( $payload['user_id'] );
        $user    = get_userdata( $user_id );
        if ( ! $user || ! in_array( 'mentor', (array) $user->roles, true ) ) {
            return new WP_Error( 'not_a_mentor', __( 'You do not have the mentor role.', 'bpu' ), array( 'status' => 403 ) );
        }
        return $user_id;
    }

    public function get_mentor_sessions( WP_REST_Request $request ) {
        $user_id = $this->verify_mentor( $request );
        if ( is_wp_error( $user_id ) ) return $user_id;

        $query = new WP_Query( array(
            'post_type'      => 'paired_session',
            'post_status'    => 'any',
            'posts_per_page' => 50,
            'meta_query'     => array(
                array( 'key' => '_session_mentor_id', 'value' => $user_id, 'compare' => '=' ),
            ),
        ) );

        $sessions = array();
        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $sessions[] = $this->format_session( get_the_ID() );
            }
            wp_reset_postdata();
        }

        return new WP_REST_Response( array( 'success' => true, 'sessions' => $sessions ), 200 );
    }

    public function create_mentor_session( WP_REST_Request $request ) {
        $user_id = $this->verify_mentor( $request );
        if ( is_wp_error( $user_id ) ) return $user_id;

        $body = $request->get_json_params();
        $name = sanitize_text_field( $body['name'] ?? '' );
        if ( empty( $name ) ) {
            return new WP_Error( 'missing_name', __( 'Session name is required.', 'bpu' ), array( 'status' => 400 ) );
        }

        $post_id = wp_insert_post( array(
            'post_type'   => 'paired_session',
            'post_title'  => $name,
            'post_status' => 'publish',
        ) );

        if ( is_wp_error( $post_id ) ) {
            return new WP_Error( 'create_failed', __( 'Failed to create session.', 'bpu' ), array( 'status' => 500 ) );
        }

        update_post_meta( $post_id, '_session_mentor_id',     $user_id );
        update_post_meta( $post_id, '_session_name',           $name );
        update_post_meta( $post_id, '_session_duration',       absint( $body['duration'] ?? 60 ) );
        update_post_meta( $post_id, '_session_description',    sanitize_textarea_field( $body['description'] ?? '' ) );
        update_post_meta( $post_id, '_session_price',          floatval( $body['price'] ?? 0 ) );
        update_post_meta( $post_id, '_session_type',           sanitize_text_field( $body['type'] ?? 'one_off' ) );
        update_post_meta( $post_id, '_session_visibility',     sanitize_text_field( $body['visibility'] ?? 'visible' ) );
        update_post_meta( $post_id, '_session_group_booking',  absint( $body['group_booking'] ?? 0 ) );
        update_post_meta( $post_id, '_session_slot_capacity',  absint( $body['slot_capacity'] ?? 1 ) );
        update_post_meta( $post_id, '_session_cover_image',    esc_url_raw( $body['cover_image'] ?? '' ) );

        return new WP_REST_Response( array( 'success' => true, 'session' => $this->format_session( $post_id ) ), 201 );
    }

    public function update_mentor_session( WP_REST_Request $request ) {
        $user_id = $this->verify_mentor( $request );
        if ( is_wp_error( $user_id ) ) return $user_id;

        $post_id = absint( $request->get_param( 'id' ) );
        $post    = get_post( $post_id );
        if ( ! $post || 'paired_session' !== $post->post_type ) {
            return new WP_Error( 'not_found', __( 'Session not found.', 'bpu' ), array( 'status' => 404 ) );
        }

        $owner = (int) get_post_meta( $post_id, '_session_mentor_id', true );
        if ( $owner !== $user_id ) {
            return new WP_Error( 'forbidden', __( 'You can only edit your own sessions.', 'bpu' ), array( 'status' => 403 ) );
        }

        $body = $request->get_json_params();
        $allowed = array(
            'name'          => '_session_name',
            'duration'      => '_session_duration',
            'description'   => '_session_description',
            'price'         => '_session_price',
            'type'          => '_session_type',
            'visibility'    => '_session_visibility',
            'group_booking' => '_session_group_booking',
            'slot_capacity' => '_session_slot_capacity',
            'cover_image'   => '_session_cover_image',
        );

        foreach ( $allowed as $key => $meta_key ) {
            if ( array_key_exists( $key, $body ) ) {
                $val = $body[ $key ];
                if ( in_array( $key, array( 'description' ), true ) ) {
                    $val = sanitize_textarea_field( $val );
                } elseif ( $key === 'cover_image' ) {
                    $val = esc_url_raw( $val );
                } elseif ( in_array( $key, array( 'duration', 'group_booking', 'slot_capacity' ), true ) ) {
                    $val = absint( $val );
                } elseif ( $key === 'price' ) {
                    $val = floatval( $val );
                } else {
                    $val = sanitize_text_field( $val );
                }
                update_post_meta( $post_id, $meta_key, $val );
            }
        }

        if ( isset( $body['name'] ) ) {
            wp_update_post( array( 'ID' => $post_id, 'post_title' => sanitize_text_field( $body['name'] ) ) );
        }

        return new WP_REST_Response( array( 'success' => true, 'session' => $this->format_session( $post_id ) ), 200 );
    }

    public function delete_mentor_session( WP_REST_Request $request ) {
        $user_id = $this->verify_mentor( $request );
        if ( is_wp_error( $user_id ) ) return $user_id;

        $post_id = absint( $request->get_param( 'id' ) );
        $post    = get_post( $post_id );
        if ( ! $post || 'paired_session' !== $post->post_type ) {
            return new WP_Error( 'not_found', __( 'Session not found.', 'bpu' ), array( 'status' => 404 ) );
        }

        $owner = (int) get_post_meta( $post_id, '_session_mentor_id', true );
        if ( $owner !== $user_id ) {
            return new WP_Error( 'forbidden', __( 'You can only delete your own sessions.', 'bpu' ), array( 'status' => 403 ) );
        }

        wp_delete_post( $post_id, true );

        return new WP_REST_Response( array( 'success' => true ), 200 );
    }

    /**
     * GET /paired/mentor/sessions/{id}/hours — Get custom hours for a session type.
     */
    public function get_session_custom_hours( WP_REST_Request $request ) {
        $user_id = $this->verify_mentor( $request );
        if ( is_wp_error( $user_id ) ) return $user_id;

        $session_id = absint( $request->get_param( 'id' ) );
        $post       = get_post( $session_id );
        if ( ! $post || 'paired_session' !== $post->post_type ) {
            return new WP_Error( 'not_found', __( 'Session not found.', 'bpu' ), array( 'status' => 404 ) );
        }

        $owner = (int) get_post_meta( $session_id, '_session_mentor_id', true );
        if ( $owner !== $user_id ) {
            return new WP_Error( 'forbidden', __( 'You can only view hours for your own sessions.', 'bpu' ), array( 'status' => 403 ) );
        }

        $hours_json = get_user_meta( $user_id, '_paired_session_hours_' . $session_id, true );
        $hours      = $hours_json ? json_decode( $hours_json, true ) : array();

        return new WP_REST_Response( array(
            'success'    => true,
            'session_id' => $session_id,
            'hours'      => $hours,
        ), 200 );
    }

    /**
     * PUT /paired/mentor/sessions/{id}/hours — Set custom hours for a session type.
     */
    public function set_session_custom_hours( WP_REST_Request $request ) {
        $user_id = $this->verify_mentor( $request );
        if ( is_wp_error( $user_id ) ) return $user_id;

        $session_id = absint( $request->get_param( 'id' ) );
        $post       = get_post( $session_id );
        if ( ! $post || 'paired_session' !== $post->post_type ) {
            return new WP_Error( 'not_found', __( 'Session not found.', 'bpu' ), array( 'status' => 404 ) );
        }

        $owner = (int) get_post_meta( $session_id, '_session_mentor_id', true );
        if ( $owner !== $user_id ) {
            return new WP_Error( 'forbidden', __( 'You can only set hours for your own sessions.', 'bpu' ), array( 'status' => 403 ) );
        }

        $body  = $request->get_json_params();
        $hours = isset( $body['hours'] ) && is_array( $body['hours'] ) ? $body['hours'] : array();

        // Sanitize each schedule entry
        $sanitized = array();
        foreach ( $hours as $entry ) {
            if ( isset( $entry['day'], $entry['start'], $entry['end'] ) ) {
                $sanitized[] = array(
                    'day'   => absint( $entry['day'] ),
                    'start' => sanitize_text_field( $entry['start'] ),
                    'end'   => sanitize_text_field( $entry['end'] ),
                );
            }
        }

        update_user_meta( $user_id, '_paired_session_hours_' . $session_id, wp_json_encode( $sanitized ) );

        return new WP_REST_Response( array(
            'success'    => true,
            'session_id' => $session_id,
            'hours'      => $sanitized,
        ), 200 );
    }

    public function get_public_mentor_sessions( WP_REST_Request $request ) {
        $mentor_id = absint( $request->get_param( 'id' ) );
        $user      = get_userdata( $mentor_id );

        if ( ! $user || ! in_array( 'mentor', (array) $user->roles, true ) ) {
            return new WP_Error( 'not_found', __( 'Mentor not found.', 'bpu' ), array( 'status' => 404 ) );
        }

        $query = new WP_Query( array(
            'post_type'      => 'paired_session',
            'post_status'    => 'publish',
            'posts_per_page' => 50,
            'meta_query'     => array(
                array( 'key' => '_session_mentor_id', 'value' => $mentor_id, 'compare' => '=' ),
                array( 'key' => '_session_visibility', 'value' => 'visible', 'compare' => '=' ),
            ),
        ) );

        $sessions = array();
        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $sessions[] = $this->format_session( get_the_ID() );
            }
            wp_reset_postdata();
        }

        return new WP_REST_Response( array( 'success' => true, 'sessions' => $sessions ), 200 );
    }

    private function format_session( $post_id ) {
        return array(
            'id'            => $post_id,
            'name'          => get_post_meta( $post_id, '_session_name', true ),
            'duration'      => (int) get_post_meta( $post_id, '_session_duration', true ),
            'description'   => get_post_meta( $post_id, '_session_description', true ),
            'price'         => (float) get_post_meta( $post_id, '_session_price', true ),
            'type'          => get_post_meta( $post_id, '_session_type', true ) ?: 'one_off',
            'visibility'    => get_post_meta( $post_id, '_session_visibility', true ) ?: 'visible',
            'group_booking' => (int) get_post_meta( $post_id, '_session_group_booking', true ),
            'slot_capacity' => (int) get_post_meta( $post_id, '_session_slot_capacity', true ) ?: 1,
            'cover_image'   => get_post_meta( $post_id, '_session_cover_image', true ),
        );
    }

    // ══════════════════════════════════════════════════════════════
    //  WORK EXPERIENCE CRUD
    // ══════════════════════════════════════════════════════════════

    public function get_mentor_experiences( WP_REST_Request $request ) {
        $user_id = $this->verify_mentor( $request );
        if ( is_wp_error( $user_id ) ) return $user_id;

        $query = new WP_Query( array(
            'post_type'      => 'mentor_experience',
            'post_status'    => 'any',
            'posts_per_page' => 50,
            'meta_query'     => array(
                array( 'key' => '_mentor_exp_user_id', 'value' => $user_id, 'compare' => '=' ),
            ),
            'orderby'        => 'meta_value',
            'meta_key'       => '_mentor_exp_start_date',
            'order'          => 'DESC',
        ) );

        $experiences = array();
        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $experiences[] = $this->format_experience( get_the_ID() );
            }
            wp_reset_postdata();
        }

        return new WP_REST_Response( array( 'success' => true, 'experiences' => $experiences ), 200 );
    }

    public function create_mentor_experience( WP_REST_Request $request ) {
        $user_id = $this->verify_mentor( $request );
        if ( is_wp_error( $user_id ) ) return $user_id;

        $body  = $request->get_json_params();
        $title = sanitize_text_field( $body['title'] ?? '' );
        $company = sanitize_text_field( $body['company'] ?? '' );

        if ( empty( $title ) || empty( $company ) ) {
            return new WP_Error( 'missing_fields', __( 'Job title and company are required.', 'bpu' ), array( 'status' => 400 ) );
        }

        $post_id = wp_insert_post( array(
            'post_type'   => 'mentor_experience',
            'post_title'  => $title . ' at ' . $company,
            'post_status' => 'publish',
        ) );

        if ( is_wp_error( $post_id ) ) {
            return new WP_Error( 'create_failed', __( 'Failed to create experience.', 'bpu' ), array( 'status' => 500 ) );
        }

        update_post_meta( $post_id, '_mentor_exp_user_id',     $user_id );
        update_post_meta( $post_id, '_mentor_exp_title',        $title );
        update_post_meta( $post_id, '_mentor_exp_company',      $company );
        update_post_meta( $post_id, '_mentor_exp_start_date',   sanitize_text_field( $body['start_date'] ?? '' ) );
        update_post_meta( $post_id, '_mentor_exp_end_date',     sanitize_text_field( $body['end_date'] ?? '' ) );
        update_post_meta( $post_id, '_mentor_exp_is_current',   absint( $body['is_current'] ?? 0 ) );
        update_post_meta( $post_id, '_mentor_exp_description',  sanitize_textarea_field( $body['description'] ?? '' ) );

        return new WP_REST_Response( array( 'success' => true, 'experience' => $this->format_experience( $post_id ) ), 201 );
    }

    public function update_mentor_experience( WP_REST_Request $request ) {
        $user_id = $this->verify_mentor( $request );
        if ( is_wp_error( $user_id ) ) return $user_id;

        $post_id = absint( $request->get_param( 'id' ) );
        $post    = get_post( $post_id );
        if ( ! $post || 'mentor_experience' !== $post->post_type ) {
            return new WP_Error( 'not_found', __( 'Experience not found.', 'bpu' ), array( 'status' => 404 ) );
        }

        $owner = (int) get_post_meta( $post_id, '_mentor_exp_user_id', true );
        if ( $owner !== $user_id ) {
            return new WP_Error( 'forbidden', __( 'You can only edit your own experiences.', 'bpu' ), array( 'status' => 403 ) );
        }

        $body    = $request->get_json_params();
        $allowed = array(
            'title'       => '_mentor_exp_title',
            'company'     => '_mentor_exp_company',
            'start_date'  => '_mentor_exp_start_date',
            'end_date'    => '_mentor_exp_end_date',
            'is_current'  => '_mentor_exp_is_current',
            'description' => '_mentor_exp_description',
        );

        foreach ( $allowed as $key => $meta_key ) {
            if ( array_key_exists( $key, $body ) ) {
                $val = $body[ $key ];
                if ( $key === 'description' ) {
                    $val = sanitize_textarea_field( $val );
                } elseif ( $key === 'is_current' ) {
                    $val = absint( $val );
                } else {
                    $val = sanitize_text_field( $val );
                }
                update_post_meta( $post_id, $meta_key, $val );
            }
        }

        return new WP_REST_Response( array( 'success' => true, 'experience' => $this->format_experience( $post_id ) ), 200 );
    }

    public function delete_mentor_experience( WP_REST_Request $request ) {
        $user_id = $this->verify_mentor( $request );
        if ( is_wp_error( $user_id ) ) return $user_id;

        $post_id = absint( $request->get_param( 'id' ) );
        $post    = get_post( $post_id );
        if ( ! $post || 'mentor_experience' !== $post->post_type ) {
            return new WP_Error( 'not_found', __( 'Experience not found.', 'bpu' ), array( 'status' => 404 ) );
        }

        $owner = (int) get_post_meta( $post_id, '_mentor_exp_user_id', true );
        if ( $owner !== $user_id ) {
            return new WP_Error( 'forbidden', __( 'You can only delete your own experiences.', 'bpu' ), array( 'status' => 403 ) );
        }

        wp_delete_post( $post_id, true );

        return new WP_REST_Response( array( 'success' => true ), 200 );
    }

    private function format_experience( $post_id ) {
        return array(
            'id'          => $post_id,
            'title'       => get_post_meta( $post_id, '_mentor_exp_title', true ),
            'company'     => get_post_meta( $post_id, '_mentor_exp_company', true ),
            'start_date'  => get_post_meta( $post_id, '_mentor_exp_start_date', true ),
            'end_date'    => get_post_meta( $post_id, '_mentor_exp_end_date', true ),
            'is_current'  => (int) get_post_meta( $post_id, '_mentor_exp_is_current', true ),
            'description' => get_post_meta( $post_id, '_mentor_exp_description', true ),
        );
    }

    // ══════════════════════════════════════════════════════════════
    //  EDUCATION CRUD
    // ══════════════════════════════════════════════════════════════

    public function get_mentor_education( WP_REST_Request $request ) {
        $user_id = $this->verify_mentor( $request );
        if ( is_wp_error( $user_id ) ) return $user_id;

        $query = new WP_Query( array(
            'post_type'      => 'mentor_education',
            'post_status'    => 'any',
            'posts_per_page' => 50,
            'meta_query'     => array(
                array( 'key' => '_mentor_edu_user_id', 'value' => $user_id, 'compare' => '=' ),
            ),
            'orderby'        => 'meta_value',
            'meta_key'       => '_mentor_edu_start_year',
            'order'          => 'DESC',
        ) );

        $education = array();
        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $education[] = $this->format_education( get_the_ID() );
            }
            wp_reset_postdata();
        }

        return new WP_REST_Response( array( 'success' => true, 'education' => $education ), 200 );
    }

    public function create_mentor_education( WP_REST_Request $request ) {
        $user_id = $this->verify_mentor( $request );
        if ( is_wp_error( $user_id ) ) return $user_id;

        $body       = $request->get_json_params();
        $institution = sanitize_text_field( $body['institution'] ?? '' );
        $degree      = sanitize_text_field( $body['degree'] ?? '' );

        if ( empty( $institution ) || empty( $degree ) ) {
            return new WP_Error( 'missing_fields', __( 'Institution and degree are required.', 'bpu' ), array( 'status' => 400 ) );
        }

        $post_id = wp_insert_post( array(
            'post_type'   => 'mentor_education',
            'post_title'  => $degree . ' — ' . $institution,
            'post_status' => 'publish',
        ) );

        if ( is_wp_error( $post_id ) ) {
            return new WP_Error( 'create_failed', __( 'Failed to create education entry.', 'bpu' ), array( 'status' => 500 ) );
        }

        update_post_meta( $post_id, '_mentor_edu_user_id',    $user_id );
        update_post_meta( $post_id, '_mentor_edu_institution', $institution );
        update_post_meta( $post_id, '_mentor_edu_degree',      $degree );
        update_post_meta( $post_id, '_mentor_edu_start_year',  sanitize_text_field( $body['start_year'] ?? '' ) );
        update_post_meta( $post_id, '_mentor_edu_end_year',    sanitize_text_field( $body['end_year'] ?? '' ) );

        return new WP_REST_Response( array( 'success' => true, 'education' => $this->format_education( $post_id ) ), 201 );
    }

    public function update_mentor_education( WP_REST_Request $request ) {
        $user_id = $this->verify_mentor( $request );
        if ( is_wp_error( $user_id ) ) return $user_id;

        $post_id = absint( $request->get_param( 'id' ) );
        $post    = get_post( $post_id );
        if ( ! $post || 'mentor_education' !== $post->post_type ) {
            return new WP_Error( 'not_found', __( 'Education entry not found.', 'bpu' ), array( 'status' => 404 ) );
        }

        $owner = (int) get_post_meta( $post_id, '_mentor_edu_user_id', true );
        if ( $owner !== $user_id ) {
            return new WP_Error( 'forbidden', __( 'You can only edit your own education.', 'bpu' ), array( 'status' => 403 ) );
        }

        $body    = $request->get_json_params();
        $allowed = array(
            'institution' => '_mentor_edu_institution',
            'degree'      => '_mentor_edu_degree',
            'start_year'  => '_mentor_edu_start_year',
            'end_year'    => '_mentor_edu_end_year',
        );

        foreach ( $allowed as $key => $meta_key ) {
            if ( array_key_exists( $key, $body ) ) {
                update_post_meta( $post_id, $meta_key, sanitize_text_field( $body[ $key ] ) );
            }
        }

        return new WP_REST_Response( array( 'success' => true, 'education' => $this->format_education( $post_id ) ), 200 );
    }

    public function delete_mentor_education( WP_REST_Request $request ) {
        $user_id = $this->verify_mentor( $request );
        if ( is_wp_error( $user_id ) ) return $user_id;

        $post_id = absint( $request->get_param( 'id' ) );
        $post    = get_post( $post_id );
        if ( ! $post || 'mentor_education' !== $post->post_type ) {
            return new WP_Error( 'not_found', __( 'Education entry not found.', 'bpu' ), array( 'status' => 404 ) );
        }

        $owner = (int) get_post_meta( $post_id, '_mentor_edu_user_id', true );
        if ( $owner !== $user_id ) {
            return new WP_Error( 'forbidden', __( 'You can only delete your own education.', 'bpu' ), array( 'status' => 403 ) );
        }

        wp_delete_post( $post_id, true );

        return new WP_REST_Response( array( 'success' => true ), 200 );
    }

    private function format_education( $post_id ) {
        return array(
            'id'          => $post_id,
            'institution' => get_post_meta( $post_id, '_mentor_edu_institution', true ),
            'degree'      => get_post_meta( $post_id, '_mentor_edu_degree', true ),
            'start_year'  => get_post_meta( $post_id, '_mentor_edu_start_year', true ),
            'end_year'    => get_post_meta( $post_id, '_mentor_edu_end_year', true ),
        );
    }

    // ══════════════════════════════════════════════════════════════
    //  AVAILABILITY & SCHEDULE
    // ══════════════════════════════════════════════════════════════

    public function get_mentor_availability( WP_REST_Request $request ) {
        $user_id = $this->verify_mentor( $request );
        if ( is_wp_error( $user_id ) ) return $user_id;

        $schedule = get_user_meta( $user_id, '_paired_weekly_schedule', true );
        $holidays = get_user_meta( $user_id, '_paired_holidays', true );
        $timezone = get_user_meta( $user_id, '_paired_timezone', true ) ?: 'Europe/London';

        return new WP_REST_Response( array(
            'success'  => true,
            'schedule' => $schedule ? json_decode( $schedule, true ) : array(),
            'holidays' => $holidays ? json_decode( $holidays, true ) : array(),
            'timezone' => $timezone,
        ), 200 );
    }

    public function update_mentor_availability( WP_REST_Request $request ) {
        $user_id = $this->verify_mentor( $request );
        if ( is_wp_error( $user_id ) ) return $user_id;

        $body = $request->get_json_params();

        if ( isset( $body['schedule'] ) && is_array( $body['schedule'] ) ) {
            // Validate schedule structure: array of { day, start, end } objects
            $clean_schedule = array();
            foreach ( $body['schedule'] as $slot ) {
                if ( ! isset( $slot['day'] ) ) continue;
                $clean_schedule[] = array(
                    'day'   => absint( $slot['day'] ),
                    'start' => sanitize_text_field( $slot['start'] ?? '09:00' ),
                    'end'   => sanitize_text_field( $slot['end'] ?? '17:00' ),
                );
            }
            update_user_meta( $user_id, '_paired_weekly_schedule', wp_json_encode( $clean_schedule ) );
        }

        if ( isset( $body['holidays'] ) && is_array( $body['holidays'] ) ) {
            $clean_holidays = array_map( 'sanitize_text_field', $body['holidays'] );
            update_user_meta( $user_id, '_paired_holidays', wp_json_encode( array_values( $clean_holidays ) ) );
        }

        if ( isset( $body['timezone'] ) ) {
            update_user_meta( $user_id, '_paired_timezone', sanitize_text_field( $body['timezone'] ) );
        }

        return new WP_REST_Response( array( 'success' => true ), 200 );
    }

    public function get_mentor_available_slots( WP_REST_Request $request ) {
        $mentor_id = absint( $request->get_param( 'id' ) );
        $user      = get_userdata( $mentor_id );

        if ( ! $user || ! in_array( 'mentor', (array) $user->roles, true ) ) {
            return new WP_Error( 'not_found', __( 'Mentor not found.', 'bpu' ), array( 'status' => 404 ) );
        }

        $date     = sanitize_text_field( $request->get_param( 'date' ) ?? '' );
        $duration = absint( $request->get_param( 'duration' ) ?? 60 );

        if ( empty( $date ) ) {
            return new WP_Error( 'missing_date', __( 'Date parameter is required (YYYY-MM-DD).', 'bpu' ), array( 'status' => 400 ) );
        }

        // Validate date format
        $d = DateTime::createFromFormat( 'Y-m-d', $date );
        if ( ! $d || $d->format( 'Y-m-d' ) !== $date ) {
            return new WP_Error( 'invalid_date', __( 'Invalid date format. Use YYYY-MM-DD.', 'bpu' ), array( 'status' => 400 ) );
        }

        // Don't allow dates in the past
        $today = new DateTime( 'now', new DateTimeZone( 'UTC' ) );
        if ( $d < $today->setTime( 0, 0, 0 ) ) {
            return new WP_REST_Response( array( 'success' => true, 'slots' => array() ), 200 );
        }

        // Get mentor schedule — use session-specific hours if session_id is provided
        $session_id = absint( $request->get_param( 'session_id' ) );
        $schedule   = null;

        if ( $session_id ) {
            $session_hours_json = get_user_meta( $mentor_id, '_paired_session_hours_' . $session_id, true );
            if ( $session_hours_json ) {
                $schedule = json_decode( $session_hours_json, true );
            }
        }

        if ( null === $schedule ) {
            $schedule_json = get_user_meta( $mentor_id, '_paired_weekly_schedule', true );
            $schedule      = $schedule_json ? json_decode( $schedule_json, true ) : array();
        }

        $holidays_json = get_user_meta( $mentor_id, '_paired_holidays', true );
        $holidays      = $holidays_json ? json_decode( $holidays_json, true ) : array();

        // Check if date is a holiday
        if ( in_array( $date, $holidays, true ) ) {
            return new WP_REST_Response( array( 'success' => true, 'slots' => array() ), 200 );
        }

        // Get day of week (1=Mon ... 7=Sun, ISO-8601)
        $day_of_week = (int) $d->format( 'N' );

        // Find matching schedule entries for this day
        $day_slots = array_filter( $schedule, function( $s ) use ( $day_of_week ) {
            return (int) $s['day'] === $day_of_week;
        } );

        if ( empty( $day_slots ) ) {
            return new WP_REST_Response( array( 'success' => true, 'slots' => array() ), 200 );
        }

        // Generate time slots based on duration
        $buffer    = 15; // minutes between slots
        $available = array();

        foreach ( $day_slots as $block ) {
            $start = strtotime( $date . ' ' . $block['start'] );
            $end   = strtotime( $date . ' ' . $block['end'] );

            while ( $start + ( $duration * 60 ) <= $end ) {
                $slot_start = date( 'H:i', $start );
                $slot_end   = date( 'H:i', $start + ( $duration * 60 ) );
                $available[] = array(
                    'start' => $slot_start,
                    'end'   => $slot_end,
                );
                $start += ( $duration + $buffer ) * 60;
            }
        }

        // Remove slots that already have confirmed bookings
        $existing = new WP_Query( array(
            'post_type'      => 'mentorship_booking',
            'post_status'    => array( 'publish', 'pending', 'draft' ),
            'posts_per_page' => -1,
            'meta_query'     => array(
                'relation' => 'AND',
                array( 'key' => '_bpu_booking_mentor_id', 'value' => $mentor_id, 'compare' => '=' ),
                array( 'key' => '_bpu_booking_date', 'value' => $date, 'compare' => '=' ),
                array( 'key' => '_bpu_booking_status', 'value' => array( 'pending', 'confirmed' ), 'compare' => 'IN' ),
            ),
        ) );

        $booked_slots = array();
        if ( $existing->have_posts() ) {
            while ( $existing->have_posts() ) {
                $existing->the_post();
                $booked_slots[] = get_post_meta( get_the_ID(), '_bpu_booking_time_slot', true );
            }
            wp_reset_postdata();
        }

        $available = array_filter( $available, function( $slot ) use ( $booked_slots ) {
            $slot_str = $slot['start'] . ' - ' . $slot['end'];
            return ! in_array( $slot_str, $booked_slots, true );
        } );

        return new WP_REST_Response( array(
            'success' => true,
            'slots'   => array_values( $available ),
        ), 200 );
    }

    // ══════════════════════════════════════════════════════════════
    //  SKILLS (HARDCODED)
    // ══════════════════════════════════════════════════════════════

    public function get_paired_skills( WP_REST_Request $request ) {
        $custom = get_option( '_paired_custom_skills' );
        if ( ! empty( $custom ) && is_array( $custom ) ) {
            return new WP_REST_Response( array( 'success' => true, 'skills' => $custom ), 200 );
        }

        $skills = array(
            'Engineering & Technology' => array(
                'Front-end Development', 'Back-end Development', 'Full Stack Development',
                'Mobile Development (iOS)', 'Mobile Development (Android)', 'DevOps',
                'Cloud Engineering (AWS)', 'Cloud Engineering (Azure)', 'Cloud Engineering (GCP)',
                'Site Reliability Engineering', 'QA & Testing', 'Data Engineering',
                'AI & Machine Learning', 'Cybersecurity', 'Blockchain', 'Embedded Systems',
                'Systems Architecture', 'Database Administration', 'API Development', 'Technical Leadership',
            ),
            'Product & Project Management' => array(
                'Product Management', 'Product Strategy', 'Product Analytics',
                'Program Management', 'Project Management', 'Agile & Scrum',
                'Product Operations', 'Technical Product Management',
            ),
            'Design & Creative' => array(
                'UX Design', 'UI Design', 'Graphic Design', 'Motion Design', 'Brand Design',
                'Industrial Design', 'Design Systems', 'Design Ops', 'UX Research',
                'Interaction Design', 'Service Design', '3D Design', 'Game Design', 'XR/VR Design',
            ),
            'Marketing & Communications' => array(
                'Digital Marketing', 'Content Marketing', 'Social Media Marketing',
                'Brand Strategy', 'Growth Marketing', 'SEO & SEM', 'Email Marketing',
                'PR & Communications', 'Event Marketing', 'Influencer Marketing',
                'Marketing Analytics', 'Community Management', 'Product Marketing', 'Performance Marketing',
            ),
            'Data & Analytics' => array(
                'Data Analysis', 'Data Science', 'Machine Learning', 'Business Intelligence',
                'Statistical Modelling', 'Data Visualisation', 'Natural Language Processing',
                'Computer Vision', 'Big Data', 'A/B Testing & Experimentation',
            ),
            'Finance & Banking' => array(
                'Investment Banking', 'Corporate Finance', 'Financial Planning & Analysis',
                'Accounting', 'Risk Management', 'Compliance & Regulation', 'Wealth Management',
                'Fintech', 'Audit', 'Tax', 'Treasury', 'Private Equity', 'Venture Capital',
                'Insurance', 'Actuarial Science',
            ),
            'Legal' => array(
                'Corporate Law', 'Employment Law', 'Intellectual Property', 'Contract Law',
                'Regulatory Compliance', 'Commercial Law', 'Immigration Law', 'Family Law',
                'Criminal Law', 'Legal Operations',
            ),
            'Healthcare & Life Sciences' => array(
                'Clinical Medicine', 'Nursing', 'Public Health', 'Health Tech',
                'Pharmaceutical', 'Biotech', 'Mental Health', 'Health Policy',
                'Clinical Research', 'Health Informatics',
            ),
            'Education & Training' => array(
                'Teaching', 'Curriculum Development', 'EdTech', 'Corporate Training',
                'Academic Research', 'Higher Education', 'STEM Education',
                'Coaching & Mentoring', 'Special Education', 'Learning Design',
            ),
            'Human Resources' => array(
                'Talent Acquisition', 'HR Business Partnering', 'Learning & Development',
                'Compensation & Benefits', 'Employee Relations', 'DEI Strategy',
                'People Analytics', 'Organisational Development', 'HR Tech', 'Employer Branding',
            ),
            'Sales & Business Development' => array(
                'Enterprise Sales', 'B2B Sales', 'Account Management', 'Business Development',
                'Sales Operations', 'Customer Success', 'Partnership Management',
                'Revenue Operations', 'Sales Engineering',
            ),
            'Operations & Strategy' => array(
                'Management Consulting', 'Business Strategy', 'Operations Management',
                'Supply Chain', 'Procurement', 'Change Management', 'Process Improvement',
                'Lean & Six Sigma', 'Logistics',
            ),
            'Media & Entertainment' => array(
                'Journalism', 'Broadcasting', 'Film Production', 'Music Industry',
                'Publishing', 'Podcasting', 'Photography', 'Content Creation',
                'Streaming & Digital Media',
            ),
            'Property & Construction' => array(
                'Property Development', 'Architecture', 'Surveying', 'Construction Management',
                'Urban Planning', 'Estate Management', 'Facilities Management',
            ),
            'Entrepreneurship' => array(
                'Startup Founding', 'Fundraising', 'Business Planning', 'Bootstrapping',
                'Social Enterprise', 'Franchise', 'E-commerce', 'Scaling & Growth',
            ),
            'Public Sector & Policy' => array(
                'Civil Service', 'Policy Analysis', 'Local Government',
                'International Development', 'Charity & Non-profit', 'Public Affairs',
                'Community Development',
            ),
        );

        return new WP_REST_Response( array( 'success' => true, 'skills' => $skills ), 200 );
    }

    // ══════════════════════════════════════════════════════════════
    //  PROFILE PHOTO UPLOAD (CLOUDFLARE R2)
    // ══════════════════════════════════════════════════════════════

    public function upload_mentor_photo( WP_REST_Request $request ) {
        $user_id = $this->verify_mentor( $request );
        if ( is_wp_error( $user_id ) ) return $user_id;

        // This endpoint will generate a pre-signed upload URL for Cloudflare R2
        // or accept a direct upload and forward to R2.
        // For now, accept a URL and store it.

        $body = $request->get_json_params();
        $url  = esc_url_raw( $body['photo_url'] ?? '' );

        if ( empty( $url ) ) {
            return new WP_Error( 'missing_url', __( 'Photo URL is required.', 'bpu' ), array( 'status' => 400 ) );
        }

        update_user_meta( $user_id, '_paired_photo_url', $url );

        return new WP_REST_Response( array( 'success' => true, 'photo_url' => $url ), 200 );
    }

    // ══════════════════════════════════════════════════════════════
    //  CORS HEADERS
    // ══════════════════════════════════════════════════════════════

    /**
     * Add CORS headers for allowed BPU origins on REST API responses.
     */
    public function add_cors_headers( $served, $result, $request, $server ) {
        $origin = isset( $_SERVER['HTTP_ORIGIN'] ) ? $_SERVER['HTTP_ORIGIN'] : '';

        $allowed_origins = array(
            'https://app.blackprofessionals.uk',
            'https://pairedbybpu.uk',
        );

        if ( in_array( $origin, $allowed_origins, true ) ) {
            header( 'Access-Control-Allow-Origin: ' . $origin );
            header( 'Access-Control-Allow-Credentials: true' );
            header( 'Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE' );
            header( 'Access-Control-Allow-Headers: Authorization, Content-Type, X-WP-Nonce' );
            header( 'Access-Control-Expose-Headers: X-WP-Total, X-WP-TotalPages' );
            header( 'Vary: Origin' );
        }

        return $served;
    }

    /**
     * Admin notice to assist the developer in configuring wp-config.php for SSO
     */
    public function display_cookie_domain_check() {
        if ( ! defined( 'COOKIE_DOMAIN' ) || COOKIE_DOMAIN !== '.blackprofessionals.uk' ) {
            ?>
            <div class="notice notice-warning is-dismissible">
                <p><strong>BPU Headless Connector Notice:</strong> Single Sign-On requires sharing authentication cookies across subdomains.</p>
                <p>Please add the following to your <code>wp-config.php</code> file:</p>
                <pre style="background: #f4f4f4; padding: 10px; border-left: 4px solid #ffb900;">
define( 'COOKIE_DOMAIN', '.blackprofessionals.uk' );
define( 'COOKIEPATH', '/' );
define( 'SITECOOKIEPATH', '/' );
define( 'BPU_JWT_SECRET', 'your-strong-random-secret-here' );</pre>
            </div>
            <?php
        }
    }

    // ═══════════════════════════════════════════════════════════════
    // JOB PLATFORM
    // ═══════════════════════════════════════════════════════════════

    // ── Job meta boxes ────────────────────────────────────────────

    public function register_job_meta_boxes() {
        add_meta_box(
            'bpu_job_details',
            'Job Details',
            array( $this, 'render_job_meta_box' ),
            'bpu_job',
            'normal',
            'high'
        );
    }

    public function render_job_meta_box( WP_Post $post ) {
        wp_nonce_field( 'bpu_job_meta_save', 'bpu_job_meta_nonce' );
        $get = fn( $k ) => get_post_meta( $post->ID, $k, true );

        $location        = $get( '_bpu_location' );
        $employment_type = $get( '_bpu_employment_type' );
        $industry        = $get( '_bpu_industry' );
        $job_type        = $get( '_bpu_job_type' ) ?: 'outbound';
        $apply_url       = $get( '_bpu_apply_url' );
        $expires         = $get( '_bpu_expires_date' );
        $sal_min         = $get( '_bpu_salary_min' );
        $sal_max         = $get( '_bpu_salary_max' );
        $sal_currency    = $get( '_bpu_salary_currency' ) ?: 'GBP';
        $remote          = (bool) $get( '_bpu_remote' );
        $featured        = (bool) $get( '_bpu_featured' );
        $filled          = (bool) $get( '_bpu_filled' );

        $emp_types  = array( 'Full-time', 'Part-time', 'Freelance', 'Contract', 'Internship' );
        $industries = array( 'Technology', 'Finance', 'Healthcare', 'Education', 'Legal', 'Marketing', 'Engineering', 'HR & Recruitment', 'Creative & Media', 'Public Sector', 'Consulting', 'Other' );
        $currencies = array( 'GBP', 'USD', 'EUR' );

        ?>
        <style>
        .bpu-meta-grid { display:grid; grid-template-columns:1fr 1fr; gap:16px 24px; margin-bottom:16px; }
        .bpu-meta-grid label, .bpu-meta-col label { display:block; font-weight:600; font-size:12px; text-transform:uppercase; letter-spacing:.04em; color:#555; margin-bottom:4px; }
        .bpu-meta-grid input, .bpu-meta-grid select, .bpu-meta-col input, .bpu-meta-col select { width:100%; }
        .bpu-meta-flags { display:flex; gap:20px; margin-top:8px; }
        .bpu-meta-flags label { font-weight:400; font-size:13px; text-transform:none; letter-spacing:0; color:#1e1e1e; display:flex; align-items:center; gap:6px; }
        .bpu-meta-section-title { font-size:13px; font-weight:700; color:#1e1e1e; border-bottom:1px solid #eee; padding-bottom:6px; margin:16px 0 12px; }
        .bpu-salary-row { display:grid; grid-template-columns:1fr 1fr 120px; gap:12px; }
        </style>

        <div class="bpu-meta-section-title">Location &amp; Role</div>
        <div class="bpu-meta-grid">
            <div>
                <label for="bpu_location">Location</label>
                <input type="text" id="bpu_location" name="bpu_location"
                    value="<?php echo esc_attr( $location ); ?>"
                    placeholder="e.g. London, United Kingdom" />
            </div>
            <div>
                <label for="bpu_employment_type">Employment Type</label>
                <select id="bpu_employment_type" name="bpu_employment_type">
                    <?php foreach ( $emp_types as $t ) : ?>
                        <option value="<?php echo esc_attr($t); ?>" <?php selected( $employment_type, $t ); ?>><?php echo esc_html($t); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="bpu_industry">Industry</label>
                <select id="bpu_industry" name="bpu_industry">
                    <?php foreach ( $industries as $i ) : ?>
                        <option value="<?php echo esc_attr($i); ?>" <?php selected( $industry, $i ); ?>><?php echo esc_html($i); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="bpu_expires_date">Closing Date</label>
                <input type="date" id="bpu_expires_date" name="bpu_expires_date"
                    value="<?php echo esc_attr( $expires ); ?>" />
            </div>
        </div>

        <div class="bpu-meta-flags">
            <label>
                <input type="checkbox" name="bpu_remote" value="1" <?php checked( $remote ); ?> />
                Remote position
            </label>
            <label>
                <input type="checkbox" name="bpu_featured" value="1" <?php checked( $featured ); ?> />
                Featured listing
            </label>
            <label>
                <input type="checkbox" name="bpu_filled" value="1" <?php checked( $filled ); ?> />
                Position filled
            </label>
        </div>

        <div class="bpu-meta-section-title">Salary</div>
        <div class="bpu-salary-row">
            <div>
                <label for="bpu_salary_min">Minimum (£)</label>
                <input type="number" id="bpu_salary_min" name="bpu_salary_min"
                    value="<?php echo esc_attr( $sal_min ); ?>" min="0" step="1000" placeholder="e.g. 30000" />
            </div>
            <div>
                <label for="bpu_salary_max">Maximum (£)</label>
                <input type="number" id="bpu_salary_max" name="bpu_salary_max"
                    value="<?php echo esc_attr( $sal_max ); ?>" min="0" step="1000" placeholder="e.g. 50000" />
            </div>
            <div>
                <label for="bpu_salary_currency">Currency</label>
                <select id="bpu_salary_currency" name="bpu_salary_currency">
                    <?php foreach ( $currencies as $c ) : ?>
                        <option value="<?php echo esc_attr($c); ?>" <?php selected( $sal_currency, $c ); ?>><?php echo esc_html($c); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="bpu-meta-section-title">Application</div>
        <div class="bpu-meta-grid">
            <div>
                <label for="bpu_job_type">Job Type</label>
                <select id="bpu_job_type" name="bpu_job_type">
                    <option value="outbound" <?php selected( $job_type, 'outbound' ); ?>>Outbound (partner site)</option>
                    <option value="inbound"  <?php selected( $job_type, 'inbound' ); ?>>Inbound (apply on BPU)</option>
                </select>
            </div>
            <div>
                <label for="bpu_apply_url">Apply URL <span style="font-weight:400;text-transform:none;">(outbound only)</span></label>
                <input type="url" id="bpu_apply_url" name="bpu_apply_url"
                    value="<?php echo esc_url( $apply_url ); ?>"
                    placeholder="https://employer.com/apply" />
            </div>
        </div>
        <?php
    }

    public function save_job_meta_boxes( int $post_id, WP_Post $post ) {
        if ( ! isset( $_POST['bpu_job_meta_nonce'] ) ) return;
        if ( ! wp_verify_nonce( $_POST['bpu_job_meta_nonce'], 'bpu_job_meta_save' ) ) return;
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
        if ( ! current_user_can( 'edit_post', $post_id ) ) return;

        $text_fields = array(
            '_bpu_location'        => 'bpu_location',
            '_bpu_employment_type' => 'bpu_employment_type',
            '_bpu_industry'        => 'bpu_industry',
            '_bpu_job_type'        => 'bpu_job_type',
            '_bpu_expires_date'    => 'bpu_expires_date',
            '_bpu_salary_currency' => 'bpu_salary_currency',
        );
        foreach ( $text_fields as $meta_key => $field ) {
            if ( isset( $_POST[ $field ] ) ) {
                update_post_meta( $post_id, $meta_key, sanitize_text_field( $_POST[ $field ] ) );
            }
        }

        // Apply URL
        if ( isset( $_POST['bpu_apply_url'] ) ) {
            update_post_meta( $post_id, '_bpu_apply_url', esc_url_raw( $_POST['bpu_apply_url'] ) );
        }

        // Numeric salary
        foreach ( array( '_bpu_salary_min' => 'bpu_salary_min', '_bpu_salary_max' => 'bpu_salary_max' ) as $meta_key => $field ) {
            if ( isset( $_POST[ $field ] ) && $_POST[ $field ] !== '' ) {
                update_post_meta( $post_id, $meta_key, intval( $_POST[ $field ] ) );
            } else {
                delete_post_meta( $post_id, $meta_key );
            }
        }

        // Checkboxes
        foreach ( array( '_bpu_remote' => 'bpu_remote', '_bpu_featured' => 'bpu_featured', '_bpu_filled' => 'bpu_filled' ) as $meta_key => $field ) {
            update_post_meta( $post_id, $meta_key, isset( $_POST[ $field ] ) ? '1' : '0' );
        }
    }

    // ── All Jobs list table columns ───────────────────────────────

    public function job_list_columns( array $columns ): array {
        $new = array();
        foreach ( $columns as $key => $label ) {
            $new[ $key ] = $label;
            if ( $key === 'title' ) {
                $new['bpu_location'] = __( 'Location', 'bpu' );
                $new['bpu_expires']  = __( 'Closes', 'bpu' );
            }
        }
        return $new;
    }

    public function job_list_column_content( string $column, int $post_id ): void {
        if ( $column === 'bpu_location' ) {
            $location = get_post_meta( $post_id, '_bpu_location', true );
            $remote   = get_post_meta( $post_id, '_bpu_remote', true );
            if ( $remote ) {
                echo '<span style="color:#0a7c42;">Remote</span>';
            } elseif ( $location ) {
                echo esc_html( $location );
            } else {
                echo '<span style="color:#aaa;">—</span>';
            }
        }
        if ( $column === 'bpu_expires' ) {
            $date = get_post_meta( $post_id, '_bpu_expires_date', true );
            if ( $date ) {
                $ts      = strtotime( $date );
                $today   = strtotime( gmdate( 'Y-m-d' ) );
                $expired = $ts < $today;
                $label   = date_i18n( 'd M Y', $ts );
                echo $expired
                    ? '<span style="color:#d63638;">' . esc_html( $label ) . ' (expired)</span>'
                    : esc_html( $label );
            } else {
                echo '<span style="color:#aaa;">—</span>';
            }
        }
    }

    public function register_job_post_type() {
        register_post_type( 'bpu_job', array(
            'labels'             => array(
                'name'          => __( 'Jobs', 'bpu' ),
                'singular_name' => __( 'Job', 'bpu' ),
                'menu_name'     => __( 'BPU Jobs', 'bpu' ),
                'add_new_item'  => __( 'Post New Job', 'bpu' ),
                'edit_item'     => __( 'Edit Job', 'bpu' ),
                'all_items'     => __( 'All Jobs', 'bpu' ),
            ),
            'public'             => false,
            'publicly_queryable' => false,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => false,
            'rewrite'            => array( 'slug' => 'jobs', 'with_front' => false ),
            'capability_type'    => 'post',
            'has_archive'        => false,
            'hierarchical'       => false,
            'menu_position'      => 26,
            'menu_icon'          => 'dashicons-portfolio',
            'supports'           => array( 'title', 'editor', 'custom-fields', 'author', 'slug' ),
            'show_in_rest'       => true,
        ) );
    }

    public function register_job_application_post_type() {
        register_post_type( 'bpu_job_application', array(
            'labels'             => array(
                'name'          => __( 'Job Applications', 'bpu' ),
                'singular_name' => __( 'Application', 'bpu' ),
                'menu_name'     => __( 'Applications', 'bpu' ),
                'all_items'     => __( 'All Applications', 'bpu' ),
            ),
            'public'             => false,
            'publicly_queryable' => false,
            'show_ui'            => true,
            'show_in_menu'       => 'edit.php?post_type=bpu_job',
            'capability_type'    => 'post',
            'has_archive'        => false,
            'hierarchical'       => false,
            'supports'           => array( 'title', 'custom-fields', 'author' ),
            'show_in_rest'       => false,
        ) );
    }

    public function register_employer_role() {
        if ( ! get_role( 'bpu_employer' ) ) {
            add_role( 'bpu_employer', __( 'BPU Employer', 'bpu' ), array( 'read' => true ) );
        }
    }

    public function register_employer_taxonomy() {
        register_taxonomy( 'bpu_employer', 'bpu_job', array(
            'labels'            => array(
                'name'          => __( 'Employers', 'bpu' ),
                'singular_name' => __( 'Employer', 'bpu' ),
                'menu_name'     => __( 'Employers', 'bpu' ),
                'all_items'     => __( 'All Employers', 'bpu' ),
                'edit_item'     => __( 'Edit Employer', 'bpu' ),
                'add_new_item'  => __( 'Add New Employer', 'bpu' ),
            ),
            'public'            => false,
            'publicly_queryable'=> false,
            'hierarchical'      => false,
            'show_ui'           => true,
            'show_in_menu'      => true,
            'show_in_rest'      => false,
            'show_admin_column' => true,
            'rewrite'           => false,
        ) );
    }

    // ── Employer ↔ User link admin page ──────────────────────────

    // ── Job Reports admin page ────────────────────────────────────

    public function register_job_reports_admin_page() {
        add_submenu_page(
            'edit.php?post_type=bpu_job',
            'Job Board Reports',
            'Reports',
            'manage_options',
            'bpu-job-reports',
            array( $this, 'render_job_reports_page' )
        );
    }

    /** Fetch job stats rows, filtered by employer term, job type, and date. */
    private function get_report_rows( array $filters ): array {
        $args = array(
            'post_type'      => 'bpu_job',
            'post_status'    => array( 'publish', 'pending', 'draft' ),
            'posts_per_page' => -1,
            'orderby'        => 'date',
            'order'          => 'DESC',
        );

        if ( ! empty( $filters['employer_term_id'] ) ) {
            $args['tax_query'] = array( array(
                'taxonomy' => 'bpu_employer',
                'field'    => 'term_id',
                'terms'    => intval( $filters['employer_term_id'] ),
            ) );
        }
        if ( ! empty( $filters['job_type'] ) ) {
            $args['meta_query'] = array( array(
                'key'   => '_bpu_job_type',
                'value' => sanitize_text_field( $filters['job_type'] ),
            ) );
        }
        if ( ! empty( $filters['date_from'] ) ) {
            $args['date_query'][] = array( 'after' => sanitize_text_field( $filters['date_from'] ), 'inclusive' => true );
        }
        if ( ! empty( $filters['date_to'] ) ) {
            $args['date_query'][] = array( 'before' => sanitize_text_field( $filters['date_to'] ), 'inclusive' => true );
        }

        $posts = get_posts( $args );
        $rows  = array();
        foreach ( $posts as $p ) {
            $imp  = intval( get_post_meta( $p->ID, '_bpu_impressions', true ) );
            $clk  = intval( get_post_meta( $p->ID, '_bpu_clicks', true ) );
            $app  = intval( get_post_meta( $p->ID, '_bpu_applications_count', true ) );
            $ctr  = $imp > 0 ? round( $clk / $imp * 100, 1 ) : 0;

            $emp_terms = wp_get_post_terms( $p->ID, 'bpu_employer', array( 'fields' => 'names' ) );
            $company   = ( ! is_wp_error( $emp_terms ) && $emp_terms ) ? $emp_terms[0] : get_post_meta( $p->ID, '_bpu_company', true );

            $rows[] = array(
                'id'          => $p->ID,
                'title'       => $p->post_title,
                'company'     => $company ?: '—',
                'job_type'    => get_post_meta( $p->ID, '_bpu_job_type', true ) ?: 'outbound',
                'industry'    => get_post_meta( $p->ID, '_bpu_industry', true ) ?: '—',
                'location'    => get_post_meta( $p->ID, '_bpu_location', true ) ?: '—',
                'status'      => $p->post_status,
                'date'        => $p->post_date,
                'impressions' => $imp,
                'clicks'      => $clk,
                'applications'=> $app,
                'ctr'         => $ctr,
            );
        }
        return $rows;
    }

    public function render_job_reports_page() {
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Not allowed.' );

        $filters = array(
            'employer_term_id' => intval( $_GET['employer_term_id'] ?? 0 ),
            'job_type'         => sanitize_text_field( $_GET['job_type'] ?? '' ),
            'date_from'        => sanitize_text_field( $_GET['date_from'] ?? '' ),
            'date_to'          => sanitize_text_field( $_GET['date_to'] ?? '' ),
        );

        $rows = $this->get_report_rows( $filters );

        $total_imp = array_sum( array_column( $rows, 'impressions' ) );
        $total_clk = array_sum( array_column( $rows, 'clicks' ) );
        $total_app = array_sum( array_column( $rows, 'applications' ) );
        $avg_ctr   = $total_imp > 0 ? round( $total_clk / $total_imp * 100, 1 ) : 0;

        $all_employers = get_terms( array( 'taxonomy' => 'bpu_employer', 'hide_empty' => false, 'orderby' => 'name' ) );
        if ( is_wp_error( $all_employers ) ) $all_employers = array();

        $export_url = add_query_arg( array_merge( (array) $filters, array(
            'action' => 'bpu_reports_export_csv',
            'nonce'  => wp_create_nonce( 'bpu_reports_csv' ),
        ) ), admin_url( 'admin-ajax.php' ) );
        ?>
        <div class="wrap">
            <h1 style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
                Job Board Reports
                <a href="<?php echo esc_url( $export_url . '&' . http_build_query( $filters ) ); ?>" class="button button-secondary">
                    ⬇ Export CSV
                </a>
            </h1>

            <!-- Filters -->
            <form method="get" style="background:#fff;padding:14px 16px;border:1px solid #ddd;border-radius:4px;display:flex;flex-wrap:wrap;gap:12px;align-items:flex-end;margin:16px 0;">
                <input type="hidden" name="post_type" value="bpu_job" />
                <input type="hidden" name="page" value="bpu-job-reports" />

                <div>
                    <label style="display:block;font-size:12px;font-weight:600;margin-bottom:3px;">Employer</label>
                    <select name="employer_term_id">
                        <option value="">All employers</option>
                        <?php foreach ( $all_employers as $t ) : ?>
                            <option value="<?php echo esc_attr( $t->term_id ); ?>" <?php selected( $filters['employer_term_id'], $t->term_id ); ?>>
                                <?php echo esc_html( $t->name ); ?> (<?php echo (int) $t->count; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label style="display:block;font-size:12px;font-weight:600;margin-bottom:3px;">Job Type</label>
                    <select name="job_type">
                        <option value="">All types</option>
                        <option value="inbound"  <?php selected( $filters['job_type'], 'inbound' );  ?>>Inbound (apply on BPU)</option>
                        <option value="outbound" <?php selected( $filters['job_type'], 'outbound' ); ?>>Outbound (partner site)</option>
                    </select>
                </div>

                <div>
                    <label style="display:block;font-size:12px;font-weight:600;margin-bottom:3px;">Posted from</label>
                    <input type="date" name="date_from" value="<?php echo esc_attr( $filters['date_from'] ); ?>" />
                </div>

                <div>
                    <label style="display:block;font-size:12px;font-weight:600;margin-bottom:3px;">Posted to</label>
                    <input type="date" name="date_to" value="<?php echo esc_attr( $filters['date_to'] ); ?>" />
                </div>

                <div style="display:flex;gap:6px;">
                    <button type="submit" class="button button-primary">Filter</button>
                    <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=bpu_job&page=bpu-job-reports' ) ); ?>" class="button">Reset</a>
                </div>
            </form>

            <!-- Summary cards -->
            <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:20px;">
                <?php foreach ( array(
                    array( 'label' => 'Total Jobs', 'value' => count( $rows ), 'color' => '#1a56db' ),
                    array( 'label' => 'Impressions', 'value' => number_format( $total_imp ), 'color' => '#0e9f6e' ),
                    array( 'label' => 'Clicks / Applications', 'value' => number_format( $total_clk ) . ' / ' . number_format( $total_app ), 'color' => '#e3a008' ),
                    array( 'label' => 'Avg CTR', 'value' => $avg_ctr . '%', 'color' => '#7e3af2' ),
                ) as $card ) : ?>
                    <div style="background:#fff;border:1px solid #ddd;border-radius:6px;padding:16px 18px;border-top:3px solid <?php echo $card['color']; ?>;">
                        <p style="font-size:11px;text-transform:uppercase;letter-spacing:.05em;color:#666;margin:0 0 6px;"><?php echo esc_html( $card['label'] ); ?></p>
                        <p style="font-size:24px;font-weight:700;color:#1e1e1e;margin:0;"><?php echo esc_html( $card['value'] ); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Per-job table -->
            <?php if ( empty( $rows ) ) : ?>
                <p>No jobs found for the selected filters.</p>
            <?php else : ?>
            <table class="widefat striped" id="bpu-reports-table">
                <thead>
                    <tr>
                        <th class="sortable" data-col="0">Job Title</th>
                        <th class="sortable" data-col="1">Employer</th>
                        <th class="sortable" data-col="2">Type</th>
                        <th class="sortable" data-col="3">Industry</th>
                        <th class="sortable" data-col="4">Status</th>
                        <th class="sortable" data-col="5" style="text-align:right;">Impressions</th>
                        <th class="sortable" data-col="6" style="text-align:right;">Clicks</th>
                        <th class="sortable" data-col="7" style="text-align:right;">Applications</th>
                        <th class="sortable" data-col="8" style="text-align:right;">CTR</th>
                        <th>Posted</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $rows as $r ) :
                        $status_colour = $r['status'] === 'publish' ? '#0e9f6e' : '#aaa';
                    ?>
                    <tr>
                        <td><a href="<?php echo esc_url( get_edit_post_link( $r['id'] ) ); ?>"><?php echo esc_html( $r['title'] ); ?></a></td>
                        <td><?php echo esc_html( $r['company'] ); ?></td>
                        <td><?php echo esc_html( $r['job_type'] ); ?></td>
                        <td><?php echo esc_html( $r['industry'] ); ?></td>
                        <td><span style="color:<?php echo $status_colour; ?>;font-weight:600;"><?php echo esc_html( $r['status'] ); ?></span></td>
                        <td style="text-align:right;"><?php echo number_format( $r['impressions'] ); ?></td>
                        <td style="text-align:right;"><?php echo number_format( $r['clicks'] ); ?></td>
                        <td style="text-align:right;"><?php echo number_format( $r['applications'] ); ?></td>
                        <td style="text-align:right;"><?php echo esc_html( $r['ctr'] ); ?>%</td>
                        <td><?php echo esc_html( date( 'd M Y', strtotime( $r['date'] ) ) ); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <script>
            (function(){
                const th = document.querySelectorAll('#bpu-reports-table thead th.sortable');
                th.forEach(function(h){
                    h.style.cursor='pointer';
                    h.title='Click to sort';
                    h.addEventListener('click', function(){
                        const col = parseInt(h.dataset.col);
                        const tbody = document.querySelector('#bpu-reports-table tbody');
                        const rows = Array.from(tbody.querySelectorAll('tr'));
                        const asc = h.dataset.dir !== 'asc';
                        h.dataset.dir = asc ? 'asc' : 'desc';
                        th.forEach(function(x){ x.style.fontStyle = 'normal'; });
                        h.style.fontStyle = 'italic';
                        rows.sort(function(a,b){
                            const av = a.cells[col].innerText.replace(/[,%]/g,'').trim();
                            const bv = b.cells[col].innerText.replace(/[,%]/g,'').trim();
                            const an = parseFloat(av), bn = parseFloat(bv);
                            if (!isNaN(an) && !isNaN(bn)) return asc ? an-bn : bn-an;
                            return asc ? av.localeCompare(bv) : bv.localeCompare(av);
                        });
                        rows.forEach(function(r){ tbody.appendChild(r); });
                    });
                });
            })();
            </script>
            <?php endif; ?>
        </div>
        <?php
    }

    public function ajax_reports_export_csv() {
        if ( ! check_ajax_referer( 'bpu_reports_csv', 'nonce', false ) ) wp_die( 'Bad nonce.' );
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Not allowed.' );

        $filters = array(
            'employer_term_id' => intval( $_GET['employer_term_id'] ?? 0 ),
            'job_type'         => sanitize_text_field( $_GET['job_type'] ?? '' ),
            'date_from'        => sanitize_text_field( $_GET['date_from'] ?? '' ),
            'date_to'          => sanitize_text_field( $_GET['date_to'] ?? '' ),
        );
        $rows = $this->get_report_rows( $filters );

        $filename = 'bpu-job-report-' . date( 'Y-m-d' ) . '.csv';
        header( 'Content-Type: text/csv; charset=utf-8' );
        header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
        header( 'Pragma: no-cache' );

        $out = fopen( 'php://output', 'w' );
        fputcsv( $out, array( 'ID', 'Title', 'Employer', 'Job Type', 'Industry', 'Location', 'Status', 'Posted', 'Impressions', 'Clicks', 'Applications', 'CTR %' ) );
        foreach ( $rows as $r ) {
            fputcsv( $out, array(
                $r['id'], $r['title'], $r['company'], $r['job_type'], $r['industry'],
                $r['location'], $r['status'], date( 'd/m/Y', strtotime( $r['date'] ) ),
                $r['impressions'], $r['clicks'], $r['applications'], $r['ctr'],
            ) );
        }
        fclose( $out );
        exit;
    }

    public function register_employer_link_admin_page() {
        add_submenu_page(
            'edit.php?post_type=bpu_job',
            'Link Employers to Users',
            'Employer Accounts',
            'manage_options',
            'bpu-employer-accounts',
            array( $this, 'render_employer_link_admin_page' )
        );
    }

    public function render_employer_link_admin_page() {
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Not allowed.' );

        $nonce    = wp_create_nonce( 'bpu_employer_link_nonce' );
        $terms    = get_terms( array( 'taxonomy' => 'bpu_employer', 'hide_empty' => false, 'orderby' => 'name', 'order' => 'ASC' ) );
        if ( is_wp_error( $terms ) ) $terms = array();

        // Build term → linked user map
        $term_users = array();
        foreach ( $terms as $term ) {
            $linked = get_users( array(
                'meta_key'   => '_bpu_employer_term_id',
                'meta_value' => $term->term_id,
                'number'     => 10,
                'fields'     => array( 'ID', 'display_name', 'user_email' ),
            ) );
            $logo_url = get_term_meta( $term->term_id, 'logo_url', true );
            $term_users[ $term->term_id ] = array(
                'term'     => $term,
                'logo_url' => $logo_url,
                'users'    => $linked,
                'jobs'     => $term->count,
            );
        }
        ?>
        <div class="wrap" id="bpu-employer-wrap">
            <h1>Employer Accounts</h1>
            <p>Link WordPress users with the <code>bpu_employer</code> role to their company's employer term.
               Linked users can manage their company profile (logo, tagline, about text, etc.) from the employer dashboard.</p>

            <?php if ( empty( $terms ) ) : ?>
                <div class="notice notice-warning inline"><p>No employer terms found. Run the WJM importer first, or create employers via the BPU Job Board.</p></div>
            <?php else : ?>

            <div id="bpu-employer-msg" style="display:none;margin-bottom:12px;"></div>

            <table class="widefat striped" style="margin-top:16px;">
                <thead>
                    <tr>
                        <th style="width:40px;"></th>
                        <th>Company</th>
                        <th>Jobs</th>
                        <th>Linked account(s)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ( $term_users as $tid => $data ) :
                    $term     = $data['term'];
                    $logo_url = $data['logo_url'];
                    $users    = $data['users'];
                    $initials = implode( '', array_map( fn($w) => strtoupper( $w[0] ?? '' ),
                                array_slice( explode( ' ', $term->name ), 0, 2 ) ) );
                ?>
                    <tr id="bpu-erow-<?php echo esc_attr( $tid ); ?>">
                        <td style="vertical-align:middle;">
                            <?php if ( $logo_url ) : ?>
                                <img src="<?php echo esc_url( $logo_url ); ?>" style="width:36px;height:36px;object-fit:contain;border-radius:4px;border:1px solid #ddd;" />
                            <?php else : ?>
                                <div style="width:36px;height:36px;border-radius:4px;background:#e8f0fe;color:#1a56db;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;border:1px solid #ddd;">
                                    <?php echo esc_html( $initials ); ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td style="vertical-align:middle;">
                            <strong><?php echo esc_html( $term->name ); ?></strong>
                            <br><small style="color:#888;">Term ID: <?php echo (int) $tid; ?></small>
                        </td>
                        <td style="vertical-align:middle;"><?php echo (int) $data['jobs']; ?></td>
                        <td style="vertical-align:middle;" id="bpu-eusers-<?php echo esc_attr( $tid ); ?>">
                            <?php if ( $users ) :
                                foreach ( $users as $u ) : ?>
                                    <div style="display:flex;align-items:center;gap:6px;margin-bottom:4px;">
                                        <span><?php echo esc_html( $u->display_name ); ?> <small style="color:#888;">(<?php echo esc_html( $u->user_email ); ?>)</small></span>
                                        <button class="button button-small bpu-unlink-btn"
                                            data-term="<?php echo esc_attr( $tid ); ?>"
                                            data-user="<?php echo esc_attr( $u->ID ); ?>"
                                            data-nonce="<?php echo esc_attr( $nonce ); ?>">
                                            Unlink
                                        </button>
                                    </div>
                                <?php endforeach;
                            else : ?>
                                <em style="color:#aaa;">No account linked</em>
                            <?php endif; ?>
                        </td>
                        <td style="vertical-align:middle;min-width:260px;">
                            <div style="display:flex;gap:6px;align-items:center;flex-wrap:wrap;">
                                <input type="text"
                                    class="bpu-user-search"
                                    data-term="<?php echo esc_attr( $tid ); ?>"
                                    placeholder="Search by name or email…"
                                    style="width:200px;"
                                    autocomplete="off"
                                />
                                <div class="bpu-user-results" data-term="<?php echo esc_attr( $tid ); ?>"
                                    style="display:none;position:absolute;background:#fff;border:1px solid #ddd;z-index:9999;max-width:280px;border-radius:4px;box-shadow:0 2px 8px rgba(0,0,0,.12);">
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <?php endif; ?>
        </div>

        <style>
        .bpu-user-results a { display:block; padding:7px 12px; font-size:13px; text-decoration:none; color:#1e1e1e; }
        .bpu-user-results a:hover { background:#f0f0f0; }
        #bpu-employer-wrap td { position:relative; }
        </style>

        <script>
        (function($){
            const nonce = '<?php echo esc_js( $nonce ); ?>';

            // ── Live user search ──────────────────────────────────
            let searchTimer = null;
            $(document).on('input', '.bpu-user-search', function(){
                const $input  = $(this);
                const termId  = $input.data('term');
                const q       = $input.val().trim();
                const $res    = $('.bpu-user-results[data-term="' + termId + '"]');

                clearTimeout(searchTimer);
                if ( q.length < 2 ) { $res.hide().empty(); return; }

                searchTimer = setTimeout(function(){
                    $.post(ajaxurl, { action: 'bpu_employer_search_users', nonce, q }, function(r){
                        $res.empty();
                        if ( !r.success || !r.data.length ) {
                            $res.html('<a href="#">No users found</a>').show();
                            return;
                        }
                        r.data.forEach(function(u){
                            $res.append(
                                $('<a href="#">').text(u.display_name + ' (' + u.user_email + ')')
                                    .data({ user: u.ID, term: termId })
                            );
                        });
                        $res.show();
                    });
                }, 300);
            });

            // ── Pick a user from search results ───────────────────
            $(document).on('click', '.bpu-user-results a', function(e){
                e.preventDefault();
                const $a     = $(this);
                const userId = $a.data('user');
                const termId = $a.data('term');
                if ( !userId ) return;

                const $res    = $('.bpu-user-results[data-term="' + termId + '"]');
                const $input  = $('.bpu-user-search[data-term="' + termId + '"]');
                $res.hide().empty();
                $input.val('');

                $.post(ajaxurl, { action: 'bpu_employer_link_user', nonce, term_id: termId, user_id: userId }, function(r){
                    if ( !r.success ) { showMsg(r.data || 'Error linking user.', 'error'); return; }
                    $('#bpu-eusers-' + termId).html(r.data.users_html);
                    showMsg(r.data.message, 'success');
                });
            });

            // ── Unlink ────────────────────────────────────────────
            $(document).on('click', '.bpu-unlink-btn', function(){
                const $btn   = $(this);
                const termId = $btn.data('term');
                const userId = $btn.data('user');
                if ( !confirm('Unlink this user from the employer term?') ) return;

                $.post(ajaxurl, { action: 'bpu_employer_unlink_user', nonce, term_id: termId, user_id: userId }, function(r){
                    if ( !r.success ) { showMsg(r.data || 'Error unlinking.', 'error'); return; }
                    $('#bpu-eusers-' + termId).html(r.data.users_html);
                    showMsg(r.data.message, 'success');
                });
            });

            // Close dropdown on outside click
            $(document).on('click', function(e){
                if ( !$(e.target).closest('.bpu-user-search, .bpu-user-results').length ) {
                    $('.bpu-user-results').hide().empty();
                }
            });

            function showMsg(msg, type){
                const colour = type === 'success' ? '#d1e7dd' : '#f8d7da';
                const border = type === 'success' ? '#a3cfbb' : '#f1aeb5';
                $('#bpu-employer-msg')
                    .html('<div style="padding:10px 14px;border:1px solid ' + border + ';background:' + colour + ';border-radius:4px;">' + msg + '</div>')
                    .show();
                setTimeout(function(){ $('#bpu-employer-msg').fadeOut(); }, 4000);
            }
        })(jQuery);
        </script>
        <?php
    }

    public function ajax_employer_search_users() {
        check_ajax_referer( 'bpu_employer_link_nonce', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error( 'Unauthorized' );

        $q = sanitize_text_field( $_POST['q'] ?? '' );
        if ( strlen( $q ) < 2 ) wp_send_json_success( array() );

        $users = get_users( array(
            'role__in'   => array( 'bpu_employer', 'administrator' ),
            'search'     => '*' . $q . '*',
            'search_columns' => array( 'display_name', 'user_email', 'user_login' ),
            'number'     => 10,
            'fields'     => array( 'ID', 'display_name', 'user_email' ),
        ) );

        wp_send_json_success( $users );
    }

    public function ajax_employer_link_user() {
        check_ajax_referer( 'bpu_employer_link_nonce', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error( 'Unauthorized' );

        $term_id = intval( $_POST['term_id'] ?? 0 );
        $user_id = intval( $_POST['user_id'] ?? 0 );

        if ( ! $term_id || ! $user_id ) wp_send_json_error( 'Invalid data.' );

        $term = get_term( $term_id, 'bpu_employer' );
        if ( ! $term || is_wp_error( $term ) ) wp_send_json_error( 'Employer term not found.' );

        $user = get_userdata( $user_id );
        if ( ! $user ) wp_send_json_error( 'User not found.' );

        // Ensure user has bpu_employer role
        if ( ! in_array( 'bpu_employer', (array) $user->roles, true ) && ! in_array( 'administrator', (array) $user->roles, true ) ) {
            $user->add_role( 'bpu_employer' );
        }

        update_user_meta( $user_id, '_bpu_employer_term_id', $term_id );
        update_user_meta( $user_id, '_bpu_company_name', $term->name );

        wp_send_json_success( array(
            'message'   => esc_html( $user->display_name ) . ' linked to <strong>' . esc_html( $term->name ) . '</strong>.',
            'users_html'=> $this->build_linked_users_html( $term_id ),
        ) );
    }

    public function ajax_employer_unlink_user() {
        check_ajax_referer( 'bpu_employer_link_nonce', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error( 'Unauthorized' );

        $term_id = intval( $_POST['term_id'] ?? 0 );
        $user_id = intval( $_POST['user_id'] ?? 0 );

        if ( ! $term_id || ! $user_id ) wp_send_json_error( 'Invalid data.' );

        delete_user_meta( $user_id, '_bpu_employer_term_id' );

        $term = get_term( $term_id, 'bpu_employer' );
        $name = $term && ! is_wp_error( $term ) ? $term->name : "term #{$term_id}";

        $user = get_userdata( $user_id );
        $uname = $user ? $user->display_name : "user #{$user_id}";

        wp_send_json_success( array(
            'message'   => esc_html( $uname ) . ' unlinked from <strong>' . esc_html( $name ) . '</strong>.',
            'users_html'=> $this->build_linked_users_html( $term_id ),
        ) );
    }

    private function build_linked_users_html( int $term_id ): string {
        $nonce = wp_create_nonce( 'bpu_employer_link_nonce' );
        $users = get_users( array(
            'meta_key'   => '_bpu_employer_term_id',
            'meta_value' => $term_id,
            'number'     => 10,
            'fields'     => array( 'ID', 'display_name', 'user_email' ),
        ) );
        if ( ! $users ) return '<em style="color:#aaa;">No account linked</em>';
        $html = '';
        foreach ( $users as $u ) {
            $html .= '<div style="display:flex;align-items:center;gap:6px;margin-bottom:4px;">'
                . '<span>' . esc_html( $u->display_name ) . ' <small style="color:#888;">(' . esc_html( $u->user_email ) . ')</small></span>'
                . '<button class="button button-small bpu-unlink-btn"'
                . ' data-term="' . esc_attr( $term_id ) . '"'
                . ' data-user="' . esc_attr( $u->ID ) . '"'
                . ' data-nonce="' . esc_attr( $nonce ) . '">'
                . 'Unlink</button></div>';
        }
        return $html;
    }

    /**
     * Get or create a bpu_employer term by company name.
     * Optionally seeds term meta on creation/update if $meta array is provided.
     */
    public static function get_or_create_employer_term( string $name, array $meta = [] ): ?int {
        if ( ! $name ) return null;
        $term = get_term_by( 'name', $name, 'bpu_employer' );
        if ( ! $term ) {
            $result = wp_insert_term( $name, 'bpu_employer' );
            if ( is_wp_error( $result ) ) return null;
            $term_id = (int) $result['term_id'];
        } else {
            $term_id = (int) $term->term_id;
        }
        // Description goes into the built-in term description column, not term meta
        if ( isset( $meta['description'] ) && $meta['description'] !== '' ) {
            wp_update_term( $term_id, 'bpu_employer', array( 'description' => $meta['description'] ) );
        }
        foreach ( $meta as $key => $value ) {
            if ( $key === 'description' ) continue; // handled above
            if ( $value !== '' && $value !== null ) {
                update_term_meta( $term_id, $key, $value );
            }
        }
        return $term_id;
    }

    /**
     * Return the employer object for a given term_id (or null).
     */
    private function get_employer_data( int $term_id ): ?array {
        if ( ! $term_id ) return null;
        $term = get_term( $term_id, 'bpu_employer' );
        if ( ! $term || is_wp_error( $term ) ) return null;
        $gm = fn( $k ) => get_term_meta( $term_id, $k, true );
        return array(
            'id'          => $term_id,
            'name'        => $term->name,
            'logo_url'    => (string) $gm( 'logo_url' ),
            'website'     => (string) $gm( 'website' ),
            'tagline'     => (string) $gm( 'tagline' ),
            'twitter'     => (string) $gm( 'twitter' ),
            'video'       => (string) $gm( 'video' ),
            // Use the built-in WP term description (wp_term_taxonomy.description),
            // not term meta — this is what the WP admin edit page writes to.
            'description' => (string) $term->description,
        );
    }

    private function make_excerpt( string $html, ?array $employer, int $length = 140 ): string {
        // Strip HTML tags and normalise whitespace
        $text = wp_strip_all_tags( $html );
        $text = preg_replace( '/\s+/', ' ', $text );
        $text = trim( $text );

        // Fall back to employer description if job has no content
        if ( $text === '' && $employer && ! empty( $employer['description'] ) ) {
            $text = wp_strip_all_tags( $employer['description'] );
            $text = preg_replace( '/\s+/', ' ', $text );
            $text = trim( $text );
        }

        if ( $text === '' ) return '';
        return mb_strlen( $text ) > $length
            ? rtrim( mb_substr( $text, 0, $length ) ) . '…'
            : $text;
    }

    private function format_job_for_api( $post ) {
        $get = function ( $key ) use ( $post ) {
            return get_post_meta( $post->ID, $key, true );
        };
        $questions = maybe_unserialize( $get( '_bpu_screening_questions' ) );

        // Resolve bpu_employer taxonomy term
        $employer_terms   = wp_get_post_terms( $post->ID, 'bpu_employer', array( 'fields' => 'ids' ) );
        $employer_term_id = ( ! is_wp_error( $employer_terms ) && ! empty( $employer_terms ) ) ? (int) $employer_terms[0] : 0;

        // If the job has no term assigned, try looking up by company name and auto-tagging
        if ( ! $employer_term_id ) {
            $company_meta = (string) $get( '_bpu_company' );
            if ( $company_meta ) {
                $found = get_term_by( 'name', $company_meta, 'bpu_employer' );
                if ( $found && ! is_wp_error( $found ) ) {
                    $employer_term_id = (int) $found->term_id;
                    wp_set_post_terms( $post->ID, array( $employer_term_id ), 'bpu_employer' );
                }
            }
        }

        $employer     = $this->get_employer_data( $employer_term_id );
        $company_name = $employer ? $employer['name'] : (string) $get( '_bpu_company' );

        return array(
            'id'                  => $post->ID,
            'title'               => $post->post_title,
            'slug'                => $post->post_name,
            'description'         => $post->post_content,
            'excerpt'             => $this->make_excerpt( $post->post_content, $employer ),
            'company'             => $company_name,
            'location'            => (string) $get( '_bpu_location' ),
            'employment_type'     => (string) $get( '_bpu_employment_type' ),
            'industry'            => (string) $get( '_bpu_industry' ),
            'salary_min'          => $get( '_bpu_salary_min' ) !== '' ? intval( $get( '_bpu_salary_min' ) ) : null,
            'salary_max'          => $get( '_bpu_salary_max' ) !== '' ? intval( $get( '_bpu_salary_max' ) ) : null,
            'salary_currency'     => $get( '_bpu_salary_currency' ) ?: 'GBP',
            'job_type'            => $get( '_bpu_job_type' ) ?: 'outbound',
            'apply_url'           => (string) $get( '_bpu_apply_url' ),
            'expires'             => (string) $get( '_bpu_expires_date' ),
            'remote'              => (bool) $get( '_bpu_remote' ),
            'featured'            => (bool) $get( '_bpu_featured' ),
            'filled'              => (bool) $get( '_bpu_filled' ),
            'impressions'         => intval( $get( '_bpu_impressions' ) ?: 0 ),
            'clicks'              => intval( $get( '_bpu_clicks' ) ?: 0 ),
            'applications'        => intval( $get( '_bpu_applications_count' ) ?: 0 ),
            'screening_questions' => is_array( $questions ) ? $questions : array(),
            'date_posted'         => $post->post_date,
            'employer_id'         => intval( $post->post_author ),
            'employer'            => $employer,
        );
    }

    public function get_jobs( WP_REST_Request $request ) {
        $per_page    = min( 50, max( 1, intval( $request->get_param( 'per_page' ) ?: 20 ) ) );
        $page        = max( 1, intval( $request->get_param( 'page' ) ?: 1 ) );
        $job_type    = sanitize_text_field( $request->get_param( 'job_type' ) ?: '' );
        $industry    = sanitize_text_field( $request->get_param( 'industry' ) ?: '' );
        $search      = sanitize_text_field( $request->get_param( 'search' ) ?: '' );
        $remote_only = (bool) $request->get_param( 'remote' );
        $emp_type    = sanitize_text_field( $request->get_param( 'employment_type' ) ?: '' );

        $args = array(
            'post_type'      => 'bpu_job',
            'post_status'    => 'publish',
            'posts_per_page' => $per_page,
            'paged'          => $page,
            'orderby'        => 'date',
            'order'          => 'DESC',
        );

        $today      = gmdate( 'Y-m-d' );
        $meta_query = array(
            'relation' => 'AND',
            // Exclude jobs where an expiry date is set and is in the past
            array(
                'relation' => 'OR',
                array( 'key' => '_bpu_expires_date', 'value' => '', 'compare' => '=' ),
                array( 'key' => '_bpu_expires_date', 'compare' => 'NOT EXISTS' ),
                array( 'key' => '_bpu_expires_date', 'value' => $today, 'compare' => '>=' ),
            ),
        );
        if ( $job_type && in_array( $job_type, array( 'inbound', 'outbound' ), true ) ) {
            $meta_query[] = array( 'key' => '_bpu_job_type', 'value' => $job_type );
        }
        if ( $industry ) {
            $meta_query[] = array( 'key' => '_bpu_industry', 'value' => $industry, 'compare' => 'LIKE' );
        }
        if ( $remote_only ) {
            $meta_query[] = array( 'key' => '_bpu_remote', 'value' => '1' );
        }
        if ( $emp_type ) {
            $meta_query[] = array( 'key' => '_bpu_employment_type', 'value' => sanitize_text_field( $emp_type ) );
        }
        $args['meta_query'] = $meta_query;
        if ( $search ) {
            $args['s'] = $search;
        }

        $query = new WP_Query( $args );
        $jobs  = array_map( array( $this, 'format_job_for_api' ), $query->posts );

        return new WP_REST_Response( array(
            'jobs'  => $jobs,
            'total' => $query->found_posts,
            'pages' => $query->max_num_pages,
        ), 200 );
    }

    public function get_single_job( WP_REST_Request $request ) {
        $job_id = intval( $request->get_param( 'id' ) );
        $post   = get_post( $job_id );

        if ( ! $post || $post->post_type !== 'bpu_job' || $post->post_status !== 'publish' ) {
            return new WP_Error( 'bpu_not_found', __( 'Job not found.', 'bpu' ), array( 'status' => 404 ) );
        }

        // Return 404 for expired jobs
        $expires = get_post_meta( $job_id, '_bpu_expires_date', true );
        if ( $expires && $expires < gmdate( 'Y-m-d' ) ) {
            return new WP_Error( 'bpu_not_found', __( 'Job not found.', 'bpu' ), array( 'status' => 404 ) );
        }

        // Track impression
        $impressions = intval( get_post_meta( $job_id, '_bpu_impressions', true ) ?: 0 );
        update_post_meta( $job_id, '_bpu_impressions', $impressions + 1 );

        $job                = $this->format_job_for_api( $post );
        $job['impressions'] = $impressions + 1;

        return new WP_REST_Response( array( 'job' => $job ), 200 );
    }

    public function track_job_outbound_click( WP_REST_Request $request ) {
        $job_id = intval( $request->get_param( 'id' ) );
        $post   = get_post( $job_id );

        if ( ! $post || $post->post_type !== 'bpu_job' ) {
            return new WP_Error( 'bpu_not_found', __( 'Job not found.', 'bpu' ), array( 'status' => 404 ) );
        }

        $clicks = intval( get_post_meta( $job_id, '_bpu_clicks', true ) ?: 0 );
        update_post_meta( $job_id, '_bpu_clicks', $clicks + 1 );

        return new WP_REST_Response( array( 'success' => true, 'clicks' => $clicks + 1 ), 200 );
    }

    public function create_job( WP_REST_Request $request ) {
        $payload = $this->verify_jwt_bearer( $request );
        if ( ! $payload || empty( $payload['user_id'] ) ) {
            return new WP_Error( 'bpu_unauthorized', __( 'Unauthorized.', 'bpu' ), array( 'status' => 401 ) );
        }

        $user_id = intval( $payload['user_id'] );
        $user    = get_userdata( $user_id );
        $is_admin    = in_array( 'administrator', (array) $user->roles, true );
        $is_employer = in_array( 'bpu_employer',  (array) $user->roles, true );

        if ( ! $is_admin && ! $is_employer ) {
            return new WP_Error( 'bpu_forbidden', __( 'Only employers can post jobs.', 'bpu' ), array( 'status' => 403 ) );
        }

        $body     = $request->get_json_params();
        $title    = sanitize_text_field( $body['title'] ?? '' );
        $job_type = in_array( $body['job_type'] ?? '', array( 'inbound', 'outbound' ), true ) ? $body['job_type'] : 'outbound';

        if ( empty( $title ) ) {
            return new WP_Error( 'bpu_missing', __( 'Job title is required.', 'bpu' ), array( 'status' => 400 ) );
        }
        if ( 'outbound' === $job_type && empty( $body['apply_url'] ) ) {
            return new WP_Error( 'bpu_missing', __( 'apply_url is required for outbound jobs.', 'bpu' ), array( 'status' => 400 ) );
        }

        $post_id = wp_insert_post( array(
            'post_type'    => 'bpu_job',
            'post_title'   => $title,
            'post_content' => wp_kses_post( $body['description'] ?? '' ),
            'post_status'  => $is_admin ? 'publish' : 'pending',
            'post_author'  => $user_id,
        ) );

        if ( is_wp_error( $post_id ) ) {
            return $post_id;
        }

        $meta = array(
            '_bpu_company'         => sanitize_text_field( $body['company'] ?? '' ),
            '_bpu_location'        => sanitize_text_field( $body['location'] ?? '' ),
            '_bpu_employment_type' => sanitize_text_field( $body['employment_type'] ?? '' ),
            '_bpu_industry'        => sanitize_text_field( $body['industry'] ?? '' ),
            '_bpu_job_type'        => $job_type,
            '_bpu_apply_url'       => esc_url_raw( $body['apply_url'] ?? '' ),
            '_bpu_salary_min'      => intval( $body['salary_min'] ?? 0 ),
            '_bpu_salary_max'      => intval( $body['salary_max'] ?? 0 ),
            '_bpu_salary_currency' => sanitize_text_field( $body['salary_currency'] ?? 'GBP' ),
            '_bpu_expires_date'    => sanitize_text_field( $body['expires_date'] ?? '' ),
            '_bpu_employer_id'     => $user_id,
            '_bpu_impressions'     => 0,
            '_bpu_clicks'          => 0,
            '_bpu_applications_count' => 0,
        );

        if ( ! empty( $body['screening_questions'] ) && is_array( $body['screening_questions'] ) ) {
            $questions = array_map( function ( $q ) {
                return array(
                    'id'       => sanitize_key( $q['id'] ?? uniqid( 'q_' ) ),
                    'question' => sanitize_text_field( $q['question'] ?? '' ),
                    'required' => ! empty( $q['required'] ),
                );
            }, $body['screening_questions'] );
            $meta['_bpu_screening_questions'] = maybe_serialize( $questions );
        }

        foreach ( $meta as $key => $value ) {
            update_post_meta( $post_id, $key, $value );
        }

        return new WP_REST_Response( array(
            'success' => true,
            'job_id'  => $post_id,
            'status'  => $is_admin ? 'published' : 'pending_review',
        ), 201 );
    }

    public function update_job( WP_REST_Request $request ) {
        $payload = $this->verify_jwt_bearer( $request );
        if ( ! $payload || empty( $payload['user_id'] ) ) {
            return new WP_Error( 'bpu_unauthorized', __( 'Unauthorized.', 'bpu' ), array( 'status' => 401 ) );
        }

        $user_id = intval( $payload['user_id'] );
        $job_id  = intval( $request->get_param( 'id' ) );
        $post    = get_post( $job_id );

        if ( ! $post || $post->post_type !== 'bpu_job' ) {
            return new WP_Error( 'bpu_not_found', __( 'Job not found.', 'bpu' ), array( 'status' => 404 ) );
        }

        $user     = get_userdata( $user_id );
        $is_admin = in_array( 'administrator', (array) $user->roles, true );
        if ( ! $is_admin && intval( $post->post_author ) !== $user_id ) {
            return new WP_Error( 'bpu_forbidden', __( 'You can only edit your own jobs.', 'bpu' ), array( 'status' => 403 ) );
        }

        $body   = $request->get_json_params();
        $update = array( 'ID' => $job_id );
        if ( isset( $body['title'] ) )       $update['post_title']   = sanitize_text_field( $body['title'] );
        if ( isset( $body['description'] ) ) $update['post_content'] = wp_kses_post( $body['description'] );
        wp_update_post( $update );

        $str_fields = array( 'company', 'location', 'employment_type', 'industry', 'apply_url', 'expires_date', 'salary_currency' );
        foreach ( $str_fields as $f ) {
            if ( isset( $body[ $f ] ) ) {
                update_post_meta( $job_id, "_bpu_$f", sanitize_text_field( $body[ $f ] ) );
            }
        }
        foreach ( array( 'salary_min', 'salary_max' ) as $f ) {
            if ( isset( $body[ $f ] ) ) {
                update_post_meta( $job_id, "_bpu_$f", intval( $body[ $f ] ) );
            }
        }
        if ( isset( $body['job_type'] ) && in_array( $body['job_type'], array( 'inbound', 'outbound' ), true ) ) {
            update_post_meta( $job_id, '_bpu_job_type', $body['job_type'] );
        }
        if ( isset( $body['screening_questions'] ) && is_array( $body['screening_questions'] ) ) {
            $questions = array_map( function ( $q ) {
                return array(
                    'id'       => sanitize_key( $q['id'] ?? uniqid( 'q_' ) ),
                    'question' => sanitize_text_field( $q['question'] ?? '' ),
                    'required' => ! empty( $q['required'] ),
                );
            }, $body['screening_questions'] );
            update_post_meta( $job_id, '_bpu_screening_questions', maybe_serialize( $questions ) );
        }

        return new WP_REST_Response( array( 'success' => true ), 200 );
    }

    public function delete_job( WP_REST_Request $request ) {
        $payload = $this->verify_jwt_bearer( $request );
        if ( ! $payload || empty( $payload['user_id'] ) ) {
            return new WP_Error( 'bpu_unauthorized', __( 'Unauthorized.', 'bpu' ), array( 'status' => 401 ) );
        }

        $user_id = intval( $payload['user_id'] );
        $job_id  = intval( $request->get_param( 'id' ) );
        $post    = get_post( $job_id );

        if ( ! $post || $post->post_type !== 'bpu_job' ) {
            return new WP_Error( 'bpu_not_found', __( 'Job not found.', 'bpu' ), array( 'status' => 404 ) );
        }

        $user = get_userdata( $user_id );
        if ( ! in_array( 'administrator', (array) $user->roles, true ) && intval( $post->post_author ) !== $user_id ) {
            return new WP_Error( 'bpu_forbidden', __( 'Forbidden.', 'bpu' ), array( 'status' => 403 ) );
        }

        wp_trash_post( $job_id );
        return new WP_REST_Response( array( 'success' => true ), 200 );
    }

    // ── Admin Job Management ────────────────────────────────────

    public function admin_get_jobs( WP_REST_Request $request ) {
        $per_page = min( 50, max( 1, intval( $request->get_param( 'per_page' ) ?: 20 ) ) );
        $page     = max( 1, intval( $request->get_param( 'page' ) ?: 1 ) );
        $status   = sanitize_text_field( $request->get_param( 'status' ) ?: '' );
        $search   = sanitize_text_field( $request->get_param( 'search' ) ?: '' );

        $post_statuses = array( 'publish', 'pending', 'draft', 'trash' );
        if ( $status && in_array( $status, $post_statuses, true ) ) {
            $post_statuses = array( $status );
        }

        $args = array(
            'post_type'      => 'bpu_job',
            'post_status'    => $post_statuses,
            'posts_per_page' => $per_page,
            'paged'          => $page,
            'orderby'        => 'date',
            'order'          => 'DESC',
        );
        if ( $search ) {
            $args['s'] = $search;
        }

        $query = new WP_Query( $args );
        $jobs  = array();
        foreach ( $query->posts as $post ) {
            $job                = $this->format_job_for_api( $post );
            $job['post_status'] = $post->post_status;
            $job['author_name'] = get_the_author_meta( 'display_name', $post->post_author );
            $jobs[]             = $job;
        }

        // Also get counts per status for the tab badges
        $counts = array(
            'all'     => intval( wp_count_posts( 'bpu_job' )->publish ) + intval( wp_count_posts( 'bpu_job' )->pending ) + intval( wp_count_posts( 'bpu_job' )->draft ),
            'pending' => intval( wp_count_posts( 'bpu_job' )->pending ),
            'publish' => intval( wp_count_posts( 'bpu_job' )->publish ),
            'draft'   => intval( wp_count_posts( 'bpu_job' )->draft ),
            'trash'   => intval( wp_count_posts( 'bpu_job' )->trash ),
        );

        return new WP_REST_Response( array(
            'jobs'        => $jobs,
            'total'       => $query->found_posts,
            'total_pages' => $query->max_num_pages,
            'counts'      => $counts,
        ), 200 );
    }

    public function admin_update_job_status( WP_REST_Request $request ) {
        $job_id     = intval( $request->get_param( 'id' ) );
        $body       = $request->get_json_params();
        $new_status = sanitize_text_field( $body['status'] ?? '' );

        $allowed = array( 'publish', 'pending', 'draft', 'trash' );
        if ( ! in_array( $new_status, $allowed, true ) ) {
            return new WP_Error( 'bpu_invalid', __( 'Invalid status.', 'bpu' ), array( 'status' => 400 ) );
        }

        $post = get_post( $job_id );
        if ( ! $post || $post->post_type !== 'bpu_job' ) {
            return new WP_Error( 'bpu_not_found', __( 'Job not found.', 'bpu' ), array( 'status' => 404 ) );
        }

        wp_update_post( array( 'ID' => $job_id, 'post_status' => $new_status ) );

        return new WP_REST_Response( array(
            'success' => true,
            'job_id'  => $job_id,
            'status'  => $new_status,
        ), 200 );
    }

    public function admin_delete_job( WP_REST_Request $request ) {
        $job_id = intval( $request->get_param( 'id' ) );
        $post   = get_post( $job_id );

        if ( ! $post || $post->post_type !== 'bpu_job' ) {
            return new WP_Error( 'bpu_not_found', __( 'Job not found.', 'bpu' ), array( 'status' => 404 ) );
        }

        wp_delete_post( $job_id, true );

        return new WP_REST_Response( array( 'success' => true ), 200 );
    }

    public function submit_job_application( WP_REST_Request $request ) {
        $payload = $this->verify_jwt_bearer( $request );
        if ( ! $payload || empty( $payload['user_id'] ) ) {
            return new WP_Error( 'bpu_unauthorized', __( 'Unauthorized.', 'bpu' ), array( 'status' => 401 ) );
        }

        $user_id = intval( $payload['user_id'] );
        $job_id  = intval( $request->get_param( 'id' ) );
        $post    = get_post( $job_id );

        if ( ! $post || $post->post_type !== 'bpu_job' || $post->post_status !== 'publish' ) {
            return new WP_Error( 'bpu_not_found', __( 'Job not found.', 'bpu' ), array( 'status' => 404 ) );
        }
        if ( get_post_meta( $job_id, '_bpu_job_type', true ) !== 'inbound' ) {
            return new WP_Error( 'bpu_invalid', __( 'This job requires an external application.', 'bpu' ), array( 'status' => 400 ) );
        }

        // Prevent duplicates
        $existing = get_posts( array(
            'post_type'      => 'bpu_job_application',
            'post_status'    => 'any',
            'posts_per_page' => 1,
            'meta_query'     => array(
                array( 'key' => '_bpu_job_id',       'value' => $job_id ),
                array( 'key' => '_bpu_applicant_id', 'value' => $user_id ),
            ),
        ) );
        if ( ! empty( $existing ) ) {
            return new WP_Error( 'bpu_duplicate', __( 'You have already applied to this job.', 'bpu' ), array( 'status' => 409 ) );
        }

        $user          = get_userdata( $user_id );
        $body          = $request->get_body_params();
        $cover_letter  = sanitize_textarea_field( $body['cover_letter'] ?? '' );
        $answers_raw   = wp_unslash( $body['screening_answers'] ?? '[]' );
        $answers       = json_decode( $answers_raw, true ) ?: array();

        // CV: new upload > stored
        $cv_id = intval( get_user_meta( $user_id, '_bpu_member_cv_id', true ) );
        $files = $request->get_file_params();
        if ( ! empty( $files['cv_file'] ) && UPLOAD_ERR_OK === $files['cv_file']['error'] ) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/media.php';
            require_once ABSPATH . 'wp-admin/includes/image.php';
            $uploaded = media_handle_upload( 'cv_file', 0 );
            if ( ! is_wp_error( $uploaded ) ) {
                $cv_id = $uploaded;
            }
        }

        $job_title = $post->post_title;
        $company   = get_post_meta( $job_id, '_bpu_company', true );

        $app_id = wp_insert_post( array(
            'post_type'   => 'bpu_job_application',
            'post_title'  => sprintf( '%s — %s (%s)', $user->display_name, $job_title, $company ),
            'post_status' => 'pending',
            'post_author' => $user_id,
            'meta_input'  => array(
                '_bpu_job_id'             => $job_id,
                '_bpu_applicant_id'       => $user_id,
                '_bpu_applicant_name'     => $user->display_name,
                '_bpu_applicant_email'    => $user->user_email,
                '_bpu_applicant_phone'    => sanitize_text_field( $body['phone'] ?? get_user_meta( $user_id, 'phone_number', true ) ),
                '_bpu_cv_id'              => $cv_id,
                '_bpu_cover_letter'       => $cover_letter,
                '_bpu_screening_answers'  => maybe_serialize( $answers ),
                '_bpu_status'             => 'pending',
                '_bpu_applied_at'         => current_time( 'mysql' ),
            ),
        ) );

        if ( is_wp_error( $app_id ) ) {
            return $app_id;
        }

        // Increment application count
        $count = intval( get_post_meta( $job_id, '_bpu_applications_count', true ) ?: 0 );
        update_post_meta( $job_id, '_bpu_applications_count', $count + 1 );

        // Notify employer
        $employer_id = intval( get_post_meta( $job_id, '_bpu_employer_id', true ) );
        $employer    = get_userdata( $employer_id );
        if ( $employer ) {
            wp_mail(
                $employer->user_email,
                sprintf( 'New application: %s', $job_title ),
                sprintf( "Hello,\n\nA new application has been received for %s from %s (%s).\n\nLog in to your employer dashboard to review it.\n\nhttps://app.blackprofessionals.uk/employer/jobs/%d\n\nBPU Team", $job_title, $user->display_name, $user->user_email, $job_id ),
                array( 'Content-Type: text/plain; charset=UTF-8', 'From: BPU <noreply@blackprofessionals.uk>' )
            );
        }

        // Confirm to applicant
        wp_mail(
            $user->user_email,
            sprintf( 'Application submitted: %s at %s', $job_title, $company ),
            sprintf( "Hi %s,\n\nYour application for %s at %s has been submitted.\n\nWe'll be in touch if you're shortlisted. Good luck!\n\nBPU Team", $user->display_name, $job_title, $company ),
            array( 'Content-Type: text/plain; charset=UTF-8', 'From: BPU <noreply@blackprofessionals.uk>' )
        );

        return new WP_REST_Response( array( 'success' => true, 'application_id' => $app_id ), 201 );
    }

    public function get_job_applications( WP_REST_Request $request ) {
        $payload = $this->verify_jwt_bearer( $request );
        if ( ! $payload || empty( $payload['user_id'] ) ) {
            return new WP_Error( 'bpu_unauthorized', __( 'Unauthorized.', 'bpu' ), array( 'status' => 401 ) );
        }

        $user_id = intval( $payload['user_id'] );
        $job_id  = intval( $request->get_param( 'id' ) );
        $post    = get_post( $job_id );

        if ( ! $post || $post->post_type !== 'bpu_job' ) {
            return new WP_Error( 'bpu_not_found', __( 'Job not found.', 'bpu' ), array( 'status' => 404 ) );
        }

        $user     = get_userdata( $user_id );
        $is_admin = in_array( 'administrator', (array) $user->roles, true );
        if ( ! $is_admin && intval( $post->post_author ) !== $user_id ) {
            return new WP_Error( 'bpu_forbidden', __( 'Forbidden.', 'bpu' ), array( 'status' => 403 ) );
        }

        $apps = get_posts( array(
            'post_type'      => 'bpu_job_application',
            'post_status'    => 'any',
            'posts_per_page' => -1,
            'meta_query'     => array( array( 'key' => '_bpu_job_id', 'value' => $job_id ) ),
            'orderby'        => 'date',
            'order'          => 'DESC',
        ) );

        $result = array_map( function ( $app ) {
            $cv_id = get_post_meta( $app->ID, '_bpu_cv_id', true );
            return array(
                'id'               => $app->ID,
                'applicant_name'   => get_post_meta( $app->ID, '_bpu_applicant_name', true ),
                'applicant_email'  => get_post_meta( $app->ID, '_bpu_applicant_email', true ),
                'applicant_phone'  => get_post_meta( $app->ID, '_bpu_applicant_phone', true ),
                'cv_url'           => $cv_id ? wp_get_attachment_url( $cv_id ) : '',
                'cover_letter'     => get_post_meta( $app->ID, '_bpu_cover_letter', true ),
                'screening_answers'=> maybe_unserialize( get_post_meta( $app->ID, '_bpu_screening_answers', true ) ) ?: array(),
                'status'           => get_post_meta( $app->ID, '_bpu_status', true ) ?: 'pending',
                'applied_at'       => get_post_meta( $app->ID, '_bpu_applied_at', true ),
            );
        }, $apps );

        return new WP_REST_Response( array( 'applications' => $result, 'total' => count( $result ) ), 200 );
    }

    public function get_employer_jobs( WP_REST_Request $request ) {
        $payload = $this->verify_jwt_bearer( $request );
        if ( ! $payload || empty( $payload['user_id'] ) ) {
            return new WP_Error( 'bpu_unauthorized', __( 'Unauthorized.', 'bpu' ), array( 'status' => 401 ) );
        }

        $user_id  = intval( $payload['user_id'] );
        $user     = get_userdata( $user_id );
        $is_admin = in_array( 'administrator', (array) $user->roles, true );

        $args = array(
            'post_type'      => 'bpu_job',
            'post_status'    => array( 'publish', 'pending', 'draft' ),
            'posts_per_page' => 50,
            'orderby'        => 'date',
            'order'          => 'DESC',
        );
        if ( ! $is_admin ) {
            $args['author'] = $user_id;
        }

        $posts = get_posts( $args );
        $jobs  = array();
        foreach ( $posts as $post ) {
            $job                = $this->format_job_for_api( $post );
            $job['post_status'] = $post->post_status;
            $jobs[]             = $job;
        }

        return new WP_REST_Response( array( 'jobs' => $jobs ), 200 );
    }

    public function handle_employer_register( WP_REST_Request $request ) {
        $body         = $request->get_json_params();
        $email        = sanitize_email( $body['email'] ?? '' );
        $password     = $body['password'] ?? '';
        $company_name = sanitize_text_field( $body['company_name'] ?? '' );
        $contact_name = sanitize_text_field( $body['contact_name'] ?? '' );

        if ( empty( $email ) || empty( $password ) || empty( $company_name ) || empty( $contact_name ) ) {
            return new WP_Error( 'bpu_missing', __( 'All fields are required.', 'bpu' ), array( 'status' => 400 ) );
        }
        if ( ! is_email( $email ) ) {
            return new WP_Error( 'bpu_invalid_email', __( 'Invalid email address.', 'bpu' ), array( 'status' => 400 ) );
        }
        if ( strlen( $password ) < 8 ) {
            return new WP_Error( 'bpu_weak_password', __( 'Password must be at least 8 characters.', 'bpu' ), array( 'status' => 400 ) );
        }
        if ( email_exists( $email ) ) {
            return new WP_Error( 'bpu_duplicate', __( 'An account with this email already exists.', 'bpu' ), array( 'status' => 409 ) );
        }

        $user_id = wp_create_user( sanitize_user( $email ), $password, $email );
        if ( is_wp_error( $user_id ) ) {
            return $user_id;
        }

        $user = new WP_User( $user_id );
        $user->set_role( 'bpu_employer' );
        wp_update_user( array( 'ID' => $user_id, 'display_name' => $contact_name ) );
        update_user_meta( $user_id, '_bpu_company_name', $company_name );
        update_user_meta( $user_id, '_bpu_contact_name', $contact_name );

        // Create/find the employer taxonomy term and link to this user
        $term_id = self::get_or_create_employer_term( $company_name );
        if ( $term_id ) {
            update_user_meta( $user_id, '_bpu_employer_term_id', $term_id );
        }

        $jwt = $this->generate_jwt( array(
            'user_id'      => $user_id,
            'email'        => $email,
            'display_name' => $contact_name,
            'username'     => $email,
            'roles'        => array( 'bpu_employer' ),
        ) );

        return new WP_REST_Response( array(
            'success'      => true,
            'token'        => $jwt,
            'user_id'      => $user_id,
            'company_name' => $company_name,
        ), 201 );
    }

    // ── Employer profile ──────────────────────────────────────────────

    /** Returns the current employer's taxonomy term profile. */
    public function get_employer_profile( WP_REST_Request $request ) {
        $payload = $this->verify_jwt_bearer( $request );
        if ( ! $payload ) {
            return new WP_Error( 'bpu_unauthorized', __( 'Authentication required.', 'bpu' ), array( 'status' => 401 ) );
        }

        $user_id = intval( $payload['user_id'] ?? 0 );
        $user    = get_userdata( $user_id );
        if ( ! $user ) {
            return new WP_Error( 'bpu_not_found', __( 'User not found.', 'bpu' ), array( 'status' => 404 ) );
        }

        $is_employer = in_array( 'bpu_employer', (array) $user->roles, true );
        $is_admin    = in_array( 'administrator', (array) $user->roles, true );
        if ( ! $is_employer && ! $is_admin ) {
            return new WP_Error( 'bpu_forbidden', __( 'Employer account required.', 'bpu' ), array( 'status' => 403 ) );
        }

        $term_id = intval( get_user_meta( $user_id, '_bpu_employer_term_id', true ) );

        // Auto-create term if user registered before this feature existed
        if ( ! $term_id ) {
            $company_name = (string) get_user_meta( $user_id, '_bpu_company_name', true );
            if ( $company_name ) {
                $term_id = (int) self::get_or_create_employer_term( $company_name );
                if ( $term_id ) {
                    update_user_meta( $user_id, '_bpu_employer_term_id', $term_id );
                }
            }
        }

        $profile = $term_id ? $this->get_employer_data( $term_id ) : null;

        return new WP_REST_Response( array(
            'profile'  => $profile,
            'term_id'  => $term_id,
        ), 200 );
    }

    /** Updates the current employer's taxonomy term profile. */
    public function update_employer_profile( WP_REST_Request $request ) {
        $payload = $this->verify_jwt_bearer( $request );
        if ( ! $payload ) {
            return new WP_Error( 'bpu_unauthorized', __( 'Authentication required.', 'bpu' ), array( 'status' => 401 ) );
        }

        $user_id = intval( $payload['user_id'] ?? 0 );
        $user    = get_userdata( $user_id );
        if ( ! $user ) {
            return new WP_Error( 'bpu_not_found', __( 'User not found.', 'bpu' ), array( 'status' => 404 ) );
        }

        $is_employer = in_array( 'bpu_employer', (array) $user->roles, true );
        $is_admin    = in_array( 'administrator', (array) $user->roles, true );
        if ( ! $is_employer && ! $is_admin ) {
            return new WP_Error( 'bpu_forbidden', __( 'Employer account required.', 'bpu' ), array( 'status' => 403 ) );
        }

        $term_id = intval( get_user_meta( $user_id, '_bpu_employer_term_id', true ) );
        if ( ! $term_id ) {
            return new WP_Error( 'bpu_no_profile', __( 'No employer profile found. Please contact support.', 'bpu' ), array( 'status' => 404 ) );
        }

        $body = $request->get_json_params() ?: array();

        // Allow updating the term name (company name)
        if ( ! empty( $body['name'] ) ) {
            $new_name = sanitize_text_field( $body['name'] );
            $existing = get_term_by( 'name', $new_name, 'bpu_employer' );
            if ( $existing && (int) $existing->term_id !== $term_id ) {
                return new WP_Error( 'bpu_duplicate', __( 'Another employer already uses that company name.', 'bpu' ), array( 'status' => 409 ) );
            }
            wp_update_term( $term_id, 'bpu_employer', array( 'name' => $new_name ) );
            update_user_meta( $user_id, '_bpu_company_name', $new_name );
        }

        $allowed_meta = array( 'tagline', 'website', 'twitter', 'video' );
        foreach ( $allowed_meta as $key ) {
            if ( array_key_exists( $key, $body ) ) {
                update_term_meta( $term_id, $key, sanitize_text_field( $body[ $key ] ) );
            }
        }
        // Description lives in the built-in wp_term_taxonomy.description column
        if ( array_key_exists( 'description', $body ) ) {
            wp_update_term( $term_id, 'bpu_employer', array(
                'description' => wp_kses_post( $body['description'] ),
            ) );
        }

        // Re-tag any BPU jobs whose _bpu_company matches this employer name
        // so they all resolve to the correct term (fixes stale/missing taxonomy links)
        $term = get_term( $term_id, 'bpu_employer' );
        if ( $term && ! is_wp_error( $term ) ) {
            $matching_jobs = get_posts( array(
                'post_type'      => 'bpu_job',
                'post_status'    => array( 'publish', 'pending', 'draft' ),
                'posts_per_page' => -1,
                'fields'         => 'ids',
                'meta_query'     => array( array(
                    'key'   => '_bpu_company',
                    'value' => $term->name,
                ) ),
            ) );
            foreach ( $matching_jobs as $job_id ) {
                wp_set_post_terms( $job_id, array( $term_id ), 'bpu_employer' );
            }
        }

        $profile = $this->get_employer_data( $term_id );
        return new WP_REST_Response( array( 'success' => true, 'profile' => $profile ), 200 );
    }

    /** Handles company logo upload — stores as WP attachment and updates employer term meta. */
    public function upload_employer_logo( WP_REST_Request $request ) {
        $payload = $this->verify_jwt_bearer( $request );
        if ( ! $payload ) {
            return new WP_Error( 'bpu_unauthorized', __( 'Authentication required.', 'bpu' ), array( 'status' => 401 ) );
        }

        $user_id = intval( $payload['user_id'] ?? 0 );
        $user    = get_userdata( $user_id );
        if ( ! $user ) {
            return new WP_Error( 'bpu_not_found', __( 'User not found.', 'bpu' ), array( 'status' => 404 ) );
        }

        $is_employer = in_array( 'bpu_employer', (array) $user->roles, true );
        $is_admin    = in_array( 'administrator', (array) $user->roles, true );
        if ( ! $is_employer && ! $is_admin ) {
            return new WP_Error( 'bpu_forbidden', __( 'Employer account required.', 'bpu' ), array( 'status' => 403 ) );
        }

        $term_id = intval( get_user_meta( $user_id, '_bpu_employer_term_id', true ) );
        if ( ! $term_id ) {
            return new WP_Error( 'bpu_no_profile', __( 'No employer profile found.', 'bpu' ), array( 'status' => 404 ) );
        }

        $files = $request->get_file_params();
        if ( empty( $files['logo'] ) ) {
            return new WP_Error( 'bpu_no_file', __( 'No file uploaded.', 'bpu' ), array( 'status' => 400 ) );
        }

        $file = $files['logo'];
        $allowed_types = array( 'image/jpeg', 'image/png', 'image/svg+xml', 'image/webp' );
        if ( ! in_array( $file['type'], $allowed_types, true ) ) {
            return new WP_Error( 'bpu_invalid_type', __( 'Logo must be a JPEG, PNG, SVG, or WebP image.', 'bpu' ), array( 'status' => 400 ) );
        }
        if ( $file['size'] > 2 * 1024 * 1024 ) {
            return new WP_Error( 'bpu_too_large', __( 'Logo must be under 2 MB.', 'bpu' ), array( 'status' => 400 ) );
        }

        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';

        // Temporarily override current user for attachment attribution
        wp_set_current_user( $user_id );

        $attachment_id = media_handle_sideload( array(
            'name'     => $file['name'],
            'type'     => $file['type'],
            'tmp_name' => $file['tmp_name'],
            'error'    => $file['error'],
            'size'     => $file['size'],
        ), 0 );

        if ( is_wp_error( $attachment_id ) ) {
            return $attachment_id;
        }

        $logo_url = wp_get_attachment_url( $attachment_id );
        update_term_meta( $term_id, 'logo_url', $logo_url );
        update_term_meta( $term_id, 'logo_attachment_id', $attachment_id );

        return new WP_REST_Response( array(
            'success'  => true,
            'logo_url' => $logo_url,
        ), 200 );
    }

    // ── Mentor application ────────────────────────────────────────────

    public function submit_mentor_application( WP_REST_Request $request ) {
        $payload = $this->verify_jwt_bearer( $request );
        if ( ! $payload ) {
            return new WP_Error( 'bpu_unauthorized', __( 'Authentication required.', 'bpu' ), array( 'status' => 401 ) );
        }

        $user_id = intval( $payload['user_id'] );
        $body    = $request->get_json_params();

        $required = array( 'job_title', 'employer', 'years_exp', 'expertise', 'availability', 'has_mentored', 'motivation' );
        foreach ( $required as $field ) {
            if ( empty( $body[ $field ] ) ) {
                return new WP_Error( 'bpu_missing', sprintf( __( '%s is required.', 'bpu' ), $field ), array( 'status' => 400 ) );
            }
        }

        // Prevent duplicate applications
        $existing = get_user_meta( $user_id, 'bpu_mentor_application_status', true );
        if ( $existing && $existing !== 'rejected' ) {
            return new WP_Error( 'bpu_duplicate', __( 'You already have a pending or approved mentor application.', 'bpu' ), array( 'status' => 409 ) );
        }

        $application = array(
            'job_title'        => sanitize_text_field( $body['job_title'] ),
            'employer'         => sanitize_text_field( $body['employer'] ),
            'years_exp'        => sanitize_text_field( $body['years_exp'] ),
            'expertise'        => sanitize_text_field( $body['expertise'] ),
            'mentorship_style' => is_array( $body['mentorship_style'] ?? null )
                ? array_map( 'sanitize_text_field', $body['mentorship_style'] )
                : array(),
            'availability'     => sanitize_text_field( $body['availability'] ),
            'has_mentored'     => sanitize_text_field( $body['has_mentored'] ),
            'linkedin_url'     => esc_url_raw( $body['linkedin_url'] ?? '' ),
            'motivation'       => sanitize_textarea_field( $body['motivation'] ),
            'applied_at'       => current_time( 'mysql' ),
        );

        update_user_meta( $user_id, 'bpu_mentor_application',        $application );
        update_user_meta( $user_id, 'bpu_mentor_application_status', 'pending' );

        // Notify admin
        $user       = get_userdata( $user_id );
        $admin_mail = get_option( 'admin_email' );
        wp_mail(
            $admin_mail,
            '[BPU PAIRED] New mentor application: ' . $application['job_title'] . ' at ' . $application['employer'],
            "A new mentor application has been submitted.\n\n" .
            'Name: '       . ( $user ? $user->display_name : "User #$user_id" ) . "\n" .
            'Email: '      . ( $user ? $user->user_email : '' ) . "\n" .
            'Role: '       . $application['job_title'] . ' at ' . $application['employer'] . "\n" .
            'Expertise: '  . $application['expertise'] . "\n" .
            'Motivation: ' . $application['motivation'] . "\n\n" .
            'Review in WordPress admin: ' . admin_url( "user-edit.php?user_id=$user_id" )
        );

        return new WP_REST_Response( array( 'success' => true, 'status' => 'pending' ), 201 );
    }

    public function approve_mentor_application( WP_REST_Request $request ) {
        $user_id = intval( $request->get_param( 'user_id' ) );
        $user    = get_userdata( $user_id );

        if ( ! $user ) {
            return new WP_Error( 'bpu_not_found', __( 'User not found.', 'bpu' ), array( 'status' => 404 ) );
        }

        $status = get_user_meta( $user_id, 'bpu_mentor_application_status', true );
        if ( $status !== 'pending' ) {
            return new WP_Error( 'bpu_invalid_status', __( 'No pending application for this user.', 'bpu' ), array( 'status' => 400 ) );
        }

        update_user_meta( $user_id, 'bpu_mentor_application_status', 'approved' );
        $user->set_role( 'mentor' );

        $approve_html = $this->build_email_html(
            'You&rsquo;re in! Application approved',
            '<p style="margin:0 0 16px;font-size:15px;line-height:1.6;color:#555;">Hi ' . esc_html( $user->display_name ) . ',</p>'
            . '<p style="margin:0 0 16px;font-size:15px;line-height:1.6;color:#555;">Great news &mdash; your mentor application has been <strong style="color:#16a34a;">approved</strong>! Your profile is now live in the PAIRED mentor directory.</p>'
            . '<p style="margin:24px 0;text-align:center;">'
            . '<a href="https://pairedbybpu.uk/mentors" style="display:inline-block;padding:12px 32px;background:#7c3aed;color:#ffffff;text-decoration:none;border-radius:8px;font-size:15px;font-weight:600;">View the directory</a>'
            . '</p>'
            . '<p style="margin:0;font-size:15px;line-height:1.6;color:#555;">Thank you for joining our mentorship community!</p>'
        );
        wp_mail(
            $user->user_email,
            '[BPU PAIRED] Your mentor application has been approved!',
            $approve_html,
            array( 'Content-Type: text/html; charset=UTF-8' )
        );

        return new WP_REST_Response( array( 'success' => true, 'status' => 'approved' ), 200 );
    }

    public function reject_mentor_application( WP_REST_Request $request ) {
        $user_id = intval( $request->get_param( 'user_id' ) );
        $user    = get_userdata( $user_id );

        if ( ! $user ) {
            return new WP_Error( 'bpu_not_found', __( 'User not found.', 'bpu' ), array( 'status' => 404 ) );
        }

        $status = get_user_meta( $user_id, 'bpu_mentor_application_status', true );
        if ( $status !== 'pending' ) {
            return new WP_Error( 'bpu_invalid_status', __( 'No pending application for this user.', 'bpu' ), array( 'status' => 400 ) );
        }

        update_user_meta( $user_id, 'bpu_mentor_application_status', 'rejected' );

        $reason = sanitize_textarea_field( $request->get_param( 'reason' ) ?? '' );

        $reason_block = ! empty( $reason )
            ? '<p style="margin:0 0 16px;font-size:14px;line-height:1.6;color:#555;padding:12px 16px;background:#fafafa;border-left:3px solid #C8102E;border-radius:4px;"><strong>Reason:</strong> ' . esc_html( $reason ) . '</p>'
            : '';
        $reject_html = $this->build_email_html(
            'Mentor application update',
            '<p style="margin:0 0 16px;font-size:15px;line-height:1.6;color:#555;">Hi ' . esc_html( $user->display_name ) . ',</p>'
            . '<p style="margin:0 0 16px;font-size:15px;line-height:1.6;color:#555;">Thank you for your interest in becoming a PAIRED mentor. Unfortunately, your application was not approved at this time.</p>'
            . $reason_block
            . '<p style="margin:0;font-size:15px;line-height:1.6;color:#555;">You are welcome to apply again in the future. We appreciate your commitment to the community.</p>'
        );
        wp_mail(
            $user->user_email,
            '[BPU PAIRED] Mentor application update',
            $reject_html,
            array( 'Content-Type: text/html; charset=UTF-8' )
        );

        return new WP_REST_Response( array( 'success' => true, 'status' => 'rejected' ), 200 );
    }

    public function list_mentor_applications( WP_REST_Request $request ) {
        $status_filter = sanitize_text_field( $request->get_param( 'status' ) ?: 'pending' );

        $user_query = new WP_User_Query( array(
            'meta_key'   => 'bpu_mentor_application_status',
            'meta_value' => $status_filter,
            'orderby'    => 'registered',
            'order'      => 'DESC',
            'number'     => 100,
        ) );

        $applications = array();
        foreach ( $user_query->get_results() as $user ) {
            $app = get_user_meta( $user->ID, 'bpu_mentor_application', true );
            $applications[] = array(
                'user_id'      => $user->ID,
                'display_name' => $user->display_name,
                'email'        => $user->user_email,
                'avatar_url'   => get_avatar_url( $user->ID, array( 'size' => 96 ) ),
                'registered'   => $user->user_registered,
                'status'       => $status_filter,
                'application'  => is_array( $app ) ? $app : array(),
            );
        }

        return new WP_REST_Response( array(
            'success'      => true,
            'applications' => $applications,
            'total'        => $user_query->get_total(),
        ), 200 );
    }

    // ── Spam User Cleanup ─────────────────────────────────────────

    public function register_spam_cleanup_admin_page() {
        add_menu_page(
            'Spam User Cleanup',
            'Spam Cleanup',
            'manage_options',
            'bpu-spam-cleanup',
            array( $this, 'render_spam_cleanup_page' ),
            'dashicons-shield-alt',
            99
        );
    }

    /** Identify likely spam users based on common patterns. */
    private function get_spam_candidates(): array {
        global $wpdb;

        // URL-like username patterns: contains a dot + common TLD or starts with www
        $users = get_users( array(
            'number'  => -1,
            'fields'  => array( 'ID', 'user_login', 'user_email', 'user_registered' ),
            'role__not_in' => array( 'administrator', 'editor', 'bpu_employer' ),
        ) );

        $spam = array();
        $url_pattern = '/\.(com|uk|net|org|io|in|co|info|biz|xyz|top|site|online|store|shop|blogspot|wordpress|ru|cn|cc)\b/i';
        $crypto_pattern = '/\b(bitcoin|coinbase|binance|crypto|nft|token|invest|forex|trading|wallet|usdt|btc|eth)\b/i';

        foreach ( $users as $u ) {
            $reasons = array();
            if ( preg_match( $url_pattern, $u->user_login ) )          $reasons[] = 'URL as username';
            if ( preg_match( '#https?://#i', $u->user_login ) )         $reasons[] = 'URL as username';
            if ( str_starts_with( strtolower( $u->user_login ), 'www.' ) ) $reasons[] = 'URL as username';
            if ( preg_match( $crypto_pattern, $u->user_login ) )        $reasons[] = 'Crypto/spam keyword';
            if ( preg_match( $crypto_pattern, $u->user_email ) )        $reasons[] = 'Crypto/spam keyword in email';
            if ( strlen( $u->user_login ) > 40 )                        $reasons[] = 'Unusually long username';
            // Username looks like random chars (8+ consonants in a row)
            if ( preg_match( '/[bcdfghjklmnpqrstvwxyz]{8,}/i', $u->user_login ) ) $reasons[] = 'Random-looking username';

            if ( $reasons ) {
                $spam[] = array(
                    'id'         => $u->ID,
                    'login'      => $u->user_login,
                    'email'      => $u->user_email,
                    'registered' => $u->user_registered,
                    'reasons'    => implode( ', ', $reasons ),
                );
            }
        }

        usort( $spam, fn( $a, $b ) => strcmp( $b['registered'], $a['registered'] ) );
        return $spam;
    }

    public function render_spam_cleanup_page() {
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Not allowed.' );

        $candidates = $this->get_spam_candidates();
        $nonce      = wp_create_nonce( 'bpu_delete_spam_users' );
        $count      = count( $candidates );
        ?>
        <div class="wrap">
            <h1>🛡️ Spam User Cleanup</h1>
            <p>The scanner checks for URL-as-username, crypto/investment keywords, and random-character patterns — common in registration spam. It excludes admins and employer accounts.</p>

            <?php if ( $count === 0 ) : ?>
                <div class="notice notice-success"><p><strong>No spam users detected.</strong> Your user database looks clean.</p></div>
            <?php else : ?>
                <div class="notice notice-warning"><p><strong><?php echo $count; ?> suspected spam user<?php echo $count !== 1 ? 's' : ''; ?> found.</strong> Review below, then delete all or select specific ones.</p></div>

                <div style="margin:16px 0;display:flex;gap:12px;align-items:center;">
                    <button id="bpu-delete-all" class="button button-primary" style="background:#d63638;border-color:#d63638;">
                        Delete all <?php echo $count; ?> spam users
                    </button>
                    <button id="bpu-delete-selected" class="button">Delete selected</button>
                    <span id="bpu-result" style="margin-left:8px;font-weight:600;"></span>
                </div>

                <table class="widefat striped" style="max-width:900px;">
                    <thead>
                        <tr>
                            <th style="width:32px;"><input type="checkbox" id="bpu-check-all" /></th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Registered</th>
                            <th>Flagged for</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $candidates as $u ) : ?>
                        <tr>
                            <td><input type="checkbox" class="bpu-spam-cb" value="<?php echo esc_attr( $u['id'] ); ?>" /></td>
                            <td><code><?php echo esc_html( $u['login'] ); ?></code></td>
                            <td><?php echo esc_html( $u['email'] ); ?></td>
                            <td><?php echo esc_html( date( 'd M Y', strtotime( $u['registered'] ) ) ); ?></td>
                            <td style="color:#d63638;"><?php echo esc_html( $u['reasons'] ); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <script>
                (function() {
                    const allIds = <?php echo wp_json_encode( array_column( $candidates, 'id' ) ); ?>;
                    const nonce  = <?php echo wp_json_encode( $nonce ); ?>;
                    const result = document.getElementById('bpu-result');

                    document.getElementById('bpu-check-all').addEventListener('change', function() {
                        document.querySelectorAll('.bpu-spam-cb').forEach(cb => cb.checked = this.checked);
                    });

                    async function doDelete(ids) {
                        if ( ! ids.length ) { result.textContent = 'No users selected.'; return; }
                        if ( ! confirm(`Delete ${ids.length} user(s)? This cannot be undone.`) ) return;
                        result.textContent = 'Deleting…';
                        const fd = new FormData();
                        fd.append('action', 'bpu_delete_spam_users');
                        fd.append('nonce', nonce);
                        fd.append('ids', JSON.stringify(ids));
                        const r = await fetch(ajaxurl, { method: 'POST', body: fd });
                        const d = await r.json();
                        if (d.success) {
                            result.style.color = 'green';
                            result.textContent = `✓ Deleted ${d.data.deleted} user(s).`;
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            result.style.color = 'red';
                            result.textContent = 'Error: ' + (d.data || 'unknown');
                        }
                    }

                    document.getElementById('bpu-delete-all').addEventListener('click', () => doDelete(allIds));
                    document.getElementById('bpu-delete-selected').addEventListener('click', () => {
                        const ids = [...document.querySelectorAll('.bpu-spam-cb:checked')].map(cb => parseInt(cb.value));
                        doDelete(ids);
                    });
                })();
                </script>
            <?php endif; ?>
        </div>
        <?php
    }

    public function ajax_delete_spam_users() {
        if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error( 'Not allowed.', 403 );
        if ( ! check_ajax_referer( 'bpu_delete_spam_users', 'nonce', false ) ) wp_send_json_error( 'Bad nonce.', 403 );

        $ids = json_decode( stripslashes( $_POST['ids'] ?? '[]' ), true );
        if ( ! is_array( $ids ) ) wp_send_json_error( 'Invalid payload.' );

        $current = get_current_user_id();
        $deleted = 0;
        require_once ABSPATH . 'wp-admin/includes/user.php';

        foreach ( $ids as $id ) {
            $id = intval( $id );
            if ( ! $id || $id === $current ) continue;
            // Double-check: never delete admins
            $user = get_userdata( $id );
            if ( ! $user ) continue;
            if ( in_array( 'administrator', (array) $user->roles, true ) ) continue;

            wp_delete_user( $id );
            $deleted++;
        }

        wp_send_json_success( array( 'deleted' => $deleted ) );
    }

    // ══════════════════════════════════════════════════════════════
    //  PHASE 2: HELPER — CREATE NOTIFICATION
    // ══════════════════════════════════════════════════════════════

    /**
     * Create a paired_notification post for a user.
     *
     * @param int    $user_id  The user to notify.
     * @param string $type     One of: new_booking, booking_status, new_message, new_review.
     * @param string $title    Short notification title.
     * @param string $message  Notification body.
     * @param string $link     Optional frontend link.
     * @return int|WP_Error    The notification post ID or WP_Error on failure.
     */
    private function create_notification( $user_id, $type, $title, $message, $link = '' ) {
        $allowed_types = array( 'new_booking', 'booking_status', 'new_message', 'new_review', 'payment', 'referral', 'kyc', 'onboarding' );
        if ( ! in_array( $type, $allowed_types, true ) ) {
            $type = 'new_booking';
        }

        $post_id = wp_insert_post( array(
            'post_type'   => 'paired_notification',
            'post_title'  => sanitize_text_field( $title ),
            'post_status' => 'publish',
            'post_author' => $user_id,
        ), true );

        if ( is_wp_error( $post_id ) ) {
            return $post_id;
        }

        update_post_meta( $post_id, '_notif_user_id', $user_id );
        update_post_meta( $post_id, '_notif_type', $type );
        update_post_meta( $post_id, '_notif_title', sanitize_text_field( $title ) );
        update_post_meta( $post_id, '_notif_message', sanitize_textarea_field( $message ) );
        update_post_meta( $post_id, '_notif_link', esc_url_raw( $link ) );
        update_post_meta( $post_id, '_notif_created_at', current_time( 'mysql' ) );

        return $post_id;
    }

    // ══════════════════════════════════════════════════════════════
    //  PHASE 2: REVIEWS & RATINGS
    // ══════════════════════════════════════════════════════════════

    /**
     * POST /paired/reviews — Mentee submits a review after a completed booking.
     */
    public function submit_mentor_review( WP_REST_Request $request ) {
        $payload = $this->verify_jwt_bearer( $request );
        if ( ! $payload || empty( $payload['user_id'] ) ) {
            return new WP_Error( 'sso_invalid_token', __( 'Invalid or missing token.', 'bpu' ), array( 'status' => 401 ) );
        }
        $user_id = intval( $payload['user_id'] );

        $body       = $request->get_json_params();
        $booking_id = isset( $body['booking_id'] ) ? absint( $body['booking_id'] ) : 0;
        $rating     = isset( $body['rating'] ) ? intval( $body['rating'] ) : 0;
        $feedback   = isset( $body['feedback'] ) ? sanitize_textarea_field( $body['feedback'] ) : '';

        // Validate rating range.
        if ( $rating < 1 || $rating > 5 ) {
            return new WP_Error( 'invalid_rating', __( 'Rating must be between 1 and 5.', 'bpu' ), array( 'status' => 400 ) );
        }

        // Validate booking exists and is the right post type.
        $booking = get_post( $booking_id );
        if ( ! $booking || 'mentorship_booking' !== $booking->post_type ) {
            return new WP_Error( 'booking_not_found', __( 'Booking not found.', 'bpu' ), array( 'status' => 404 ) );
        }

        // Validate the booking is completed.
        $status = get_post_meta( $booking_id, '_bpu_booking_status', true );
        if ( 'completed' !== $status ) {
            return new WP_Error( 'booking_not_completed', __( 'You can only review completed bookings.', 'bpu' ), array( 'status' => 400 ) );
        }

        // Validate the current user is the mentee on this booking.
        $mentee_id = (int) get_post_meta( $booking_id, '_bpu_booking_mentee_id', true );
        if ( $user_id !== $mentee_id ) {
            return new WP_Error( 'not_your_booking', __( 'You can only review your own bookings.', 'bpu' ), array( 'status' => 403 ) );
        }

        $mentor_id = (int) get_post_meta( $booking_id, '_bpu_booking_mentor_id', true );

        // Check for duplicate review on the same booking.
        $existing = new WP_Query( array(
            'post_type'      => 'mentor_review',
            'post_status'    => 'any',
            'posts_per_page' => 1,
            'meta_query'     => array(
                array( 'key' => '_review_booking_id', 'value' => $booking_id, 'compare' => '=' ),
            ),
        ) );

        if ( $existing->have_posts() ) {
            return new WP_Error( 'duplicate_review', __( 'You have already reviewed this booking.', 'bpu' ), array( 'status' => 409 ) );
        }

        // Create the review.
        $mentee = get_userdata( $user_id );
        $mentor = get_userdata( $mentor_id );
        $title  = sprintf( 'Review by %s for %s', $mentee ? $mentee->display_name : $user_id, $mentor ? $mentor->display_name : $mentor_id );

        $post_id = wp_insert_post( array(
            'post_type'   => 'mentor_review',
            'post_title'  => sanitize_text_field( $title ),
            'post_status' => 'publish',
            'post_author' => $user_id,
        ), true );

        if ( is_wp_error( $post_id ) ) {
            return $post_id;
        }

        update_post_meta( $post_id, '_review_mentor_id', $mentor_id );
        update_post_meta( $post_id, '_review_mentee_id', $user_id );
        update_post_meta( $post_id, '_review_booking_id', $booking_id );
        update_post_meta( $post_id, '_review_rating', $rating );
        update_post_meta( $post_id, '_review_feedback', $feedback );
        update_post_meta( $post_id, '_review_created_at', current_time( 'mysql' ) );

        // Create a notification for the mentor.
        $this->create_notification(
            $mentor_id,
            'new_review',
            __( 'New Review Received', 'bpu' ),
            sprintf( '%s left you a %d-star review.', $mentee ? $mentee->display_name : 'A mentee', $rating ),
            '/dashboard/reviews'
        );

        return new WP_REST_Response( array(
            'success' => true,
            'review'  => array(
                'id'         => $post_id,
                'mentor_id'  => $mentor_id,
                'mentee_id'  => $user_id,
                'booking_id' => $booking_id,
                'rating'     => $rating,
                'feedback'   => $feedback,
                'created_at' => current_time( 'mysql' ),
            ),
        ), 201 );
    }

    /**
     * GET /paired/mentors/{id}/reviews — Public endpoint to list a mentor's reviews.
     */
    public function get_mentor_reviews( WP_REST_Request $request ) {
        $mentor_id = absint( $request->get_param( 'id' ) );

        $mentor = get_userdata( $mentor_id );
        if ( ! $mentor || ! in_array( 'mentor', (array) $mentor->roles, true ) ) {
            return new WP_Error( 'mentor_not_found', __( 'Mentor not found.', 'bpu' ), array( 'status' => 404 ) );
        }

        $query = new WP_Query( array(
            'post_type'      => 'mentor_review',
            'post_status'    => 'publish',
            'posts_per_page' => 100,
            'meta_query'     => array(
                array( 'key' => '_review_mentor_id', 'value' => $mentor_id, 'compare' => '=' ),
            ),
            'orderby'        => 'date',
            'order'          => 'DESC',
        ) );

        $reviews      = array();
        $total_rating = 0;

        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $pid       = get_the_ID();
                $mentee_id = (int) get_post_meta( $pid, '_review_mentee_id', true );
                $mentee    = get_userdata( $mentee_id );
                $rating    = (int) get_post_meta( $pid, '_review_rating', true );
                $total_rating += $rating;

                $reviews[] = array(
                    'id'           => $pid,
                    'mentee_name'  => $mentee ? $mentee->display_name : __( 'Anonymous', 'bpu' ),
                    'mentee_avatar'=> $mentee ? get_avatar_url( $mentee->ID, array( 'size' => 96 ) ) : '',
                    'rating'       => $rating,
                    'feedback'     => get_post_meta( $pid, '_review_feedback', true ),
                    'created_at'   => get_post_meta( $pid, '_review_created_at', true ),
                );
            }
            wp_reset_postdata();
        }

        $review_count   = count( $reviews );
        $average_rating = $review_count > 0 ? round( $total_rating / $review_count, 2 ) : 0;

        return new WP_REST_Response( array(
            'success'        => true,
            'reviews'        => $reviews,
            'review_count'   => $review_count,
            'average_rating' => $average_rating,
        ), 200 );
    }

    // ══════════════════════════════════════════════════════════════
    //  PHASE 2: IN-APP MESSAGING
    // ══════════════════════════════════════════════════════════════

    /**
     * GET /paired/messages — List conversations for the current user.
     */
    public function get_message_conversations( WP_REST_Request $request ) {
        $payload = $this->verify_jwt_bearer( $request );
        if ( ! $payload || empty( $payload['user_id'] ) ) {
            return new WP_Error( 'sso_invalid_token', __( 'Invalid or missing token.', 'bpu' ), array( 'status' => 401 ) );
        }
        $user_id = intval( $payload['user_id'] );

        // Fetch all messages where user is sender or recipient.
        $query = new WP_Query( array(
            'post_type'      => 'paired_message',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'meta_query'     => array(
                'relation' => 'OR',
                array( 'key' => '_msg_from_user_id', 'value' => $user_id, 'compare' => '=' ),
                array( 'key' => '_msg_to_user_id', 'value' => $user_id, 'compare' => '=' ),
            ),
            'orderby'        => 'date',
            'order'          => 'DESC',
        ) );

        // Group by "other user" and collect last message + unread count.
        $conversations = array();

        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $pid     = get_the_ID();
                $from_id = (int) get_post_meta( $pid, '_msg_from_user_id', true );
                $to_id   = (int) get_post_meta( $pid, '_msg_to_user_id', true );

                $other_id = ( $from_id === $user_id ) ? $to_id : $from_id;

                if ( ! isset( $conversations[ $other_id ] ) ) {
                    $other_user = get_userdata( $other_id );
                    $conversations[ $other_id ] = array(
                        'user_id'         => $other_id,
                        'display_name'    => $other_user ? $other_user->display_name : __( 'Unknown User', 'bpu' ),
                        'avatar_url'      => $other_user ? get_avatar_url( $other_id, array( 'size' => 96 ) ) : '',
                        'last_message'    => get_post_meta( $pid, '_msg_message', true ),
                        'last_message_at' => get_post_meta( $pid, '_msg_created_at', true ),
                        'unread_count'    => 0,
                    );
                }

                // Count unread messages FROM the other user (not yet read by current user).
                if ( $from_id === $other_id ) {
                    $read_at = get_post_meta( $pid, '_msg_read_at', true );
                    if ( empty( $read_at ) ) {
                        $conversations[ $other_id ]['unread_count']++;
                    }
                }
            }
            wp_reset_postdata();
        }

        return new WP_REST_Response( array(
            'success'       => true,
            'conversations' => array_values( $conversations ),
        ), 200 );
    }

    /**
     * GET /paired/messages/{user_id} — Get message thread with a specific user.
     */
    public function get_message_thread( WP_REST_Request $request ) {
        $payload = $this->verify_jwt_bearer( $request );
        if ( ! $payload || empty( $payload['user_id'] ) ) {
            return new WP_Error( 'sso_invalid_token', __( 'Invalid or missing token.', 'bpu' ), array( 'status' => 401 ) );
        }
        $current_user_id = intval( $payload['user_id'] );
        $other_user_id   = absint( $request->get_param( 'user_id' ) );

        $other_user = get_userdata( $other_user_id );
        if ( ! $other_user ) {
            return new WP_Error( 'user_not_found', __( 'User not found.', 'bpu' ), array( 'status' => 404 ) );
        }

        // Fetch messages between these two users.
        $query = new WP_Query( array(
            'post_type'      => 'paired_message',
            'post_status'    => 'publish',
            'posts_per_page' => 200,
            'meta_query'     => array(
                'relation' => 'OR',
                array(
                    'relation' => 'AND',
                    array( 'key' => '_msg_from_user_id', 'value' => $current_user_id, 'compare' => '=' ),
                    array( 'key' => '_msg_to_user_id', 'value' => $other_user_id, 'compare' => '=' ),
                ),
                array(
                    'relation' => 'AND',
                    array( 'key' => '_msg_from_user_id', 'value' => $other_user_id, 'compare' => '=' ),
                    array( 'key' => '_msg_to_user_id', 'value' => $current_user_id, 'compare' => '=' ),
                ),
            ),
            'orderby'        => 'date',
            'order'          => 'ASC',
        ) );

        $messages = array();
        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $pid     = get_the_ID();
                $from_id = (int) get_post_meta( $pid, '_msg_from_user_id', true );

                // Mark unread messages from the other user as read.
                if ( $from_id === $other_user_id ) {
                    $read_at = get_post_meta( $pid, '_msg_read_at', true );
                    if ( empty( $read_at ) ) {
                        update_post_meta( $pid, '_msg_read_at', current_time( 'mysql' ) );
                    }
                }

                $messages[] = array(
                    'id'         => $pid,
                    'from_user_id' => $from_id,
                    'to_user_id'   => (int) get_post_meta( $pid, '_msg_to_user_id', true ),
                    'message'    => get_post_meta( $pid, '_msg_message', true ),
                    'created_at' => get_post_meta( $pid, '_msg_created_at', true ),
                    'read_at'    => get_post_meta( $pid, '_msg_read_at', true ),
                );
            }
            wp_reset_postdata();
        }

        return new WP_REST_Response( array(
            'success'  => true,
            'messages' => $messages,
            'contact'  => array(
                'user_id'      => $other_user_id,
                'display_name' => $other_user->display_name,
                'avatar_url'   => get_avatar_url( $other_user_id, array( 'size' => 96 ) ),
            ),
        ), 200 );
    }

    /**
     * POST /paired/messages — Send a message to another user.
     */
    public function send_message( WP_REST_Request $request ) {
        $payload = $this->verify_jwt_bearer( $request );
        if ( ! $payload || empty( $payload['user_id'] ) ) {
            return new WP_Error( 'sso_invalid_token', __( 'Invalid or missing token.', 'bpu' ), array( 'status' => 401 ) );
        }
        $from_user_id = intval( $payload['user_id'] );

        $body       = $request->get_json_params();
        $to_user_id = isset( $body['to_user_id'] ) ? absint( $body['to_user_id'] ) : 0;
        $message    = isset( $body['message'] ) ? sanitize_textarea_field( $body['message'] ) : '';

        if ( empty( $to_user_id ) ) {
            return new WP_Error( 'missing_recipient', __( 'Recipient user ID is required.', 'bpu' ), array( 'status' => 400 ) );
        }

        if ( empty( $message ) ) {
            return new WP_Error( 'missing_message', __( 'Message text is required.', 'bpu' ), array( 'status' => 400 ) );
        }

        if ( $from_user_id === $to_user_id ) {
            return new WP_Error( 'self_message', __( 'You cannot send a message to yourself.', 'bpu' ), array( 'status' => 400 ) );
        }

        $to_user = get_userdata( $to_user_id );
        if ( ! $to_user ) {
            return new WP_Error( 'user_not_found', __( 'Recipient user not found.', 'bpu' ), array( 'status' => 404 ) );
        }

        // Validate booking relationship: must have at least one booking where these two are mentor/mentee.
        $relationship = new WP_Query( array(
            'post_type'      => 'mentorship_booking',
            'post_status'    => array( 'publish', 'pending', 'draft' ),
            'posts_per_page' => 1,
            'meta_query'     => array(
                'relation' => 'OR',
                array(
                    'relation' => 'AND',
                    array( 'key' => '_bpu_booking_mentee_id', 'value' => $from_user_id, 'compare' => '=' ),
                    array( 'key' => '_bpu_booking_mentor_id', 'value' => $to_user_id, 'compare' => '=' ),
                ),
                array(
                    'relation' => 'AND',
                    array( 'key' => '_bpu_booking_mentee_id', 'value' => $to_user_id, 'compare' => '=' ),
                    array( 'key' => '_bpu_booking_mentor_id', 'value' => $from_user_id, 'compare' => '=' ),
                ),
            ),
        ) );

        if ( ! $relationship->have_posts() ) {
            return new WP_Error( 'no_booking_relationship', __( 'You can only message users with whom you have a booking relationship.', 'bpu' ), array( 'status' => 403 ) );
        }

        $from_user = get_userdata( $from_user_id );
        $title     = sprintf( 'Message from %s to %s', $from_user ? $from_user->display_name : $from_user_id, $to_user->display_name );

        $post_id = wp_insert_post( array(
            'post_type'   => 'paired_message',
            'post_title'  => sanitize_text_field( $title ),
            'post_status' => 'publish',
            'post_author' => $from_user_id,
        ), true );

        if ( is_wp_error( $post_id ) ) {
            return $post_id;
        }

        $now = current_time( 'mysql' );
        update_post_meta( $post_id, '_msg_from_user_id', $from_user_id );
        update_post_meta( $post_id, '_msg_to_user_id', $to_user_id );
        update_post_meta( $post_id, '_msg_message', $message );
        update_post_meta( $post_id, '_msg_created_at', $now );

        // Create notification for the recipient.
        $this->create_notification(
            $to_user_id,
            'new_message',
            __( 'New Message', 'bpu' ),
            sprintf( '%s sent you a message.', $from_user ? $from_user->display_name : 'Someone' ),
            '/dashboard/messages/' . $from_user_id
        );

        // Send email notification to the recipient
        $sender_name   = $from_user ? $from_user->display_name : 'Someone';
        $email_heading = 'New message from ' . $sender_name;
        $email_body    = '<p style="color:#333;font-size:15px;line-height:1.6;"><strong>' . esc_html( $sender_name ) . '</strong> sent you a message on PAIRED.</p>';
        $email_body   .= '<p style="margin-top:24px;"><a href="https://pairedbybpu.uk/paired/messages" style="display:inline-block;background:#7c3aed;color:#fff;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:600;">Read message</a></p>';

        wp_mail(
            $to_user->user_email,
            sprintf( 'New message from %s — PAIRED by BPU', $sender_name ),
            $this->build_email_html( $email_heading, $email_body ),
            array( 'Content-Type: text/html; charset=UTF-8' )
        );

        return new WP_REST_Response( array(
            'success' => true,
            'message' => array(
                'id'           => $post_id,
                'from_user_id' => $from_user_id,
                'to_user_id'   => $to_user_id,
                'message'      => $message,
                'created_at'   => $now,
                'read_at'      => null,
            ),
        ), 201 );
    }

    // ══════════════════════════════════════════════════════════════
    //  PHASE 2: IN-APP NOTIFICATIONS
    // ══════════════════════════════════════════════════════════════

    /**
     * GET /paired/notifications — List notifications for the current user.
     */
    public function get_notifications( WP_REST_Request $request ) {
        $payload = $this->verify_jwt_bearer( $request );
        if ( ! $payload || empty( $payload['user_id'] ) ) {
            return new WP_Error( 'sso_invalid_token', __( 'Invalid or missing token.', 'bpu' ), array( 'status' => 401 ) );
        }
        $user_id  = intval( $payload['user_id'] );
        $per_page = min( 100, max( 1, absint( $request->get_param( 'per_page' ) ?: 20 ) ) );

        $query = new WP_Query( array(
            'post_type'      => 'paired_notification',
            'post_status'    => 'publish',
            'posts_per_page' => $per_page,
            'meta_query'     => array(
                array( 'key' => '_notif_user_id', 'value' => $user_id, 'compare' => '=' ),
            ),
            'orderby'        => 'date',
            'order'          => 'DESC',
        ) );

        $notifications = array();
        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $pid = get_the_ID();
                $notifications[] = array(
                    'id'         => $pid,
                    'type'       => get_post_meta( $pid, '_notif_type', true ),
                    'title'      => get_post_meta( $pid, '_notif_title', true ),
                    'message'    => get_post_meta( $pid, '_notif_message', true ),
                    'link'       => get_post_meta( $pid, '_notif_link', true ),
                    'read_at'    => get_post_meta( $pid, '_notif_read_at', true ) ?: null,
                    'created_at' => get_post_meta( $pid, '_notif_created_at', true ),
                );
            }
            wp_reset_postdata();
        }

        // Count total unread notifications.
        $unread_query = new WP_Query( array(
            'post_type'      => 'paired_notification',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'meta_query'     => array(
                'relation' => 'AND',
                array( 'key' => '_notif_user_id', 'value' => $user_id, 'compare' => '=' ),
                array(
                    'relation' => 'OR',
                    array( 'key' => '_notif_read_at', 'compare' => 'NOT EXISTS' ),
                    array( 'key' => '_notif_read_at', 'value' => '', 'compare' => '=' ),
                ),
            ),
        ) );

        return new WP_REST_Response( array(
            'success'       => true,
            'notifications' => $notifications,
            'unread_count'  => $unread_query->found_posts,
        ), 200 );
    }

    /**
     * PUT /paired/notifications/{id}/read — Mark a single notification as read.
     */
    public function mark_notification_read( WP_REST_Request $request ) {
        $payload = $this->verify_jwt_bearer( $request );
        if ( ! $payload || empty( $payload['user_id'] ) ) {
            return new WP_Error( 'sso_invalid_token', __( 'Invalid or missing token.', 'bpu' ), array( 'status' => 401 ) );
        }
        $user_id         = intval( $payload['user_id'] );
        $notification_id = absint( $request->get_param( 'id' ) );

        $post = get_post( $notification_id );
        if ( ! $post || 'paired_notification' !== $post->post_type ) {
            return new WP_Error( 'notification_not_found', __( 'Notification not found.', 'bpu' ), array( 'status' => 404 ) );
        }

        // Ensure the notification belongs to the current user.
        $notif_user_id = (int) get_post_meta( $notification_id, '_notif_user_id', true );
        if ( $notif_user_id !== $user_id ) {
            return new WP_Error( 'not_your_notification', __( 'You do not have permission to modify this notification.', 'bpu' ), array( 'status' => 403 ) );
        }

        update_post_meta( $notification_id, '_notif_read_at', current_time( 'mysql' ) );

        return new WP_REST_Response( array( 'success' => true ), 200 );
    }

    /**
     * POST /paired/notifications/read-all — Mark all notifications as read for the current user.
     */
    public function mark_all_notifications_read( WP_REST_Request $request ) {
        $payload = $this->verify_jwt_bearer( $request );
        if ( ! $payload || empty( $payload['user_id'] ) ) {
            return new WP_Error( 'sso_invalid_token', __( 'Invalid or missing token.', 'bpu' ), array( 'status' => 401 ) );
        }
        $user_id = intval( $payload['user_id'] );

        $query = new WP_Query( array(
            'post_type'      => 'paired_notification',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'meta_query'     => array(
                'relation' => 'AND',
                array( 'key' => '_notif_user_id', 'value' => $user_id, 'compare' => '=' ),
                array(
                    'relation' => 'OR',
                    array( 'key' => '_notif_read_at', 'compare' => 'NOT EXISTS' ),
                    array( 'key' => '_notif_read_at', 'value' => '', 'compare' => '=' ),
                ),
            ),
        ) );

        $now   = current_time( 'mysql' );
        $count = 0;
        if ( $query->have_posts() ) {
            foreach ( $query->posts as $pid ) {
                update_post_meta( $pid, '_notif_read_at', $now );
                $count++;
            }
        }

        return new WP_REST_Response( array( 'success' => true, 'marked_read' => $count ), 200 );
    }

    // ══════════════════════════════════════════════════════════════
    //  PHASE 2: FAVOURITE MENTORS
    // ══════════════════════════════════════════════════════════════

    /**
     * GET /paired/favourites — List the current user's favourite mentor IDs.
     */
    public function get_favourites( WP_REST_Request $request ) {
        $payload = $this->verify_jwt_bearer( $request );
        if ( ! $payload || empty( $payload['user_id'] ) ) {
            return new WP_Error( 'sso_invalid_token', __( 'Invalid or missing token.', 'bpu' ), array( 'status' => 401 ) );
        }
        $user_id = intval( $payload['user_id'] );

        $favourites = get_user_meta( $user_id, '_paired_favourites', true );
        if ( ! is_array( $favourites ) ) {
            $favourites = array();
        }

        // Return enriched data: mentor IDs with basic info.
        $mentors = array();
        foreach ( $favourites as $mentor_id ) {
            $mentor = get_userdata( $mentor_id );
            if ( $mentor && in_array( 'mentor', (array) $mentor->roles, true ) ) {
                $photo_url = get_user_meta( $mentor->ID, '_paired_photo_url', true );
                $mentors[] = array(
                    'id'           => $mentor->ID,
                    'mentor_id'    => $mentor->ID,
                    'display_name' => $mentor->display_name,
                    'avatar_url'   => $photo_url ?: get_avatar_url( $mentor->ID, array( 'size' => 200 ) ),
                    'job_title'    => get_user_meta( $mentor->ID, 'current_role', true ),
                    'company'      => get_user_meta( $mentor->ID, 'company', true ),
                    'industry'     => get_user_meta( $mentor->ID, 'industry', true ),
                );
            }
        }

        return new WP_REST_Response( array(
            'success'    => true,
            'favourites' => $mentors,
        ), 200 );
    }

    /**
     * POST /paired/favourites — Add a mentor to favourites.
     */
    public function add_favourite( WP_REST_Request $request ) {
        $payload = $this->verify_jwt_bearer( $request );
        if ( ! $payload || empty( $payload['user_id'] ) ) {
            return new WP_Error( 'sso_invalid_token', __( 'Invalid or missing token.', 'bpu' ), array( 'status' => 401 ) );
        }
        $user_id = intval( $payload['user_id'] );

        $body      = $request->get_json_params();
        $mentor_id = isset( $body['mentor_id'] ) ? absint( $body['mentor_id'] ) : 0;

        if ( empty( $mentor_id ) ) {
            return new WP_Error( 'missing_mentor_id', __( 'Mentor ID is required.', 'bpu' ), array( 'status' => 400 ) );
        }

        $mentor = get_userdata( $mentor_id );
        if ( ! $mentor || ! in_array( 'mentor', (array) $mentor->roles, true ) ) {
            return new WP_Error( 'mentor_not_found', __( 'Mentor not found or user is not a mentor.', 'bpu' ), array( 'status' => 404 ) );
        }

        $favourites = get_user_meta( $user_id, '_paired_favourites', true );
        if ( ! is_array( $favourites ) ) {
            $favourites = array();
        }

        if ( in_array( $mentor_id, $favourites, true ) ) {
            return new WP_REST_Response( array( 'success' => true, 'message' => __( 'Mentor is already in your favourites.', 'bpu' ) ), 200 );
        }

        $favourites[] = $mentor_id;
        update_user_meta( $user_id, '_paired_favourites', $favourites );

        return new WP_REST_Response( array( 'success' => true, 'favourites' => $favourites ), 201 );
    }

    /**
     * DELETE /paired/favourites/{mentor_id} — Remove a mentor from favourites.
     */
    public function remove_favourite( WP_REST_Request $request ) {
        $payload = $this->verify_jwt_bearer( $request );
        if ( ! $payload || empty( $payload['user_id'] ) ) {
            return new WP_Error( 'sso_invalid_token', __( 'Invalid or missing token.', 'bpu' ), array( 'status' => 401 ) );
        }
        $user_id   = intval( $payload['user_id'] );
        $mentor_id = absint( $request->get_param( 'mentor_id' ) );

        $favourites = get_user_meta( $user_id, '_paired_favourites', true );
        if ( ! is_array( $favourites ) ) {
            $favourites = array();
        }

        $key = array_search( $mentor_id, $favourites, true );
        if ( false === $key ) {
            return new WP_Error( 'not_in_favourites', __( 'Mentor is not in your favourites.', 'bpu' ), array( 'status' => 404 ) );
        }

        array_splice( $favourites, $key, 1 );
        update_user_meta( $user_id, '_paired_favourites', $favourites );

        return new WP_REST_Response( array( 'success' => true, 'favourites' => $favourites ), 200 );
    }

    // ══════════════════════════════════════════════════════════════
    //  PHASE 2: MENTEE PROFILE
    // ══════════════════════════════════════════════════════════════

    /**
     * GET /paired/mentee/profile — Get the current user's mentee profile.
     */
    public function get_mentee_profile( WP_REST_Request $request ) {
        $payload = $this->verify_jwt_bearer( $request );
        if ( ! $payload || empty( $payload['user_id'] ) ) {
            return new WP_Error( 'sso_invalid_token', __( 'Invalid or missing token.', 'bpu' ), array( 'status' => 401 ) );
        }
        $user_id = intval( $payload['user_id'] );
        $user    = get_userdata( $user_id );

        if ( ! $user ) {
            return new WP_Error( 'user_not_found', __( 'User not found.', 'bpu' ), array( 'status' => 404 ) );
        }

        $skills = get_user_meta( $user_id, '_paired_skills_to_develop', true );
        if ( ! is_array( $skills ) ) {
            $skills = array();
        }

        return new WP_REST_Response( array(
            'success' => true,
            'profile' => array(
                'user_id'                 => $user_id,
                'display_name'            => $user->display_name,
                'email'                   => $user->user_email,
                'first_name'              => $user->first_name ?: get_user_meta( $user_id, 'first_name', true ) ?: '',
                'last_name'               => $user->last_name ?: get_user_meta( $user_id, 'last_name', true ) ?: '',
                'phone_number'            => get_user_meta( $user_id, 'phone_number', true ) ?: '',
                'gender'                  => get_user_meta( $user_id, 'what_is_your_gender', true ) ?: get_user_meta( $user_id, 'gender', true ) ?: '',
                'country'                 => get_user_meta( $user_id, 'country_location', true ) ?: '',
                'city'                    => get_user_meta( $user_id, 'location_city', true ) ?: '',
                'employment_status'       => get_user_meta( $user_id, 'current_employment_status', true ) ?: get_user_meta( $user_id, 'employment_status', true ) ?: '',
                'linkedin_profile'        => get_user_meta( $user_id, 'linkedin_profile', true ) ?: '',
                'years_of_experience'     => get_user_meta( $user_id, 'years_of_experience', true ) ?: '',
                'mentorship_availability' => get_user_meta( $user_id, 'mentorship_availability', true ) ?: '',
                'bp_network'              => get_user_meta( $user_id, 'bp_network', true ) ?: '',
                'avatar_url'              => get_user_meta( $user_id, '_paired_photo_url', true ) ?: get_avatar_url( $user_id, array( 'size' => 128 ) ),
                'career_goals'            => get_user_meta( $user_id, '_paired_career_goals', true ) ?: '',
                'skills_to_develop'       => $skills,
                'industry'                => get_user_meta( $user_id, '_paired_industry', true ) ?: '',
                'bio'                     => get_user_meta( $user_id, '_paired_bio', true ) ?: '',
            ),
        ), 200 );
    }

    /**
     * PUT /paired/mentee/profile — Update the current user's mentee profile.
     */
    public function update_mentee_profile( WP_REST_Request $request ) {
        $payload = $this->verify_jwt_bearer( $request );
        if ( ! $payload || empty( $payload['user_id'] ) ) {
            return new WP_Error( 'sso_invalid_token', __( 'Invalid or missing token.', 'bpu' ), array( 'status' => 401 ) );
        }
        $user_id = intval( $payload['user_id'] );

        $body = $request->get_json_params();

        // Simple string fields mapped to their meta keys.
        $simple_fields = array(
            'phone_number'            => 'phone_number',
            'gender'                  => 'what_is_your_gender',
            'country'                 => 'country_location',
            'city'                    => 'location_city',
            'employment_status'       => 'current_employment_status',
            'linkedin_profile'        => 'linkedin_profile',
            'years_of_experience'     => 'years_of_experience',
            'mentorship_availability' => 'mentorship_availability',
            'bp_network'              => 'bp_network',
            'industry'                => '_paired_industry',
        );
        foreach ( $simple_fields as $body_key => $meta_key ) {
            if ( array_key_exists( $body_key, $body ) ) {
                update_user_meta( $user_id, $meta_key, sanitize_text_field( (string) $body[ $body_key ] ) );
            }
        }

        // Name fields — update WP core fields and display_name.
        $first = isset( $body['first_name'] ) ? sanitize_text_field( (string) $body['first_name'] ) : null;
        $last  = isset( $body['last_name'] )  ? sanitize_text_field( (string) $body['last_name'] )  : null;
        if ( $first !== null || $last !== null ) {
            $user_obj  = get_userdata( $user_id );
            $new_first = $first ?? $user_obj->first_name;
            $new_last  = $last  ?? $user_obj->last_name;
            wp_update_user( array(
                'ID'           => $user_id,
                'first_name'   => $new_first,
                'last_name'    => $new_last,
                'display_name' => trim( "$new_first $new_last" ) ?: $user_obj->user_login,
            ) );
        }

        if ( isset( $body['career_goals'] ) ) {
            update_user_meta( $user_id, '_paired_career_goals', sanitize_textarea_field( $body['career_goals'] ) );
        }

        if ( isset( $body['bio'] ) ) {
            update_user_meta( $user_id, '_paired_bio', sanitize_textarea_field( $body['bio'] ) );
        }

        if ( isset( $body['skills_to_develop'] ) ) {
            $skills = array();
            if ( is_array( $body['skills_to_develop'] ) ) {
                foreach ( $body['skills_to_develop'] as $skill ) {
                    $skills[] = sanitize_text_field( $skill );
                }
            }
            update_user_meta( $user_id, '_paired_skills_to_develop', $skills );
        }

        // Return the updated profile (re-read from DB).
        $updated_skills = get_user_meta( $user_id, '_paired_skills_to_develop', true );
        if ( ! is_array( $updated_skills ) ) {
            $updated_skills = array();
        }
        $updated_user = get_userdata( $user_id );

        return new WP_REST_Response( array(
            'success' => true,
            'profile' => array(
                'user_id'                 => $user_id,
                'display_name'            => $updated_user ? $updated_user->display_name : '',
                'email'                   => $updated_user ? $updated_user->user_email : '',
                'first_name'              => $updated_user ? $updated_user->first_name : '',
                'last_name'               => $updated_user ? $updated_user->last_name : '',
                'phone_number'            => get_user_meta( $user_id, 'phone_number', true ) ?: '',
                'gender'                  => get_user_meta( $user_id, 'what_is_your_gender', true ) ?: get_user_meta( $user_id, 'gender', true ) ?: '',
                'country'                 => get_user_meta( $user_id, 'country_location', true ) ?: '',
                'city'                    => get_user_meta( $user_id, 'location_city', true ) ?: '',
                'employment_status'       => get_user_meta( $user_id, 'current_employment_status', true ) ?: get_user_meta( $user_id, 'employment_status', true ) ?: '',
                'linkedin_profile'        => get_user_meta( $user_id, 'linkedin_profile', true ) ?: '',
                'years_of_experience'     => get_user_meta( $user_id, 'years_of_experience', true ) ?: '',
                'mentorship_availability' => get_user_meta( $user_id, 'mentorship_availability', true ) ?: '',
                'bp_network'              => get_user_meta( $user_id, 'bp_network', true ) ?: '',
                'avatar_url'              => get_user_meta( $user_id, '_paired_photo_url', true ) ?: get_avatar_url( $user_id, array( 'size' => 128 ) ),
                'career_goals'            => get_user_meta( $user_id, '_paired_career_goals', true ) ?: '',
                'skills_to_develop'       => $updated_skills,
                'industry'                => get_user_meta( $user_id, '_paired_industry', true ) ?: '',
                'bio'                     => get_user_meta( $user_id, '_paired_bio', true ) ?: '',
            ),
        ), 200 );
    }

    /**
     * POST /paired/account/change-password — Change the authenticated user's password.
     */
    public function change_password( WP_REST_Request $request ) {
        $payload = $this->verify_jwt_bearer( $request );
        if ( ! $payload || empty( $payload['user_id'] ) ) {
            return new WP_Error( 'sso_invalid_token', __( 'Invalid or missing token.', 'bpu' ), array( 'status' => 401 ) );
        }

        $user_id = intval( $payload['user_id'] );
        $user    = get_userdata( $user_id );
        if ( ! $user ) {
            return new WP_Error( 'user_not_found', __( 'User not found.', 'bpu' ), array( 'status' => 404 ) );
        }

        $body = $request->get_json_params();
        if ( ! is_array( $body ) ) {
            $body = array();
        }
        $current_password = $body['current_password'] ?? '';
        $new_password     = $body['new_password'] ?? '';

        if ( empty( $current_password ) || empty( $new_password ) ) {
            return new WP_Error( 'missing_fields', __( 'Both current and new password are required.', 'bpu' ), array( 'status' => 400 ) );
        }

        if ( strlen( $new_password ) < 8 ) {
            return new WP_Error( 'password_too_short', __( 'New password must be at least 8 characters.', 'bpu' ), array( 'status' => 400 ) );
        }

        if ( ! wp_check_password( $current_password, $user->user_pass, $user_id ) ) {
            return new WP_Error( 'wrong_password', __( 'Current password is incorrect.', 'bpu' ), array( 'status' => 403 ) );
        }

        wp_set_password( $new_password, $user_id );

        return new WP_REST_Response( array( 'success' => true ), 200 );
    }

    // ══════════════════════════════════════════════════════════════
    //  PHASE 2: ADMIN MENTOR MANAGEMENT
    // ══════════════════════════════════════════════════════════════

    /**
     * GET /paired/admin/mentors — List all mentors with stats. Admin only.
     */
    public function admin_list_mentors( WP_REST_Request $request ) {
        $search   = sanitize_text_field( $request->get_param( 'search' ) ?: '' );
        $page     = max( 1, absint( $request->get_param( 'page' ) ?: 1 ) );
        $per_page = min( 100, max( 1, absint( $request->get_param( 'per_page' ) ?: 20 ) ) );

        $user_args = array(
            'role'   => 'mentor',
            'number' => $per_page,
            'paged'  => $page,
            'orderby'=> 'registered',
            'order'  => 'DESC',
        );

        if ( ! empty( $search ) ) {
            $user_args['search']         = '*' . $search . '*';
            $user_args['search_columns'] = array( 'user_login', 'user_email', 'display_name' );
        }

        $user_query = new WP_User_Query( $user_args );
        $mentors    = array();

        foreach ( $user_query->get_results() as $user ) {
            // Booking count.
            $bookings_query = new WP_Query( array(
                'post_type'      => 'mentorship_booking',
                'post_status'    => array( 'publish', 'pending', 'draft' ),
                'posts_per_page' => -1,
                'fields'         => 'ids',
                'meta_query'     => array(
                    array( 'key' => '_bpu_booking_mentor_id', 'value' => $user->ID, 'compare' => '=' ),
                ),
            ) );
            $booking_count = $bookings_query->found_posts;

            // Unique mentee count.
            $mentee_ids = array();
            if ( $bookings_query->have_posts() ) {
                foreach ( $bookings_query->posts as $bid ) {
                    $mid = (int) get_post_meta( $bid, '_bpu_booking_mentee_id', true );
                    if ( $mid ) {
                        $mentee_ids[ $mid ] = true;
                    }
                }
            }

            // Average rating.
            $reviews_query = new WP_Query( array(
                'post_type'      => 'mentor_review',
                'post_status'    => 'publish',
                'posts_per_page' => -1,
                'fields'         => 'ids',
                'meta_query'     => array(
                    array( 'key' => '_review_mentor_id', 'value' => $user->ID, 'compare' => '=' ),
                ),
            ) );
            $total_rating  = 0;
            $review_count  = 0;
            if ( $reviews_query->have_posts() ) {
                foreach ( $reviews_query->posts as $rid ) {
                    $total_rating += (int) get_post_meta( $rid, '_review_rating', true );
                    $review_count++;
                }
            }
            $avg_rating = $review_count > 0 ? round( $total_rating / $review_count, 2 ) : 0;

            $mentors[] = array(
                'user_id'        => $user->ID,
                'display_name'   => $user->display_name,
                'email'          => $user->user_email,
                'avatar_url'     => get_avatar_url( $user->ID, array( 'size' => 96 ) ),
                'registered'     => $user->user_registered,
                'booking_count'  => $booking_count,
                'mentee_count'   => count( $mentee_ids ),
                'average_rating' => $avg_rating,
                'review_count'   => $review_count,
            );
        }

        return new WP_REST_Response( array(
            'success' => true,
            'mentors' => $mentors,
            'total'   => $user_query->get_total(),
            'page'    => $page,
            'per_page'=> $per_page,
        ), 200 );
    }

    /**
     * PUT /paired/admin/mentors/{id} — Admin updates a mentor's profile.
     */
    public function admin_update_mentor( WP_REST_Request $request ) {
        $mentor_id = absint( $request->get_param( 'id' ) );
        $mentor    = get_userdata( $mentor_id );

        if ( ! $mentor || ! in_array( 'mentor', (array) $mentor->roles, true ) ) {
            return new WP_Error( 'mentor_not_found', __( 'Mentor not found.', 'bpu' ), array( 'status' => 404 ) );
        }

        $body = $request->get_json_params();

        // Update display name.
        if ( isset( $body['display_name'] ) ) {
            wp_update_user( array(
                'ID'           => $mentor_id,
                'display_name' => sanitize_text_field( $body['display_name'] ),
            ) );
        }

        // Update common mentor meta fields.
        $meta_fields = array(
            'first_name'       => 'first_name',
            'last_name'        => 'last_name',
            'user_bio'         => 'user_bio',
            'industry'         => 'industry',
            'linkedin_profile' => 'linkedin_profile',
            'phone_number'     => 'phone_number',
        );

        foreach ( $meta_fields as $param_key => $meta_key ) {
            if ( isset( $body[ $param_key ] ) ) {
                update_user_meta( $mentor_id, $meta_key, sanitize_text_field( $body[ $param_key ] ) );
            }
        }

        // Update paired-specific meta.
        $paired_fields = array(
            'headline'         => '_paired_headline',
            'bio'              => '_paired_bio',
            'years_experience' => '_paired_years_experience',
            'photo_url'        => '_paired_photo_url',
        );

        foreach ( $paired_fields as $param_key => $meta_key ) {
            if ( isset( $body[ $param_key ] ) ) {
                if ( $param_key === 'photo_url' ) {
                    update_user_meta( $mentor_id, $meta_key, esc_url_raw( $body[ $param_key ] ) );
                } else {
                    update_user_meta( $mentor_id, $meta_key, sanitize_text_field( $body[ $param_key ] ) );
                }
            }
        }

        if ( isset( $body['skills'] ) && is_array( $body['skills'] ) ) {
            $skills = array_map( 'sanitize_text_field', $body['skills'] );
            update_user_meta( $mentor_id, '_paired_skills', $skills );
        }

        $updated_mentor = get_userdata( $mentor_id );

        return new WP_REST_Response( array(
            'success' => true,
            'mentor'  => array(
                'user_id'      => $mentor_id,
                'display_name' => $updated_mentor->display_name,
                'email'        => $updated_mentor->user_email,
                'avatar_url'   => get_avatar_url( $mentor_id, array( 'size' => 96 ) ),
            ),
        ), 200 );
    }

    /**
     * POST /paired/admin/mentors/{id}/deactivate — Remove mentor role. Admin only.
     */
    public function admin_deactivate_mentor( WP_REST_Request $request ) {
        $mentor_id = absint( $request->get_param( 'id' ) );
        $user      = get_userdata( $mentor_id );

        if ( ! $user ) {
            return new WP_Error( 'user_not_found', __( 'User not found.', 'bpu' ), array( 'status' => 404 ) );
        }

        if ( ! in_array( 'mentor', (array) $user->roles, true ) ) {
            return new WP_Error( 'not_a_mentor', __( 'This user does not have the mentor role.', 'bpu' ), array( 'status' => 400 ) );
        }

        $user->remove_role( 'mentor' );

        // Ensure the user still has at least the subscriber role.
        if ( empty( $user->roles ) ) {
            $user->add_role( 'subscriber' );
        }

        return new WP_REST_Response( array(
            'success' => true,
            'message' => sprintf( __( 'Mentor role removed from user %d.', 'bpu' ), $mentor_id ),
            'roles'   => (array) $user->roles,
        ), 200 );
    }

    /**
     * POST /paired/admin/mentors/{id}/activate — Add mentor role back. Admin only.
     */
    public function admin_activate_mentor( WP_REST_Request $request ) {
        $mentor_id = absint( $request->get_param( 'id' ) );
        $user      = get_userdata( $mentor_id );

        if ( ! $user ) {
            return new WP_Error( 'user_not_found', __( 'User not found.', 'bpu' ), array( 'status' => 404 ) );
        }

        if ( in_array( 'mentor', (array) $user->roles, true ) ) {
            return new WP_REST_Response( array(
                'success' => true,
                'message' => __( 'User already has the mentor role.', 'bpu' ),
                'roles'   => (array) $user->roles,
            ), 200 );
        }

        $user->add_role( 'mentor' );

        return new WP_REST_Response( array(
            'success' => true,
            'message' => sprintf( __( 'Mentor role added to user %d.', 'bpu' ), $mentor_id ),
            'roles'   => (array) $user->roles,
        ), 200 );
    }

    // ══════════════════════════════════════════════════════════════
    //  PHASE 2: ADMIN PLATFORM ANALYTICS
    // ══════════════════════════════════════════════════════════════

    /**
     * GET /paired/admin/stats — Return platform-wide analytics. Admin only.
     */
    public function admin_get_platform_stats( WP_REST_Request $request ) {
        // Total mentors.
        $mentor_query  = new WP_User_Query( array( 'role' => 'mentor', 'count_total' => true, 'number' => 0 ) );
        $total_mentors = $mentor_query->get_total();

        // Total mentees (users who have at least one booking as mentee).
        global $wpdb;
        $total_mentees = (int) $wpdb->get_var(
            "SELECT COUNT(DISTINCT meta_value) FROM {$wpdb->postmeta}
             WHERE meta_key = '_bpu_booking_mentee_id'"
        );

        // Total bookings.
        $all_bookings = new WP_Query( array(
            'post_type'      => 'mentorship_booking',
            'post_status'    => array( 'publish', 'pending', 'draft' ),
            'posts_per_page' => -1,
            'fields'         => 'ids',
        ) );
        $total_bookings = $all_bookings->found_posts;

        // Bookings by status.
        $status_counts = array(
            'pending'   => 0,
            'confirmed' => 0,
            'completed' => 0,
            'cancelled' => 0,
        );

        if ( $all_bookings->have_posts() ) {
            foreach ( $all_bookings->posts as $bid ) {
                $s = get_post_meta( $bid, '_bpu_booking_status', true ) ?: 'pending';
                if ( isset( $status_counts[ $s ] ) ) {
                    $status_counts[ $s ]++;
                }
            }
        }

        // Completion rate.
        $completion_rate = $total_bookings > 0
            ? round( ( $status_counts['completed'] / $total_bookings ) * 100, 1 )
            : 0;

        // Bookings this month.
        $first_of_month = gmdate( 'Y-m-01' );
        $bookings_month = new WP_Query( array(
            'post_type'      => 'mentorship_booking',
            'post_status'    => array( 'publish', 'pending', 'draft' ),
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'date_query'     => array(
                array( 'after' => $first_of_month, 'inclusive' => true ),
            ),
        ) );
        $bookings_this_month = $bookings_month->found_posts;

        // New mentors this month.
        $new_mentors_query = new WP_User_Query( array(
            'role'       => 'mentor',
            'count_total'=> true,
            'number'     => 0,
            'date_query' => array(
                array( 'after' => $first_of_month, 'inclusive' => true ),
            ),
        ) );
        $new_mentors_this_month = $new_mentors_query->get_total();

        // Top mentors by booking count (top 10).
        $mentor_booking_counts = array();
        if ( $all_bookings->have_posts() ) {
            foreach ( $all_bookings->posts as $bid ) {
                $mid = (int) get_post_meta( $bid, '_bpu_booking_mentor_id', true );
                if ( $mid ) {
                    if ( ! isset( $mentor_booking_counts[ $mid ] ) ) {
                        $mentor_booking_counts[ $mid ] = 0;
                    }
                    $mentor_booking_counts[ $mid ]++;
                }
            }
        }
        arsort( $mentor_booking_counts );
        $top_mentor_ids = array_slice( $mentor_booking_counts, 0, 10, true );

        $top_mentors = array();
        foreach ( $top_mentor_ids as $mid => $count ) {
            $m = get_userdata( $mid );
            $top_mentors[] = array(
                'user_id'       => $mid,
                'display_name'  => $m ? $m->display_name : 'Unknown',
                'avatar_url'    => get_avatar_url( $mid, array( 'size' => 96 ) ),
                'booking_count' => $count,
            );
        }

        // Average platform rating.
        $all_reviews = new WP_Query( array(
            'post_type'      => 'mentor_review',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'fields'         => 'ids',
        ) );
        $platform_total_rating = 0;
        $platform_review_count = 0;
        if ( $all_reviews->have_posts() ) {
            foreach ( $all_reviews->posts as $rid ) {
                $platform_total_rating += (int) get_post_meta( $rid, '_review_rating', true );
                $platform_review_count++;
            }
        }
        $average_rating_platform = $platform_review_count > 0
            ? round( $platform_total_rating / $platform_review_count, 2 )
            : 0;

        return new WP_REST_Response( array(
            'success'                 => true,
            'total_mentors'           => $total_mentors,
            'total_mentees'           => $total_mentees,
            'total_bookings'          => $total_bookings,
            'bookings_by_status'      => $status_counts,
            'completion_rate'         => $completion_rate,
            'bookings_this_month'     => $bookings_this_month,
            'new_mentors_this_month'  => $new_mentors_this_month,
            'top_mentors'             => $top_mentors,
            'average_rating_platform' => $average_rating_platform,
        ), 200 );
    }

    // ═══════════════════════════════════════════════════════════════
    //  PHASE 3: STRIPE PAID SESSIONS
    // ═══════════════════════════════════════════════════════════════

    private function get_stripe_secret_key() {
        if ( defined( 'PAIRED_STRIPE_SECRET_KEY' ) ) {
            return PAIRED_STRIPE_SECRET_KEY;
        }
        return get_option( '_paired_stripe_secret_key', '' );
    }

    private function get_stripe_webhook_secret() {
        if ( defined( 'PAIRED_STRIPE_WEBHOOK_SECRET' ) ) {
            return PAIRED_STRIPE_WEBHOOK_SECRET;
        }
        return get_option( '_paired_stripe_webhook_secret', '' );
    }

    private function stripe_api( $endpoint, $body = array(), $method = 'POST' ) {
        $secret_key = $this->get_stripe_secret_key();
        if ( empty( $secret_key ) ) {
            return new WP_Error( 'stripe_not_configured', 'Stripe is not configured.', array( 'status' => 503 ) );
        }

        $args = array(
            'method'  => $method,
            'headers' => array(
                'Authorization' => 'Bearer ' . $secret_key,
                'Content-Type'  => 'application/x-www-form-urlencoded',
            ),
            'timeout' => 30,
        );

        if ( ! empty( $body ) && in_array( $method, array( 'POST', 'PUT' ), true ) ) {
            $args['body'] = $body;
        }

        $response = wp_remote_request( 'https://api.stripe.com/v1/' . $endpoint, $args );
        if ( is_wp_error( $response ) ) {
            return $response;
        }

        return json_decode( wp_remote_retrieve_body( $response ), true );
    }

    public function stripe_create_checkout( WP_REST_Request $request ) {
        $payload = $this->verify_jwt_bearer( $request );
        if ( ! $payload || empty( $payload['user_id'] ) ) {
            return new WP_Error( 'sso_invalid_token', 'Invalid or missing token.', array( 'status' => 401 ) );
        }

        $user_id    = intval( $payload['user_id'] );
        $booking_id = absint( $request->get_param( 'booking_id' ) );

        $post = get_post( $booking_id );
        if ( ! $post || 'mentorship_booking' !== $post->post_type ) {
            return new WP_Error( 'booking_not_found', 'Booking not found.', array( 'status' => 404 ) );
        }

        $mentee_id = (int) get_post_meta( $booking_id, '_bpu_booking_mentee_id', true );
        if ( $user_id !== $mentee_id ) {
            return new WP_Error( 'forbidden', 'Only the mentee can pay for this booking.', array( 'status' => 403 ) );
        }

        $session_id = (int) get_post_meta( $booking_id, '_bpu_booking_session_id', true );
        $price = 0;
        if ( $session_id ) {
            $price = (float) get_post_meta( $session_id, '_session_price', true );
        }

        if ( $price <= 0 ) {
            return new WP_Error( 'free_session', 'This session is free — no payment required.', array( 'status' => 400 ) );
        }

        $already_paid = get_post_meta( $booking_id, '_bpu_booking_payment_status', true );
        if ( 'paid' === $already_paid ) {
            return new WP_Error( 'already_paid', 'This booking has already been paid.', array( 'status' => 400 ) );
        }

        $mentor_id   = (int) get_post_meta( $booking_id, '_bpu_booking_mentor_id', true );
        $mentor      = get_userdata( $mentor_id );
        $session_name = $session_id ? get_post_meta( $session_id, '_session_name', true ) : 'Mentorship Session';
        $mentor_name  = $mentor ? $mentor->display_name : 'Mentor';

        $site_url     = 'https://pairedbybpu.uk';
        $success_url  = $site_url . '/paired/dashboard?payment=success&booking=' . $booking_id;
        $cancel_url   = $site_url . '/paired/dashboard?payment=cancelled&booking=' . $booking_id;

        $checkout_params = array(
            'mode'                       => 'payment',
            'currency'                   => 'gbp',
            'line_items[0][price_data][currency]'    => 'gbp',
            'line_items[0][price_data][unit_amount]'  => intval( $price * 100 ),
            'line_items[0][price_data][product_data][name]' => sanitize_text_field( $session_name . ' with ' . $mentor_name ),
            'line_items[0][quantity]'     => 1,
            'success_url'                => $success_url,
            'cancel_url'                 => $cancel_url,
            'metadata[booking_id]'       => $booking_id,
            'metadata[mentee_id]'        => $user_id,
            'metadata[mentor_id]'        => $mentor_id,
        );

        $mentor_stripe_id = get_user_meta( $mentor_id, '_paired_stripe_account_id', true );
        if ( $mentor_stripe_id ) {
            $platform_fee = intval( $price * 100 * 0.10 );
            $checkout_params['payment_intent_data[transfer_data][destination]'] = $mentor_stripe_id;
            $checkout_params['payment_intent_data[application_fee_amount]']     = $platform_fee;
        }

        $result = $this->stripe_api( 'checkout/sessions', $checkout_params );
        if ( is_wp_error( $result ) ) {
            return $result;
        }

        if ( ! empty( $result['error'] ) ) {
            return new WP_Error( 'stripe_error', $result['error']['message'] ?? 'Stripe error.', array( 'status' => 400 ) );
        }

        update_post_meta( $booking_id, '_bpu_booking_stripe_session_id', $result['id'] );
        update_post_meta( $booking_id, '_bpu_booking_payment_status', 'pending' );

        return new WP_REST_Response( array(
            'success'      => true,
            'checkout_url'  => $result['url'],
            'session_id'   => $result['id'],
        ), 200 );
    }

    public function stripe_webhook( WP_REST_Request $request ) {
        $payload   = $request->get_body();
        $sig_header = isset( $_SERVER['HTTP_STRIPE_SIGNATURE'] ) ? $_SERVER['HTTP_STRIPE_SIGNATURE'] : '';
        $secret    = $this->get_stripe_webhook_secret();

        if ( $secret && $sig_header ) {
            $elements = array();
            foreach ( explode( ',', $sig_header ) as $part ) {
                list( $key, $val ) = explode( '=', $part, 2 );
                $elements[ trim( $key ) ] = trim( $val );
            }
            $timestamp     = isset( $elements['t'] ) ? $elements['t'] : '';
            $signature     = isset( $elements['v1'] ) ? $elements['v1'] : '';
            $signed_payload = $timestamp . '.' . $payload;
            $expected       = hash_hmac( 'sha256', $signed_payload, $secret );

            if ( ! hash_equals( $expected, $signature ) ) {
                return new WP_REST_Response( array( 'error' => 'Invalid signature' ), 400 );
            }
        }

        $event = json_decode( $payload, true );
        if ( ! $event || empty( $event['type'] ) ) {
            return new WP_REST_Response( array( 'error' => 'Invalid payload' ), 400 );
        }

        $type = $event['type'];
        $data = $event['data']['object'] ?? array();

        if ( 'checkout.session.completed' === $type ) {
            $booking_id = isset( $data['metadata']['booking_id'] ) ? absint( $data['metadata']['booking_id'] ) : 0;
            if ( $booking_id ) {
                update_post_meta( $booking_id, '_bpu_booking_payment_status', 'paid' );
                update_post_meta( $booking_id, '_bpu_booking_payment_amount', ( $data['amount_total'] ?? 0 ) / 100 );
                update_post_meta( $booking_id, '_bpu_booking_stripe_payment_id', $data['payment_intent'] ?? '' );

                $mentee_id = (int) get_post_meta( $booking_id, '_bpu_booking_mentee_id', true );
                $mentor_id = (int) get_post_meta( $booking_id, '_bpu_booking_mentor_id', true );
                $this->create_notification( $mentor_id, 'payment', 'Payment Received', 'Payment received for your upcoming session.', '/paired/mentor/bookings' );
                $this->create_notification( $mentee_id, 'payment', 'Payment Confirmed', 'Your payment has been confirmed.', '/paired/dashboard' );
            }
        } elseif ( 'charge.refunded' === $type ) {
            $payment_intent = $data['payment_intent'] ?? '';
            if ( $payment_intent ) {
                $query = new WP_Query( array(
                    'post_type'      => 'mentorship_booking',
                    'post_status'    => 'any',
                    'posts_per_page' => 1,
                    'meta_query'     => array(
                        array( 'key' => '_bpu_booking_stripe_payment_id', 'value' => $payment_intent ),
                    ),
                ) );
                if ( $query->have_posts() ) {
                    $booking_id = $query->posts[0]->ID;
                    update_post_meta( $booking_id, '_bpu_booking_payment_status', 'refunded' );
                }
                wp_reset_postdata();
            }
        }

        return new WP_REST_Response( array( 'received' => true ), 200 );
    }

    public function get_payout_settings( WP_REST_Request $request ) {
        $user_id = $this->verify_mentor( $request );
        if ( is_wp_error( $user_id ) ) return $user_id;

        $stripe_account_id = get_user_meta( $user_id, '_paired_stripe_account_id', true );
        $payout_enabled    = false;

        if ( $stripe_account_id ) {
            $account = $this->stripe_api( 'accounts/' . $stripe_account_id, array(), 'GET' );
            if ( ! is_wp_error( $account ) && ! empty( $account['payouts_enabled'] ) ) {
                $payout_enabled = true;
            }
        }

        return new WP_REST_Response( array(
            'success'          => true,
            'stripe_account_id' => $stripe_account_id ?: null,
            'payout_enabled'    => $payout_enabled,
            'onboarding_complete' => $payout_enabled,
        ), 200 );
    }

    public function update_payout_settings( WP_REST_Request $request ) {
        $user_id = $this->verify_mentor( $request );
        if ( is_wp_error( $user_id ) ) return $user_id;

        $user = get_userdata( $user_id );
        $stripe_account_id = get_user_meta( $user_id, '_paired_stripe_account_id', true );

        if ( ! $stripe_account_id ) {
            $result = $this->stripe_api( 'accounts', array(
                'type'                   => 'express',
                'country'                => 'GB',
                'email'                  => $user->user_email,
                'capabilities[card_payments][requested]'  => 'true',
                'capabilities[transfers][requested]'      => 'true',
                'metadata[user_id]'      => $user_id,
            ) );
            if ( is_wp_error( $result ) ) return $result;
            if ( ! empty( $result['error'] ) ) {
                return new WP_Error( 'stripe_error', $result['error']['message'] ?? 'Could not create Stripe account.', array( 'status' => 400 ) );
            }
            $stripe_account_id = $result['id'];
            update_user_meta( $user_id, '_paired_stripe_account_id', $stripe_account_id );
        }

        $link_result = $this->stripe_api( 'account_links', array(
            'account'     => $stripe_account_id,
            'refresh_url' => 'https://pairedbybpu.uk/paired/mentor/payout-settings?refresh=1',
            'return_url'  => 'https://pairedbybpu.uk/paired/mentor/payout-settings?onboarding=complete',
            'type'        => 'account_onboarding',
        ) );

        if ( is_wp_error( $link_result ) ) return $link_result;
        if ( ! empty( $link_result['error'] ) ) {
            return new WP_Error( 'stripe_error', $link_result['error']['message'] ?? 'Could not create onboarding link.', array( 'status' => 400 ) );
        }

        return new WP_REST_Response( array(
            'success'       => true,
            'onboarding_url' => $link_result['url'],
        ), 200 );
    }

    public function admin_get_payouts( WP_REST_Request $request ) {
        $payload = $this->verify_jwt_bearer( $request );
        if ( ! $payload || empty( $payload['user_id'] ) ) {
            return new WP_Error( 'sso_invalid_token', 'Invalid or missing token.', array( 'status' => 401 ) );
        }

        $query = new WP_Query( array(
            'post_type'      => 'mentorship_booking',
            'post_status'    => 'any',
            'posts_per_page' => 100,
            'meta_query'     => array(
                array( 'key' => '_bpu_booking_payment_status', 'value' => 'paid' ),
            ),
            'orderby'        => 'date',
            'order'          => 'DESC',
        ) );

        $payouts = array();
        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $pid       = get_the_ID();
                $mentor_id = (int) get_post_meta( $pid, '_bpu_booking_mentor_id', true );
                $mentor    = get_userdata( $mentor_id );
                $payouts[] = array(
                    'booking_id'     => $pid,
                    'mentor_id'      => $mentor_id,
                    'mentor_name'    => $mentor ? $mentor->display_name : 'Unknown',
                    'amount'         => (float) get_post_meta( $pid, '_bpu_booking_payment_amount', true ),
                    'payment_status' => get_post_meta( $pid, '_bpu_booking_payment_status', true ),
                    'date'           => get_post_meta( $pid, '_bpu_booking_date', true ),
                    'stripe_account' => $mentor ? get_user_meta( $mentor_id, '_paired_stripe_account_id', true ) : '',
                );
            }
        }
        wp_reset_postdata();

        return new WP_REST_Response( array( 'success' => true, 'payouts' => $payouts ), 200 );
    }

    public function admin_list_bookings( WP_REST_Request $request ) {
        $status   = sanitize_text_field( $request->get_param( 'status' ) ?: 'all' );
        $search   = sanitize_text_field( $request->get_param( 'search' ) ?: '' );
        $page     = max( 1, (int) $request->get_param( 'page' ) ?: 1 );
        $per_page = 20;

        $args = array(
            'post_type'      => 'bpu_booking',
            'post_status'    => 'publish',
            'posts_per_page' => $per_page,
            'paged'          => $page,
            'orderby'        => 'date',
            'order'          => 'DESC',
            'meta_query'     => array(),
        );

        if ( $status !== 'all' ) {
            $args['meta_query'][] = array(
                'key'   => '_bpu_booking_status',
                'value' => $status,
            );
        }

        $query  = new WP_Query( $args );
        $total  = $query->found_posts;
        $result = array();

        foreach ( $query->posts as $post ) {
            $pid        = $post->ID;
            $mentor_id  = (int) get_post_meta( $pid, '_bpu_booking_mentor_id', true );
            $mentee_id  = (int) get_post_meta( $pid, '_bpu_booking_mentee_id', true );
            $mentor     = $mentor_id ? get_user_by( 'id', $mentor_id ) : null;
            $mentee     = $mentee_id ? get_user_by( 'id', $mentee_id ) : null;
            $bk_status  = get_post_meta( $pid, '_bpu_booking_status', true ) ?: 'pending';
            $date       = get_post_meta( $pid, '_bpu_booking_date', true );
            $time_slot  = get_post_meta( $pid, '_bpu_booking_time_slot', true );

            // Search filter
            if ( $search ) {
                $mentor_name = $mentor ? $mentor->display_name : '';
                $mentee_name = $mentee ? $mentee->display_name : '';
                $mentee_email = $mentee ? $mentee->user_email : '';
                $haystack = strtolower( $mentor_name . ' ' . $mentee_name . ' ' . $mentee_email );
                if ( strpos( $haystack, strtolower( $search ) ) === false ) {
                    continue;
                }
            }

            $result[] = array(
                'id'           => $pid,
                'status'       => $bk_status,
                'date'         => $date,
                'time_slot'    => $time_slot,
                'notes'        => get_post_meta( $pid, '_bpu_booking_notes', true ) ?: '',
                'created_at'   => get_the_date( 'c', $post ),
                'payment_amount' => (float) get_post_meta( $pid, '_bpu_booking_payment_amount', true ),
                'payment_status' => get_post_meta( $pid, '_bpu_booking_payment_status', true ) ?: '',
                'mentor'       => $mentor ? array(
                    'id'           => $mentor_id,
                    'display_name' => $mentor->display_name,
                    'email'        => $mentor->user_email,
                    'avatar_url'   => get_user_meta( $mentor_id, '_paired_photo_url', true ) ?: get_avatar_url( $mentor_id, array( 'size' => 64 ) ),
                ) : null,
                'mentee'       => $mentee ? array(
                    'id'           => $mentee_id,
                    'display_name' => $mentee->display_name,
                    'email'        => $mentee->user_email,
                    'avatar_url'   => get_avatar_url( $mentee_id, array( 'size' => 64 ) ),
                ) : null,
            );
        }

        wp_reset_postdata();

        return new WP_REST_Response( array(
            'success'    => true,
            'bookings'   => $result,
            'total'      => $total,
            'page'       => $page,
            'per_page'   => $per_page,
            'pages'      => ceil( $total / $per_page ),
        ), 200 );
    }

    public function admin_update_booking_status( WP_REST_Request $request ) {
        $booking_id = (int) $request->get_param( 'id' );
        $body       = $request->get_json_params();
        if ( ! is_array( $body ) ) $body = array();
        $new_status = sanitize_text_field( $body['status'] ?? '' );

        $allowed = array( 'confirmed', 'completed', 'cancelled', 'pending' );
        if ( ! in_array( $new_status, $allowed, true ) ) {
            return new WP_Error( 'invalid_status', 'Invalid status.', array( 'status' => 400 ) );
        }

        $post = get_post( $booking_id );
        if ( ! $post || $post->post_type !== 'bpu_booking' ) {
            return new WP_Error( 'not_found', 'Booking not found.', array( 'status' => 404 ) );
        }

        update_post_meta( $booking_id, '_bpu_booking_status', $new_status );

        return new WP_REST_Response( array( 'success' => true, 'status' => $new_status ), 200 );
    }

    public function admin_list_mentees( WP_REST_Request $request ) {
        $search   = sanitize_text_field( $request->get_param( 'search' ) ?: '' );
        $page     = max( 1, (int) $request->get_param( 'page' ) ?: 1 );
        $per_page = 20;

        $args = array(
            'number'  => $per_page,
            'offset'  => ( $page - 1 ) * $per_page,
            'orderby' => 'registered',
            'order'   => 'DESC',
            'fields'  => 'all',
            // Exclude mentors and admins — show users who have booked as mentees
            'role__not_in' => array( 'administrator' ),
        );

        if ( $search ) {
            $args['search']         = '*' . $search . '*';
            $args['search_columns'] = array( 'user_login', 'user_email', 'display_name' );
        }

        $user_query = new WP_User_Query( $args );
        $users      = $user_query->get_results();
        $total      = $user_query->get_total();
        $result     = array();

        foreach ( $users as $user ) {
            $uid = $user->ID;

            // Booking count as mentee
            $bookings = get_posts( array(
                'post_type'   => 'bpu_booking',
                'post_status' => 'publish',
                'numberposts' => -1,
                'meta_query'  => array(
                    array( 'key' => '_bpu_booking_mentee_id', 'value' => $uid, 'type' => 'NUMERIC' ),
                ),
                'fields' => 'ids',
            ) );
            $booking_count = count( $bookings );

            // Last booking date
            $last_booking = '';
            if ( $booking_count > 0 ) {
                $latest = get_posts( array(
                    'post_type'      => 'bpu_booking',
                    'post_status'    => 'publish',
                    'numberposts'    => 1,
                    'orderby'        => 'date',
                    'order'          => 'DESC',
                    'meta_query'     => array(
                        array( 'key' => '_bpu_booking_mentee_id', 'value' => $uid, 'type' => 'NUMERIC' ),
                    ),
                ) );
                $last_booking = $latest ? get_the_date( 'c', $latest[0] ) : '';
            }

            $is_active = ! get_user_meta( $uid, '_paired_deactivated', true );

            $result[] = array(
                'id'            => $uid,
                'display_name'  => $user->display_name,
                'email'         => $user->user_email,
                'avatar_url'    => get_avatar_url( $uid, array( 'size' => 64 ) ),
                'registered'    => $user->user_registered,
                'booking_count' => $booking_count,
                'last_booking'  => $last_booking,
                'is_active'     => $is_active,
                'roles'         => $user->roles,
            );
        }

        return new WP_REST_Response( array(
            'success'  => true,
            'mentees'  => $result,
            'total'    => $total,
            'page'     => $page,
            'per_page' => $per_page,
            'pages'    => ceil( $total / $per_page ),
        ), 200 );
    }

    public function admin_deactivate_mentee( WP_REST_Request $request ) {
        $user_id = (int) $request->get_param( 'id' );
        $user    = get_user_by( 'id', $user_id );
        if ( ! $user ) {
            return new WP_Error( 'not_found', 'User not found.', array( 'status' => 404 ) );
        }
        update_user_meta( $user_id, '_paired_deactivated', '1' );
        return new WP_REST_Response( array( 'success' => true ), 200 );
    }

    public function admin_activate_mentee( WP_REST_Request $request ) {
        $user_id = (int) $request->get_param( 'id' );
        $user    = get_user_by( 'id', $user_id );
        if ( ! $user ) {
            return new WP_Error( 'not_found', 'User not found.', array( 'status' => 404 ) );
        }
        delete_user_meta( $user_id, '_paired_deactivated' );
        return new WP_REST_Response( array( 'success' => true ), 200 );
    }

    // ═══════════════════════════════════════════════════════════════
    //  TIER 2: TRANSACTION HISTORY, REPORTS, COUPONS, SETTINGS
    // ═══════════════════════════════════════════════════════════════

    /**
     * Admin: Transaction History — bookings with payment data.
     */
    public function admin_list_transactions( WP_REST_Request $request ) {
        $search    = sanitize_text_field( $request->get_param( 'search' ) ?: '' );
        $page      = max( 1, (int) $request->get_param( 'page' ) ?: 1 );
        $per_page  = 20;
        $date_from = sanitize_text_field( $request->get_param( 'date_from' ) ?: '' );
        $date_to   = sanitize_text_field( $request->get_param( 'date_to' ) ?: '' );
        $payment_status = sanitize_text_field( $request->get_param( 'payment_status' ) ?: '' );

        $args = array(
            'post_type'      => 'bpu_booking',
            'post_status'    => 'publish',
            'posts_per_page' => $per_page,
            'paged'          => $page,
            'orderby'        => 'date',
            'order'          => 'DESC',
            'meta_query'     => array(
                array(
                    'key'     => '_bpu_booking_payment_status',
                    'compare' => 'EXISTS',
                ),
            ),
        );

        if ( $payment_status ) {
            $args['meta_query'][] = array(
                'key'   => '_bpu_booking_payment_status',
                'value' => $payment_status,
            );
        }

        if ( $date_from || $date_to ) {
            $date_meta = array( 'key' => '_bpu_booking_date' );
            if ( $date_from && $date_to ) {
                $date_meta['value']   = array( $date_from, $date_to );
                $date_meta['compare'] = 'BETWEEN';
                $date_meta['type']    = 'DATE';
            } elseif ( $date_from ) {
                $date_meta['value']   = $date_from;
                $date_meta['compare'] = '>=';
                $date_meta['type']    = 'DATE';
            } else {
                $date_meta['value']   = $date_to;
                $date_meta['compare'] = '<=';
                $date_meta['type']    = 'DATE';
            }
            $args['meta_query'][] = $date_meta;
        }

        $query  = new WP_Query( $args );
        $total  = $query->found_posts;
        $result = array();

        foreach ( $query->posts as $post ) {
            $pid        = $post->ID;
            $mentor_id  = (int) get_post_meta( $pid, '_bpu_booking_mentor_id', true );
            $mentee_id  = (int) get_post_meta( $pid, '_bpu_booking_mentee_id', true );
            $mentor     = $mentor_id ? get_user_by( 'id', $mentor_id ) : null;
            $mentee     = $mentee_id ? get_user_by( 'id', $mentee_id ) : null;

            // Search filter
            if ( $search ) {
                $mentor_name  = $mentor ? $mentor->display_name : '';
                $mentee_name  = $mentee ? $mentee->display_name : '';
                $mentee_email = $mentee ? $mentee->user_email : '';
                $haystack = strtolower( $mentor_name . ' ' . $mentee_name . ' ' . $mentee_email );
                if ( strpos( $haystack, strtolower( $search ) ) === false ) {
                    continue;
                }
            }

            $result[] = array(
                'id'                  => $pid,
                'booking_date'        => get_post_meta( $pid, '_bpu_booking_date', true ),
                'booking_status'      => get_post_meta( $pid, '_bpu_booking_status', true ) ?: 'pending',
                'payment_status'      => get_post_meta( $pid, '_bpu_booking_payment_status', true ),
                'payment_amount'      => (float) get_post_meta( $pid, '_bpu_booking_payment_amount', true ),
                'stripe_payment_id'   => get_post_meta( $pid, '_bpu_booking_stripe_payment_id', true ) ?: '',
                'stripe_session_id'   => get_post_meta( $pid, '_bpu_booking_stripe_session_id', true ) ?: '',
                'created_at'          => get_the_date( 'c', $post ),
                'mentor'              => $mentor ? array(
                    'id'           => $mentor_id,
                    'display_name' => $mentor->display_name,
                    'email'        => $mentor->user_email,
                    'avatar_url'   => get_user_meta( $mentor_id, '_paired_photo_url', true ) ?: get_avatar_url( $mentor_id, array( 'size' => 64 ) ),
                ) : null,
                'mentee'              => $mentee ? array(
                    'id'           => $mentee_id,
                    'display_name' => $mentee->display_name,
                    'email'        => $mentee->user_email,
                    'avatar_url'   => get_avatar_url( $mentee_id, array( 'size' => 64 ) ),
                ) : null,
            );
        }

        wp_reset_postdata();

        return new WP_REST_Response( array(
            'success'      => true,
            'transactions' => $result,
            'total'        => $total,
            'page'         => $page,
            'per_page'     => $per_page,
            'pages'        => ceil( $total / $per_page ),
        ), 200 );
    }

    /**
     * Admin: Financial Reports — aggregated stats.
     */
    public function admin_financial_reports( WP_REST_Request $request ) {
        // Fetch all bookings with payment data
        $args = array(
            'post_type'      => 'bpu_booking',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'meta_query'     => array(
                array(
                    'key'     => '_bpu_booking_payment_status',
                    'value'   => 'paid',
                ),
            ),
        );

        $query = new WP_Query( $args );
        $total_revenue     = 0.0;
        $revenue_by_month  = array();
        $revenue_by_mentor = array();
        $booking_count     = 0;

        foreach ( $query->posts as $post ) {
            $pid    = $post->ID;
            $amount = (float) get_post_meta( $pid, '_bpu_booking_payment_amount', true );
            $date   = get_post_meta( $pid, '_bpu_booking_date', true );
            $mentor_id = (int) get_post_meta( $pid, '_bpu_booking_mentor_id', true );

            $total_revenue += $amount;
            $booking_count++;

            // Revenue by month
            if ( $date ) {
                $month_key = substr( $date, 0, 7 ); // YYYY-MM
                if ( ! isset( $revenue_by_month[ $month_key ] ) ) {
                    $revenue_by_month[ $month_key ] = array( 'month' => $month_key, 'revenue' => 0.0, 'count' => 0 );
                }
                $revenue_by_month[ $month_key ]['revenue'] += $amount;
                $revenue_by_month[ $month_key ]['count']++;
            }

            // Revenue by mentor
            if ( $mentor_id ) {
                $mk = (string) $mentor_id;
                if ( ! isset( $revenue_by_mentor[ $mk ] ) ) {
                    $mentor = get_user_by( 'id', $mentor_id );
                    $revenue_by_mentor[ $mk ] = array(
                        'mentor_id'    => $mentor_id,
                        'display_name' => $mentor ? $mentor->display_name : 'Unknown',
                        'revenue'      => 0.0,
                        'count'        => 0,
                    );
                }
                $revenue_by_mentor[ $mk ]['revenue'] += $amount;
                $revenue_by_mentor[ $mk ]['count']++;
            }
        }

        wp_reset_postdata();

        // Sort months descending
        krsort( $revenue_by_month );

        // Sort mentors by revenue descending
        usort( $revenue_by_mentor, function( $a, $b ) {
            return $b['revenue'] <=> $a['revenue'];
        } );

        $average_booking_value = $booking_count > 0 ? round( $total_revenue / $booking_count, 2 ) : 0;

        return new WP_REST_Response( array(
            'success'               => true,
            'total_revenue'         => round( $total_revenue, 2 ),
            'total_paid_bookings'   => $booking_count,
            'average_booking_value' => $average_booking_value,
            'revenue_by_month'      => array_values( $revenue_by_month ),
            'revenue_by_mentor'     => array_values( $revenue_by_mentor ),
        ), 200 );
    }

    /**
     * Admin: List Coupons.
     */
    public function admin_list_coupons( WP_REST_Request $request ) {
        $page     = max( 1, (int) $request->get_param( 'page' ) ?: 1 );
        $per_page = 20;
        $search   = sanitize_text_field( $request->get_param( 'search' ) ?: '' );

        $args = array(
            'post_type'      => 'bpu_coupon',
            'post_status'    => 'publish',
            'posts_per_page' => $per_page,
            'paged'          => $page,
            'orderby'        => 'date',
            'order'          => 'DESC',
        );

        if ( $search ) {
            $args['meta_query'] = array(
                array(
                    'key'     => '_bpu_coupon_code',
                    'value'   => $search,
                    'compare' => 'LIKE',
                ),
            );
        }

        $query  = new WP_Query( $args );
        $total  = $query->found_posts;
        $result = array();

        foreach ( $query->posts as $post ) {
            $pid = $post->ID;
            $result[] = array(
                'id'             => $pid,
                'code'           => get_post_meta( $pid, '_bpu_coupon_code', true ),
                'discount_type'  => get_post_meta( $pid, '_bpu_coupon_discount_type', true ),
                'discount_value' => (float) get_post_meta( $pid, '_bpu_coupon_discount_value', true ),
                'expiry_date'    => get_post_meta( $pid, '_bpu_coupon_expiry_date', true ),
                'max_uses'       => (int) get_post_meta( $pid, '_bpu_coupon_max_uses', true ),
                'current_uses'   => (int) get_post_meta( $pid, '_bpu_coupon_current_uses', true ),
                'is_active'      => (bool) get_post_meta( $pid, '_bpu_coupon_is_active', true ),
                'created_at'     => get_the_date( 'c', $post ),
            );
        }

        wp_reset_postdata();

        return new WP_REST_Response( array(
            'success'  => true,
            'coupons'  => $result,
            'total'    => $total,
            'page'     => $page,
            'per_page' => $per_page,
            'pages'    => ceil( $total / $per_page ),
        ), 200 );
    }

    /**
     * Admin: Create Coupon.
     */
    public function admin_create_coupon( WP_REST_Request $request ) {
        $body = $request->get_json_params();
        if ( ! is_array( $body ) ) $body = array();

        $code = sanitize_text_field( $body['code'] ?? '' );
        if ( ! $code ) {
            return new WP_Error( 'missing_code', 'Coupon code is required.', array( 'status' => 400 ) );
        }

        $discount_type = sanitize_text_field( $body['discount_type'] ?? 'percentage' );
        if ( ! in_array( $discount_type, array( 'percentage', 'fixed' ), true ) ) {
            return new WP_Error( 'invalid_discount_type', 'Discount type must be percentage or fixed.', array( 'status' => 400 ) );
        }

        $discount_value = (float) ( $body['discount_value'] ?? 0 );
        $expiry_date    = sanitize_text_field( $body['expiry_date'] ?? '' );
        $max_uses       = (int) ( $body['max_uses'] ?? 0 );
        $is_active      = isset( $body['is_active'] ) ? ( (int) $body['is_active'] ) : 1;

        // Check for duplicate coupon code
        $existing = get_posts( array(
            'post_type'   => 'bpu_coupon',
            'post_status' => 'publish',
            'numberposts' => 1,
            'meta_query'  => array(
                array( 'key' => '_bpu_coupon_code', 'value' => strtoupper( $code ) ),
            ),
        ) );
        if ( ! empty( $existing ) ) {
            return new WP_Error( 'duplicate_code', 'A coupon with this code already exists.', array( 'status' => 409 ) );
        }

        $post_id = wp_insert_post( array(
            'post_type'   => 'bpu_coupon',
            'post_status' => 'publish',
            'post_title'  => strtoupper( $code ),
        ) );

        if ( is_wp_error( $post_id ) ) {
            return new WP_Error( 'create_failed', 'Failed to create coupon.', array( 'status' => 500 ) );
        }

        update_post_meta( $post_id, '_bpu_coupon_code', strtoupper( $code ) );
        update_post_meta( $post_id, '_bpu_coupon_discount_type', $discount_type );
        update_post_meta( $post_id, '_bpu_coupon_discount_value', $discount_value );
        update_post_meta( $post_id, '_bpu_coupon_expiry_date', $expiry_date );
        update_post_meta( $post_id, '_bpu_coupon_max_uses', $max_uses );
        update_post_meta( $post_id, '_bpu_coupon_current_uses', 0 );
        update_post_meta( $post_id, '_bpu_coupon_is_active', $is_active );

        return new WP_REST_Response( array(
            'success' => true,
            'coupon'  => array(
                'id'             => $post_id,
                'code'           => strtoupper( $code ),
                'discount_type'  => $discount_type,
                'discount_value' => $discount_value,
                'expiry_date'    => $expiry_date,
                'max_uses'       => $max_uses,
                'current_uses'   => 0,
                'is_active'      => (bool) $is_active,
            ),
        ), 201 );
    }

    /**
     * Admin: Update Coupon.
     */
    public function admin_update_coupon( WP_REST_Request $request ) {
        $coupon_id = (int) $request->get_param( 'id' );
        $post      = get_post( $coupon_id );

        if ( ! $post || $post->post_type !== 'bpu_coupon' ) {
            return new WP_Error( 'not_found', 'Coupon not found.', array( 'status' => 404 ) );
        }

        $body = $request->get_json_params();
        if ( ! is_array( $body ) ) $body = array();

        $updatable_fields = array(
            'code'           => '_bpu_coupon_code',
            'discount_type'  => '_bpu_coupon_discount_type',
            'discount_value' => '_bpu_coupon_discount_value',
            'expiry_date'    => '_bpu_coupon_expiry_date',
            'max_uses'       => '_bpu_coupon_max_uses',
            'is_active'      => '_bpu_coupon_is_active',
        );

        foreach ( $updatable_fields as $field => $meta_key ) {
            if ( ! isset( $body[ $field ] ) ) continue;

            $value = $body[ $field ];

            if ( $field === 'code' ) {
                $value = strtoupper( sanitize_text_field( $value ) );
                wp_update_post( array( 'ID' => $coupon_id, 'post_title' => $value ) );
            } elseif ( $field === 'discount_type' ) {
                if ( ! in_array( $value, array( 'percentage', 'fixed' ), true ) ) continue;
                $value = sanitize_text_field( $value );
            } elseif ( $field === 'discount_value' ) {
                $value = (float) $value;
            } elseif ( $field === 'max_uses' ) {
                $value = (int) $value;
            } elseif ( $field === 'is_active' ) {
                $value = (int) $value;
            } else {
                $value = sanitize_text_field( $value );
            }

            update_post_meta( $coupon_id, $meta_key, $value );
        }

        return new WP_REST_Response( array(
            'success' => true,
            'coupon'  => array(
                'id'             => $coupon_id,
                'code'           => get_post_meta( $coupon_id, '_bpu_coupon_code', true ),
                'discount_type'  => get_post_meta( $coupon_id, '_bpu_coupon_discount_type', true ),
                'discount_value' => (float) get_post_meta( $coupon_id, '_bpu_coupon_discount_value', true ),
                'expiry_date'    => get_post_meta( $coupon_id, '_bpu_coupon_expiry_date', true ),
                'max_uses'       => (int) get_post_meta( $coupon_id, '_bpu_coupon_max_uses', true ),
                'current_uses'   => (int) get_post_meta( $coupon_id, '_bpu_coupon_current_uses', true ),
                'is_active'      => (bool) get_post_meta( $coupon_id, '_bpu_coupon_is_active', true ),
            ),
        ), 200 );
    }

    /**
     * Admin: Delete Coupon.
     */
    public function admin_delete_coupon( WP_REST_Request $request ) {
        $coupon_id = (int) $request->get_param( 'id' );
        $post      = get_post( $coupon_id );

        if ( ! $post || $post->post_type !== 'bpu_coupon' ) {
            return new WP_Error( 'not_found', 'Coupon not found.', array( 'status' => 404 ) );
        }

        wp_delete_post( $coupon_id, true );

        return new WP_REST_Response( array( 'success' => true ), 200 );
    }

    /**
     * Admin: Get Platform Settings.
     */
    public function admin_get_settings( WP_REST_Request $request ) {
        return new WP_REST_Response( array(
            'success'  => true,
            'settings' => array(
                'commission_rate'       => (float) get_option( '_paired_platform_commission_rate', 0 ),
                'currency'              => get_option( '_paired_platform_currency', 'GBP' ),
                'booking_buffer_hours'  => (int) get_option( '_paired_platform_booking_buffer_hours', 24 ),
                'max_bookings_per_day'  => (int) get_option( '_paired_platform_max_bookings_per_day', 10 ),
            ),
        ), 200 );
    }

    /**
     * Admin: Update Platform Settings.
     */
    public function admin_update_settings( WP_REST_Request $request ) {
        $body = $request->get_json_params();
        if ( ! is_array( $body ) ) $body = array();

        $allowed = array(
            'commission_rate'      => '_paired_platform_commission_rate',
            'currency'             => '_paired_platform_currency',
            'booking_buffer_hours' => '_paired_platform_booking_buffer_hours',
            'max_bookings_per_day' => '_paired_platform_max_bookings_per_day',
        );

        foreach ( $allowed as $field => $option_key ) {
            if ( ! isset( $body[ $field ] ) ) continue;

            $value = $body[ $field ];

            if ( $field === 'commission_rate' ) {
                $value = max( 0, min( 100, (float) $value ) );
            } elseif ( $field === 'currency' ) {
                $value = strtoupper( sanitize_text_field( $value ) );
            } elseif ( $field === 'booking_buffer_hours' ) {
                $value = max( 0, (int) $value );
            } elseif ( $field === 'max_bookings_per_day' ) {
                $value = max( 1, (int) $value );
            }

            update_option( $option_key, $value );
        }

        return new WP_REST_Response( array(
            'success'  => true,
            'settings' => array(
                'commission_rate'       => (float) get_option( '_paired_platform_commission_rate', 0 ),
                'currency'              => get_option( '_paired_platform_currency', 'GBP' ),
                'booking_buffer_hours'  => (int) get_option( '_paired_platform_booking_buffer_hours', 24 ),
                'max_bookings_per_day'  => (int) get_option( '_paired_platform_max_bookings_per_day', 10 ),
            ),
        ), 200 );
    }

    // ═══════════════════════════════════════════════════════════════
    //  TIER 3: EMAIL TEMPLATES
    // ═══════════════════════════════════════════════════════════════

    /**
     * Return the default email template definitions.
     */
    private function get_email_template_definitions() {
        return array(
            'welcome' => array(
                'label'           => 'Welcome email',
                'default_subject' => 'Welcome to Black Professionals United!',
                'default_body'    => "Hi {{name}},\r\n\r\nWelcome to the Black Professionals United community — we are thrilled to have you.\r\n\r\nHere is what you can do next:\r\n• Complete your profile so we can match you with the right jobs and mentors\r\n• Upload your CV to our AI-powered CV Clinic for personalised feedback\r\n• Browse PAIRED — our free 1-on-1 mentorship platform\r\n\r\nYour member portal: https://app.blackprofessionals.uk\r\nFind a mentor: https://pairedbybpu.uk/mentors\r\n\r\nTo your career success,\r\nThe BPU Team",
                'variables'       => array( '{{name}}' ),
            ),
            'booking_mentee' => array(
                'label'           => 'Booking confirmation to mentee',
                'default_subject' => 'Booking requested with {{mentor_name}} — PAIRED by BPU',
                'default_body'    => "Hi {{name}},\r\n\r\nYour session request with {{mentor_name}} has been sent. They will confirm shortly.\r\n\r\nDate: {{date}}\r\nTime: {{time}}\r\n\r\nView your sessions: https://pairedbybpu.uk/dashboard\r\n\r\nThe PAIRED Team",
                'variables'       => array( '{{name}}', '{{mentor_name}}', '{{date}}', '{{time}}', '{{notes}}' ),
            ),
            'booking_mentor' => array(
                'label'           => 'New booking notification to mentor',
                'default_subject' => 'New session request from {{mentee_name}} — PAIRED by BPU',
                'default_body'    => "Hi {{name}},\r\n\r\n{{mentee_name}} has requested a 1-on-1 session with you.\r\n\r\nDate: {{date}}\r\nTime: {{time}}\r\n\r\nLog in to confirm or reschedule: https://pairedbybpu.uk/dashboard\r\n\r\nThe PAIRED Team",
                'variables'       => array( '{{name}}', '{{mentee_name}}', '{{date}}', '{{time}}', '{{notes}}' ),
            ),
            'booking_confirmed' => array(
                'label'           => 'Booking confirmed notification',
                'default_subject' => 'Session confirmed — PAIRED by BPU',
                'default_body'    => "Hi {{name}},\r\n\r\nYour session with {{other_name}} has been confirmed.\r\n\r\nDate: {{date}}\r\nTime: {{time}}\r\n\r\nView your sessions: https://pairedbybpu.uk/dashboard\r\n\r\nThe PAIRED Team",
                'variables'       => array( '{{name}}', '{{other_name}}', '{{date}}', '{{time}}' ),
            ),
            'booking_cancelled' => array(
                'label'           => 'Booking cancelled notification',
                'default_subject' => 'Session cancelled — PAIRED by BPU',
                'default_body'    => "Hi {{name}},\r\n\r\nYour session with {{other_name}} on {{date}} at {{time}} has been cancelled.\r\n\r\nView your sessions: https://pairedbybpu.uk/dashboard\r\n\r\nThe PAIRED Team",
                'variables'       => array( '{{name}}', '{{other_name}}', '{{date}}', '{{time}}' ),
            ),
            'password_reset' => array(
                'label'           => 'Password reset email',
                'default_subject' => 'Reset your password — Black Professionals United',
                'default_body'    => "Hi {{name}},\r\n\r\nWe received a request to reset your password. Click the link below to choose a new one:\r\n\r\n{{reset_link}}\r\n\r\nThis link will expire in 1 hour. If you did not request a password reset, please ignore this email.\r\n\r\nThe BPU Team",
                'variables'       => array( '{{name}}', '{{reset_link}}' ),
            ),
        );
    }

    /**
     * Admin: Get Email Templates.
     */
    public function admin_get_email_templates( WP_REST_Request $request ) {
        $definitions = $this->get_email_template_definitions();
        $templates   = array();

        foreach ( $definitions as $key => $def ) {
            $subject = get_option( '_paired_email_tpl_' . $key . '_subject', '' );
            $body    = get_option( '_paired_email_tpl_' . $key, '' );

            $templates[] = array(
                'key'             => $key,
                'label'           => $def['label'],
                'subject'         => is_string( $subject ) ? $subject : '',
                'body'            => is_string( $body ) ? $body : '',
                'default_subject' => $def['default_subject'],
                'default_body'    => $def['default_body'],
                'variables'       => $def['variables'],
            );
        }

        return new WP_REST_Response( array( 'success' => true, 'templates' => $templates ), 200 );
    }

    /**
     * Admin: Update an Email Template.
     */
    public function admin_update_email_template( WP_REST_Request $request ) {
        $body = $request->get_json_params();
        if ( ! is_array( $body ) ) {
            return new WP_Error( 'invalid_body', 'Invalid request body.', array( 'status' => 400 ) );
        }

        $key     = isset( $body['key'] ) ? sanitize_text_field( $body['key'] ) : '';
        $subject = isset( $body['subject'] ) ? $body['subject'] : null;
        $tpl     = isset( $body['body'] ) ? $body['body'] : null;

        $definitions = $this->get_email_template_definitions();
        if ( ! isset( $definitions[ $key ] ) ) {
            return new WP_Error( 'invalid_key', 'Unknown email template key.', array( 'status' => 400 ) );
        }

        // Subject: empty string deletes (reverts to default), non-empty saves.
        if ( $subject !== null ) {
            $subject = sanitize_text_field( $subject );
            if ( $subject === '' ) {
                delete_option( '_paired_email_tpl_' . $key . '_subject' );
            } else {
                update_option( '_paired_email_tpl_' . $key . '_subject', $subject );
            }
        }

        // Body: empty string deletes (reverts to default), non-empty saves.
        if ( $tpl !== null ) {
            $tpl = wp_kses_post( $tpl );
            if ( $tpl === '' ) {
                delete_option( '_paired_email_tpl_' . $key );
            } else {
                update_option( '_paired_email_tpl_' . $key, $tpl );
            }
        }

        return new WP_REST_Response( array( 'success' => true, 'key' => $key ), 200 );
    }

    // ═══════════════════════════════════════════════════════════════
    //  TIER 3: CATEGORY / SKILL MANAGEMENT
    // ═══════════════════════════════════════════════════════════════

    /**
     * Return the hardcoded default skills array.
     */
    private function get_default_skills() {
        return array(
            'Engineering & Technology' => array(
                'Front-end Development', 'Back-end Development', 'Full Stack Development',
                'Mobile Development (iOS)', 'Mobile Development (Android)', 'DevOps',
                'Cloud Engineering (AWS)', 'Cloud Engineering (Azure)', 'Cloud Engineering (GCP)',
                'Site Reliability Engineering', 'QA & Testing', 'Data Engineering',
                'AI & Machine Learning', 'Cybersecurity', 'Blockchain', 'Embedded Systems',
                'Systems Architecture', 'Database Administration', 'API Development', 'Technical Leadership',
            ),
            'Product & Project Management' => array(
                'Product Management', 'Product Strategy', 'Product Analytics',
                'Program Management', 'Project Management', 'Agile & Scrum',
                'Product Operations', 'Technical Product Management',
            ),
            'Design & Creative' => array(
                'UX Design', 'UI Design', 'Graphic Design', 'Motion Design', 'Brand Design',
                'Industrial Design', 'Design Systems', 'Design Ops', 'UX Research',
                'Interaction Design', 'Service Design', '3D Design', 'Game Design', 'XR/VR Design',
            ),
            'Marketing & Communications' => array(
                'Digital Marketing', 'Content Marketing', 'Social Media Marketing',
                'Brand Strategy', 'Growth Marketing', 'SEO & SEM', 'Email Marketing',
                'PR & Communications', 'Event Marketing', 'Influencer Marketing',
                'Marketing Analytics', 'Community Management', 'Product Marketing', 'Performance Marketing',
            ),
            'Data & Analytics' => array(
                'Data Analysis', 'Data Science', 'Machine Learning', 'Business Intelligence',
                'Statistical Modelling', 'Data Visualisation', 'Natural Language Processing',
                'Computer Vision', 'Big Data', 'A/B Testing & Experimentation',
            ),
            'Finance & Banking' => array(
                'Investment Banking', 'Corporate Finance', 'Financial Planning & Analysis',
                'Accounting', 'Risk Management', 'Compliance & Regulation', 'Wealth Management',
                'Fintech', 'Audit', 'Tax', 'Treasury', 'Private Equity', 'Venture Capital',
                'Insurance', 'Actuarial Science',
            ),
            'Legal' => array(
                'Corporate Law', 'Employment Law', 'Intellectual Property', 'Contract Law',
                'Regulatory Compliance', 'Commercial Law', 'Immigration Law', 'Family Law',
                'Criminal Law', 'Legal Operations',
            ),
            'Healthcare & Life Sciences' => array(
                'Clinical Medicine', 'Nursing', 'Public Health', 'Health Tech',
                'Pharmaceutical', 'Biotech', 'Mental Health', 'Health Policy',
                'Clinical Research', 'Health Informatics',
            ),
            'Education & Training' => array(
                'Teaching', 'Curriculum Development', 'EdTech', 'Corporate Training',
                'Academic Research', 'Higher Education', 'STEM Education',
                'Coaching & Mentoring', 'Special Education', 'Learning Design',
            ),
            'Human Resources' => array(
                'Talent Acquisition', 'HR Business Partnering', 'Learning & Development',
                'Compensation & Benefits', 'Employee Relations', 'DEI Strategy',
                'People Analytics', 'Organisational Development', 'HR Tech', 'Employer Branding',
            ),
            'Sales & Business Development' => array(
                'Enterprise Sales', 'B2B Sales', 'Account Management', 'Business Development',
                'Sales Operations', 'Customer Success', 'Partnership Management',
                'Revenue Operations', 'Sales Engineering',
            ),
            'Operations & Strategy' => array(
                'Management Consulting', 'Business Strategy', 'Operations Management',
                'Supply Chain', 'Procurement', 'Change Management', 'Process Improvement',
                'Lean & Six Sigma', 'Logistics',
            ),
            'Media & Entertainment' => array(
                'Journalism', 'Broadcasting', 'Film Production', 'Music Industry',
                'Publishing', 'Podcasting', 'Photography', 'Content Creation',
                'Streaming & Digital Media',
            ),
            'Property & Construction' => array(
                'Property Development', 'Architecture', 'Surveying', 'Construction Management',
                'Urban Planning', 'Estate Management', 'Facilities Management',
            ),
            'Entrepreneurship' => array(
                'Startup Founding', 'Fundraising', 'Business Planning', 'Bootstrapping',
                'Social Enterprise', 'Franchise', 'E-commerce', 'Scaling & Growth',
            ),
            'Public Sector & Policy' => array(
                'Civil Service', 'Policy Analysis', 'Local Government',
                'International Development', 'Charity & Non-profit', 'Public Affairs',
                'Community Development',
            ),
        );
    }

    /**
     * Admin: Get Skills (custom or default).
     */
    public function admin_get_skills( WP_REST_Request $request ) {
        $custom = get_option( '_paired_custom_skills' );
        $is_custom = ! empty( $custom ) && is_array( $custom );

        return new WP_REST_Response( array(
            'success'   => true,
            'is_custom' => $is_custom,
            'skills'    => $is_custom ? $custom : $this->get_default_skills(),
        ), 200 );
    }

    /**
     * Admin: Update Skills (replace entire structure).
     */
    public function admin_update_skills( WP_REST_Request $request ) {
        $body = $request->get_json_params();
        if ( ! is_array( $body ) || ! isset( $body['skills'] ) || ! is_array( $body['skills'] ) ) {
            return new WP_Error( 'invalid_body', 'Request must include a skills object.', array( 'status' => 400 ) );
        }

        // Sanitize: each category key is a string, each skill is a string.
        $sanitized = array();
        foreach ( $body['skills'] as $category => $skill_list ) {
            $cat_name = sanitize_text_field( $category );
            if ( empty( $cat_name ) || ! is_array( $skill_list ) ) continue;

            $sanitized[ $cat_name ] = array();
            foreach ( $skill_list as $skill ) {
                $s = sanitize_text_field( $skill );
                if ( $s !== '' ) {
                    $sanitized[ $cat_name ][] = $s;
                }
            }
        }

        if ( empty( $sanitized ) ) {
            return new WP_Error( 'empty_skills', 'Skills structure cannot be empty.', array( 'status' => 400 ) );
        }

        update_option( '_paired_custom_skills', $sanitized );

        return new WP_REST_Response( array(
            'success'   => true,
            'is_custom' => true,
            'skills'    => $sanitized,
        ), 200 );
    }

    /**
     * Admin: Reset Skills to hardcoded defaults.
     */
    public function admin_reset_skills( WP_REST_Request $request ) {
        delete_option( '_paired_custom_skills' );

        return new WP_REST_Response( array(
            'success'   => true,
            'is_custom' => false,
            'skills'    => $this->get_default_skills(),
        ), 200 );
    }

    // ═══════════════════════════════════════════════════════════════
    //  TIER 3: REFERRAL SETTINGS
    // ═══════════════════════════════════════════════════════════════

    /**
     * Admin: Get Referral Settings.
     */
    public function admin_get_referral_settings( WP_REST_Request $request ) {
        return new WP_REST_Response( array(
            'success'  => true,
            'settings' => array(
                'points_per_referral'    => (int) get_option( '_paired_referral_points_per_referral', 10 ),
                'referral_bonus_type'    => get_option( '_paired_referral_bonus_type', 'points' ),
                'referral_bonus_value'   => (int) get_option( '_paired_referral_bonus_value', 10 ),
                'referral_enabled'       => get_option( '_paired_referral_enabled', '1' ),
                'max_referrals_per_user' => (int) get_option( '_paired_referral_max_per_user', 0 ),
            ),
        ), 200 );
    }

    /**
     * Admin: Update Referral Settings.
     */
    public function admin_update_referral_settings( WP_REST_Request $request ) {
        $body = $request->get_json_params();
        if ( ! is_array( $body ) ) $body = array();

        $allowed = array(
            'points_per_referral'    => '_paired_referral_points_per_referral',
            'referral_bonus_type'    => '_paired_referral_bonus_type',
            'referral_bonus_value'   => '_paired_referral_bonus_value',
            'referral_enabled'       => '_paired_referral_enabled',
            'max_referrals_per_user' => '_paired_referral_max_per_user',
        );

        foreach ( $allowed as $field => $option_key ) {
            if ( ! isset( $body[ $field ] ) ) continue;

            $value = $body[ $field ];

            if ( $field === 'points_per_referral' ) {
                $value = max( 0, (int) $value );
            } elseif ( $field === 'referral_bonus_type' ) {
                $value = in_array( $value, array( 'points', 'discount' ), true ) ? $value : 'points';
            } elseif ( $field === 'referral_bonus_value' ) {
                $value = max( 0, (int) $value );
            } elseif ( $field === 'referral_enabled' ) {
                $value = in_array( $value, array( '1', '0', 1, 0, true, false ), true ) ? ( $value ? '1' : '0' ) : '1';
            } elseif ( $field === 'max_referrals_per_user' ) {
                $value = max( 0, (int) $value );
            }

            update_option( $option_key, $value );
        }

        return new WP_REST_Response( array(
            'success'  => true,
            'settings' => array(
                'points_per_referral'    => (int) get_option( '_paired_referral_points_per_referral', 10 ),
                'referral_bonus_type'    => get_option( '_paired_referral_bonus_type', 'points' ),
                'referral_bonus_value'   => (int) get_option( '_paired_referral_bonus_value', 10 ),
                'referral_enabled'       => get_option( '_paired_referral_enabled', '1' ),
                'max_referrals_per_user' => (int) get_option( '_paired_referral_max_per_user', 0 ),
            ),
        ), 200 );
    }

    // ═══════════════════════════════════════════════════════════════
    //  PHASE 3: MEETING SETTINGS & CALENDAR SYNC
    // ═══════════════════════════════════════════════════════════════

    public function get_meeting_settings( WP_REST_Request $request ) {
        $user_id = $this->verify_mentor( $request );
        if ( is_wp_error( $user_id ) ) return $user_id;

        return new WP_REST_Response( array(
            'success'          => true,
            'meeting_provider' => get_user_meta( $user_id, '_paired_meeting_provider', true ) ?: 'custom',
            'custom_url'       => get_user_meta( $user_id, '_paired_meeting_custom_url', true ) ?: '',
            'calendar_sync'    => (bool) get_user_meta( $user_id, '_paired_calendar_sync_enabled', true ),
            'timezone'         => get_user_meta( $user_id, '_paired_timezone', true ) ?: 'Europe/London',
        ), 200 );
    }

    public function update_meeting_settings( WP_REST_Request $request ) {
        $user_id = $this->verify_mentor( $request );
        if ( is_wp_error( $user_id ) ) return $user_id;

        $provider   = sanitize_text_field( $request->get_param( 'meeting_provider' ) );
        $custom_url = esc_url_raw( $request->get_param( 'custom_url' ) );
        $timezone   = sanitize_text_field( $request->get_param( 'timezone' ) );

        $valid_providers = array( 'google_meet', 'zoom', 'teams', 'custom' );
        if ( $provider && in_array( $provider, $valid_providers, true ) ) {
            update_user_meta( $user_id, '_paired_meeting_provider', $provider );
        }
        if ( $custom_url !== null ) {
            update_user_meta( $user_id, '_paired_meeting_custom_url', $custom_url );
        }
        if ( $timezone ) {
            try {
                new DateTimeZone( $timezone );
                update_user_meta( $user_id, '_paired_timezone', $timezone );
            } catch ( Exception $e ) {
                // Invalid timezone, ignore
            }
        }

        return new WP_REST_Response( array( 'success' => true ), 200 );
    }

    public function toggle_calendar_sync( WP_REST_Request $request ) {
        $user_id = $this->verify_mentor( $request );
        if ( is_wp_error( $user_id ) ) return $user_id;

        $enabled = (bool) $request->get_param( 'enabled' );
        update_user_meta( $user_id, '_paired_calendar_sync_enabled', $enabled ? 1 : 0 );

        return new WP_REST_Response( array(
            'success'       => true,
            'calendar_sync' => $enabled,
        ), 200 );
    }

    private function get_google_access_token() {
        $creds_json = get_option( '_paired_google_service_account', '' );
        if ( empty( $creds_json ) ) return null;

        $creds = json_decode( $creds_json, true );
        if ( ! $creds || empty( $creds['client_email'] ) || empty( $creds['private_key'] ) ) return null;

        $now   = time();
        $header  = base64_encode( json_encode( array( 'alg' => 'RS256', 'typ' => 'JWT' ) ) );
        $claims  = base64_encode( json_encode( array(
            'iss'   => $creds['client_email'],
            'scope' => 'https://www.googleapis.com/auth/calendar',
            'aud'   => 'https://oauth2.googleapis.com/token',
            'iat'   => $now,
            'exp'   => $now + 3600,
        ) ) );

        $unsigned = $header . '.' . $claims;
        $signature = '';
        $key = openssl_pkey_get_private( $creds['private_key'] );
        if ( ! $key ) return null;
        openssl_sign( $unsigned, $signature, $key, 'sha256WithRSAEncryption' );

        $jwt = $unsigned . '.' . base64_encode( $signature );

        $token_response = wp_remote_post( 'https://oauth2.googleapis.com/token', array(
            'body' => array(
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion'  => $jwt,
            ),
            'timeout' => 15,
        ) );

        if ( is_wp_error( $token_response ) ) return null;

        $token_data = json_decode( wp_remote_retrieve_body( $token_response ), true );
        return $token_data['access_token'] ?? null;
    }

    private function create_google_calendar_event( $booking_id ) {
        $access_token = $this->get_google_access_token();
        if ( ! $access_token ) return null;

        $mentor_id  = (int) get_post_meta( $booking_id, '_bpu_booking_mentor_id', true );
        $mentee_id  = (int) get_post_meta( $booking_id, '_bpu_booking_mentee_id', true );
        $date       = get_post_meta( $booking_id, '_bpu_booking_date', true );
        $time_slot  = get_post_meta( $booking_id, '_bpu_booking_time_slot', true );
        $session_id = (int) get_post_meta( $booking_id, '_bpu_booking_session_id', true );
        $notes      = get_post_meta( $booking_id, '_bpu_booking_notes', true );

        $mentor = get_userdata( $mentor_id );
        $mentee = get_userdata( $mentee_id );
        $session_name = $session_id ? get_post_meta( $session_id, '_session_name', true ) : 'Mentorship Session';
        $duration     = $session_id ? (int) get_post_meta( $session_id, '_session_duration', true ) : 60;
        $timezone     = get_user_meta( $mentor_id, '_paired_timezone', true ) ?: 'Europe/London';

        $times = explode( '-', $time_slot );
        $start_time = trim( $times[0] ?? '09:00' );
        $start_dt   = $date . 'T' . $start_time . ':00';
        $end_dt_obj = new DateTime( $start_dt, new DateTimeZone( $timezone ) );
        $end_dt_obj->modify( '+' . $duration . ' minutes' );
        $end_dt = $end_dt_obj->format( 'Y-m-d\TH:i:s' );

        $event = array(
            'summary'     => 'PAIRED: ' . $session_name . ' — ' . ( $mentor ? $mentor->display_name : '' ) . ' & ' . ( $mentee ? $mentee->display_name : '' ),
            'description' => ( $notes ? 'Mentee notes: ' . $notes : '' ),
            'start'       => array( 'dateTime' => $start_dt, 'timeZone' => $timezone ),
            'end'         => array( 'dateTime' => $end_dt, 'timeZone' => $timezone ),
            'attendees'   => array_filter( array(
                $mentor ? array( 'email' => $mentor->user_email ) : null,
                $mentee ? array( 'email' => $mentee->user_email ) : null,
            ) ),
            'conferenceData' => array(
                'createRequest' => array(
                    'requestId'             => 'paired-' . $booking_id . '-' . time(),
                    'conferenceSolutionKey' => array( 'type' => 'hangoutsMeet' ),
                ),
            ),
        );

        $response = wp_remote_post(
            'https://www.googleapis.com/calendar/v3/calendars/primary/events?conferenceDataVersion=1&sendUpdates=all',
            array(
                'headers' => array(
                    'Authorization' => 'Bearer ' . $access_token,
                    'Content-Type'  => 'application/json',
                ),
                'body'    => wp_json_encode( $event ),
                'timeout' => 30,
            )
        );

        if ( is_wp_error( $response ) ) return null;

        $result = json_decode( wp_remote_retrieve_body( $response ), true );
        if ( empty( $result['id'] ) ) return null;

        $meet_link = $result['conferenceData']['entryPoints'][0]['uri'] ?? '';

        update_post_meta( $booking_id, '_bpu_booking_calendar_event_id', $result['id'] );
        if ( $meet_link ) {
            update_post_meta( $booking_id, '_bpu_booking_meet_link', $meet_link );
        }

        return $meet_link;
    }

    private function delete_google_calendar_event( $booking_id ) {
        $event_id = get_post_meta( $booking_id, '_bpu_booking_calendar_event_id', true );
        if ( ! $event_id ) return;

        $access_token = $this->get_google_access_token();
        if ( ! $access_token ) return;

        wp_remote_request(
            'https://www.googleapis.com/calendar/v3/calendars/primary/events/' . urlencode( $event_id ) . '?sendUpdates=all',
            array(
                'method'  => 'DELETE',
                'headers' => array( 'Authorization' => 'Bearer ' . $access_token ),
                'timeout' => 15,
            )
        );

        delete_post_meta( $booking_id, '_bpu_booking_calendar_event_id' );
        delete_post_meta( $booking_id, '_bpu_booking_meet_link' );
    }

    private function create_zoom_meeting( $booking_id ) {
        $account_id    = get_option( '_paired_zoom_account_id', '' );
        $client_id     = get_option( '_paired_zoom_client_id', '' );
        $client_secret = get_option( '_paired_zoom_client_secret', '' );

        if ( ! $account_id || ! $client_id || ! $client_secret ) return null;

        $token_response = wp_remote_post( 'https://zoom.us/oauth/token', array(
            'headers' => array(
                'Authorization' => 'Basic ' . base64_encode( $client_id . ':' . $client_secret ),
                'Content-Type'  => 'application/x-www-form-urlencoded',
            ),
            'body' => array(
                'grant_type' => 'account_credentials',
                'account_id' => $account_id,
            ),
            'timeout' => 15,
        ) );

        if ( is_wp_error( $token_response ) ) return null;
        $token_data = json_decode( wp_remote_retrieve_body( $token_response ), true );
        $access_token = $token_data['access_token'] ?? '';
        if ( ! $access_token ) return null;

        $mentor_id  = (int) get_post_meta( $booking_id, '_bpu_booking_mentor_id', true );
        $mentee_id  = (int) get_post_meta( $booking_id, '_bpu_booking_mentee_id', true );
        $date       = get_post_meta( $booking_id, '_bpu_booking_date', true );
        $time_slot  = get_post_meta( $booking_id, '_bpu_booking_time_slot', true );
        $session_id = (int) get_post_meta( $booking_id, '_bpu_booking_session_id', true );
        $mentor     = get_userdata( $mentor_id );
        $mentee     = get_userdata( $mentee_id );
        $session_name = $session_id ? get_post_meta( $session_id, '_session_name', true ) : 'Mentorship Session';
        $duration     = $session_id ? (int) get_post_meta( $session_id, '_session_duration', true ) : 60;
        $timezone     = get_user_meta( $mentor_id, '_paired_timezone', true ) ?: 'Europe/London';

        $times = explode( '-', $time_slot );
        $start_time = trim( $times[0] ?? '09:00' );
        $start_dt   = $date . 'T' . $start_time . ':00';

        $meeting_response = wp_remote_post( 'https://api.zoom.us/v2/users/me/meetings', array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $access_token,
                'Content-Type'  => 'application/json',
            ),
            'body' => wp_json_encode( array(
                'topic'      => 'PAIRED: ' . $session_name . ' — ' . ( $mentor ? $mentor->display_name : '' ) . ' & ' . ( $mentee ? $mentee->display_name : '' ),
                'type'       => 2,
                'start_time' => $start_dt,
                'duration'   => $duration,
                'timezone'   => $timezone,
                'settings'   => array(
                    'join_before_host' => true,
                    'waiting_room'     => false,
                ),
            ) ),
            'timeout' => 30,
        ) );

        if ( is_wp_error( $meeting_response ) ) return null;
        $meeting_data = json_decode( wp_remote_retrieve_body( $meeting_response ), true );
        $join_url = $meeting_data['join_url'] ?? '';

        if ( $join_url ) {
            update_post_meta( $booking_id, '_bpu_booking_meet_link', $join_url );
            update_post_meta( $booking_id, '_bpu_booking_zoom_meeting_id', $meeting_data['id'] ?? '' );
        }

        return $join_url;
    }

    private function generate_meeting_link( $booking_id, $mentor_id ) {
        $provider = get_user_meta( $mentor_id, '_paired_meeting_provider', true ) ?: 'custom';

        switch ( $provider ) {
            case 'google_meet':
                $calendar_sync = get_user_meta( $mentor_id, '_paired_calendar_sync_enabled', true );
                if ( $calendar_sync ) {
                    return $this->create_google_calendar_event( $booking_id );
                }
                return null;

            case 'zoom':
                return $this->create_zoom_meeting( $booking_id );

            case 'teams':
            case 'custom':
                $custom_url = get_user_meta( $mentor_id, '_paired_meeting_custom_url', true );
                if ( $custom_url ) {
                    update_post_meta( $booking_id, '_bpu_booking_meet_link', $custom_url );
                    return $custom_url;
                }
                return null;

            default:
                return null;
        }
    }

    // ═══════════════════════════════════════════════════════════════
    //  PHASE 3: MENTOR ONBOARDING CHECKLIST
    // ═══════════════════════════════════════════════════════════════

    public function get_onboarding_status( WP_REST_Request $request ) {
        $user_id = $this->verify_mentor( $request );
        if ( is_wp_error( $user_id ) ) return $user_id;

        $bio       = get_user_meta( $user_id, 'user_bio', true );
        $industry  = get_user_meta( $user_id, 'industry', true );
        $role      = get_user_meta( $user_id, 'current_role', true );
        $photo     = get_user_meta( $user_id, '_paired_photo_url', true ) ?: get_user_meta( $user_id, 'paired_image_path', true );
        $skills    = get_user_meta( $user_id, 'skills_separate', true );
        $schedule  = get_user_meta( $user_id, '_paired_weekly_schedule', true );
        $stripe_id = get_user_meta( $user_id, '_paired_stripe_account_id', true );
        $kyc       = get_user_meta( $user_id, '_paired_kyc_status', true );

        $sessions_query = new WP_Query( array(
            'post_type'      => 'paired_session',
            'post_status'    => 'publish',
            'posts_per_page' => 1,
            'meta_query'     => array(
                array( 'key' => '_session_mentor_id', 'value' => $user_id ),
            ),
        ) );
        $has_session = $sessions_query->found_posts > 0;
        wp_reset_postdata();

        $skills_arr = is_string( $skills ) ? array_filter( explode( ',', $skills ) ) : ( is_array( $skills ) ? $skills : array() );

        $steps = array(
            array(
                'key'       => 'profile_complete',
                'label'     => 'Complete your profile',
                'completed' => ! empty( $bio ) && ! empty( $industry ) && ! empty( $role ),
                'link'      => '/paired/mentor/settings',
            ),
            array(
                'key'       => 'photo_uploaded',
                'label'     => 'Upload a profile photo',
                'completed' => ! empty( $photo ),
                'link'      => '/paired/mentor/settings',
            ),
            array(
                'key'       => 'session_created',
                'label'     => 'Create a session type',
                'completed' => $has_session,
                'link'      => '/paired/mentor/sessions',
            ),
            array(
                'key'       => 'availability_set',
                'label'     => 'Set your availability',
                'completed' => ! empty( $schedule ),
                'link'      => '/paired/mentor/settings',
            ),
            array(
                'key'       => 'skills_added',
                'label'     => 'Add at least 3 skills',
                'completed' => count( $skills_arr ) >= 3,
                'link'      => '/paired/mentor/settings',
            ),
            array(
                'key'       => 'payout_configured',
                'label'     => 'Connect Stripe for payouts',
                'completed' => ! empty( $stripe_id ),
                'link'      => '/paired/mentor/payout-settings',
                'optional'  => true,
            ),
            array(
                'key'       => 'kyc_verified',
                'label'     => 'Verify your identity (KYC)',
                'completed' => 'approved' === $kyc,
                'link'      => '/paired/mentor/kyc',
                'optional'  => true,
            ),
        );

        $required   = array_filter( $steps, function( $s ) { return empty( $s['optional'] ); } );
        $completed  = count( array_filter( $steps, function( $s ) { return $s['completed']; } ) );
        $total      = count( $steps );
        $req_done   = count( array_filter( $required, function( $s ) { return $s['completed']; } ) );
        $req_total  = count( $required );

        return new WP_REST_Response( array(
            'success'            => true,
            'steps'              => $steps,
            'completed'          => $completed,
            'total'              => $total,
            'percentage'         => $total > 0 ? round( ( $completed / $total ) * 100 ) : 0,
            'required_completed' => $req_done,
            'required_total'     => $req_total,
            'all_required_done'  => $req_done >= $req_total,
        ), 200 );
    }

    public function complete_onboarding_step( WP_REST_Request $request ) {
        $user_id = $this->verify_mentor( $request );
        if ( is_wp_error( $user_id ) ) return $user_id;

        $step = sanitize_text_field( $request->get_param( 'step' ) );
        update_user_meta( $user_id, '_paired_onboarding_' . $step, 1 );

        return new WP_REST_Response( array( 'success' => true, 'step' => $step ), 200 );
    }

    // ═══════════════════════════════════════════════════════════════
    //  PHASE 3: REFERRAL PROGRAMME
    // ═══════════════════════════════════════════════════════════════

    public function get_referral_code( WP_REST_Request $request ) {
        $payload = $this->verify_jwt_bearer( $request );
        if ( ! $payload || empty( $payload['user_id'] ) ) {
            return new WP_Error( 'sso_invalid_token', 'Invalid or missing token.', array( 'status' => 401 ) );
        }
        $user_id = intval( $payload['user_id'] );

        $code = get_user_meta( $user_id, '_paired_referral_code', true );
        if ( empty( $code ) ) {
            $code = 'BPU-' . $user_id . '-' . strtoupper( substr( wp_generate_password( 4, false ), 0, 4 ) );
            update_user_meta( $user_id, '_paired_referral_code', $code );
        }

        return new WP_REST_Response( array(
            'success' => true,
            'code'    => $code,
            'link'    => 'https://pairedbybpu.uk/register?ref=' . urlencode( $code ),
        ), 200 );
    }

    public function get_referral_stats( WP_REST_Request $request ) {
        $payload = $this->verify_jwt_bearer( $request );
        if ( ! $payload || empty( $payload['user_id'] ) ) {
            return new WP_Error( 'sso_invalid_token', 'Invalid or missing token.', array( 'status' => 401 ) );
        }
        $user_id = intval( $payload['user_id'] );

        $query = new WP_Query( array(
            'post_type'      => 'paired_referral',
            'post_status'    => 'publish',
            'posts_per_page' => 100,
            'meta_query'     => array(
                array( 'key' => '_referral_referrer_id', 'value' => $user_id ),
            ),
            'orderby' => 'date',
            'order'   => 'DESC',
        ) );

        $referrals = array();
        $mentor_count = 0;
        $mentee_count = 0;

        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $rid       = get_the_ID();
                $referee_id = (int) get_post_meta( $rid, '_referral_referee_id', true );
                $referee    = get_userdata( $referee_id );
                $is_mentor  = $referee && in_array( 'mentor', (array) $referee->roles, true );
                if ( $is_mentor ) $mentor_count++;
                else $mentee_count++;

                $referrals[] = array(
                    'id'          => $rid,
                    'referee_id'  => $referee_id,
                    'referee_name' => $referee ? $referee->display_name : 'User',
                    'is_mentor'   => $is_mentor,
                    'created_at'  => get_the_date( 'c' ),
                    'status'      => get_post_meta( $rid, '_referral_status', true ) ?: 'registered',
                );
            }
        }
        wp_reset_postdata();

        $points = (int) get_user_meta( $user_id, '_paired_referral_points', true );

        return new WP_REST_Response( array(
            'success'            => true,
            'total_referrals'    => count( $referrals ),
            'successful_mentors' => $mentor_count,
            'successful_mentees' => $mentee_count,
            'points'             => $points,
            'referrals'          => $referrals,
        ), 200 );
    }

    public function apply_referral_code( WP_REST_Request $request ) {
        $code    = sanitize_text_field( $request->get_param( 'code' ) );
        $user_id = absint( $request->get_param( 'user_id' ) );

        if ( empty( $code ) || empty( $user_id ) ) {
            return new WP_Error( 'missing_params', 'Code and user_id are required.', array( 'status' => 400 ) );
        }

        $users = get_users( array(
            'meta_key'   => '_paired_referral_code',
            'meta_value' => $code,
            'number'     => 1,
        ) );

        if ( empty( $users ) ) {
            return new WP_Error( 'invalid_code', 'Referral code not found.', array( 'status' => 404 ) );
        }

        $referrer_id = $users[0]->ID;
        if ( $referrer_id === $user_id ) {
            return new WP_Error( 'self_referral', 'You cannot refer yourself.', array( 'status' => 400 ) );
        }

        $existing = new WP_Query( array(
            'post_type'      => 'paired_referral',
            'post_status'    => 'publish',
            'posts_per_page' => 1,
            'meta_query'     => array(
                array( 'key' => '_referral_referee_id', 'value' => $user_id ),
            ),
        ) );
        if ( $existing->found_posts > 0 ) {
            wp_reset_postdata();
            return new WP_Error( 'already_referred', 'This user was already referred.', array( 'status' => 409 ) );
        }
        wp_reset_postdata();

        $referee = get_userdata( $user_id );
        $post_id = wp_insert_post( array(
            'post_type'   => 'paired_referral',
            'post_title'  => sprintf( 'Referral: %s → %s', $users[0]->display_name, $referee ? $referee->display_name : 'User #' . $user_id ),
            'post_status' => 'publish',
            'post_author' => $referrer_id,
        ), true );

        if ( is_wp_error( $post_id ) ) return $post_id;

        update_post_meta( $post_id, '_referral_referrer_id', $referrer_id );
        update_post_meta( $post_id, '_referral_referee_id', $user_id );
        update_post_meta( $post_id, '_referral_code', $code );
        update_post_meta( $post_id, '_referral_status', 'registered' );

        $current_points = (int) get_user_meta( $referrer_id, '_paired_referral_points', true );
        update_user_meta( $referrer_id, '_paired_referral_points', $current_points + 10 );

        update_user_meta( $user_id, '_paired_referred_by', $referrer_id );

        $this->create_notification(
            $referrer_id,
            'referral',
            'New Referral',
            ( $referee ? $referee->display_name : 'Someone' ) . ' signed up using your referral code!',
            '/paired/referral'
        );

        return new WP_REST_Response( array( 'success' => true, 'referral_id' => $post_id ), 200 );
    }

    public function admin_get_referrals( WP_REST_Request $request ) {
        $payload = $this->verify_jwt_bearer( $request );
        if ( ! $payload || empty( $payload['user_id'] ) ) {
            return new WP_Error( 'sso_invalid_token', 'Invalid or missing token.', array( 'status' => 401 ) );
        }

        $query = new WP_Query( array(
            'post_type'      => 'paired_referral',
            'post_status'    => 'publish',
            'posts_per_page' => 200,
            'orderby'        => 'date',
            'order'          => 'DESC',
        ) );

        $referrals = array();
        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $rid         = get_the_ID();
                $referrer_id = (int) get_post_meta( $rid, '_referral_referrer_id', true );
                $referee_id  = (int) get_post_meta( $rid, '_referral_referee_id', true );
                $referrer    = get_userdata( $referrer_id );
                $referee     = get_userdata( $referee_id );

                $referrals[] = array(
                    'id'            => $rid,
                    'referrer_id'   => $referrer_id,
                    'referrer_name' => $referrer ? $referrer->display_name : 'Unknown',
                    'referee_id'    => $referee_id,
                    'referee_name'  => $referee ? $referee->display_name : 'Unknown',
                    'code'          => get_post_meta( $rid, '_referral_code', true ),
                    'status'        => get_post_meta( $rid, '_referral_status', true ),
                    'created_at'    => get_the_date( 'c' ),
                );
            }
        }
        wp_reset_postdata();

        return new WP_REST_Response( array(
            'success'   => true,
            'total'     => count( $referrals ),
            'referrals' => $referrals,
        ), 200 );
    }

    // ═══════════════════════════════════════════════════════════════
    //  PHASE 3: KYC VERIFICATION
    // ═══════════════════════════════════════════════════════════════

    public function kyc_submit( WP_REST_Request $request ) {
        $user_id = $this->verify_mentor( $request );
        if ( is_wp_error( $user_id ) ) return $user_id;

        $current_status = get_user_meta( $user_id, '_paired_kyc_status', true );
        if ( 'approved' === $current_status ) {
            return new WP_Error( 'already_approved', 'Your identity has already been verified.', array( 'status' => 400 ) );
        }

        if ( ! function_exists( 'wp_handle_upload' ) ) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
        }

        $uploaded_files = array();
        $file_keys = array( 'id_front', 'id_back' );

        foreach ( $file_keys as $key ) {
            if ( ! empty( $_FILES[ $key ] ) && $_FILES[ $key ]['error'] === UPLOAD_ERR_OK ) {
                $allowed_types = array( 'image/jpeg', 'image/png', 'image/webp', 'application/pdf' );
                if ( ! in_array( $_FILES[ $key ]['type'], $allowed_types, true ) ) {
                    return new WP_Error( 'invalid_file_type', 'Only JPEG, PNG, WebP, and PDF files are accepted.', array( 'status' => 400 ) );
                }

                if ( $_FILES[ $key ]['size'] > 10 * 1024 * 1024 ) {
                    return new WP_Error( 'file_too_large', 'File must be under 10MB.', array( 'status' => 400 ) );
                }

                $upload = wp_handle_upload( $_FILES[ $key ], array(
                    'test_form' => false,
                    'unique_filename_callback' => function( $dir, $name, $ext ) use ( $user_id, $key ) {
                        return 'kyc-' . $user_id . '-' . $key . '-' . time() . $ext;
                    },
                ) );

                if ( ! empty( $upload['error'] ) ) {
                    return new WP_Error( 'upload_failed', $upload['error'], array( 'status' => 500 ) );
                }

                $uploaded_files[ $key ] = $upload['url'];
            }
        }

        if ( empty( $uploaded_files['id_front'] ) && empty( get_user_meta( $user_id, '_paired_kyc_doc_front', true ) ) ) {
            return new WP_Error( 'missing_document', 'At least the front of your ID document is required.', array( 'status' => 400 ) );
        }

        if ( ! empty( $uploaded_files['id_front'] ) ) {
            update_user_meta( $user_id, '_paired_kyc_doc_front', $uploaded_files['id_front'] );
        }
        if ( ! empty( $uploaded_files['id_back'] ) ) {
            update_user_meta( $user_id, '_paired_kyc_doc_back', $uploaded_files['id_back'] );
        }

        update_user_meta( $user_id, '_paired_kyc_status', 'pending' );
        update_user_meta( $user_id, '_paired_kyc_submitted_at', current_time( 'mysql' ) );

        $admins = get_users( array( 'role' => 'administrator' ) );
        $user   = get_userdata( $user_id );
        foreach ( $admins as $admin ) {
            $this->create_notification(
                $admin->ID,
                'kyc',
                'KYC Review Required',
                ( $user ? $user->display_name : 'A mentor' ) . ' has submitted identity documents for verification.',
                '/paired/admin/kyc'
            );
        }

        return new WP_REST_Response( array(
            'success' => true,
            'status'  => 'pending',
            'message' => 'Documents submitted. We will review within 48 hours.',
        ), 200 );
    }

    public function kyc_get_status( WP_REST_Request $request ) {
        $user_id = $this->verify_mentor( $request );
        if ( is_wp_error( $user_id ) ) return $user_id;

        $status = get_user_meta( $user_id, '_paired_kyc_status', true ) ?: 'not_submitted';

        return new WP_REST_Response( array(
            'success'          => true,
            'status'           => $status,
            'submitted_at'     => get_user_meta( $user_id, '_paired_kyc_submitted_at', true ) ?: null,
            'reviewed_at'      => get_user_meta( $user_id, '_paired_kyc_reviewed_at', true ) ?: null,
            'rejection_reason' => get_user_meta( $user_id, '_paired_kyc_rejection_reason', true ) ?: null,
        ), 200 );
    }

    public function admin_list_kyc( WP_REST_Request $request ) {
        $payload = $this->verify_jwt_bearer( $request );
        if ( ! $payload || empty( $payload['user_id'] ) ) {
            return new WP_Error( 'sso_invalid_token', 'Invalid or missing token.', array( 'status' => 401 ) );
        }

        $status_filter = sanitize_text_field( $request->get_param( 'status' ) ) ?: 'pending';

        $users = get_users( array(
            'meta_key'   => '_paired_kyc_status',
            'meta_value' => $status_filter,
            'role'       => 'mentor',
            'number'     => 100,
            'orderby'    => 'registered',
            'order'      => 'DESC',
        ) );

        $submissions = array();
        foreach ( $users as $u ) {
            $submissions[] = array(
                'user_id'       => $u->ID,
                'display_name'  => $u->display_name,
                'email'         => $u->user_email,
                'doc_front'     => get_user_meta( $u->ID, '_paired_kyc_doc_front', true ) ?: null,
                'doc_back'      => get_user_meta( $u->ID, '_paired_kyc_doc_back', true ) ?: null,
                'status'        => $status_filter,
                'submitted_at'  => get_user_meta( $u->ID, '_paired_kyc_submitted_at', true ) ?: null,
            );
        }

        return new WP_REST_Response( array(
            'success'     => true,
            'total'       => count( $submissions ),
            'submissions' => $submissions,
        ), 200 );
    }

    public function admin_review_kyc( WP_REST_Request $request ) {
        $payload = $this->verify_jwt_bearer( $request );
        if ( ! $payload || empty( $payload['user_id'] ) ) {
            return new WP_Error( 'sso_invalid_token', 'Invalid or missing token.', array( 'status' => 401 ) );
        }

        $mentor_id = absint( $request->get_param( 'id' ) );
        $status    = sanitize_text_field( $request->get_param( 'status' ) );
        $reason    = sanitize_textarea_field( $request->get_param( 'rejection_reason' ) );

        if ( ! in_array( $status, array( 'approved', 'rejected' ), true ) ) {
            return new WP_Error( 'invalid_status', 'Status must be approved or rejected.', array( 'status' => 400 ) );
        }

        $mentor = get_userdata( $mentor_id );
        if ( ! $mentor ) {
            return new WP_Error( 'user_not_found', 'Mentor not found.', array( 'status' => 404 ) );
        }

        update_user_meta( $mentor_id, '_paired_kyc_status', $status );
        update_user_meta( $mentor_id, '_paired_kyc_reviewed_at', current_time( 'mysql' ) );

        if ( 'rejected' === $status && $reason ) {
            update_user_meta( $mentor_id, '_paired_kyc_rejection_reason', $reason );
        } else {
            delete_user_meta( $mentor_id, '_paired_kyc_rejection_reason' );
        }

        if ( 'approved' === $status ) {
            $heading   = 'Identity Verified!';
            $body_html = '<p style="color:#333;font-size:15px;line-height:1.6;">Your identity has been verified. Your profile now displays a verification badge.</p>';
            $body_html .= '<p style="margin-top:24px;"><a href="https://pairedbybpu.uk/paired/mentor/settings" style="display:inline-block;background:#7c3aed;color:#fff;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:600;">View my profile</a></p>';

            $this->create_notification( $mentor_id, 'kyc', 'Identity Verified', 'Your identity has been verified! A badge is now displayed on your profile.', '/paired/mentor/settings' );
        } else {
            $heading   = 'KYC Review Update';
            $body_html = '<p style="color:#333;font-size:15px;line-height:1.6;">Unfortunately, we were unable to verify your identity at this time.</p>';
            if ( $reason ) {
                $body_html .= '<p style="color:#555;font-size:14px;">Reason: ' . esc_html( $reason ) . '</p>';
            }
            $body_html .= '<p style="color:#555;font-size:14px;">You can resubmit your documents for review.</p>';
            $body_html .= '<p style="margin-top:24px;"><a href="https://pairedbybpu.uk/paired/mentor/kyc" style="display:inline-block;background:#C8102E;color:#fff;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:600;">Resubmit documents</a></p>';

            $this->create_notification( $mentor_id, 'kyc', 'KYC Review', 'Your identity verification was not approved. Please resubmit your documents.', '/paired/mentor/kyc' );
        }

        wp_mail(
            $mentor->user_email,
            $heading . ' — PAIRED by BPU',
            $this->build_email_html( $heading, $body_html ),
            array( 'Content-Type: text/html; charset=UTF-8' )
        );

        return new WP_REST_Response( array( 'success' => true, 'status' => $status ), 200 );
    }
}

// Initialize the connector
new BPU_Headless_Connector();

// Schedule the weekly digest on activation; clear on deactivation.
register_activation_hook( __FILE__, 'bpu_schedule_weekly_digest' );
register_deactivation_hook( __FILE__, 'bpu_unschedule_weekly_digest' );

function bpu_schedule_weekly_digest() {
    if ( ! wp_next_scheduled( 'bpu_weekly_job_digest' ) ) {
        wp_schedule_event( strtotime( 'next Monday 08:00:00' ), 'weekly', 'bpu_weekly_job_digest' );
    }
}

function bpu_unschedule_weekly_digest() {
    wp_clear_scheduled_hook( 'bpu_weekly_job_digest' );
}
