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

        // Register bpu_pro role
        add_action( 'init', array( $this, 'register_pro_role' ) );

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

    /**
     * Register the bpu_pro role (idempotent).
     */
    public function register_pro_role() {
        if ( ! get_role( 'bpu_pro' ) ) {
            add_role( 'bpu_pro', __( 'BPU Pro Member', 'bpu' ), array( 'read' => true ) );
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
                'per_page' => array( 'default' => 12, 'sanitize_callback' => 'absint' ),
                'page'     => array( 'default' => 1,  'sanitize_callback' => 'absint' ),
            ),
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

        register_rest_route( $this->namespace, '/paired/mentor-apply', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array( $this, 'submit_mentor_application' ),
            'permission_callback' => array( $this, 'check_jwt_bearer_auth' ),
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
     * GET /courses — Paginated course listing sourced directly from Tutor LMS post type.
     * Works regardless of whether Tutor LMS exposes its own REST routes.
     */
    public function get_courses( WP_REST_Request $request ) {
        $per_page = min( 50, max( 1, $request->get_param( 'per_page' ) ) );
        $page     = max( 1, $request->get_param( 'page' ) );

        $query = new WP_Query( array(
            'post_type'      => 'courses',   // Tutor LMS post type
            'post_status'    => 'publish',
            'posts_per_page' => $per_page,
            'paged'          => $page,
            'orderby'        => 'date',
            'order'          => 'DESC',
        ) );

        $courses = array();

        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $post_id = get_the_ID();
                $courses[] = array(
                    'id'            => $post_id,
                    'title'         => get_the_title(),
                    'excerpt'       => wp_strip_all_tags( get_the_excerpt() ),
                    'provider'      => get_post_meta( $post_id, '_tutor_course_instructor', true )
                        ? get_userdata( (int) get_post_meta( $post_id, '_tutor_course_instructor', true ) )->display_name ?? 'BPU Partner'
                        : 'BPU Partner',
                    'category'      => wp_get_post_terms( $post_id, 'course-category', array( 'fields' => 'names' ) )[0] ?? 'Professional Development',
                    'learn_more_url' => get_the_permalink(),
                    'image'         => get_the_post_thumbnail_url( $post_id, 'medium' ) ?: '',
                    'duration'      => get_post_meta( $post_id, '_course_duration', true ) ?: '',
                    'level'         => get_post_meta( $post_id, '_tutor_course_level', true ) ?: '',
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
     * ATS-style CV analysis via Gemini (returns score, strengths, weaknesses, recommendation).
     */
    private function analyze_cv_with_gemini( $base64_pdf, $target_role, $job_description = '' ) {
        $api_key = defined( 'GEMINI_API_KEY' ) ? GEMINI_API_KEY : get_option( 'bpu_gemini_api_key', '' );

        if ( empty( $api_key ) ) {
            return new WP_Error( 'gemini_missing_api_key', __( 'Gemini API Key is not configured.', 'bpu' ), array( 'status' => 500 ) );
        }

        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $api_key;

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
            'generationConfig' => array(
                'responseMimeType' => 'application/json',
            ),
        );

        $response = wp_remote_post( $url, array(
            'headers' => array( 'Content-Type' => 'application/json' ),
            'body'    => wp_json_encode( $body ),
            'timeout' => 90,
        ) );

        if ( is_wp_error( $response ) ) {
            return new WP_Error( 'gemini_request_failed', $response->get_error_message(), array( 'status' => 502 ) );
        }

        $status = wp_remote_retrieve_response_code( $response );
        $raw    = wp_remote_retrieve_body( $response );

        if ( 200 !== $status ) {
            return new WP_Error( 'gemini_api_error', 'Gemini returned status ' . $status, array( 'status' => 502 ) );
        }

        $data = json_decode( $raw, true );
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
     * Parse base64 PDF CV directly via Google Gemini Pro Multimodal API
     */
    private function parse_cv_with_gemini( $base64_pdf ) {
        // Retrieve Gemini/Vertex API Key defined as a constant
        $api_key = defined( 'GEMINI_API_KEY' ) ? GEMINI_API_KEY : get_option( 'bpu_gemini_api_key', '' );

        if ( empty( $api_key ) ) {
            return new WP_Error(
                'gemini_missing_api_key',
                __( 'Gemini API Key is not configured on the WordPress host.', 'bpu' ),
                array( 'status' => 500 )
            );
        }

        // Gemini Multimodal API URL
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-pro:generateContent?key=' . $api_key;

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
                        array(
                            'text' => $prompt
                        ),
                        array(
                            'inlineData' => array(
                                'mimeType' => 'application/pdf',
                                'data'     => $base64_pdf
                            )
                        )
                    )
                )
            ),
            'generationConfig' => array(
                'responseMimeType' => 'application/json' // Forces JSON outputs
            )
        );

        $response = wp_safe_remote_post( $url, array(
            'headers' => array( 'Content-Type' => 'application/json' ),
            'body'    => wp_json_encode( $body ),
            'timeout' => 45, // CV parsing can take some time
        ) );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        $response_code = wp_remote_retrieve_response_code( $response );
        $response_body = wp_remote_retrieve_body( $response );

        if ( 200 !== $response_code ) {
            return new WP_Error(
                'gemini_api_error',
                __( 'Gemini API returned error: ' . $response_body, 'bpu' ),
                array( 'status' => 500 )
            );
        }

        $data = json_decode( $response_body, true );
        
        if ( ! isset( $data['candidates'][0]['content']['parts'][0]['text'] ) ) {
            return new WP_Error(
                'gemini_invalid_response',
                __( 'Invalid response format returned by AI engine.', 'bpu' ),
                array( 'status' => 500 )
            );
        }

        $raw_json_text = $data['candidates'][0]['content']['parts'][0]['text'];
        $parsed_data = json_decode( trim( $raw_json_text ), true );

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
        // work_experiences → bpu_experiences (same key used by the PAIRED importer)
        if ( ! empty( $data['work_experiences'] ) && is_array( $data['work_experiences'] ) ) {
            $experiences = array_map( function( $e ) {
                return [
                    'title'        => sanitize_text_field( $e['title']       ?? '' ),
                    'company'      => sanitize_text_field( $e['company']     ?? '' ),
                    'start_date'   => sanitize_text_field( $e['start_date']  ?? '' ),
                    'end_date'     => sanitize_text_field( $e['end_date']    ?? '' ),
                    'is_present'   => ! empty( $e['is_current'] ) ? '1' : '0',
                    'contribution' => sanitize_textarea_field( $e['description'] ?? '' ),
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

        // Build user profile payload
        $acf_profile = array();
        if ( function_exists( 'get_fields' ) ) {
            $fields = get_fields( 'user_' . $user_id );
            if ( $fields ) {
                $acf_profile = $fields;
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

        $acf_profile = array();
        if ( function_exists( 'get_fields' ) ) {
            $fields = get_fields( 'user_' . $user_id );
            if ( $fields ) {
                $acf_profile = $fields;
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
     * Format a mentor user for list responses (summary).
     */
    private function format_mentor_summary( WP_User $user ) {
        $acf = $this->get_mentor_acf_fields( $user->ID );

        return array(
            'id'                        => $user->ID,
            'display_name'              => $user->display_name,
            'avatar_url'                => get_avatar_url( $user->ID, array( 'size' => 256 ) ),
            'industry'                  => isset( $acf['industry'] ) ? $acf['industry'] : '',
            'years_of_experience'       => isset( $acf['years_of_experience'] ) ? $acf['years_of_experience'] : '',
            'skills_separate'           => isset( $acf['skills_separate'] ) ? $acf['skills_separate'] : '',
            'user_bio'                  => isset( $acf['user_bio'] ) ? $acf['user_bio'] : '',
            'industryfield_of_expertise' => isset( $acf['industryfield_of_expertise'] ) ? $acf['industryfield_of_expertise'] : '',
        );
    }

    /**
     * Format a mentor user for detail responses (full profile).
     */
    private function format_mentor_detail( WP_User $user ) {
        $acf = $this->get_mentor_acf_fields( $user->ID );

        // Availability data (stored as user meta)
        $availability = get_user_meta( $user->ID, '_bpu_mentor_availability', true );

        return array(
            'id'                        => $user->ID,
            'display_name'              => $user->display_name,
            'username'                  => $user->user_login,
            'avatar_url'                => get_avatar_url( $user->ID, array( 'size' => 512 ) ),
            'profile'                   => $acf,
            'availability'              => ! empty( $availability ) ? $availability : array(),
            'registered_date'           => $user->user_registered,
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
                'level_of_education',
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
        $user_id   = intval( $payload['user_id'] );
        $mentor_id = $request->get_param( 'mentor_id' );
        $date      = $request->get_param( 'date' );
        $time_slot = $request->get_param( 'time_slot' );
        $notes     = $request->get_param( 'notes' );

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

        // Check for duplicate bookings (same mentee + mentor + date + time)
        $duplicate_check = new WP_Query( array(
            'post_type'   => 'mentorship_booking',
            'post_status' => array( 'publish', 'pending' ),
            'meta_query'  => array(
                'relation' => 'AND',
                array( 'key' => '_bpu_booking_mentee_id', 'value' => $user_id, 'compare' => '=' ),
                array( 'key' => '_bpu_booking_mentor_id', 'value' => $mentor_id, 'compare' => '=' ),
                array( 'key' => '_bpu_booking_date', 'value' => $date, 'compare' => '=' ),
                array( 'key' => '_bpu_booking_time_slot', 'value' => $time_slot, 'compare' => '=' ),
            ),
        ) );

        if ( $duplicate_check->found_posts > 0 ) {
            wp_reset_postdata();
            return new WP_Error(
                'booking_duplicate',
                __( 'You already have a booking for this slot.', 'bpu' ),
                array( 'status' => 409 )
            );
        }
        wp_reset_postdata();

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
        update_post_meta( $post_id, '_bpu_booking_date', $date );
        update_post_meta( $post_id, '_bpu_booking_time_slot', $time_slot );
        update_post_meta( $post_id, '_bpu_booking_notes', $notes );
        update_post_meta( $post_id, '_bpu_booking_status', 'pending' );
        update_post_meta( $post_id, '_bpu_booking_created_at', current_time( 'mysql' ) );

        $this->send_booking_emails( $mentee, $mentor, $date, $time_slot, $notes );

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
        );
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
            'capability_type'    => 'post',
            'has_archive'        => false,
            'hierarchical'       => false,
            'menu_position'      => 26,
            'menu_icon'          => 'dashicons-portfolio',
            'supports'           => array( 'title', 'editor', 'custom-fields', 'author' ),
            'show_in_rest'       => false,
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
        foreach ( $meta as $key => $value ) {
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
            'description' => (string) $gm( 'description' ),
        );
    }

    private function format_job_for_api( $post ) {
        $get = function ( $key ) use ( $post ) {
            return get_post_meta( $post->ID, $key, true );
        };
        $questions = maybe_unserialize( $get( '_bpu_screening_questions' ) );

        // Resolve bpu_employer taxonomy term
        $employer_terms = wp_get_post_terms( $post->ID, 'bpu_employer', array( 'fields' => 'ids' ) );
        $employer_term_id = ( ! is_wp_error( $employer_terms ) && ! empty( $employer_terms ) ) ? (int) $employer_terms[0] : 0;
        $employer = $this->get_employer_data( $employer_term_id );

        // Fall back to plain meta for company name if no taxonomy term yet
        $company_name = $employer ? $employer['name'] : (string) $get( '_bpu_company' );

        return array(
            'id'                  => $post->ID,
            'title'               => $post->post_title,
            'slug'                => $post->post_name,
            'description'         => $post->post_content,
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
        $per_page  = min( 50, max( 1, intval( $request->get_param( 'per_page' ) ?: 20 ) ) );
        $page      = max( 1, intval( $request->get_param( 'page' ) ?: 1 ) );
        $job_type  = sanitize_text_field( $request->get_param( 'job_type' ) ?: '' );
        $industry  = sanitize_text_field( $request->get_param( 'industry' ) ?: '' );
        $search    = sanitize_text_field( $request->get_param( 'search' ) ?: '' );

        $args = array(
            'post_type'      => 'bpu_job',
            'post_status'    => 'publish',
            'posts_per_page' => $per_page,
            'paged'          => $page,
            'orderby'        => 'date',
            'order'          => 'DESC',
        );

        $meta_query = array( 'relation' => 'AND' );
        if ( $job_type && in_array( $job_type, array( 'inbound', 'outbound' ), true ) ) {
            $meta_query[] = array( 'key' => '_bpu_job_type', 'value' => $job_type );
        }
        if ( $industry ) {
            $meta_query[] = array( 'key' => '_bpu_industry', 'value' => $industry, 'compare' => 'LIKE' );
        }
        if ( count( $meta_query ) > 1 ) {
            $args['meta_query'] = $meta_query;
        }
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
