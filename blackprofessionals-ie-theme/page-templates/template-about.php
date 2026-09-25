<?php
/**
 * Template Name: About
 *
 * Full "Our Story" page — hero, organisation intro, Purpose & Vision,
 * Why & How, Impact. All sections have hardcoded defaults so the page
 * looks complete before any ACF data is entered.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
$post_id = get_the_ID();

bpu_ie_hero(
    '',
    bpu_ie_field( 'about_hub_heading', $post_id, 'Our Story' ),
    bpu_ie_field( 'about_hub_intro', $post_id, 'Black Professionals Ireland is a professional network committed to empowering Black professionals and students across Ireland — building community, creating opportunity, and driving systemic change.' )
);
?>

<!-- ── Org intro ── -->
<?php
$story_text = bpu_ie_field( 'about_story_text', $post_id,
    'Our journey began in 2016 with Edinburgh Black Professionals, a networking platform supporting Black professionals. We\'ve since expanded to become Black Professionals Scotland, spanning the entire country with thousands of members boasting expertise in Tech, Finance, Art, and more. Furthermore, we have also fostered partnerships with businesses and organisations across Scotland.' . "\n\n" .
    'Today, we proudly extend our reach to Ireland as Black Professionals Ireland, driven by our commitment to addressing the unique obstacles faced by Black professionals and creating a safe space for our members to share their experiences, seek support and connect. We are truly excited about what is coming and invite you to come on the journey with us.'
);
?>
<section class="section section-alt">
    <div class="container">
        <div style="max-width:820px; margin:0 auto; text-align:center;">
            <?php echo wp_kses_post( wpautop( $story_text ) ); ?>
        </div>
    </div>
</section>

<!-- ── Purpose & Vision ── -->
<section class="section">
    <div class="container">
        <h2 class="section-title"><?php echo esc_html( bpu_ie_field( 'purpose_vision_heading', $post_id, 'Purpose & Vision' ) ); ?></h2>
        <div class="card-grid" style="margin-top:2rem;">
            <div class="card">
                <h3><?php echo esc_html( bpu_ie_field( 'purpose_heading', $post_id, 'Our Purpose' ) ); ?></h3>
                <p><?php echo esc_html( bpu_ie_field( 'purpose_text', $post_id, 'To work with members, partners, and communities to create an inclusive, equitable, and thriving professional landscape for Black professionals across Ireland.' ) ); ?></p>
            </div>
            <div class="card">
                <h3><?php echo esc_html( bpu_ie_field( 'vision_heading', $post_id, 'Our Vision' ) ); ?></h3>
                <p><?php echo esc_html( bpu_ie_field( 'vision_text', $post_id, 'An Ireland where Black professionals are not only well-represented but thrive, contributing their diverse talents to drive innovation, prosperity, and positive change.' ) ); ?></p>
            </div>
        </div>
    </div>
</section>

<!-- ── Why & How ── -->
<section class="section section-alt">
    <div class="container">
        <h2 class="section-title"><?php echo esc_html( bpu_ie_field( 'why_how_heading', $post_id, 'Why We Exist' ) ); ?></h2>
        <div class="card-grid" style="margin-top:2rem;">
            <div class="card">
                <h3><?php echo esc_html( bpu_ie_field( 'problem_heading', $post_id, 'The Challenge' ) ); ?></h3>
                <p><?php echo esc_html( bpu_ie_field( 'problem_text', $post_id, 'Black professionals in Ireland still face significant barriers — limited access to networks, unconscious bias in hiring, and underrepresentation at senior levels. This hinders both individual careers and organisational diversity.' ) ); ?></p>
            </div>
            <div class="card">
                <h3><?php echo esc_html( bpu_ie_field( 'solution_heading', $post_id, 'Our Approach' ) ); ?></h3>
                <p><?php echo esc_html( bpu_ie_field( 'solution_text', $post_id, 'We address these challenges through mentorship programmes, skills development workshops, career-enhancing networking events, recruiting partnerships, and building a community where members can share experiences and support one another.' ) ); ?></p>
            </div>
        </div>
    </div>
</section>

<!-- ── Impact ── -->
<section class="section">
    <div class="container">
        <h2 class="section-title"><?php echo esc_html( bpu_ie_field( 'impact_heading', $post_id, 'Our Impact' ) ); ?></h2>
        <div style="max-width:820px; margin:2rem auto 0; text-align:center;">
            <?php
            $impact_text = bpu_ie_field( 'impact_text', $post_id,
                'Our journey began in Scotland where BPE achieved significant milestones — 900+ members benefiting from programmes and 20+ corporate partners committed to the cause.' . "\n\n" .
                'Now we are bringing that same energy to Ireland. We are growing our membership, forging new partnerships, and building the infrastructure to support Black professionals at every stage of their careers.'
            );
            echo wp_kses_post( wpautop( $impact_text ) );
            ?>
        </div>
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
