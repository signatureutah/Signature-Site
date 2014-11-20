<?php
/* QUERY PROPERTIES
============================== */
if ( is_author() ) {
	$author = get_user_by( 'slug', get_query_var( 'author_name' ) );
	$property_args = array(
		'post_type' 				=> 'property',
		'posts_per_page' 		=> -1,
		'author'						=> $author->ID,
		/*
		'meta_query' 				=> array(
			array(
				'key' 		=> 'estate_property_custom_agent',
				'value' 	=> $author->ID,
				'compare'	=> '='
			)
		)'*/
	);
}
else {
	$property_args = array(
		'post_type' 				=> 'property',
		'posts_per_page' 		=> -1,
		'meta_query' 				=> array(
			array(
				'key' 	=> 'estate_property_featured',
				'value' => 1,
				'type'  => 'NUMERIC'
			)
		)
	);
}

$query_property = new WP_Query( $property_args );

global $post;

if ( $query_property->have_posts() ) : 

// On author page use two column carousel. All other carousel column counts are set via shortcode.
if ( is_author() ) {
	echo '<h3 class="section-title"><span>' . __( 'My Listings', 'tt' ) . '</span></h3>';
	echo '<div class="owl-carousel-2">';
}

while ( $query_property->have_posts() ) : $query_property->the_post();
$property_location = get_the_terms( $post->ID, 'property-location' );
$property_status = get_the_terms( $post->ID, 'property-status' );
$property_type = get_the_terms( $post->ID, 'property-type' );
if ( $property_type || $property_status || $property_location ) {	
	$no_property_details = false;
}
else {
	$no_property_details = true;	
}
?>
<div>
	<a href="<?php the_permalink(); ?>">
		<div class="property-thumbnail">
			<?php 
			if ( has_post_thumbnail() ) { 
				the_post_thumbnail( 'property-thumb' );
			}	
			else {
				echo '<img src ="//placehold.it/600x300/eee/ccc/&text=.." />';
			}
			?>
		</div>
		<div class="content-with-details">
			<div <?php if ( $no_property_details ) { echo 'class="no-details"'; } ?>>
				<?php the_title( '<h4 class="title">', '</h4>' ); ?>
				<div class="property-price">
					<?php
					// echo tt_icon_new_property();
					if ( $property_status ) {
						foreach ( $property_status as $status ) { echo $status->name . ': '; break; } 
					}
					echo tt_property_price();
					?>
					</div>
				<?php if ( !$no_property_details ) { ?>
				<div class="on-hover">
					<?php
					$property_meta = array();
					if ( $property_type ) {
						foreach ( $property_type as $type ) { $property_meta[] = $type->name; break; } 
					}
					if ( $property_location ) {
						foreach ( $property_location as $location ) { $property_meta[] = $location->name; break; }
					}
					echo join( ' in ', $property_meta );
					?>
				</div>
				<?php } ?>
			</div>
		</div>
	</a>
</div>
<?php
endwhile;
?>
</div><!-- .owl-carousel -->
<?php
wp_reset_query();
endif;