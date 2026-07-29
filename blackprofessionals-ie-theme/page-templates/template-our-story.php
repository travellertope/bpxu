<?php
/**
 * Template Name: Our Story
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
$post_id = get_the_ID();

bpu_ie_hero(
    bpu_ie_field( 'story_hero_eyebrow', $post_id, 'Our Story' ),
    bpu_ie_field( 'story_hero_heading', $post_id, 'How Black Professionals Ireland came to be' ),
    bpu_ie_field( 'story_hero_subtext', $post_id )
);
?>

<section class="section">
    <div class="container">
        <?php $quote = bpu_ie_field( 'story_quote', $post_id ); ?>
        <?php if ( $quote ) : ?>
            <p class="pull-quote"><?php echo esc_html( $quote ); ?></p>
        <?php endif; ?>

        <?php $timeline = bpu_ie_field( 'story_timeline', $post_id ); ?>
        <?php if ( ! empty( $timeline ) ) : ?>
            <div class="timeline">
                <?php foreach ( $timeline as $item ) : ?>
                    <div class="timeline-item">
                        <div class="timeline-year"><?php echo esc_html( $item['year'] ?? '' ); ?></div>
                        <div class="timeline-body">
                            <h4><?php echo esc_html( $item['title'] ?? '' ); ?></h4>
                            <p><?php echo esc_html( $item['description'] ?? '' ); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
get_footer();
