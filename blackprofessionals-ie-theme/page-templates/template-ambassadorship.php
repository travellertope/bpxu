<?php
/**
 * Template Name: Ambassadorship
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
$post_id = get_the_ID();

bpu_ie_hero(
    bpu_ie_field( 'ambassadorship_hero_eyebrow', $post_id, 'Ambassadorship' ),
    bpu_ie_field( 'ambassadorship_hero_heading', $post_id, 'Become a Black Professionals Ireland Ambassador' ),
    bpu_ie_field( 'ambassadorship_hero_subtext', $post_id )
);
?>

<section class="section">
    <div class="container">
        <?php bpu_ie_tier_grid( bpu_ie_field( 'ambassadorship_tiers', $post_id ) ); ?>
    </div>
</section>

<?php
bpu_ie_cta_band(
    bpu_ie_field( 'ambassadorship_cta_heading', $post_id, 'Ready to take the next step?' ),
    bpu_ie_field( 'ambassadorship_cta_text', $post_id ),
    bpu_ie_field( 'ambassadorship_cta_button_text', $post_id, 'Get in Touch' ),
    bpu_ie_field( 'ambassadorship_cta_button_link', $post_id )
);

get_footer();
