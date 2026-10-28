<?php
/**
 * Template Name: Partnership
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
$post_id = get_the_ID();

bpu_ie_hero(
    bpu_ie_field( 'partnership_hero_eyebrow', $post_id, 'Partnership' ),
    bpu_ie_field( 'partnership_hero_heading', $post_id, 'Partner with Black Professionals Ireland' ),
    bpu_ie_field( 'partnership_hero_subtext', $post_id )
);
?>

<section class="section">
    <div class="container">
        <?php bpu_ie_tier_grid( bpu_ie_field( 'partnership_tiers', $post_id ) ); ?>
    </div>
</section>

<section class="section section-alt">
    <div class="container">
        <?php bpu_ie_render_partnership_form(); ?>
    </div>
</section>

<?php
bpu_ie_cta_band(
    bpu_ie_field( 'partnership_cta_heading', $post_id, 'Ready to partner with us?' ),
    bpu_ie_field( 'partnership_cta_text', $post_id, 'Join organisations committed to diversity, equity, and inclusion — and gain access to Ireland\'s most dynamic network of Black professionals.' ),
    bpu_ie_field( 'partnership_cta_button_text', $post_id, 'Become a Partner' ),
    bpu_ie_field( 'partnership_cta_button_link', $post_id )
);

get_footer();
