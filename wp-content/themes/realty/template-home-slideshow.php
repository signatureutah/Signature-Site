<?php get_header();
/*
Template Name: Home - Slideshow
*/
?>

<div id="home-slideshow" class="flexslider">
	
	<div class="spinner">
	  <div class="bounce1"></div>
	  <div class="bounce2"></div>
	  <div class="bounce3"></div>
	</div>
	
	<ul class="slides">
		<?php
		global $realty_theme_option;		
		$home_layout = $realty_theme_option['home-slideshow-type'];
		$home_slideshow_properties_mode = $realty_theme_option['home-slideshow-properties-mode'];

		/* SLIDESHOW - CUSTOM
		============================== */

		if ( $home_layout == "slideshow-custom" ) {
		
			$home_slideshow = $realty_theme_option['home-slides'];
			
			foreach ($home_slideshow as $home_slide) { 

				$attachment_array = wp_get_attachment_image_src( $home_slide['attachment_id'], 'full' );

				$attachment_url_slide = $attachment_array[0];
					
				?>
				<li class="slide-<?php echo $home_slide['attachment_id'];  ?>" style="background-image: url(<?php echo $attachment_url_slide; ?>)">
					<div class="wrapper">
						<div class="inner">
							<div class="container">
								<a href="<?php echo $home_slide['url']; ?>" class="slideshow-content-link">
									<?php
									if ( $home_slide['title'] ) { ?>
									<h3 class="title"><?php echo do_shortcode($home_slide['title']); ?></h3>
									<?php 
									}
									if ( $home_slide['description'] ) { ?>
									<div class="clearfix"></div>
									<div class="description">
										<?php echo do_shortcode($home_slide['description']); ?>
										<?php if ( $home_slide['title'] ) { ?><div class="arrow-right"></div><?php } ?>
									</div>
									<?php } ?>
								</a>
							</div>
						</div>
					</div>
				</li>
			<?php	
			} 
		
		} // END Slideshow Custom
	
	
		/* SLIDESHOW - PROPERTIES
		============================== */
		
		if ( $home_layout == "slideshow-properties" ) {	
			
			// Shwo Featured Properties
			if ( $home_slideshow_properties_mode == "slideshow-properties-show-featured" ) {
						
				$home_properties_slides_args = array(
					'post_type' 			=> 'property',
					'posts_per_page'	=> -1,
					'meta_query' 			=> array(
													       array(
													           'key' 			=> 'estate_property_featured',
													           'value' 		=> 1,
													           'compare' 	=> '=',
													       )
				  )
				);
							
			}
			
			// Show Latest Three Properties
			if ( $home_slideshow_properties_mode == "slideshow-properties-show-latest" ) {
			
					$home_properties_slides_args = array(
					'post_type' 			=> 'property',
					'posts_per_page' 	=> 3,
				);
				
			}
			
			// Show Selected Properties
			if ( $home_slideshow_properties_mode == "slideshow-properties-show-selected" ) {
		
				$home_properties_slides_id = $realty_theme_option['home-property-slides'];
				
				$home_properties_slides_args = array(
					'post_type' 			=> 'property',
					'post__in' 				=> $home_properties_slides_id,
					'posts_per_page' 	=> count($home_properties_slides_id),
					'orderby' 				=> 'post__in',
				);
			
			}
						
			$home_properties_slides = new WP_Query( $home_properties_slides_args );
			
			if ( $home_properties_slides->have_posts() ) : while ( $home_properties_slides->have_posts() ) : $home_properties_slides->the_post(); 
			
				
				$home_property_slide_thumbnail_id = get_post_thumbnail_id();
				$home_property_slide_thumbnail = wp_get_attachment_image_src( $home_property_slide_thumbnail_id, 'full', true );
				?>
				<li class="slide-<?php echo $home_property_slide_thumbnail_id; ?>" style="background-image: url(<?php echo $home_property_slide_thumbnail[0]; ?>)">
					<div class="wrapper">
						<div class="inner">
							<div class="container">
								<a href="<?php the_permalink(); ?>" class="slideshow-content-link">
									<h3 class="title"><?php echo the_title(); ?></h3>
									<div class="clearfix"></div>
									<div class="description">
										<?php the_excerpt(); ?>
										<?php if ( get_the_title() ) { ?><div class="arrow-right"></div><?php } ?>
										
										<?php
										global $post;
										$size = get_post_meta( $post->ID, 'estate_property_size', true );
										$size_unit = get_post_meta( $post->ID, 'estate_property_size_unit', true );
										$bedrooms = get_post_meta( $post->ID, 'estate_property_bedrooms', true );
										$bathrooms = get_post_meta( $post->ID, 'estate_property_bathrooms', true );
										
										echo '<div class="property-data">';
										echo '<div class="property-price">' . tt_property_price() . '</div>';
										
										echo '<div class="property-details">';
										if ( $bedrooms ) { echo '<i class="fa fa-inbox"></i>' . $bedrooms . ' ' . _n( __( 'Bedroom', 'tt'), __( 'Bedrooms', 'tt'), $bedrooms, 'tt' ); }
										if ( $bathrooms ) { echo '<i class="fa fa-tint"></i>' . $bathrooms . ' ' . _n( __( 'Bathroom', 'tt'), __( 'Bathrooms', 'tt'), $bathrooms, 'tt' ); }
										if ( $size ) { echo '<i class="fa fa-expand"></i>' . $size . $size_unit; }
										echo '</div></div>';	
										?>
									</div>
								</a>
							</div>
						</div>
					</div>
				</li>
			<?php
			endwhile;
			wp_reset_query();
			endif;
			
		}
		?>
	</ul>
	
</div>

<div class="container">
	<?php 
	if ( have_posts() ) : while ( have_posts() ) : the_post();
		the_content(); 
	endwhile;
	endif;
	?>
</div><!-- .container -->

<?php get_footer(); ?>