<?php
/**
 * "Explore Partnership" form on the Partnership page — a lightweight
 * contact-request form (no sensitive personal data), so it's handled the
 * same way as the general contact form: emailed straight to the org,
 * nothing stored in the database.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function bpu_ie_render_partnership_form() {
    ?>
    <div class="app-form-wrap">
        <h2 class="section-title">Explore Partnership</h2>
        <p class="section-sub">Complete the form below and our team will contact you to schedule a call.</p>

        <?php bpu_ie_partnership_form_notice(); ?>

        <form method="post" action="" class="app-form">
            <?php wp_nonce_field( 'bpu_ie_partnership_form', 'bpu_pf_nonce' ); ?>
            <input type="text" name="bpu_pf_website" class="form-honeypot" tabindex="-1" autocomplete="off" value="">

            <div class="form-group">
                <input type="text" class="form-control" name="bpu_pf_org_name" placeholder="Name of Organisation" required>
            </div>
            <div class="form-group">
                <input type="text" class="form-control" name="bpu_pf_full_name" placeholder="Your Name &amp; Surname" required>
            </div>
            <div class="form-group">
                <input type="text" class="form-control" name="bpu_pf_job_title" placeholder="Your Job Title">
            </div>
            <div class="form-group">
                <input type="email" class="form-control" name="bpu_pf_email" placeholder="Your Email" required>
            </div>
            <div class="form-group">
                <textarea class="form-control" name="bpu_pf_message" placeholder="How can <?php echo esc_attr( get_bloginfo( 'name' ) ); ?> help you?" required></textarea>
            </div>

            <button type="submit" name="bpu_pf_submit" value="1" class="form-submit btn-red"><?php esc_html_e( 'Submit', 'bpu-ireland' ); ?></button>
        </form>
    </div>
    <?php
}

function bpu_ie_partnership_form_notice() {
    if ( empty( $_GET['bpu_pf'] ) ) {
        return;
    }
    $status = sanitize_key( $_GET['bpu_pf'] );
    if ( 'success' === $status ) {
        echo '<div class="form-notice success">Thanks — our team will be in touch to schedule a call.</div>';
    } elseif ( 'error' === $status ) {
        echo '<div class="form-notice error">Something went wrong submitting the form. Please check the required fields and try again.</div>';
    }
}

function bpu_ie_handle_partnership_submission() {
    if ( empty( $_POST['bpu_pf_submit'] ) ) {
        return;
    }

    $redirect_url = wp_get_referer() ?: home_url( '/' );

    if (
        ! isset( $_POST['bpu_pf_nonce'] ) ||
        ! wp_verify_nonce( sanitize_key( $_POST['bpu_pf_nonce'] ), 'bpu_ie_partnership_form' )
    ) {
        wp_safe_redirect( add_query_arg( 'bpu_pf', 'error', $redirect_url ) );
        exit;
    }

    if ( ! empty( $_POST['bpu_pf_website'] ) ) {
        wp_safe_redirect( add_query_arg( 'bpu_pf', 'success', $redirect_url ) );
        exit;
    }

    $org_name  = isset( $_POST['bpu_pf_org_name'] ) ? sanitize_text_field( wp_unslash( $_POST['bpu_pf_org_name'] ) ) : '';
    $full_name = isset( $_POST['bpu_pf_full_name'] ) ? sanitize_text_field( wp_unslash( $_POST['bpu_pf_full_name'] ) ) : '';
    $job_title = isset( $_POST['bpu_pf_job_title'] ) ? sanitize_text_field( wp_unslash( $_POST['bpu_pf_job_title'] ) ) : '';
    $email     = isset( $_POST['bpu_pf_email'] ) ? sanitize_email( wp_unslash( $_POST['bpu_pf_email'] ) ) : '';
    $message   = isset( $_POST['bpu_pf_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['bpu_pf_message'] ) ) : '';

    if ( ! $org_name || ! $full_name || ! is_email( $email ) || ! $message ) {
        wp_safe_redirect( add_query_arg( 'bpu_pf', 'error', $redirect_url ) );
        exit;
    }

    $to      = bpu_ie_option( 'contact_email', get_option( 'admin_email' ) );
    $subject = sprintf( '[Partnership Enquiry] %s', $org_name );
    $body    = "New partnership enquiry from " . get_bloginfo( 'name' ) . "\n\n"
        . "Organisation: {$org_name}\n"
        . "Contact: {$full_name}" . ( $job_title ? " ({$job_title})" : '' ) . "\n"
        . "Email: {$email}\n\n"
        . "Message:\n{$message}\n";
    $headers = array( 'Reply-To: ' . $full_name . ' <' . $email . '>' );

    $sent = wp_mail( $to, $subject, $body, $headers );

    wp_safe_redirect( add_query_arg( 'bpu_pf', $sent ? 'success' : 'error', $redirect_url ) );
    exit;
}
add_action( 'template_redirect', 'bpu_ie_handle_partnership_submission' );
