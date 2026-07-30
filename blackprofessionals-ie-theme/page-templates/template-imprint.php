<?php
/**
 * Template Name: Imprint
 *
 * German-law legal notice (Impressum) for the registered entity behind
 * this site. Org name, address, and email live in Theme Settings so this
 * page and the site footer always show the same details.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

$org_name     = bpu_ie_option( 'org_legal_name', 'Black Professionals Europe e. V.' );
$address      = bpu_ie_option( 'contact_address' );
$email        = bpu_ie_option( 'contact_email' );
$board        = bpu_ie_field( 'imprint_board_members' );
$image        = bpu_ie_field( 'imprint_image' );
?>

<div class="page-content">
    <h1><?php echo esc_html( bpu_ie_field( 'imprint_heading', get_the_ID(), 'Imprint' ) ); ?></h1>

    <h3><?php echo esc_html( bpu_ie_field( 'imprint_provider_heading', get_the_ID(), 'The provider of this website is:' ) ); ?></h3>

    <p class="org-name"><strong><?php echo esc_html( $org_name ); ?></strong></p>
    <?php if ( $address ) : ?>
        <p><?php echo wp_kses_post( nl2br( esc_html( $address ) ) ); ?></p>
    <?php endif; ?>

    <?php if ( ! empty( $board ) ) : ?>
        <h4><?php echo esc_html( bpu_ie_field( 'imprint_board_heading', get_the_ID(), 'Authorized representative board:' ) ); ?></h4>
        <ol>
            <?php foreach ( $board as $member ) : ?>
                <li><?php echo esc_html( $member['name'] ?? '' ); ?></li>
            <?php endforeach; ?>
        </ol>
    <?php endif; ?>

    <?php if ( $email ) : ?>
        <p><?php esc_html_e( 'E-Mail:', 'bpu-ireland' ); ?> <a href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo esc_html( antispambot( $email ) ); ?></a></p>
    <?php endif; ?>

    <?php $registration = bpu_ie_field( 'imprint_registration_text' ); ?>
    <?php if ( $registration ) : ?>
        <p><?php echo esc_html( $registration ); ?></p>
    <?php endif; ?>

    <?php $vat = bpu_ie_field( 'imprint_vat_id' ); ?>
    <?php if ( $vat ) : ?>
        <p><?php esc_html_e( 'VAT ID:', 'bpu-ireland' ); ?> <?php echo esc_html( $vat ); ?></p>
    <?php endif; ?>

    <?php if ( ! empty( $image['url'] ) ) : ?>
        <img src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ?? '' ); ?>">
    <?php endif; ?>

    <div class="legal-block">
        <p class="org-name"><?php echo esc_html( $org_name ); ?></p>
        <?php if ( $address ) : ?>
            <p><?php echo wp_kses_post( nl2br( esc_html( $address ) ) ); ?></p>
        <?php endif; ?>
        <div class="footer-social" style="margin-top:1rem;">
            <?php bpu_ie_social_links(); ?>
        </div>
    </div>
</div>

<?php
get_footer();
