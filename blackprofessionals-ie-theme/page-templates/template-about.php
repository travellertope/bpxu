<?php
/**
 * Template Name: About
 *
 * A lightweight hub page — the real story/content lives on Our Story
 * (a child of this page). This just introduces the section and links
 * out to its children (Our Story, Our Team), so nothing needs manual
 * linking as long as those pages are set as children of this one.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
$post_id = get_the_ID();

bpu_ie_hero(
    bpu_ie_field( 'about_hub_eyebrow', $post_id, 'About' ),
    get_the_title(),
    bpu_ie_field( 'about_hub_intro', $post_id, 'Learn more about our mission, our journey, and the team driving Black Professionals Ireland forward.' )
);

$children = get_pages( array( 'child_of' => $post_id, 'sort_column' => 'menu_order' ) );
?>

<section class="section">
    <div class="container">
        <?php if ( ! empty( $children ) ) : ?>
            <div class="card-grid">
                <?php foreach ( $children as $child ) : ?>
                    <a class="card" href="<?php echo esc_url( get_permalink( $child ) ); ?>">
                        <h3><?php echo esc_html( get_the_title( $child ) ); ?></h3>
                        <?php if ( has_excerpt( $child ) ) : ?>
                            <p><?php echo esc_html( get_the_excerpt( $child ) ); ?></p>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
get_footer();
