<?php get_header();
/*
Template Name: Home - Property Map
*/
get_template_part( 'lib/inc/template/google-map-multiple-properties' );
?>

<div class="container">
	<?php the_content(); ?>
</div><!-- .container -->

<?php get_footer(); ?>