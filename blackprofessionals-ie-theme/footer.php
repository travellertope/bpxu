</main>

<footer class="site-footer">
    <div class="container">
        <div class="footer-legal">
            <p class="footer-org-name"><?php echo esc_html( bpu_ie_option( 'org_legal_name', 'Black Professionals Europe e. V.' ) ); ?></p>
            <?php $address = bpu_ie_option( 'contact_address' ); ?>
            <?php if ( $address ) : ?>
                <p class="footer-address"><?php echo wp_kses_post( nl2br( esc_html( $address ) ) ); ?></p>
            <?php endif; ?>
        </div>

        <?php
        wp_nav_menu( array(
            'theme_location' => 'footer',
            'container'      => 'nav',
            'container_class' => 'footer-nav',
            'menu_class'     => 'menu',
            'fallback_cb'    => false,
            'depth'          => 1,
        ) );
        ?>

        <div class="footer-social">
            <?php bpu_ie_social_links(); ?>
        </div>

        <div class="footer-bottom">
            <span>&copy; <?php echo esc_html( date_i18n( 'Y' ) ); ?> <?php echo esc_html( bpu_ie_option( 'org_legal_name', get_bloginfo( 'name' ) ) ); ?>. <?php esc_html_e( 'All rights reserved.', 'bpu-ireland' ); ?></span>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
