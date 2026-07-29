<?php
/**
 * Custom post types for Black Professionals Ireland.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Events CPT — powers the Events page template.
 */
function bpu_ie_register_event_cpt() {
    register_post_type( 'bpu_event', array(
        'labels' => array(
            'name'               => __( 'Events', 'bpu-ireland' ),
            'singular_name'      => __( 'Event', 'bpu-ireland' ),
            'add_new_item'       => __( 'Add New Event', 'bpu-ireland' ),
            'edit_item'          => __( 'Edit Event', 'bpu-ireland' ),
            'all_items'          => __( 'Events', 'bpu-ireland' ),
            'menu_name'          => __( 'Events', 'bpu-ireland' ),
        ),
        'public'       => true,
        'has_archive'  => false,
        'show_in_rest' => true,
        'menu_icon'    => 'dashicons-calendar-alt',
        'supports'     => array( 'title', 'editor', 'thumbnail' ),
        'rewrite'      => array( 'slug' => 'event' ),
    ) );
}
add_action( 'init', 'bpu_ie_register_event_cpt' );

/**
 * Membership Application CPT — stores submissions from the "Become a
 * Member" form on the Membership page. Kept out of the public site and
 * out of search; only users who can edit_pages (site editors/admins) can
 * see the list, since applications include sensitive personal data
 * (ethnicity, sexuality, immigration status) collected under the site's
 * Privacy Statement & Consent.
 */
function bpu_ie_register_membership_application_cpt() {
    register_post_type( 'bpu_member_app', array(
        'labels' => array(
            'name'          => __( 'Membership Applications', 'bpu-ireland' ),
            'singular_name' => __( 'Membership Application', 'bpu-ireland' ),
            'all_items'     => __( 'Membership Applications', 'bpu-ireland' ),
            'menu_name'     => __( 'Membership Applications', 'bpu-ireland' ),
            'view_item'     => __( 'View Application', 'bpu-ireland' ),
        ),
        'public'             => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'show_in_rest'       => false,
        'has_archive'        => false,
        'menu_icon'          => 'dashicons-groups',
        'capability_type'    => 'page',
        'capabilities'       => array(
            'create_posts' => 'do_not_allow', // submissions only ever come from the front-end form
        ),
        'map_meta_cap'       => true,
        'supports'           => array( 'title' ),
    ) );
}
add_action( 'init', 'bpu_ie_register_membership_application_cpt' );
