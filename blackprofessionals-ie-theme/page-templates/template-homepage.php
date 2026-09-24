<?php
/**
 * Template Name: Homepage
 *
 * Assignable page template that renders the full homepage layout:
 * hero, about/story teaser, member benefits, partner benefits,
 * ambassadorship CTA band, and info teasers.
 *
 * Mirrors front-page.php so the same ACF fields (group_bpu_ie_home)
 * drive both; this template lets editors preview or reuse the layout
 * on any page, not only the site front page.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

$post_id        = get_the_ID();
$hero_image     = bpu_ie_field( 'home_hero_image', $post_id );
$has_hero_image = ! empty( $hero_image['url'] );
?>

<header id="hero" class="hero<?php echo $has_hero_image ? ' hero--has-image' : ''; ?>"
        <?php if ( $has_hero_image ) : ?>style="background-image:url('<?php echo esc_url( $hero_image['url'] ); ?>')"<?php endif; ?>>
    <div class="container">
        <h1><?php echo esc_html( bpu_ie_field( 'home_hero_heading', $post_id, 'Black Professionals Ireland' ) ); ?></h1>
        <p class="hero-sub"><?php echo esc_html( bpu_ie_field( 'home_hero_text', $post_id, 'We are the fastest-growing network of Black professionals and students across Ireland and we exist to support our members and the business community.' ) ); ?></p>
        <div class="hero-actions">
            <?php $cta_link = bpu_ie_field( 'home_hero_cta_link', $post_id ); ?>
            <a class="btn btn-primary" href="<?php echo esc_url( $cta_link ?: '#' ); ?>">
                <?php echo esc_html( bpu_ie_field( 'home_hero_cta_text', $post_id, 'Sign Up' ) ); ?>
            </a>
            <?php $secondary_link = bpu_ie_field( 'home_hero_secondary_link', $post_id ); ?>
            <?php if ( $secondary_link ) : ?>
                <a class="btn btn-ghost" href="<?php echo esc_url( $secondary_link ); ?>">
                    <?php echo esc_html( bpu_ie_field( 'home_hero_secondary_text', $post_id, 'Learn More' ) ); ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</header>

<?php
$about_heading = bpu_ie_field( 'home_about_heading', $post_id, 'Black Professionals Europe' );
$about_text    = bpu_ie_field( 'home_about_text', $post_id, 'Our journey began in 2016 with Edinburgh Black Professionals, a networking platform supporting Black professionals. We\'ve since expanded to become Black Professionals Scotland, spanning the entire country with thousands of members boasting expertise in Tech, Finance, Art, and more. Furthermore, we have also fostered partnerships with businesses and organisations across Scotland.' . "\n\n" . 'Today, we proudly extend our reach to the rest of Europe as Black Professionals Europe driven by our commitment to addressing the unique obstacles faced by Black professionals on the continent and creating a safe space for our members to share their experiences, seek support and connect. In the background, we are growing our membership across Europe and working toward an early 2024 launch. We are truly excited about what is coming and invite you to come on the journey with us.' );
if ( $about_heading || $about_text ) :
?>
<section class="section section-alt" id="about">
    <div class="container">
        <?php if ( $about_heading ) : ?>
            <h2 class="section-title"><?php echo esc_html( $about_heading ); ?></h2>
        <?php endif; ?>
        <?php if ( $about_text ) : ?>
            <p class="section-sub" style="max-width:820px;"><?php echo esc_html( $about_text ); ?></p>
        <?php endif; ?>
        <?php $about_link = bpu_ie_field( 'home_about_link', $post_id ); ?>
        <?php if ( $about_link ) : ?>
            <p style="text-align:center;">
                <a class="btn btn-ghost" href="<?php echo esc_url( $about_link ); ?>">
                    <?php echo esc_html( bpu_ie_field( 'home_about_link_text', $post_id, 'Read Our Story' ) ); ?>
                </a>
            </p>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<section class="section" id="members">
    <div class="container">
        <h2 class="section-title"><?php echo esc_html( bpu_ie_field( 'members_heading', $post_id, 'For Our Members' ) ); ?></h2>
        <?php $members_cards = bpu_ie_field( 'members_cards', $post_id ); ?>
        <?php if ( ! empty( $members_cards ) ) : ?>
            <div class="card-grid">
                <?php foreach ( $members_cards as $card ) : ?>
                    <div class="card">
                        <h3>
                            <?php if ( ! empty( $card['link'] ) ) : ?>
                                <a href="<?php echo esc_url( $card['link'] ); ?>"><?php echo esc_html( $card['title'] ?? '' ); ?></a>
                            <?php else : ?>
                                <?php echo esc_html( $card['title'] ?? '' ); ?>
                            <?php endif; ?>
                        </h3>
                        <p><?php echo esc_html( $card['description'] ?? '' ); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="section section-alt" id="partners">
    <div class="container">
        <h2 class="section-title"><?php echo esc_html( bpu_ie_field( 'partners_intro_heading', $post_id, 'Partnership with Black Professionals Ireland' ) ); ?></h2>
        <p class="section-sub"><?php echo esc_html( bpu_ie_field( 'partners_intro_text', $post_id ) ); ?></p>

        <h3 class="section-title" style="margin-top:2.5rem;"><?php echo esc_html( bpu_ie_field( 'partners_heading', $post_id, 'For Our Partners' ) ); ?></h3>
        <?php $partners_cards = bpu_ie_field( 'partners_cards', $post_id ); ?>
        <?php if ( ! empty( $partners_cards ) ) : ?>
            <div class="card-grid">
                <?php foreach ( $partners_cards as $card ) : ?>
                    <div class="card">
                        <h3><?php echo esc_html( $card['title'] ?? '' ); ?></h3>
                        <p><?php echo esc_html( $card['description'] ?? '' ); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php $partners_cta_link = bpu_ie_field( 'partners_cta_link', $post_id ); ?>
        <?php if ( $partners_cta_link ) : ?>
            <p style="text-align:center; margin-top:2.5rem;">
                <a class="btn btn-primary" href="<?php echo esc_url( $partners_cta_link ); ?>">
                    <?php echo esc_html( bpu_ie_field( 'partners_cta_text', $post_id, 'Become a Partner' ) ); ?>
                </a>
            </p>
        <?php endif; ?>
    </div>
</section>

<section class="section" id="ambassadorship">
    <div class="container">
        <div class="cta-band">
            <h2><?php echo esc_html( bpu_ie_field( 'ambassador_heading', $post_id, 'Ambassadorship' ) ); ?></h2>
            <h3 style="color:#fff;"><?php echo esc_html( bpu_ie_field( 'ambassador_subheading', $post_id ) ); ?></h3>
            <p><?php echo esc_html( bpu_ie_field( 'ambassador_text', $post_id ) ); ?></p>
            <?php $ambassador_link = bpu_ie_field( 'ambassador_cta_link', $post_id ); ?>
            <?php if ( $ambassador_link ) : ?>
                <a class="btn btn-primary" href="<?php echo esc_url( $ambassador_link ); ?>">
                    <?php echo esc_html( bpu_ie_field( 'ambassador_cta_text', $post_id, 'Become an Ambassador' ) ); ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="section section-alt" id="info">
    <div class="container">
        <div class="info-teasers">
            <div class="info-teaser">
                <h3><?php echo esc_html( bpu_ie_field( 'info_members_heading', $post_id, 'Information for Members' ) ); ?></h3>
                <p><?php echo esc_html( bpu_ie_field( 'info_members_text', $post_id ) ); ?></p>
                <?php $info_members_link = bpu_ie_field( 'info_members_link', $post_id ); ?>
                <?php if ( $info_members_link ) : ?>
                    <a href="<?php echo esc_url( $info_members_link ); ?>"><?php esc_html_e( 'Learn more', 'bpu-ireland' ); ?> &rarr;</a>
                <?php endif; ?>
            </div>
            <div class="info-teaser">
                <h3><?php echo esc_html( bpu_ie_field( 'info_partners_heading', $post_id, 'Information for Partners' ) ); ?></h3>
                <p><?php echo esc_html( bpu_ie_field( 'info_partners_text', $post_id ) ); ?></p>
                <?php $info_partners_link = bpu_ie_field( 'info_partners_link', $post_id ); ?>
                <?php if ( $info_partners_link ) : ?>
                    <a href="<?php echo esc_url( $info_partners_link ); ?>"><?php esc_html_e( 'Learn more', 'bpu-ireland' ); ?> &rarr;</a>
                <?php endif; ?>
            </div>
        </div>

        <?php $closing_image = bpu_ie_field( 'home_closing_image', $post_id ); ?>
        <?php if ( ! empty( $closing_image['url'] ) ) : ?>
            <div class="home-closing-image">
                <img src="<?php echo esc_url( $closing_image['url'] ); ?>" alt="<?php echo esc_attr( $closing_image['alt'] ?? '' ); ?>">
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
get_footer();
