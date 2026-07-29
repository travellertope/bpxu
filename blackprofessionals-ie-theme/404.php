<?php
/**
 * 404 template.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
?>

<div class="error-404">
    <div class="container">
        <h1>404</h1>
        <p><?php esc_html_e( 'The page you\'re looking for doesn\'t exist.', 'bpu-ireland' ); ?></p>
        <a class="btn btn-primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to Home', 'bpu-ireland' ); ?></a>
    </div>
</div>

<?php
get_footer();
