<?php
/**
 * Provider-agnostic captcha support, shared by all four forms (Contact,
 * Membership, Partnership, Ambassadorship). The provider is chosen once
 * in Theme Settings → Captcha; "None" (the default) skips captcha
 * entirely so forms work before any keys are configured.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Returns 'hcaptcha' / 'recaptcha' / '' — empty when the provider is set
 * to None, or when its site/secret keys aren't both filled in yet.
 */
function bpu_ie_captcha_provider() {
    $provider = bpu_ie_option( 'captcha_provider', 'none' );

    if ( 'hcaptcha' === $provider && bpu_ie_option( 'hcaptcha_site_key' ) && bpu_ie_option( 'hcaptcha_secret_key' ) ) {
        return 'hcaptcha';
    }
    if ( 'recaptcha' === $provider && bpu_ie_option( 'recaptcha_site_key' ) && bpu_ie_option( 'recaptcha_secret_key' ) ) {
        return 'recaptcha';
    }
    return '';
}

/**
 * Outputs the captcha widget (if a provider is configured) and enqueues
 * its script. Call this just before a form's submit button.
 */
function bpu_ie_render_captcha() {
    $provider = bpu_ie_captcha_provider();
    if ( ! $provider ) {
        return;
    }

    if ( 'hcaptcha' === $provider ) {
        printf( '<div class="form-group"><div class="h-captcha" data-sitekey="%s"></div></div>', esc_attr( bpu_ie_option( 'hcaptcha_site_key' ) ) );
        wp_enqueue_script( 'bpu-ie-hcaptcha', 'https://js.hcaptcha.com/1/api.js', array(), null, true );
    } elseif ( 'recaptcha' === $provider ) {
        printf( '<div class="form-group"><div class="g-recaptcha" data-sitekey="%s"></div></div>', esc_attr( bpu_ie_option( 'recaptcha_site_key' ) ) );
        wp_enqueue_script( 'bpu-ie-recaptcha', 'https://www.google.com/recaptcha/api.js', array(), null, true );
    }
}

/**
 * Verifies the submitted captcha response server-side. Returns true when
 * no provider is configured, so forms keep working before keys are added.
 */
function bpu_ie_verify_captcha() {
    $provider = bpu_ie_captcha_provider();
    if ( ! $provider ) {
        return true;
    }

    if ( 'hcaptcha' === $provider ) {
        $secret       = bpu_ie_option( 'hcaptcha_secret_key' );
        $token        = isset( $_POST['h-captcha-response'] ) ? sanitize_text_field( wp_unslash( $_POST['h-captcha-response'] ) ) : '';
        $verify_url   = 'https://hcaptcha.com/siteverify';
        $secret_field = 'secret';
    } else {
        $secret       = bpu_ie_option( 'recaptcha_secret_key' );
        $token        = isset( $_POST['g-recaptcha-response'] ) ? sanitize_text_field( wp_unslash( $_POST['g-recaptcha-response'] ) ) : '';
        $verify_url   = 'https://www.google.com/recaptcha/api/siteverify';
        $secret_field = 'secret';
    }

    if ( ! $token ) {
        return false;
    }

    $response = wp_remote_post( $verify_url, array(
        'timeout' => 10,
        'body'    => array( $secret_field => $secret, 'response' => $token ),
    ) );
    if ( is_wp_error( $response ) ) {
        return false;
    }
    $body = json_decode( wp_remote_retrieve_body( $response ), true );
    return ! empty( $body['success'] );
}
