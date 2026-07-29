<?php
/**
 * Template Name: About
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
$post_id = get_the_ID();

bpu_ie_hero(
    bpu_ie_field( 'about_hero_eyebrow', $post_id, 'About Us' ),
    bpu_ie_field( 'about_hero_heading', $post_id, 'Building community and opportunity for Black professionals in Ireland' )
);
?>

<section class="section">
    <div class="container">
        <?php $intro = bpu_ie_field( 'about_intro_text', $post_id ); ?>
        <?php if ( $intro ) : ?>
            <p class="pull-quote"><?php echo esc_html( $intro ); ?></p>
        <?php endif; ?>

        <h2 class="section-title"><?php echo esc_html( bpu_ie_field( 'about_mission_heading', $post_id, 'Our Mission' ) ); ?></h2>
        <p class="section-sub"><?php echo esc_html( bpu_ie_field( 'about_mission_text', $post_id ) ); ?></p>

        <?php $values = bpu_ie_field( 'about_values', $post_id ); ?>
        <?php if ( ! empty( $values ) ) : ?>
            <div class="card-grid">
                <?php foreach ( $values as $value ) : ?>
                    <div class="card">
                        <h3><?php echo esc_html( $value['title'] ?? '' ); ?></h3>
                        <p><?php echo esc_html( $value['description'] ?? '' ); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
get_footer();
