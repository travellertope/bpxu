<?php
/**
 * SMTP configuration for wp_mail(), driven by Theme Settings → SMTP.
 * Off by default (uses the server's normal mail() function); switching
 * "Send mail via SMTP" on and filling in host/credentials routes all
 * outgoing mail (contact/membership/partnership/ambassador
 * notifications) through that SMTP server instead.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Sets the From address/name on all outgoing mail when configured,
 * regardless of whether SMTP itself is enabled — this alone fixes the
 * default "wordpress@yourdomain" sender most hosts fall back to.
 */
function bpu_ie_mail_from( $original_email ) {
    $from = bpu_ie_option( 'smtp_from_email' );
    return $from ? $from : $original_email;
}
add_filter( 'wp_mail_from', 'bpu_ie_mail_from' );

function bpu_ie_mail_from_name( $original_name ) {
    $name = bpu_ie_option( 'smtp_from_name' );
    return $name ? $name : $original_name;
}
add_filter( 'wp_mail_from_name', 'bpu_ie_mail_from_name' );

/**
 * Configures PHPMailer to send via SMTP when enabled and a host is set.
 */
function bpu_ie_configure_smtp( $phpmailer ) {
    if ( ! bpu_ie_option( 'smtp_enabled' ) ) {
        return;
    }
    $host = bpu_ie_option( 'smtp_host' );
    if ( ! $host ) {
        return;
    }

    $phpmailer->isSMTP();
    $phpmailer->Host       = $host;
    $phpmailer->Port       = (int) bpu_ie_option( 'smtp_port', 587 );
    $phpmailer->SMTPAuth   = (bool) bpu_ie_option( 'smtp_username' );
    $phpmailer->Username   = bpu_ie_option( 'smtp_username' );
    $phpmailer->Password   = bpu_ie_option( 'smtp_password' );

    $encryption = bpu_ie_option( 'smtp_encryption', 'tls' );
    if ( 'none' === $encryption ) {
        $phpmailer->SMTPSecure  = '';
        $phpmailer->SMTPAutoTLS = false;
    } else {
        $phpmailer->SMTPSecure = $encryption; // 'tls' or 'ssl'
    }
}
add_action( 'phpmailer_init', 'bpu_ie_configure_smtp' );
