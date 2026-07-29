<?php
/**
 * "Become an Ambassador" application form on the Ambassadorship page.
 * Reuses the nearest-city/ethnicity/current-status choice lists already
 * defined for the membership form (see inc/membership-form.php) so both
 * forms stay in sync if those lists are edited.
 *
 * Like the membership form, submissions (which include ethnicity) are
 * stored privately as bpu_ambassador_app posts rather than emailed.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function bpu_ie_render_ambassador_form() {
    $choices = bpu_ie_membership_form_choices();
    ?>
    <div class="app-form-wrap">
        <h2 class="section-title">Become a <?php echo esc_html( get_bloginfo( 'name' ) ); ?> ambassador</h2>

        <?php bpu_ie_ambassador_form_notice(); ?>

        <form method="post" action="" enctype="multipart/form-data" class="app-form">
            <?php wp_nonce_field( 'bpu_ie_ambassador_form', 'bpu_af_nonce' ); ?>
            <input type="text" name="bpu_af_website" class="form-honeypot" tabindex="-1" autocomplete="off" value="">

            <div class="form-group">
                <input type="text" class="form-control" name="bpu_af_first_name" placeholder="First Name" required>
            </div>
            <div class="form-group">
                <input type="text" class="form-control" name="bpu_af_last_name" placeholder="Last Name" required>
            </div>
            <div class="form-group">
                <input type="email" class="form-control" name="bpu_af_email" placeholder="Your Email" required>
            </div>
            <div class="form-group">
                <input type="tel" class="form-control" name="bpu_af_phone" placeholder="Phone Number">
            </div>

            <?php bpu_ie_render_select( 'nearest_city', 'What is your current location/nearest city?', $choices['nearest_city'] ); ?>
            <?php bpu_ie_render_select( 'ethnicity', 'How would you best describe your ethnicity?', $choices['ethnicity'] ); ?>
            <?php bpu_ie_render_select( 'current_status', 'I am a...', $choices['current_status'] ); ?>

            <div class="form-group">
                <label for="bpu_af_cv">Upload your CV and Cover letter</label>
                <input type="file" id="bpu_af_cv" name="bpu_af_cv" accept=".pdf,.doc,.docx">
            </div>

            <button type="submit" name="bpu_af_submit" value="1" class="form-submit btn-red"><?php esc_html_e( 'Submit', 'bpu-ireland' ); ?></button>
        </form>
    </div>
    <?php
}

function bpu_ie_ambassador_form_notice() {
    if ( empty( $_GET['bpu_af'] ) ) {
        return;
    }
    $status = sanitize_key( $_GET['bpu_af'] );
    if ( 'success' === $status ) {
        echo '<div class="form-notice success">Thanks for applying to become an ambassador — we\'ll be in touch soon.</div>';
    } elseif ( 'error' === $status ) {
        echo '<div class="form-notice error">Something went wrong submitting your application. Please check the required fields and try again.</div>';
    }
}

function bpu_ie_handle_ambassador_submission() {
    if ( empty( $_POST['bpu_af_submit'] ) ) {
        return;
    }

    $redirect_url = wp_get_referer() ?: home_url( '/' );

    if (
        ! isset( $_POST['bpu_af_nonce'] ) ||
        ! wp_verify_nonce( sanitize_key( $_POST['bpu_af_nonce'] ), 'bpu_ie_ambassador_form' )
    ) {
        wp_safe_redirect( add_query_arg( 'bpu_af', 'error', $redirect_url ) );
        exit;
    }

    if ( ! empty( $_POST['bpu_af_website'] ) ) {
        wp_safe_redirect( add_query_arg( 'bpu_af', 'success', $redirect_url ) );
        exit;
    }

    $first_name = isset( $_POST['bpu_af_first_name'] ) ? sanitize_text_field( wp_unslash( $_POST['bpu_af_first_name'] ) ) : '';
    $last_name  = isset( $_POST['bpu_af_last_name'] ) ? sanitize_text_field( wp_unslash( $_POST['bpu_af_last_name'] ) ) : '';
    $email      = isset( $_POST['bpu_af_email'] ) ? sanitize_email( wp_unslash( $_POST['bpu_af_email'] ) ) : '';

    if ( ! $first_name || ! $last_name || ! is_email( $email ) ) {
        wp_safe_redirect( add_query_arg( 'bpu_af', 'error', $redirect_url ) );
        exit;
    }

    $meta = array(
        'first_name'     => $first_name,
        'last_name'      => $last_name,
        'email'          => $email,
        'phone'          => isset( $_POST['bpu_af_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['bpu_af_phone'] ) ) : '',
        'nearest_city'   => isset( $_POST['bpu_ma_nearest_city'] ) ? sanitize_text_field( wp_unslash( $_POST['bpu_ma_nearest_city'] ) ) : '',
        'ethnicity'      => isset( $_POST['bpu_ma_ethnicity'] ) ? sanitize_text_field( wp_unslash( $_POST['bpu_ma_ethnicity'] ) ) : '',
        'current_status' => isset( $_POST['bpu_ma_current_status'] ) ? sanitize_text_field( wp_unslash( $_POST['bpu_ma_current_status'] ) ) : '',
        'submitted'      => current_time( 'mysql' ),
    );

    $application_id = wp_insert_post( array(
        'post_type'   => 'bpu_ambassador_app',
        'post_title'  => sprintf( '%s %s — %s', $first_name, $last_name, current_time( 'Y-m-d H:i' ) ),
        'post_status' => 'private',
    ) );

    if ( is_wp_error( $application_id ) || ! $application_id ) {
        wp_safe_redirect( add_query_arg( 'bpu_af', 'error', $redirect_url ) );
        exit;
    }

    foreach ( $meta as $meta_key => $meta_value ) {
        update_post_meta( $application_id, $meta_key, $meta_value );
    }

    if ( ! empty( $_FILES['bpu_af_cv']['name'] ) ) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        add_filter( 'upload_mimes', 'bpu_ie_cv_allowed_mimes' );
        $attachment_id = media_handle_upload( 'bpu_af_cv', $application_id );
        remove_filter( 'upload_mimes', 'bpu_ie_cv_allowed_mimes' );

        if ( ! is_wp_error( $attachment_id ) ) {
            update_post_meta( $application_id, 'cv_attachment_id', $attachment_id );
        }
    }

    $to      = bpu_ie_option( 'contact_email', get_option( 'admin_email' ) );
    $subject = sprintf( '[Ambassador Application] %s %s', $first_name, $last_name );
    $body    = "A new ambassador application was submitted on " . get_bloginfo( 'name' ) . ".\n\n"
        . "Name: {$first_name} {$last_name}\n"
        . "Email: {$email}\n\n"
        . "Review the full application in wp-admin:\n"
        . admin_url( 'post.php?post=' . $application_id . '&action=edit' ) . "\n";
    wp_mail( $to, $subject, $body );

    wp_safe_redirect( add_query_arg( 'bpu_af', 'success', $redirect_url ) );
    exit;
}
add_action( 'template_redirect', 'bpu_ie_handle_ambassador_submission' );

function bpu_ie_ambassador_app_meta_box() {
    add_meta_box(
        'bpu_ie_ambassador_app_details',
        'Application Details',
        function ( $post ) {
            $labels = array(
                'email' => 'Email', 'phone' => 'Phone', 'nearest_city' => 'Nearest city',
                'ethnicity' => 'Ethnicity', 'current_status' => 'Current status', 'submitted' => 'Submitted',
            );
            echo '<table class="widefat"><tbody>';
            foreach ( $labels as $key => $label ) {
                $value = get_post_meta( $post->ID, $key, true );
                printf( '<tr><th style="text-align:left;width:220px;">%s</th><td>%s</td></tr>', esc_html( $label ), esc_html( $value ) );
            }
            $cv_id = get_post_meta( $post->ID, 'cv_attachment_id', true );
            if ( $cv_id ) {
                printf( '<tr><th>CV / Cover Letter</th><td><a href="%s" target="_blank">Download</a></td></tr>', esc_url( wp_get_attachment_url( $cv_id ) ) );
            }
            echo '</tbody></table>';
        },
        'bpu_ambassador_app',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'bpu_ie_ambassador_app_meta_box' );
