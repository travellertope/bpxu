<?php
/**
 * Template Name: Our Story
 *
 * Content ported from the live blackprofessionals.eu About page: intro,
 * Purpose & Vision, Problem & Solution, and Impact/Traction. (On this
 * site, "About" is just the nav hub — this page carries the actual
 * story/content.)
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
$post_id = get_the_ID();

bpu_ie_hero(
    bpu_ie_field( 'about_hero_eyebrow', $post_id, 'About Us' ),
    get_the_title(),
    bpu_ie_field( 'about_intro_text', $post_id )
);

function bpu_ie_render_content_image( $image ) {
    if ( empty( $image['url'] ) ) {
        return;
    }
    ?>
    <div class="content-image">
        <img src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ?? '' ); ?>">
    </div>
    <?php
}

function bpu_ie_render_bullet_points( $points ) {
    if ( empty( $points ) ) {
        return;
    }
    ?>
    <ul class="bullet-list">
        <?php foreach ( $points as $point ) : ?>
            <li><?php echo wp_kses_post( $point['text'] ?? '' ); ?></li>
        <?php endforeach; ?>
    </ul>
    <?php
}
?>

<section class="section">
    <div class="container">
        <h2 class="section-title" style="text-align:left;"><?php echo esc_html( bpu_ie_field( 'purpose_heading', $post_id, 'Purpose' ) ); ?></h2>
        <p><?php echo esc_html( bpu_ie_field( 'purpose_text', $post_id ) ); ?></p>

        <h2 class="section-title" style="text-align:left; margin-top:2rem;"><?php echo esc_html( bpu_ie_field( 'vision_heading', $post_id, 'Vision' ) ); ?></h2>
        <p><?php echo esc_html( bpu_ie_field( 'vision_text', $post_id ) ); ?></p>

        <?php bpu_ie_render_content_image( bpu_ie_field( 'about_image_1', $post_id ) ); ?>
    </div>
</section>

<section class="section section-alt">
    <div class="container">
        <h2 class="section-title" style="text-align:left;"><?php echo esc_html( bpu_ie_field( 'why_how_heading', $post_id, 'Why and How?' ) ); ?></h2>

        <h3><?php echo esc_html( bpu_ie_field( 'problem_heading', $post_id, 'Problem:' ) ); ?></h3>
        <?php bpu_ie_render_bullet_points( bpu_ie_field( 'problem_points', $post_id ) ); ?>

        <h3 style="margin-top:2rem;"><?php echo esc_html( bpu_ie_field( 'solution_heading', $post_id, 'Solution:' ) ); ?></h3>
        <p><?php echo esc_html( bpu_ie_field( 'solution_text', $post_id ) ); ?></p>

        <?php bpu_ie_render_content_image( bpu_ie_field( 'about_image_2', $post_id ) ); ?>
    </div>
</section>

<section class="section">
    <div class="container">
        <h2 class="section-title" style="text-align:left;"><?php echo esc_html( bpu_ie_field( 'impact_heading', $post_id, 'Impact: Driving Positive Change' ) ); ?></h2>

        <h3><?php echo esc_html( bpu_ie_field( 'traction_heading', $post_id, 'Traction:' ) ); ?></h3>
        <?php bpu_ie_render_bullet_points( bpu_ie_field( 'traction_points', $post_id ) ); ?>

        <?php bpu_ie_render_content_image( bpu_ie_field( 'about_image_3', $post_id ) ); ?>
    </div>
</section>

<?php
get_footer();
