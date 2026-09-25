<?php
/**
 * Template Name: Our Team
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
$post_id = get_the_ID();

bpu_ie_hero(
    '',
    bpu_ie_field( 'team_hero_heading', $post_id, 'Meet the Team' ),
    bpu_ie_field( 'team_hero_subtext', $post_id, 'Black Professionals Ireland is led by a dedicated group of professionals who are passionate about building an equitable future for the Black community across Ireland.' )
);

$members = bpu_ie_field( 'team_members', $post_id );
?>

<!-- ── Team intro ── -->
<section class="section section-alt">
    <div class="container">
        <div style="max-width:720px; margin:0 auto; text-align:center;">
            <p><?php echo esc_html( bpu_ie_field( 'team_intro_text', $post_id, 'Our board and leadership team bring diverse backgrounds in technology, finance, law, and the arts. Together, they steer our programmes, partnerships, and community initiatives to create lasting impact for Black professionals in Ireland.' ) ); ?></p>
        </div>
    </div>
</section>

<!-- ── Team grid ── -->
<section class="section">
    <div class="container">
        <?php if ( ! empty( $members ) ) : ?>
            <div class="team-grid">
                <?php foreach ( $members as $member ) : ?>
                    <div class="team-member">
                        <?php if ( ! empty( $member['photo']['sizes']['thumbnail'] ) ) : ?>
                            <img class="team-photo" src="<?php echo esc_url( $member['photo']['sizes']['thumbnail'] ); ?>" alt="<?php echo esc_attr( $member['name'] ?? '' ); ?>">
                        <?php else : ?>
                            <div class="team-photo"></div>
                        <?php endif; ?>
                        <p class="team-name"><?php echo esc_html( $member['name'] ?? '' ); ?></p>
                        <p class="team-role"><?php echo esc_html( $member['role'] ?? '' ); ?></p>
                        <?php if ( ! empty( $member['bio'] ) ) : ?>
                            <p class="team-bio"><?php echo esc_html( $member['bio'] ); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <div style="text-align:center; padding:3rem 0;">
                <h2 class="section-title">We&rsquo;re building our team</h2>
                <p class="section-sub" style="max-width:560px; margin:0 auto 2rem;">We are a growing organisation and will be sharing more about our leadership team soon. If you are passionate about empowering Black professionals in Ireland, we&rsquo;d love to hear from you.</p>
                <a class="btn btn-primary" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Get in Touch', 'bpu-ireland' ); ?></a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
bpu_ie_cta_band(
    bpu_ie_field( 'team_cta_heading', $post_id, 'Want to get involved?' ),
    bpu_ie_field( 'team_cta_text', $post_id, 'There are many ways to contribute — from volunteering and mentoring to becoming an ambassador for Black Professionals Ireland.' ),
    bpu_ie_field( 'team_cta_button_text', $post_id, 'Become an Ambassador' ),
    bpu_ie_field( 'team_cta_button_link', $post_id, home_url( '/ambassadorship/' ) )
);

get_footer();
