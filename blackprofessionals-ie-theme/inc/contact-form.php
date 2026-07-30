<?php
/**
 * Native contact form (no third-party form plugin dependency).
 *
 * No sensitive personal data, so submissions are stored via the shared
 * bpu_form_submission post type (see inc/custom-post-types.php) purely
 * as a durable record — success/failure to the visitor is based on that
 * storage succeeding, with the notification email treated as best-effort
 * so a slow/broken mail server never turns a recorded message into a
 * user-facing error.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Renders the contact form. Called from
 * page-templates/template-contact.php.
 */
function bpu_ie_render_contact_form() {
    ?>
    <div class="contact-form">
        <?php bpu_ie_contact_notice(); ?>
        <form method="post" action="">
            <?php wp_nonce_field( 'bpu_ie_contact_form', 'bpu_ie_contact_nonce' ); ?>
            <input type="text" name="bpu_ie_website" class="form-honeypot" tabindex="-1" autocomplete="off" value="">

            <div class="form-group">
                <label for="bpu_ie_name"><?php esc_html_e( 'Name', 'bpu-ireland' ); ?></label>
                <input type="text" class="form-control" id="bpu_ie_name" name="bpu_ie_name" required>
            </div>
            <div class="form-group">
                <label for="bpu_ie_email"><?php esc_html_e( 'Email', 'bpu-ireland' ); ?></label>
                <input type="email" class="form-control" id="bpu_ie_email" name="bpu_ie_email" required>
            </div>
            <div class="form-group">
                <label for="bpu_ie_subject"><?php esc_html_e( 'Subject', 'bpu-ireland' ); ?></label>
                <input type="text" class="form-control" id="bpu_ie_subject" name="bpu_ie_subject">
            </div>
            <div class="form-group">
                <label for="bpu_ie_message"><?php esc_html_e( 'Message', 'bpu-ireland' ); ?></label>
                <textarea class="form-control" id="bpu_ie_message" name="bpu_ie_message" required></textarea>
            </div>

            <?php bpu_ie_render_captcha(); ?>

            <button type="submit" name="bpu_ie_contact_submit" value="1" class="form-submit"><?php esc_html_e( 'Send Message', 'bpu-ireland' ); ?></button>
        </form>
    </div>
    <?php
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

    if ( ! bpu_ie_verify_captcha() ) {
        wp_safe_redirect( add_query_arg( 'bpu_contact', 'captcha', $redirect_url ) );
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

    $submission_id = bpu_ie_store_form_submission( 'contact', $name, array(
        'name'    => $name,
        'email'   => $email,
        'subject' => $subject,
        'message' => $message,
    ) );

    if ( ! $submission_id ) {
        wp_safe_redirect( add_query_arg( 'bpu_contact', 'error', $redirect_url ) );
        exit;
    }

    // Best-effort notification — the submission is already safely stored.
    $mail_subject = $subject ? sprintf( '[Contact] %s', $subject ) : '[Contact] New message';
    $body         = "New contact form submission on " . get_bloginfo( 'name' ) . "\n\n"
        . "Name: {$name}\n"
        . "Email: {$email}\n"
        . "Subject: {$subject}\n\n"
        . "Message:\n{$message}\n";
    $headers = array( 'Reply-To: ' . $name . ' <' . $email . '>' );
    wp_mail( bpu_ie_notification_recipients(), $mail_subject, $body, $headers );

    wp_safe_redirect( add_query_arg( 'bpu_contact', 'success', $redirect_url ) );
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
        echo '<div class="form-notice success">' . esc_html( bpu_ie_option( 'contact_success_message', "Thanks for reaching out — we'll be in touch soon." ) ) . '</div>';
    } elseif ( 'error' === $status ) {
        echo '<div class="form-notice error">' . esc_html( bpu_ie_option( 'contact_error_message', 'Something went wrong sending your message. Please check the form and try again.' ) ) . '</div>';
    } elseif ( 'captcha' === $status ) {
        echo '<div class="form-notice error">' . esc_html__( "We couldn't verify you're human — please try the captcha again.", 'bpu-ireland' ) . '</div>';
    }
}
