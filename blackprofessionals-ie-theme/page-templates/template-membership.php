<?php
/**
 * Template Name: Membership
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
$post_id = get_the_ID();

bpu_ie_hero(
    bpu_ie_field( 'membership_hero_eyebrow', $post_id, 'Membership' ),
    bpu_ie_field( 'membership_hero_heading', $post_id, 'Join Black Professionals Ireland' ),
    bpu_ie_field( 'membership_hero_subtext', $post_id )
);
?>

<section class="section">
    <div class="container">
        <?php bpu_ie_tier_grid( bpu_ie_field( 'membership_tiers', $post_id ) ); ?>
    </div>
</section>

<section class="section section-alt">
    <div class="container">
        <?php bpu_ie_render_membership_form(); ?>
    </div>
</section>

<?php
bpu_ie_cta_band(
    bpu_ie_field( 'membership_cta_heading', $post_id, 'Ready to take the next step?' ),
    bpu_ie_field( 'membership_cta_text', $post_id ),
    bpu_ie_field( 'membership_cta_button_text', $post_id, 'Get in Touch' ),
    bpu_ie_field( 'membership_cta_button_link', $post_id )
);

get_footer();
