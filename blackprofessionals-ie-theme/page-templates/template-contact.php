<?php
/**
 * Template Name: Contact
 *
 * Leads with the real Contact page layout (Address block + labelled
 * LinkedIn/Instagram/Email links, same org info as the footer/Imprint),
 * with the site's native contact form offered as a secondary option.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
$post_id = get_the_ID();

bpu_ie_hero(
    bpu_ie_field( 'contact_hero_eyebrow', $post_id ),
    bpu_ie_field( 'contact_hero_heading', $post_id, 'Contact' ),
    bpu_ie_field( 'contact_hero_subtext', $post_id )
);

$org_name = bpu_ie_option( 'org_legal_name', 'Black Professionals Europe e. V.' );
$address  = bpu_ie_option( 'contact_address' );
$email    = bpu_ie_option( 'contact_email' );
$linkedin = bpu_ie_option( 'social_linkedin' );
$instagram = bpu_ie_option( 'social_instagram' );
$image    = bpu_ie_field( 'contact_image', $post_id );
?>

<section class="section">
    <div class="container">
        <h2 class="section-title" style="text-align:left;"><?php echo esc_html( bpu_ie_field( 'contact_address_heading', $post_id, 'Address' ) ); ?></h2>

        <p class="org-name"><strong><?php echo esc_html( $org_name ); ?></strong></p>
        <?php if ( $address ) : ?>
            <p><?php echo wp_kses_post( nl2br( esc_html( $address ) ) ); ?></p>
        <?php endif; ?>

        <?php if ( $linkedin ) : ?>
            <p><strong><?php esc_html_e( 'LinkedIn:', 'bpu-ireland' ); ?></strong> <a href="<?php echo esc_url( $linkedin ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $linkedin ); ?></a></p>
        <?php endif; ?>
        <?php if ( $instagram ) : ?>
            <p><strong><?php esc_html_e( 'Instagram:', 'bpu-ireland' ); ?></strong> <a href="<?php echo esc_url( $instagram ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $instagram ); ?></a></p>
        <?php endif; ?>
        <?php if ( $email ) : ?>
            <p><strong><?php esc_html_e( 'Email:', 'bpu-ireland' ); ?></strong> <a href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo esc_html( antispambot( $email ) ); ?></a></p>
        <?php endif; ?>

        <?php if ( ! empty( $image['url'] ) ) : ?>
            <div class="content-image">
                <img src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ?? '' ); ?>">
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="section section-alt">
    <div class="container">
        <div class="contact-layout" style="grid-template-columns: 1fr; max-width: 620px;">
            <div>
                <h3 style="margin-bottom:1rem;"><?php esc_html_e( 'Or send us a message', 'bpu-ireland' ); ?></h3>
                <?php bpu_ie_render_contact_form(); ?>
            </div>
        </div>
    </div>
</section>

<?php
get_footer();
