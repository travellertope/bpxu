<?php
/**
 * "Become a Member" application form — renders on the Membership page
 * template and stores submissions as private bpu_member_app posts.
 *
 * Sensitive fields (immigration status, ethnicity, sexuality) are only
 * ever stored server-side for admins to review, never included in the
 * notification email body.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Central place to adjust the dropdown/radio choices used by the form,
 * without touching the render or submission-handling logic.
 */
function bpu_ie_membership_form_choices() {
    return array(
        'current_status' => array(
            'employed_professional'   => 'An Employed Professional',
            'university_student'      => 'A University Student',
            'college_student'         => 'A College Student',
            'entrepreneur'            => 'An Entrepreneur',
            'unemployed_professional' => 'Unemployed Professional',
        ),
        'want_out_of_membership' => array(
            'career_support' => 'Career Journey Support',
            'networking'     => 'Networking',
            'ally'           => 'To Become An Ally',
        ),
        'want_to_be' => array(
            'mentee'      => 'A Mentee',
            'mentor'      => 'A Mentor',
            'both'        => 'Both a Mentor and a Mentee',
            'member_only' => 'Just a Member for Now',
        ),
        'migration_country' => array(
            'nigeria' => 'Nigeria', 'ghana' => 'Ghana', 'kenya' => 'Kenya', 'south_africa' => 'South Africa',
            'zimbabwe' => 'Zimbabwe', 'somalia' => 'Somalia', 'ethiopia' => 'Ethiopia', 'uganda' => 'Uganda',
            'tanzania' => 'Tanzania', 'cameroon' => 'Cameroon', 'drc' => 'Democratic Republic of Congo',
            'ivory_coast' => "Côte d'Ivoire", 'senegal' => 'Senegal', 'sierra_leone' => 'Sierra Leone',
            'zambia' => 'Zambia', 'rwanda' => 'Rwanda', 'sudan' => 'Sudan', 'eritrea' => 'Eritrea',
            'jamaica' => 'Jamaica', 'trinidad' => 'Trinidad and Tobago', 'haiti' => 'Haiti', 'barbados' => 'Barbados',
            'guyana' => 'Guyana', 'usa' => 'United States', 'uk' => 'United Kingdom', 'brazil' => 'Brazil',
            'other' => 'Other',
        ),
        'reside_country' => array(
            'ireland' => 'Ireland', 'uk' => 'United Kingdom', 'germany' => 'Germany', 'france' => 'France',
            'netherlands' => 'Netherlands', 'spain' => 'Spain', 'italy' => 'Italy', 'portugal' => 'Portugal',
            'belgium' => 'Belgium', 'sweden' => 'Sweden', 'other_europe' => 'Other European Country',
            'outside_europe' => 'Outside Europe',
        ),
        'industry' => array(
            'technology' => 'Technology', 'finance' => 'Finance & Banking', 'law' => 'Law',
            'healthcare' => 'Healthcare', 'education' => 'Education', 'marketing' => 'Marketing & Communications',
            'consulting' => 'Consulting', 'engineering' => 'Engineering', 'public_sector' => 'Public Sector / Government',
            'non_profit' => 'Non-Profit', 'other' => 'Other',
        ),
        'years_experience' => array(
            '0-2'  => '0–2 years', '3-5' => '3–5 years', '6-10' => '6–10 years',
            '11-15' => '11–15 years', '16+' => '16+ years',
        ),
        'nearest_city' => array(
            'dublin' => 'Dublin', 'cork' => 'Cork', 'galway' => 'Galway', 'limerick' => 'Limerick',
            'waterford' => 'Waterford', 'belfast' => 'Belfast', 'other' => 'Other',
        ),
        'ethnicity' => array(
            'black_african'    => 'Black African',
            'black_caribbean'  => 'Black Caribbean',
            'black_irish_british' => 'Black Irish / British',
            'mixed_black'      => 'Mixed — Black and Other Ethnicity',
            'prefer_not_to_say' => 'Prefer Not to Say',
            'other'            => 'Other',
        ),
        'sexuality' => array(
            'heterosexual'      => 'Heterosexual / Straight',
            'gay'               => 'Gay',
            'lesbian'           => 'Lesbian',
            'bisexual'          => 'Bisexual',
            'prefer_not_to_say' => 'Prefer Not to Say',
            'other'             => 'Other',
        ),
        'volunteer_area' => array(
            'content_social' => 'Content & Social Media',
            'events'         => 'Events & Logistics',
            'mentorship'     => 'Mentorship',
            'partnerships'   => 'Partnerships & Fundraising',
            'not_now'        => 'Not Interested Right Now',
        ),
    );
}

function bpu_ie_render_select( $name, $label, $choices, $required = false ) {
    ?>
    <div class="form-group">
        <label for="bpu_ma_<?php echo esc_attr( $name ); ?>"><?php echo esc_html( $label ); ?></label>
        <select class="form-control" id="bpu_ma_<?php echo esc_attr( $name ); ?>" name="bpu_ma_<?php echo esc_attr( $name ); ?>" <?php echo $required ? 'required' : ''; ?>>
            <option value="">&mdash; Please choose an option &mdash;</option>
            <?php foreach ( $choices as $value => $text ) : ?>
                <option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $text ); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php
}

function bpu_ie_render_radio_group( $name, $label, $choices, $required = false ) {
    ?>
    <div class="form-group">
        <label><?php echo esc_html( $label ); ?></label>
        <div class="radio-group">
            <?php foreach ( $choices as $value => $text ) : ?>
                <label class="radio-option">
                    <input type="radio" name="bpu_ma_<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>" <?php echo $required ? 'required' : ''; ?>>
                    <?php echo esc_html( $text ); ?>
                </label>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
}

/**
 * Renders the full "Become a Member" form. Called from
 * page-templates/template-membership.php.
 */
function bpu_ie_render_membership_form() {
    $choices  = bpu_ie_membership_form_choices();
    $org_name = bpu_ie_option( 'org_legal_name', 'Black Professionals Ireland' );

    $privacy_url = bpu_ie_option( 'privacy_policy_link' );
    if ( ! $privacy_url && function_exists( 'get_privacy_policy_url' ) ) {
        $privacy_url = get_privacy_policy_url();
    }
    ?>
    <div class="app-form-wrap">
        <h2 class="section-title">Become a <?php echo esc_html( get_bloginfo( 'name' ) ); ?> member</h2>

        <?php bpu_ie_membership_form_notice(); ?>

        <form method="post" action="" enctype="multipart/form-data" class="app-form">
            <?php wp_nonce_field( 'bpu_ie_membership_form', 'bpu_ma_nonce' ); ?>
            <input type="text" name="bpu_ma_website" class="form-honeypot" tabindex="-1" autocomplete="off" value="">

            <div class="form-group">
                <label for="bpu_ma_first_name">First Name</label>
                <input type="text" class="form-control" id="bpu_ma_first_name" name="bpu_ma_first_name" required>
            </div>
            <div class="form-group">
                <label for="bpu_ma_last_name">Last Name</label>
                <input type="text" class="form-control" id="bpu_ma_last_name" name="bpu_ma_last_name" required>
            </div>
            <div class="form-group">
                <label for="bpu_ma_email">E-mail</label>
                <input type="email" class="form-control" id="bpu_ma_email" name="bpu_ma_email" required>
            </div>
            <div class="form-group">
                <label for="bpu_ma_linkedin">LinkedIn @username (Input N/A if none)</label>
                <input type="text" class="form-control" id="bpu_ma_linkedin" name="bpu_ma_linkedin">
            </div>
            <div class="form-group">
                <label for="bpu_ma_phone">Phone Number (Including country code)</label>
                <input type="tel" class="form-control" id="bpu_ma_phone" name="bpu_ma_phone">
            </div>

            <?php bpu_ie_render_radio_group( 'first_gen_immigrant', 'Are you a first-generation immigrant?', array( 'yes' => 'Yes', 'no' => 'No' ) ); ?>
            <?php bpu_ie_render_radio_group( 'current_status', 'Which Best Describes Your Current Status?', $choices['current_status'], true ); ?>
            <?php bpu_ie_render_radio_group( 'want_out_of_membership', "What Do You Most Want To Get Out Of {$org_name}?", $choices['want_out_of_membership'] ); ?>
            <?php bpu_ie_render_select( 'want_to_be', 'I Want To Be a', $choices['want_to_be'] ); ?>
            <?php bpu_ie_render_select( 'migration_country', 'If You Were Not Born In Ireland, Which Country Did You Migrate From?', $choices['migration_country'] ); ?>
            <?php bpu_ie_render_select( 'years_experience', 'How Many Years Experience Do You Have In Your Field?', $choices['years_experience'] ); ?>

            <div class="form-group">
                <label for="bpu_ma_cv">Attach Your CV If You Would Like It Reviewed?</label>
                <input type="file" id="bpu_ma_cv" name="bpu_ma_cv" accept=".pdf,.doc,.docx">
            </div>

            <?php bpu_ie_render_radio_group( 'resides_in_ireland', 'Do You Reside In Ireland?', array( 'yes' => 'Yes', 'no' => 'No' ) ); ?>
            <?php bpu_ie_render_select( 'reside_country', 'What Country Do You Mainly Reside In?', $choices['reside_country'] ); ?>
            <?php bpu_ie_render_select( 'industry', 'Select Your Current/Preferred Industry?', $choices['industry'] ); ?>

            <div class="form-group">
                <label for="bpu_ma_expertise">What is your area of expertise?</label>
                <input type="text" class="form-control" id="bpu_ma_expertise" name="bpu_ma_expertise" placeholder="e.g. Data, Tech, Finance, Law etc.">
            </div>

            <?php bpu_ie_render_select( 'nearest_city', 'What is your nearest city?', $choices['nearest_city'] ); ?>
            <?php bpu_ie_render_select( 'ethnicity', 'How would you best describe your ethnicity?', $choices['ethnicity'] ); ?>
            <?php bpu_ie_render_radio_group( 'gender', 'Which is your gender?', array( 'male' => 'Male', 'female' => 'Female', 'other' => 'Other' ) ); ?>
            <?php bpu_ie_render_select( 'sexuality', 'How Would You Best Describe Your Sexuality?', $choices['sexuality'] ); ?>

            <?php
            $current_year = (int) date_i18n( 'Y' );
            $birth_years  = array();
            for ( $y = $current_year - 16; $y >= $current_year - 100; $y-- ) {
                $birth_years[ $y ] = (string) $y;
            }
            bpu_ie_render_select( 'birth_year', 'What year were you born?', $birth_years );
            ?>

            <?php bpu_ie_render_select( 'volunteer_area', "{$org_name} is a community so we need volunteers to really drive lasting change. If you would like to be a volunteer, select an area you can help with", $choices['volunteer_area'] ); ?>

            <?php bpu_ie_render_captcha(); ?>

            <div class="form-group form-consent">
                <label>
                    <input type="checkbox" name="bpu_ma_consent" value="1" required>
                    I agree to the
                    <?php if ( $privacy_url ) : ?>
                        <a href="<?php echo esc_url( $privacy_url ); ?>" target="_blank" rel="noopener noreferrer">Privacy Statement &amp; Consent</a>,
                    <?php else : ?>
                        Privacy Statement &amp; Consent,
                    <?php endif; ?>
                    including the use of photos from <?php echo esc_html( $org_name ); ?> events on official social media accounts.
                </label>
            </div>

            <button type="submit" name="bpu_ma_submit" value="1" class="form-submit btn-red"><?php esc_html_e( 'Submit', 'bpu-ireland' ); ?></button>
        </form>
    </div>
    <?php
}

function bpu_ie_membership_form_notice() {
    if ( empty( $_GET['bpu_ma'] ) ) {
        return;
    }
    $status = sanitize_key( $_GET['bpu_ma'] );
    if ( 'success' === $status ) {
        echo '<div class="form-notice success">' . esc_html( bpu_ie_option( 'membership_success_message', "Thank you for applying — we'll be in touch soon." ) ) . '</div>';
    } elseif ( 'error' === $status ) {
        echo '<div class="form-notice error">' . esc_html( bpu_ie_option( 'membership_error_message', 'Something went wrong submitting your application. Please check the required fields and try again.' ) ) . '</div>';
    } elseif ( 'captcha' === $status ) {
        echo '<div class="form-notice error">' . esc_html( bpu_ie_option( 'membership_captcha_error_message', "We couldn't verify you're human — please try the captcha again." ) ) . '</div>';
    }
}

function bpu_ie_handle_membership_submission() {
    if ( empty( $_POST['bpu_ma_submit'] ) ) {
        return;
    }

    $redirect_url = wp_get_referer() ?: home_url( '/' );

    if (
        ! isset( $_POST['bpu_ma_nonce'] ) ||
        ! wp_verify_nonce( sanitize_key( $_POST['bpu_ma_nonce'] ), 'bpu_ie_membership_form' )
    ) {
        wp_safe_redirect( add_query_arg( 'bpu_ma', 'error', $redirect_url ) );
        exit;
    }

    // Honeypot.
    if ( ! empty( $_POST['bpu_ma_website'] ) ) {
        wp_safe_redirect( add_query_arg( 'bpu_ma', 'success', $redirect_url ) );
        exit;
    }

    if ( ! bpu_ie_verify_captcha() ) {
        wp_safe_redirect( add_query_arg( 'bpu_ma', 'captcha', $redirect_url ) );
        exit;
    }

    $first_name = isset( $_POST['bpu_ma_first_name'] ) ? sanitize_text_field( wp_unslash( $_POST['bpu_ma_first_name'] ) ) : '';
    $last_name  = isset( $_POST['bpu_ma_last_name'] ) ? sanitize_text_field( wp_unslash( $_POST['bpu_ma_last_name'] ) ) : '';
    $email      = isset( $_POST['bpu_ma_email'] ) ? sanitize_email( wp_unslash( $_POST['bpu_ma_email'] ) ) : '';
    $consent    = ! empty( $_POST['bpu_ma_consent'] );

    if ( ! $first_name || ! $last_name || ! is_email( $email ) || ! $consent ) {
        wp_safe_redirect( add_query_arg( 'bpu_ma', 'error', $redirect_url ) );
        exit;
    }

    // Every other field: sanitize as plain text and store as post meta.
    $text_fields = array(
        'linkedin', 'phone', 'first_gen_immigrant', 'current_status', 'want_out_of_membership',
        'want_to_be', 'migration_country', 'years_experience', 'resides_in_ireland', 'reside_country',
        'industry', 'expertise', 'nearest_city', 'ethnicity', 'gender', 'sexuality', 'birth_year',
        'volunteer_area',
    );
    $meta = array(
        'first_name' => $first_name,
        'last_name'  => $last_name,
        'email'      => $email,
        'consent'    => 'yes',
        'submitted'  => current_time( 'mysql' ),
    );
    foreach ( $text_fields as $field ) {
        $key = 'bpu_ma_' . $field;
        $meta[ $field ] = isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : '';
    }

    $application_id = wp_insert_post( array(
        'post_type'   => 'bpu_member_app',
        'post_title'  => sprintf( '%s %s — %s', $first_name, $last_name, current_time( 'Y-m-d H:i' ) ),
        'post_status' => 'private',
    ) );

    if ( is_wp_error( $application_id ) || ! $application_id ) {
        wp_safe_redirect( add_query_arg( 'bpu_ma', 'error', $redirect_url ) );
        exit;
    }

    foreach ( $meta as $meta_key => $meta_value ) {
        update_post_meta( $application_id, $meta_key, $meta_value );
    }

    // Optional CV upload — restricted to PDF/Word, 5MB max.
    if ( ! empty( $_FILES['bpu_ma_cv']['name'] ) ) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        add_filter( 'upload_mimes', 'bpu_ie_cv_allowed_mimes' );
        $attachment_id = media_handle_upload( 'bpu_ma_cv', $application_id );
        remove_filter( 'upload_mimes', 'bpu_ie_cv_allowed_mimes' );

        if ( ! is_wp_error( $attachment_id ) ) {
            update_post_meta( $application_id, 'cv_attachment_id', $attachment_id );
        }
    }

    // Notify admins without dumping sensitive demographic answers into email.
    $subject = sprintf( '[Membership Application] %s %s', $first_name, $last_name );
    $body    = "A new membership application was submitted on " . get_bloginfo( 'name' ) . ".\n\n"
        . "Name: {$first_name} {$last_name}\n"
        . "Email: {$email}\n\n"
        . "Review the full application in wp-admin:\n"
        . admin_url( 'post.php?post=' . $application_id . '&action=edit' ) . "\n";
    wp_mail( bpu_ie_notification_recipients(), $subject, $body );

    wp_safe_redirect( add_query_arg( 'bpu_ma', 'success', $redirect_url ) );
    exit;
}
add_action( 'template_redirect', 'bpu_ie_handle_membership_submission' );

function bpu_ie_cv_allowed_mimes( $mimes ) {
    return array(
        'pdf'  => 'application/pdf',
        'doc'  => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    );
}

/**
 * Simple read-only display of a submitted application's answers on its
 * edit screen, since the CPT only supports a title.
 */
function bpu_ie_membership_app_meta_box() {
    add_meta_box(
        'bpu_ie_membership_app_details',
        'Application Details',
        function ( $post ) {
            $labels = array(
                'email' => 'Email', 'linkedin' => 'LinkedIn', 'phone' => 'Phone',
                'first_gen_immigrant' => 'First-generation immigrant', 'current_status' => 'Current status',
                'want_out_of_membership' => 'Wants out of membership', 'want_to_be' => 'Wants to be',
                'migration_country' => 'Migrated from', 'years_experience' => 'Years experience',
                'resides_in_ireland' => 'Resides in Ireland', 'reside_country' => 'Resides in (country)',
                'industry' => 'Industry', 'expertise' => 'Area of expertise', 'nearest_city' => 'Nearest city',
                'ethnicity' => 'Ethnicity', 'gender' => 'Gender', 'sexuality' => 'Sexuality',
                'birth_year' => 'Birth year', 'volunteer_area' => 'Volunteer interest', 'submitted' => 'Submitted',
            );
            echo '<table class="widefat"><tbody>';
            foreach ( $labels as $key => $label ) {
                $value = get_post_meta( $post->ID, $key, true );
                printf( '<tr><th style="text-align:left;width:220px;">%s</th><td>%s</td></tr>', esc_html( $label ), esc_html( $value ) );
            }
            $cv_id = get_post_meta( $post->ID, 'cv_attachment_id', true );
            if ( $cv_id ) {
                printf( '<tr><th>CV</th><td><a href="%s" target="_blank">Download CV</a></td></tr>', esc_url( wp_get_attachment_url( $cv_id ) ) );
            }
            echo '</tbody></table>';
        },
        'bpu_member_app',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'bpu_ie_membership_app_meta_box' );
