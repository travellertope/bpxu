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

        $prompt = "You are a professional CV Parser. Extract details from this PDF CV.
You MUST output a strictly valid JSON object matching these ACF profile keys:
- first_name: string (e.g. \"Segun\")
- last_name: string (e.g. \"Odumoye\")
- phone_number: string
- current_employment_status: MUST match one of the exact choices [Employed Full-Time, Employed Part-Time, Self-employed, Not employed but looking for work, Student]
- level_of_education: MUST match one of the exact choices [High School, Bachelor's Degree, Masters Degree, PhD, Other]
- industry: MUST match one of the choices [Information Technology, Legal, Banking & Financial Services, Engineering, Healthcare, Sales & Retail]
- industryfield_of_expertise: MUST match one of the choices [Technology, Finance and Accounting, Legal, Human Resources, Engineering, Sciences (STEM)]
- years_of_experience: MUST match one of [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11-15, 16-20, 20+]
- skills_separate: comma-separated list of major technical or business skills
- user_bio: One-paragraph professional summary profile based on the resume.

Output ONLY the raw JSON object. Do not include markdown wraps or backticks.";

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
     * Map parsed Gemini keys back to WordPress ACF fields (referencing acf_fields.json configurations)
     */
    private function update_member_acf_profile( $user_id, $data ) {
        if ( ! function_exists( 'update_field' ) ) {
            // Fallback to standard WordPress usermeta if ACF is missing
            foreach ( $data as $key => $value ) {
                update_user_meta( $user_id, $key, $value );
            }
            return;
        }

        // Map keys programmatically to the User profile fields
        $selector = 'user_' . $user_id;

        update_field( 'first_name', sanitize_text_field( $data['first_name'] ), $selector );
        update_field( 'last_name', sanitize_text_field( $data['last_name'] ), $selector );
        update_field( 'phone_number', sanitize_text_field( $data['phone_number'] ), $selector );
        update_field( 'current_employment_status', sanitize_text_field( $data['current_employment_status'] ), $selector );
        update_field( 'level_of_education', sanitize_text_field( $data['level_of_education'] ), $selector );
        update_field( 'industry', sanitize_text_field( $data['industry'] ), $selector );
        update_field( 'industryfield_of_expertise', sanitize_text_field( $data['industryfield_of_expertise'] ), $selector );
        update_field( 'years_of_experience', sanitize_text_field( $data['years_of_experience'] ), $selector );
        update_field( 'skills_separate', sanitize_text_field( $data['skills_separate'] ), $selector );
        update_field( 'user_bio', sanitize_textarea_field( $data['user_bio'] ), $selector );
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

        return new WP_REST_Response( array(
            'profile' => $acf_profile,
            'cv_url'  => $cv_url,
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
