<?php
/**
 * Native contact form handler (no third-party form plugin dependency).
 *
 * Submits back to the same page, verifies a nonce + honeypot, sends mail
 * to the address set in Theme Settings, then redirects with a status flag
 * so refreshing the page never resubmits the form.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function bpu_ie_handle_contact_submission() {
    if ( empty( $_POST['bpu_ie_contact_submit'] ) ) {
        return;
    }

    $redirect_url = wp_get_referer() ?: home_url( '/' );

    if (
        ! isset( $_POST['bpu_ie_contact_nonce'] ) ||
        ! wp_verify_nonce( sanitize_key( $_POST['bpu_ie_contact_nonce'] ), 'bpu_ie_contact_form' )
    ) {
        wp_safe_redirect( add_query_arg( 'bpu_contact', 'error', $redirect_url ) );
        exit;
    }

    // Honeypot: real visitors never fill this hidden field in.
    if ( ! empty( $_POST['bpu_ie_website'] ) ) {
        wp_safe_redirect( add_query_arg( 'bpu_contact', 'success', $redirect_url ) );
        exit;
    }

    $name    = isset( $_POST['bpu_ie_name'] ) ? sanitize_text_field( wp_unslash( $_POST['bpu_ie_name'] ) ) : '';
    $email   = isset( $_POST['bpu_ie_email'] ) ? sanitize_email( wp_unslash( $_POST['bpu_ie_email'] ) ) : '';
    $subject = isset( $_POST['bpu_ie_subject'] ) ? sanitize_text_field( wp_unslash( $_POST['bpu_ie_subject'] ) ) : '';
    $message = isset( $_POST['bpu_ie_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['bpu_ie_message'] ) ) : '';

    if ( ! $name || ! is_email( $email ) || ! $message ) {
        wp_safe_redirect( add_query_arg( 'bpu_contact', 'error', $redirect_url ) );
        exit;
    }

    $to = bpu_ie_option( 'contact_email', get_option( 'admin_email' ) );
    $mail_subject = $subject ? sprintf( '[BPU Ireland Contact] %s', $subject ) : '[BPU Ireland Contact] New message';
    $body = "New contact form submission from blackprofessionals.eu\n\n"
        . "Name: {$name}\n"
        . "Email: {$email}\n"
        . "Subject: {$subject}\n\n"
        . "Message:\n{$message}\n";
    $headers = array( 'Reply-To: ' . $name . ' <' . $email . '>' );

    $sent = wp_mail( $to, $mail_subject, $body, $headers );

    wp_safe_redirect( add_query_arg( 'bpu_contact', $sent ? 'success' : 'error', $redirect_url ) );
    exit;
}
add_action( 'template_redirect', 'bpu_ie_handle_contact_submission' );

/**
 * Renders the success/error notice banner above the contact form, based
 * on the ?bpu_contact= query var set by the redirect above.
 */
function bpu_ie_contact_notice() {
    if ( empty( $_GET['bpu_contact'] ) ) {
        return;
    }
    $status = sanitize_key( $_GET['bpu_contact'] );
    if ( 'success' === $status ) {
        echo '<div class="form-notice success">Thanks for reaching out — we\'ll be in touch soon.</div>';
    } elseif ( 'error' === $status ) {
        echo '<div class="form-notice error">Something went wrong sending your message. Please check the form and try again.</div>';
    }
}
