<?php
/**
 * Black Professionals Ireland theme functions.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'BPU_IE_VERSION', '1.0.0' );
define( 'BPU_IE_DIR', get_template_directory() );
define( 'BPU_IE_URI', get_template_directory_uri() );

/**
 * Theme setup.
 */
function bpu_ie_setup() {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'custom-logo', array(
        'height'      => 60,
        'width'       => 200,
        'flex-height' => true,
        'flex-width'  => true,
    ) );
    add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'script', 'style' ) );
    add_theme_support( 'automatic-feed-links' );

    register_nav_menus( array(
        'primary' => __( 'Primary Menu', 'bpu-ireland' ),
        'footer'  => __( 'Footer Menu', 'bpu-ireland' ),
    ) );
}
add_action( 'after_setup_theme', 'bpu_ie_setup' );

/**
 * Enqueue styles and scripts.
 */
function bpu_ie_assets() {
    wp_enqueue_style( 'bpu-ie-style', get_stylesheet_uri(), array(), BPU_IE_VERSION );
    wp_enqueue_script( 'bpu-ie-main', BPU_IE_URI . '/assets/js/main.js', array(), BPU_IE_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'bpu_ie_assets' );

/**
 * Includes.
 */
require BPU_IE_DIR . '/inc/custom-post-types.php';
require BPU_IE_DIR . '/inc/acf-fields.php';
require BPU_IE_DIR . '/inc/template-tags.php';
require BPU_IE_DIR . '/inc/contact-form.php';
require BPU_IE_DIR . '/inc/membership-form.php';

/**
 * Admin notice if ACF isn't active — most editable content on this theme's
 * templates (hero text, benefits, team members, events) is stored in ACF
 * fields, matching the field-group pattern already used across the BPU
 * WordPress plugins in this org.
 */
function bpu_ie_acf_notice() {
    if ( ! function_exists( 'acf_add_local_field_group' ) && current_user_can( 'activate_plugins' ) ) {
        echo '<div class="notice notice-warning"><p><strong>Black Professionals Ireland theme:</strong> Advanced Custom Fields (ACF) is not active. Install and activate ACF (free or Pro) so editors can fill in the Home, About, Events, Membership, Partnership, and Ambassadorship page content.</p></div>';
    }
}
add_action( 'admin_notices', 'bpu_ie_acf_notice' );

/**
 * Fallback content width.
 */
if ( ! isset( $content_width ) ) {
    $content_width = 1180;
}
