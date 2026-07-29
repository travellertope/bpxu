<?php
/**
 * Reusable template helpers shared across page templates.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Small wrapper so templates work identically whether or not ACF is active.
 */
function bpu_ie_field( $selector, $post_id = false, $default = '' ) {
    if ( function_exists( 'get_field' ) ) {
        $value = get_field( $selector, $post_id );
        if ( ! empty( $value ) ) {
            return $value;
        }
    }
    return $default;
}

function bpu_ie_option( $selector, $default = '' ) {
    if ( function_exists( 'get_field' ) ) {
        $value = get_field( $selector, 'option' );
        if ( ! empty( $value ) ) {
            return $value;
        }
    }
    return $default;
}

/**
 * Where "new submission" emails go, for every form. Uses the dedicated
 * Notification Email if set, otherwise falls back to the public Contact
 * Email, otherwise the site admin email. Supports a comma-separated list
 * of addresses.
 */
function bpu_ie_notification_recipients() {
    $value  = bpu_ie_option( 'notification_email', bpu_ie_option( 'contact_email', get_option( 'admin_email' ) ) );
    $emails = array_filter( array_map( 'trim', explode( ',', $value ) ) );
    return $emails ? $emails : array( get_option( 'admin_email' ) );
}

/**
 * Renders a standard page hero (eyebrow + heading + optional subtext).
 */
function bpu_ie_hero( $eyebrow = '', $heading = '', $subtext = '' ) {
    ?>
    <header class="hero">
        <div class="container">
            <?php if ( $eyebrow ) : ?>
                <span class="hero-eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
            <?php endif; ?>
            <?php if ( $heading ) : ?>
                <h1><?php echo wp_kses_post( $heading ); ?></h1>
            <?php endif; ?>
            <?php if ( $subtext ) : ?>
                <p class="hero-sub"><?php echo esc_html( $subtext ); ?></p>
            <?php endif; ?>
        </div>
    </header>
    <?php
}

/**
 * Renders a tier/pricing grid used by Membership, Partnership and
 * Ambassadorship templates.
 *
 * @param array $tiers Each item: name, price, is_featured, benefits[], cta_text, cta_link.
 */
function bpu_ie_tier_grid( $tiers ) {
    if ( empty( $tiers ) ) {
        return;
    }
    ?>
    <div class="tier-grid">
        <?php foreach ( $tiers as $tier ) : ?>
            <div class="tier-card <?php echo ! empty( $tier['is_featured'] ) ? 'is-featured' : ''; ?>">
                <h3 class="tier-name"><?php echo esc_html( $tier['name'] ?? '' ); ?></h3>
                <?php if ( ! empty( $tier['price'] ) ) : ?>
                    <div class="tier-price"><?php echo esc_html( $tier['price'] ); ?></div>
                <?php endif; ?>
                <?php if ( ! empty( $tier['benefits'] ) ) : ?>
                    <ul class="tier-benefits">
                        <?php foreach ( $tier['benefits'] as $benefit ) : ?>
                            <li><?php echo esc_html( $benefit['text'] ?? '' ); ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
                <?php if ( ! empty( $tier['cta_link'] ) ) : ?>
                    <a class="btn btn-primary" href="<?php echo esc_url( $tier['cta_link'] ); ?>"><?php echo esc_html( $tier['cta_text'] ?? 'Apply Now' ); ?></a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
}

/**
 * Renders the navy closing call-to-action band.
 */
function bpu_ie_cta_band( $heading, $text, $button_text, $button_link ) {
    if ( ! $heading && ! $text ) {
        return;
    }
    ?>
    <section class="section">
        <div class="container">
            <div class="cta-band">
                <?php if ( $heading ) : ?><h2><?php echo esc_html( $heading ); ?></h2><?php endif; ?>
                <?php if ( $text ) : ?><p><?php echo esc_html( $text ); ?></p><?php endif; ?>
                <?php if ( $button_link ) : ?>
                    <a class="btn btn-primary" href="<?php echo esc_url( $button_link ); ?>"><?php echo esc_html( $button_text ?: 'Get in Touch' ); ?></a>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php
}

/**
 * Outputs the footer social icons from Theme Settings, skipping any that
 * are empty. Order matches the live site: LinkedIn, Instagram, then a
 * mailto icon built from the Theme Settings contact email.
 */
function bpu_ie_social_links() {
    $links = array(
        'linkedin'  => bpu_ie_option( 'social_linkedin' ),
        'instagram' => bpu_ie_option( 'social_instagram' ),
        'facebook'  => bpu_ie_option( 'social_facebook' ),
        'twitter'   => bpu_ie_option( 'social_twitter' ),
    );
    foreach ( $links as $name => $url ) {
        if ( $url ) {
            printf(
                '<a href="%s" target="_blank" rel="noopener noreferrer" aria-label="%s">%s</a>',
                esc_url( $url ),
                esc_attr( ucfirst( $name ) ),
                esc_html( ucfirst( substr( $name, 0, 1 ) ) )
            );
        }
    }

    $email = bpu_ie_option( 'contact_email' );
    if ( $email ) {
        printf(
            '<a href="mailto:%1$s" aria-label="%2$s">%3$s</a>',
            esc_attr( antispambot( $email ) ),
            esc_attr__( 'Email', 'bpu-ireland' ),
            '&#9993;'
        );
    }
}

/**
 * Formats a bpu_event's ACF date_picker value (Ymd) for the event badge.
 */
function bpu_ie_event_date_parts( $ymd ) {
    if ( ! $ymd ) {
        return array( 'day' => '', 'month' => '' );
    }
    $timestamp = strtotime( $ymd );
    return array(
        'day'   => date_i18n( 'j', $timestamp ),
        'month' => date_i18n( 'M', $timestamp ),
    );
}
