<?php
/**
 * Template Name: Contact
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
$post_id = get_the_ID();

bpu_ie_hero(
    bpu_ie_field( 'contact_hero_eyebrow', $post_id, 'Contact' ),
    bpu_ie_field( 'contact_hero_heading', $post_id, 'Get in touch' ),
    bpu_ie_field( 'contact_hero_subtext', $post_id )
);
?>

<section class="section">
    <div class="container">
        <div class="contact-layout">
            <div class="contact-info">
                <h3><?php esc_html_e( 'Contact Details', 'bpu-ireland' ); ?></h3>
                <?php $email = bpu_ie_option( 'contact_email' ); ?>
                <?php if ( $email ) : ?>
                    <div class="contact-info-item">
                        <strong><?php esc_html_e( 'Email', 'bpu-ireland' ); ?></strong>
                        <a href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo esc_html( antispambot( $email ) ); ?></a>
                    </div>
                <?php endif; ?>
                <?php $phone = bpu_ie_option( 'contact_phone' ); ?>
                <?php if ( $phone ) : ?>
                    <div class="contact-info-item">
                        <strong><?php esc_html_e( 'Phone', 'bpu-ireland' ); ?></strong>
                        <span><?php echo esc_html( $phone ); ?></span>
                    </div>
                <?php endif; ?>
                <div class="footer-social" style="margin-top:1rem;">
                    <?php bpu_ie_social_links(); ?>
                </div>
            </div>

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
                    <button type="submit" name="bpu_ie_contact_submit" value="1" class="form-submit"><?php esc_html_e( 'Send Message', 'bpu-ireland' ); ?></button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php
get_footer();
