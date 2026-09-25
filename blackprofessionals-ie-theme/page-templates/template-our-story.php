<?php
/**
 * Template Name: Our Story
 *
 * Visual redesign — navy hero, Purpose & Vision cards,
 * Challenge & Solution split, Impact section, CTA band.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
$post_id = get_the_ID();
?>

<!-- ── Navy hero ── -->
<header class="hero hero--navy">
    <div class="container">
        <h1><?php echo esc_html( bpu_ie_field( 'about_hero_heading', $post_id, 'Our Story' ) ); ?></h1>
        <p class="hero-sub"><?php echo esc_html( bpu_ie_field( 'about_intro_text', $post_id, 'Black Professionals Ireland is a professional network committed to empowering Black professionals and students across Ireland — building community, creating opportunity, and driving systemic change.' ) ); ?></p>
    </div>
</header>

<!-- ── Organisation story ── -->
<?php
$story_text = bpu_ie_field( 'about_story_text', $post_id,
    'Our journey began in 2016 with Edinburgh Black Professionals, a networking platform supporting Black professionals. We\'ve since expanded to become Black Professionals Scotland, spanning the entire country with thousands of members in Tech, Finance, Art, and more.' . "\n\n" .
    'Today, we extend our reach to Ireland — driven by our commitment to addressing the unique obstacles faced by Black professionals and creating a safe space for our members to share experiences, seek support and connect.'
);
?>
<section class="section section-alt">
    <div class="container">
        <div class="story-prose">
            <?php echo wp_kses_post( wpautop( $story_text ) ); ?>
        </div>
    </div>
</section>

<!-- ── Purpose & Vision ── -->
<section class="section">
    <div class="container">
        <h2 class="section-title"><?php echo esc_html( bpu_ie_field( 'purpose_vision_heading', $post_id, 'Purpose & Vision' ) ); ?></h2>
        <div class="card-grid two-col" style="margin-top:2.5rem;">
            <div class="card card--accent-top">
                <span class="card-label"><?php echo esc_html( bpu_ie_field( 'purpose_heading', $post_id, 'Our Purpose' ) ); ?></span>
                <p><?php echo esc_html( bpu_ie_field( 'purpose_text', $post_id, 'To work with members, partners, and communities to create an inclusive, equitable, and thriving professional landscape for Black professionals across Ireland.' ) ); ?></p>
            </div>
            <div class="card card--accent-top">
                <span class="card-label"><?php echo esc_html( bpu_ie_field( 'vision_heading', $post_id, 'Our Vision' ) ); ?></span>
                <p><?php echo esc_html( bpu_ie_field( 'vision_text', $post_id, 'An Ireland where Black professionals are not only well-represented but thrive, contributing their diverse talents to drive innovation, prosperity, and positive change.' ) ); ?></p>
            </div>
        </div>
    </div>
</section>

<!-- ── Why we exist ── -->
<section class="section section-alt">
    <div class="container">
        <h2 class="section-title"><?php echo esc_html( bpu_ie_field( 'why_how_heading', $post_id, 'Why We Exist' ) ); ?></h2>
        <div class="card-grid two-col" style="margin-top:2.5rem;">
            <div class="card card--problem">
                <span class="card-label"><?php echo esc_html( bpu_ie_field( 'problem_heading', $post_id, 'The Challenge' ) ); ?></span>
                <?php
                $problem_points = bpu_ie_field( 'problem_points', $post_id );
                if ( ! empty( $problem_points ) ) : ?>
                    <ul class="card-bullets">
                        <?php foreach ( $problem_points as $point ) : ?>
                            <li><?php echo wp_kses_post( $point['text'] ?? '' ); ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php else : ?>
                    <p><?php esc_html_e( 'Black professionals in Ireland still face significant barriers — limited access to networks, unconscious bias in hiring, and underrepresentation at senior levels.', 'bpu-ireland' ); ?></p>
                <?php endif; ?>
            </div>
            <div class="card card--solution">
                <span class="card-label"><?php echo esc_html( bpu_ie_field( 'solution_heading', $post_id, 'Our Approach' ) ); ?></span>
                <p><?php echo esc_html( bpu_ie_field( 'solution_text', $post_id, 'We address these challenges through mentorship programmes, skills development workshops, career-enhancing networking events, recruiting partnerships, and building a community where members can share experiences and support one another.' ) ); ?></p>
            </div>
        </div>
    </div>
</section>

<!-- ── Impact ── -->
<section class="section">
    <div class="container">
        <h2 class="section-title"><?php echo esc_html( bpu_ie_field( 'impact_heading', $post_id, 'Our Impact' ) ); ?></h2>

        <?php
        $traction_points = bpu_ie_field( 'traction_points', $post_id );
        if ( ! empty( $traction_points ) ) : ?>
            <div class="impact-list">
                <?php foreach ( $traction_points as $point ) : ?>
                    <div class="impact-item">
                        <span class="impact-dot"></span>
                        <div><?php echo wp_kses_post( $point['text'] ?? '' ); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <div class="impact-list">
                <div class="impact-item">
                    <span class="impact-dot"></span>
                    <div><?php esc_html_e( 'Our journey began in Scotland where BPE reached 900+ members and 20+ corporate partners committed to the cause.', 'bpu-ireland' ); ?></div>
                </div>
                <div class="impact-item">
                    <span class="impact-dot"></span>
                    <div><?php esc_html_e( 'Now we are bringing that same energy to Ireland — growing our membership, forging new partnerships, and building the infrastructure to support Black professionals at every stage of their careers.', 'bpu-ireland' ); ?></div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
bpu_ie_cta_band(
    bpu_ie_field( 'about_cta_heading', $post_id, 'Join our growing community' ),
    bpu_ie_field( 'about_cta_text', $post_id, 'Become part of the fastest-growing network of Black professionals in Ireland.' ),
    bpu_ie_field( 'about_cta_button_text', $post_id, 'Become a Member' ),
    bpu_ie_field( 'about_cta_button_link', $post_id, home_url( '/membership/' ) )
);

get_footer();
