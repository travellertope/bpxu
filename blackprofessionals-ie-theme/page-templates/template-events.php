<?php
/**
 * Template Name: Events
 *
 * Lists bpu_event posts (see inc/custom-post-types.php), split into
 * Upcoming and Past based on the event_date ACF field.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
$post_id = get_the_ID();
$today   = date_i18n( 'Ymd' );

bpu_ie_hero(
    bpu_ie_field( 'events_hero_eyebrow', $post_id, 'Events' ),
    bpu_ie_field( 'events_hero_heading', $post_id, 'Upcoming events & workshops' ),
    bpu_ie_field( 'events_hero_subtext', $post_id )
);

$upcoming = new WP_Query( array(
    'post_type'      => 'bpu_event',
    'posts_per_page' => -1,
    'meta_key'       => 'event_date',
    'orderby'        => 'meta_value',
    'order'          => 'ASC',
    'meta_query'     => array( array( 'key' => 'event_date', 'value' => $today, 'compare' => '>=', 'type' => 'NUMERIC' ) ),
) );

$past = new WP_Query( array(
    'post_type'      => 'bpu_event',
    'posts_per_page' => 10,
    'meta_key'       => 'event_date',
    'orderby'        => 'meta_value',
    'order'          => 'DESC',
    'meta_query'     => array( array( 'key' => 'event_date', 'value' => $today, 'compare' => '<', 'type' => 'NUMERIC' ) ),
) );

function bpu_ie_render_event_card( $is_past = false ) {
    $date_parts = bpu_ie_event_date_parts( get_field( 'event_date' ) );
    $time       = get_field( 'event_time' );
    $location   = get_field( 'event_location' );
    $link       = get_field( 'event_registration_link' );
    ?>
    <div class="event-card <?php echo $is_past ? 'is-past' : ''; ?>">
        <div class="event-date">
            <span class="day"><?php echo esc_html( $date_parts['day'] ); ?></span>
            <span class="month"><?php echo esc_html( $date_parts['month'] ); ?></span>
        </div>
        <div>
            <p class="event-title"><?php the_title(); ?></p>
            <p class="event-meta">
                <?php if ( $time ) : ?><span><?php echo esc_html( $time ); ?></span><?php endif; ?>
                <?php if ( $location ) : ?><span><?php echo esc_html( $location ); ?></span><?php endif; ?>
            </p>
        </div>
        <?php if ( ! $is_past && $link ) : ?>
            <a class="btn btn-ghost" href="<?php echo esc_url( $link ); ?>"><?php esc_html_e( 'Register', 'bpu-ireland' ); ?></a>
        <?php endif; ?>
    </div>
    <?php
}
?>

<section class="section">
    <div class="container">
        <?php if ( $upcoming->have_posts() ) : ?>
            <div class="event-list">
                <?php while ( $upcoming->have_posts() ) : $upcoming->the_post(); ?>
                    <?php bpu_ie_render_event_card( false ); ?>
                <?php endwhile; ?>
            </div>
        <?php else : ?>
            <p class="empty-state"><?php esc_html_e( 'No upcoming events yet — check back soon.', 'bpu-ireland' ); ?></p>
        <?php endif; ?>
        <?php wp_reset_postdata(); ?>
    </div>
</section>

<?php if ( $past->have_posts() ) : ?>
    <section class="section section-alt">
        <div class="container">
            <h2 class="section-title"><?php esc_html_e( 'Past Events', 'bpu-ireland' ); ?></h2>
            <div class="event-list">
                <?php while ( $past->have_posts() ) : $past->the_post(); ?>
                    <?php bpu_ie_render_event_card( true ); ?>
                <?php endwhile; ?>
            </div>
        </div>
    </section>
    <?php wp_reset_postdata(); ?>
<?php endif; ?>

<?php
get_footer();
