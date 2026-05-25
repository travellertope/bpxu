<?php
/**
 * Plugin Name: BPU Headless Connector
 * Plugin URI: https://blackprofessionals.uk
 * Description: Custom Headless API connector for Black Professionals United (BPU). Provides Cross-Subdomain SSO verification, headless Job Board Click Tracking, headless Tutor LMS progress triggers, Gemini Pro AI CV parsing, and CV Clinic manual reviews dashboard.
 * Version: 1.1.0
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
        // Register custom post types
        add_action( 'init', array( $this, 'register_cv_review_post_type' ) );

        // Register API routes
        add_action( 'rest_api_init', array( $this, 'register_api_routes' ) );
        
        // CORS and Cookie domain reminders in Admin Panel
        add_action( 'admin_notices', array( $this, 'display_cookie_domain_check' ) );

        // Hook when manual CV review is published to trigger alerts
        add_action( 'publish_cv_clinic_review', array( $this, 'notify_candidate_of_cv_review' ), 10, 2 );
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

        // 4. CV Upload & Gemini Pro Autofill parser
        register_rest_route( $this->namespace, '/member/cv-upload', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array( $this, 'handle_member_cv_upload' ),
            'permission_callback' => array( $this, 'check_auth_permissions' ),
        ) );

        // 5. Get Member CV Reviews List
        register_rest_route( $this->namespace, '/member/cv-reviews', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( $this, 'get_member_cv_reviews' ),
            'permission_callback' => array( $this, 'check_auth_permissions' ),
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
     * 4. CV Upload & Gemini Pro Parser
     * Handles file uploads securely and calls Vertex AI (Gemini Pro) to parser the PDF.
     */
    public function handle_member_cv_upload( WP_REST_Request $request ) {
        $user_id = wp_get_current_user()->ID;

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
        $user_id = wp_get_current_user()->ID;

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
