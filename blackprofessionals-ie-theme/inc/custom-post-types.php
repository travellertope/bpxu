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

/**
 * Ambassador Application CPT — stores submissions from the "Become an
 * Ambassador" form on the Ambassadorship page. Same visibility/capability
 * rules as Membership Applications, since it also collects ethnicity.
 */
function bpu_ie_register_ambassador_application_cpt() {
    register_post_type( 'bpu_ambassador_app', array(
        'labels' => array(
            'name'          => __( 'Ambassador Applications', 'bpu-ireland' ),
            'singular_name' => __( 'Ambassador Application', 'bpu-ireland' ),
            'all_items'     => __( 'Ambassador Applications', 'bpu-ireland' ),
            'menu_name'     => __( 'Ambassador Applications', 'bpu-ireland' ),
            'view_item'     => __( 'View Application', 'bpu-ireland' ),
        ),
        'public'          => false,
        'show_ui'         => true,
        'show_in_menu'    => true,
        'show_in_rest'    => false,
        'has_archive'     => false,
        'menu_icon'       => 'dashicons-star-filled',
        'capability_type' => 'page',
        'capabilities'    => array(
            'create_posts' => 'do_not_allow',
        ),
        'map_meta_cap' => true,
        'supports'     => array( 'title' ),
    ) );
}
add_action( 'init', 'bpu_ie_register_ambassador_application_cpt' );

/**
 * Form Submissions CPT — shared storage for the simpler forms (Contact,
 * Partnership) that don't collect sensitive personal data and so don't
 * need their own dedicated post type like Membership/Ambassador
 * Applications do. A 'form_type' meta value ('contact' | 'partnership')
 * distinguishes which form each entry came from.
 */
function bpu_ie_register_form_submission_cpt() {
    register_post_type( 'bpu_form_submission', array(
        'labels' => array(
            'name'          => __( 'Form Submissions', 'bpu-ireland' ),
            'singular_name' => __( 'Form Submission', 'bpu-ireland' ),
            'all_items'     => __( 'Form Submissions', 'bpu-ireland' ),
            'menu_name'     => __( 'Form Submissions', 'bpu-ireland' ),
            'view_item'     => __( 'View Submission', 'bpu-ireland' ),
        ),
        'public'          => false,
        'show_ui'         => true,
        'show_in_menu'    => true,
        'show_in_rest'    => false,
        'has_archive'     => false,
        'menu_icon'       => 'dashicons-email-alt',
        'capability_type' => 'page',
        'capabilities'    => array(
            'create_posts' => 'do_not_allow',
        ),
        'map_meta_cap' => true,
        'supports'     => array( 'title' ),
    ) );
}
add_action( 'init', 'bpu_ie_register_form_submission_cpt' );

/**
 * Stores a submission from any "simple" form (no dedicated CPT) as a
 * bpu_form_submission post, with every $meta entry saved as post meta.
 * Returns the new post ID, or 0 on failure.
 */
function bpu_ie_store_form_submission( $form_type, $title, $meta ) {
    $post_id = wp_insert_post( array(
        'post_type'   => 'bpu_form_submission',
        'post_title'  => sprintf( '[%s] %s', ucfirst( $form_type ), $title ),
        'post_status' => 'private',
    ) );

    if ( is_wp_error( $post_id ) || ! $post_id ) {
        return 0;
    }

    update_post_meta( $post_id, 'form_type', $form_type );
    foreach ( $meta as $meta_key => $meta_value ) {
        update_post_meta( $post_id, $meta_key, $meta_value );
    }

    return $post_id;
}

/**
 * Generic read-only display of a stored submission's fields, since the
 * CPT only supports a title.
 */
function bpu_ie_form_submission_meta_box() {
    add_meta_box(
        'bpu_ie_form_submission_details',
        'Submission Details',
        function ( $post ) {
            $meta = get_post_meta( $post->ID );
            echo '<table class="widefat"><tbody>';
            foreach ( $meta as $key => $values ) {
                if ( 0 === strpos( $key, '_' ) ) {
                    continue; // skip internal/protected meta
                }
                $value = is_array( $values ) ? implode( ', ', $values ) : $values;
                printf( '<tr><th style="text-align:left;width:220px;">%s</th><td>%s</td></tr>', esc_html( ucwords( str_replace( '_', ' ', $key ) ) ), esc_html( $value ) );
            }
            echo '</tbody></table>';
        },
        'bpu_form_submission',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'bpu_ie_form_submission_meta_box' );
