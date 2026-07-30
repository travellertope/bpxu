<?php
/**
 * Default fallback template for plain WordPress pages that don't use one
 * of the custom page-templates/*.php templates.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
?>

<?php while ( have_posts() ) : the_post(); ?>
    <header class="hero">
        <div class="container">
            <h1><?php the_title(); ?></h1>
        </div>
    </header>
    <div class="page-content">
        <?php the_content(); ?>
    </div>
<?php endwhile; ?>

<?php
get_footer();
