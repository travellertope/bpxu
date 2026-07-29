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
    bpu_ie_field( 'team_hero_eyebrow', $post_id, 'Our Team' ),
    bpu_ie_field( 'team_hero_heading', $post_id, 'Board Members' ),
    bpu_ie_field( 'team_hero_subtext', $post_id )
);

$members = bpu_ie_field( 'team_members', $post_id );
?>

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
            <p class="empty-state"><?php esc_html_e( 'Team members will appear here once added in the page editor.', 'bpu-ireland' ); ?></p>
        <?php endif; ?>
    </div>
</section>

<?php
get_footer();
