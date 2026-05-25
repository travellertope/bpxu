<?php
/**
 * Plugin Name: BPU Headless Connector
 * Plugin URI: https://blackprofessionals.uk
 * Description: Custom Headless API connector for Black Professionals United (BPU). Provides Cross-Subdomain SSO verification, headless Job Board Click Tracking, and headless Tutor LMS progress triggers.
 * Version: 1.0.0
 * Author: Antigravity AI & BPU Tech Team
 * Author URI: https://blackprofessionals.uk
 * License: GPL2
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class BPU_Headless_Connector {

    private $namespace = 'bpu/v1';

    public function __construct() {
        // Register API routes
        add_action( 'rest_api_init', array( $this, 'register_api_routes' ) );
        
        // CORS and Cookie domain reminders in Admin Panel
        add_action( 'admin_notices', array( $this, 'display_cookie_domain_check' ) );
    }

    /**
     * Register Custom REST API routes
     */
    public function register_api_routes() {
        
        // 1. SSO Session Verification
        register_rest_route( $this->namespace, '/sso/validate', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( $this, 'validate_sso_session' ),
            'permission_callback' => '__return_true', // Core cookie validation happens internally
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
            'permission_callback' => array( $this, 'check_auth_permissions' ), // Only authenticated members can trigger course progress
            'args'                => array(
                'course_id' => array(
                    'required'          => true,
                    'sanitize_callback' => 'absint',
                ),
            ),
        ) );
    }

    /**
     * 1. Validate SSO Session
     * Automatically uses the root-scoped cookie header (.blackprofessionals.uk) 
     * forwarded by Next.js to authenticate and return rich profile details.
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

        // Dynamically fetch all ACF profile fields saved under 'user_{ID}'
        if ( function_exists( 'get_fields' ) ) {
            $fields = get_fields( 'user_' . $user_id );
            if ( $fields ) {
                $acf_profile = $fields;
            }
        }

        return new WP_REST_Response( array(
            'authenticated' => true,
            'user'          => array(
                'id'           => $user_id,
                'username'     => $current_user->user_login,
                'email'        => $current_user->user_email,
                'display_name' => $current_user->display_name,
                'roles'        => $current_user->roles,
                'profile'      => $acf_profile,
            )
        ), 200 );
    }

    /**
     * 2. Headless Job Click Tracker
     * Intercepts and records clicks on external job links from Next.js, 
     * writing directly to both WP postmeta and custom tracking DB tables.
     */
    public function track_job_click( WP_REST_Request $request ) {
        global $wpdb;
        $job_id  = $request->get_param( 'job_id' );
        $user_id = $request->get_param( 'user_id' );
        $ip_addr = $_SERVER['REMOTE_ADDR'];

        // Verify the post type is a valid job listing
        $post_type = get_post_type( $job_id );
        if ( 'job_listing' !== $post_type && 'post' !== $post_type ) {
            return new WP_Error(
                'rest_invalid_job_id',
                __( 'Invalid Job Listing ID.', 'bpu' ),
                array( 'status' => 400 )
            );
        }

        // Universal Method 1: Increment post meta click counts
        $current_clicks = (int) get_post_meta( $job_id, '_click_count', true );
        update_post_meta( $job_id, '_click_count', $current_clicks + 1 );

        // Universal Method 2: Check for known custom click tracker DB tables
        // Mapped to standard click trackers (like WP Job Manager Stats or custom click logs)
        $clicks_table = $wpdb->prefix . 'job_manager_clicks'; // Check default table
        $alt_table    = $wpdb->prefix . 'job_clicks';          // Alternate popular schema
        
        $inserted = false;
        
        if ( $wpdb->get_var( "SHOW TABLES LIKE '$clicks_table'" ) === $clicks_table ) {
            // Write to legacy stats table
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
            // Write to custom click table
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
     * 3. Tutor LMS Course Progress Trigger
     * Enrolls member and triggers progress state in Tutor LMS when external course links are clicked.
     */
    public function track_course_progress( WP_REST_Request $request ) {
        $course_id = $request->get_param( 'course_id' );
        $user_id   = wp_get_current_user()->ID;

        // Verify Tutor LMS is active
        if ( ! function_exists( 'tutor' ) || ! function_exists( 'tutor_utils' ) ) {
            return new WP_Error(
                'tutor_lms_inactive',
                __( 'Tutor LMS is not active on the WordPress backend.', 'bpu' ),
                array( 'status' => 500 )
            );
        }

        // 1. Enroll user in Tutor LMS course if not already enrolled
        $is_enrolled = tutor_utils()->is_enrolled_to_course( $course_id, $user_id );
        
        if ( ! $is_enrolled ) {
            // Tutor LMS Native Enrollment Call
            tutor_utils()->do_enroll( $course_id, $user_id );
        }

        // 2. Set course starting tracking progress
        // tutor_utils() handles database enrollment logging which we trigger natively
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
     * Helper permission check for authenticated API requests
     */
    public function check_auth_permissions() {
        return wp_get_current_user()->exists();
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
define( 'SITECOOKIEPATH', '/' );</pre>
            </div>
            <?php
        }
    }
}

// Initialize the connector
new BPU_Headless_Connector();
