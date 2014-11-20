<?php
$property_status_update = get_post_meta( $post->ID, 'estate_property_status_update', true );
$property_location = get_the_terms( $post->ID, 'property-location' );
$property_status = get_the_terms( $post->ID, 'property-status' );
$property_type = get_the_terms( $post->ID, 'property-type' );

$address = get_post_meta( $post->ID, 'estate_property_address', true );
$size = get_post_meta( $post->ID, 'estate_property_size', true );
$size_unit = get_post_meta( $post->ID, 'estate_property_size_unit', true );
$rooms = get_post_meta( $post->ID, 'estate_property_rooms', true );
$bedrooms = get_post_meta( $post->ID, 'estate_property_bedrooms', true );
$bathrooms = get_post_meta( $post->ID, 'estate_property_bathrooms', true );
?>
<div class="property-item primary-tooltips">

	<a href="<?php the_permalink(); ?>">
		<figure class="property-thumbnail">
			<?php 
			global $realty_theme_option;
			$columns = $realty_theme_option['property-listing-columns'];
			
			// Use A Different Thumbnail Dimension For 4 Column Grid
			if ( $columns == "col-lg-3 col-md-6" ) {
				if ( has_post_thumbnail() ) { 
					the_post_thumbnail( 'thumbnail-400-300' );
				}	
				else {
					echo '<img src ="//placehold.it/400x300/eee/ccc/&text=.." />';
				}
			}
			// Default Property Thumbnail Dimension
			else {
				if ( has_post_thumbnail() ) { 
					the_post_thumbnail( 'property-thumb' );
				}	
				else {
					echo '<img src ="//placehold.it/600x300/eee/ccc/&text=.." />';
				}
			}
			?>
			<figcaption>
				<div class="property-title">
					<h3 class="title"><?php the_title(); ?></h3>
					<?php 
					if ( $property_type || $property_status || $property_location ) { 
						$property_meta = array();
					?>
					<div class="subtitle">
						<?php 
						if ( $property_type ) { 
							foreach ( $property_type as $type ) { 
								$property_meta[] = $type->name; break; 
							} 
						}
						if ( $property_status ) { 
							foreach ( $property_status as $status ) { 
								$property_meta[] = $status->name; break; 
							} 
						}
						if ( $property_location ) {
							foreach ( $property_location as $location ) { 
								$property_meta[] = $location->name; break; 
							} 
						}
						echo join( ' <span>-</span> ', $property_meta );
						?>
					</div>
				</div>
				<?php } ?>
				<div class="property-excerpt">
					<h4 class="address"><?php echo $address; ?></h4>
					<?php the_excerpt(); ?>
				</div>
			</figcaption>
		</figure>
	</a>
	
	<div class="property-content">
		<?php if ( $size || $rooms || $bedrooms || $bathrooms ) { ?>
		<div class="property-meta clearfix">
			<?php
			if ( !empty( $size ) ) { ?>
				<div>
					<div class="meta-title"><i class="fa fa-expand"></i></div>
					<div class="meta-data" data-toggle="tooltip" title="<?php _e( 'Size', 'tt' ); ?>"><?php echo $size . ' ' . $size_unit; ?></div>
				</div>
			<?php }
			if ( !empty( $rooms ) ) { ?>
				<div>
					<div class="meta-title"><i class="fa fa-building-o"></i></div>
					<div class="meta-data" data-toggle="tooltip" title="<?php echo __( 'Rooms', 'tt' ); ?>"><?php echo $rooms . ' ' . _n( __( 'Room', 'tt' ), __( 'Rooms', 'tt' ), $rooms, 'tt' ); ?></div>
				</div>
			<?php }
			if ( !empty( $bedrooms ) ) { ?>
				<div>
					<div class="meta-title"><i class="fa fa-inbox"></i></div>
					<div class="meta-data" data-toggle="tooltip" title="<?php echo __( 'Bedrooms', 'tt' ); ?>"><?php echo $bedrooms . ' ' . _n( __( 'Bedroom', 'tt' ), __( 'Bedrooms', 'tt' ), $bedrooms, 'tt' ); ?></div>
				</div>
			<?php }
			if ( !empty( $bathrooms ) ) { ?>
				<div>
					<div class="meta-title"><i class="fa fa-tint"></i></div>
					<div class="meta-data" data-toggle="tooltip" title="<?php echo __( 'Bathrooms', 'tt' ); ?>"><?php echo $bathrooms . ' ' . _n( __( 'Bathroom', 'tt' ), __( 'Bathrooms', 'tt' ), $bathrooms, 'tt' ); ?></div>
				</div>
			<?php }
			?>
		</div>
		<?php } ?>
		<div class="property-price">
			<?php 
			if ( $property_status_update || $property_status ) {			
				if ( $property_status_update ) {
					echo '<span class="property-status" data-toggle="tooltip" title="' . __( 'Status', 'tt' ) . '">' . __( $property_status_update, 'tt' ) . '</span>';
				}
				else {
					if ( $property_status ) { 
						foreach ( $property_status as $status ) { 
							echo '<span class="property-status" data-toggle="tooltip" title="' . __( 'Status', 'tt' ) . '">' . $status->name . '</span>';
							break;
						} 
					}	
				}			
			}
			//echo tt_icon_property_address();
			echo tt_icon_property_featured();
			if ( get_post_status($post->ID) == "publish" ) {
				echo tt_icon_new_property();
				echo tt_add_remove_favorites();
			}
			// Property Submit Listing
			if ( is_page_template( 'template-property-submit-listing.php' ) ) { ?>
				<a href="<?php the_permalink(); ?>"><i class="fa fa-pencil" data-toggle="tooltip" title="<?php _e( 'Edit Property', 'tt' ); ?>"></i></a>
				<?php if ( get_post_status($post->ID) == "publish" ) { ?>
					<a href="<?php echo get_the_permalink(); ?>" target="_blank"><i class="fa fa-check" data-toggle="tooltip" title="<?php _e( 'Published', 'tt' ); ?>"></i></a>
					<?php 
					$paypal_payment_status = get_post_meta( $post->ID, 'property_payment_status', true );				
					if ( isset( $paypal_payment_status ) && $paypal_payment_status == "Completed" ) {
						echo '<i class="fa fa-usd" data-toggle="tooltip" title="' . __( 'Paid', 'tt' ) . '"></i>';
					}
				}
				else if ( get_post_status($post->ID) == "pending" ) { ?>
				<a href="<?php echo the_permalink(); ?>" target="_blank"><i class="fa fa-clock-o" data-toggle="tooltip" title="<?php _e( 'Pending', 'tt' ); ?>"></i></a>	
				<?php
				echo tt_paypal_payment_button();
				}
			}
			?>
			<div class="price-tag"><?php echo tt_property_price(); ?></div>
		</div>
	</div>
	
</div>