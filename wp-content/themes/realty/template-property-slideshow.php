<?php get_header();
/*
Template Name: Property Slideshow
*/
global $post;
$mini_search = get_post_meta( $post->ID, 'estate_property_slideshow_mini_search', true ); 
$slideshow_fullscreeen = get_post_meta( $post->ID, 'estate_property_slideshow_fullscreen', true ); 
?>

<div id="main-slideshow" class="flexslider<?php if ( $slideshow_fullscreeen ) { echo ' fullscreen'; } ?>">
	
	<div class="spinner">
	  <div class="bounce1"></div>
	  <div class="bounce2"></div>
	  <div class="bounce3"></div>
	</div>
	
	<?php if ( $mini_search ) { ?>
	<div class="property-mini-search">
		<div class="container">
			<form class="mini-search-form" action="<?php if ( tt_page_id_template_search() ) { echo get_permalink( tt_page_id_template_search() ); } ?>">
						
				<?php // http://wordpress.stackexchange.com/questions/14652/how-to-show-a-hierarchical-terms-list#answer-14658 ?>
				<select name="estate_property_location" id="mini-search-location">
					<option value="all"><?php _e( 'Any Location', 'tt' ); ?></option>
			    <?php 
			    $location = get_terms('property-location', array( 'orderby' => 'slug', 'parent' => 0 ) ); 
			    if ( isset( $_GET['estate_property_location'] ) ) {
						$get_location = $_GET['estate_property_location'];
					}
					else {
						$get_location = '';
					}
					?>
			    <?php foreach ( $location as $key => $location ) : ?>
	        <option value="<?php echo $location->slug; ?>" <?php selected( $location->slug, $get_location ); ?>>
            <?php 
            echo $location->name;
            $location2 = get_terms( 'property-location', array( 'orderby' => 'slug', 'parent' => $location->term_id ) );
            if( $location2 ) : 
            ?>
            <optgroup label="<?php if ( wp_is_mobile() ) { echo $location->name; } ?>">
              <?php foreach( $location2 as $key => $location2 ) : ?>
                  <option value="<?php echo $location2->slug; ?>" class="level2" <?php selected( $location2->slug, $get_location ); ?>>
                  	<?php 
                  	echo $location2->name;
                  	$location3 = get_terms( 'property-location', array( 'orderby' => 'slug', 'parent' => $location2->term_id ) );
                  	if( $location3 ) : ?>
                  	<optgroup label="<?php if ( wp_is_mobile() ) { echo $location2->name; } ?>">
                  		<?php foreach( $location3 as $key => $location3 ) : ?>
                    		<option value="<?php echo $location3->slug; ?>" class="level3" <?php selected( $location3->slug, $get_location ); ?>>
                    		<?php 
                    		echo $location3->name;
	                    	$location4 = get_terms( 'property-location', array( 'orderby' => 'slug', 'parent' => $location3->term_id ) );
	                    	if( $location4 ) :
                    		?>
                    		<optgroup label="<?php if ( wp_is_mobile() ) { echo $location3->name; } ?>">
                    			<?php foreach( $location4 as $key => $location4 ) : ?>
                    			<option value="<?php echo $location4->slug; ?>" class="level4" <?php selected( $location4->slug, $get_location ); ?>>
													<?php echo $location4->name; ?>
                    			</option>
                    			<?php endforeach; ?>
                    		</optgroup>
                    		<?php endif; ?>
                    		</option>
                  		<?php endforeach; ?>
                  	</optgroup>
                  	<?php endif; ?>
                  </option>
              <?php endforeach; ?>
            </optgroup>
            <?php endif; ?>
	        </option>
			    <?php endforeach; ?>
				</select>
				
				<select name="estate_property_status" id="mini-search-status" class="mini-search-status">
					<option value="all"><?php _e( 'Any Status', 'tt' ); ?></option>
					<?php					
					$property_all_status = get_terms( 'property-status', array( 'hide_empty' => false, 'parent' => 0 ) );
					
					foreach ( $property_all_status as $property_status ) {
						echo '<option value="' . $property_status->slug . '">' . $property_status->name . '</option>';
					}
					?>
				</select>
				
				<input type="submit" value="<?php _e( 'Search', 'tt' ); ?>" />
				
			</form>
		</div>
	</div>
	<?php } // END if Mini Search ?>
	
	<ul class="slides">
		<?php
		global $realty_theme_option;
		$slideshow_type = get_post_meta( $post->ID, 'estate_property_slideshow_type', true );
	
		/* PROPERTY SLIDESHOW
		============================== */

		// Shwo Featured Properties
		if ( $slideshow_type == "featured" ) {
					
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
		if ( $slideshow_type == "latest" ) {
		
				$home_properties_slides_args = array(
				'post_type' 			=> 'property',
				'posts_per_page' 	=> 3,
			);
			
		}
		
		// Show Selected Properties
		if ( $slideshow_type == "selected" ) {
	
			$home_properties_slides_id = get_post_meta( $post->ID, 'estate_property_slideshow_selected_properties', false );
			
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
					<div class="inner<?php if ( $mini_search ) { echo ' bottom'; } ?>">
						<div class="container">
							<a href="<?php the_permalink(); ?>" class="slideshow-content-link">
								<h3 class="title"><?php echo the_title(); ?></h3>
								<?php if ( !$mini_search ) { ?>
								<div class="clearfix"></div>
								<div class="description">
									<?php 
									the_excerpt();
									if ( get_the_title() ) { ?>
									<div class="arrow-right"></div>
									<?php }
									
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
								<?php } ?>
							</a>
						</div>
					</div>
				</div>
			</li>
		<?php
		endwhile;
		wp_reset_query();
		endif;
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