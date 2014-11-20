<?php get_header();
/*
Template Name: Property Submit
*/
        
// Check If User Wants To Edit Property
if ( isset( $_GET['edit'] ) && !empty( $_GET['edit'] ) ) {
	$update_property = true;
}
else {
	$update_property = false;
}

$submit_success = false;

if ( is_user_logged_in() ) {

	global $realty_theme_option, $current_user;
	get_currentuserinfo();
	
	$current_user_role = $current_user->roles[0];
	
	// Check User Role -> Allow Agents To Publish
	if ( $current_user_role == "agent" || current_user_can( 'manage_options' ) ) {
		$allow_to_publish = true;
		$submit_button_text = 'Publish Property';
		$submit_result_text = 'Property has been published.';
	}
	else {
		$submit_button_text = 'Submit Property';
		$allow_to_publish = false;
		$submit_result_text = 'Property has been submitted.';
	}

}

// Check If User If Logged-In & Form Submitted Nonce Is Valid
if ( is_user_logged_in() && isset( $_POST['submit'] ) ) {
	
	
	if ( wp_verify_nonce( $_POST['nonce_property_submit'], 'property_submit' ) ) {
	
	// Update Property
	if ( $update_property ) {
	  $property['ID'] = intval( $_POST['property_id'] );
	  $property['post_title'] = sanitize_text_field( $_POST['property-title'] );
	  $property['post_content'] = $_POST['property-description'];
	  $property_id = wp_update_post( $property );

		// Property Update Result Message
	  if ( $property_id > 0 ) {
	  	$submit_success = true;
	  	$submit_result = '<p class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' . __( 'Property update successful.', 'tt' ) . '</p>';
	  }
	  else {
		  $submit_success = false;
		  $submit_result = '<p class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' . __( 'Property update failed. Please try again.', 'tt' ) . '</p>';
	  }
		
  }
  
  // Add New Property In Database Table "wp_posts"
  else {

		// Check User Capabilities (Agents Can Publish)
		if ( $allow_to_publish ) {
			$property = array(
		  	'post_author' 		=> get_current_user_id(),
		  	'post_status' 		=> 'publish',
		  	'post_type' 			=> 'property',
		  	'post_title'			=> sanitize_text_field( $_POST['property-title'] ),
		  	'post_content' 		=> $_POST['property-description']
		  );
		}
		// Normal User Submits Pending Property
		else {
			$property = array(
		  	'post_author' 		=> get_current_user_id(),
		  	'post_status' 		=> 'pending',
		  	'post_type' 			=> 'property',
		  	'post_title'			=> sanitize_text_field( $_POST['property-title'] ),
		  	'post_content' 		=> $_POST['property-description']
		  );	
		}
	  
	  $property_id = wp_insert_post( $property );
	  
	  // New Property - Result Message
	  if( $property_id > 0 ) {
	  	$submit_success = true;
			$submit_result  = '<p class="alert alert-info alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
			$submit_result .= __( $submit_result_text, 'tt' );
			$submit_result .= '</p>';
		}
		else {
			$submit_success = false;
			$submit_result = '<p class="alert alert-info alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' . __( 'Property submit failed. Please try again.', 'tt' ) . '</p>';
		}
		
		// New Property - Notification Email
		$property_title = $_POST['property-title'];
		$property_edit_url = get_edit_post_link( $property_id, '' );
		
		if ( $realty_theme_option['property-submit-notification-email-recipient'] ) {
			$notification_recipient = $realty_theme_option['property-submit-notification-email-recipient'];
		}
		else {
			$notification_recipient = get_option( 'admin_email' );
		}
		
		$subject = __( 'New Property Submit', 'tt' ) . ' - '. $property_title;
	  $headers = "From: " . get_option( 'blogname' ) . "<$notification_recipient>";
	  $message  = __( 'A new property has been submitted.', 'tt' ) . "\r\n\n";
	  $message .= $property_title . " (" . $property_edit_url . ")\r\n\n";
	  if ( $_POST['message'] ) {
	  	$message .= __( 'Message:', 'tt' ) . "\r\n" . $_POST['message'];
	  }
	  	
		wp_mail( $notification_recipient, $subject, $message, $headers );
	  
  }
  
  // Add OR Update Post Meta Data After Submit
  if ( $submit_success ) {
	  
	  if ( isset( $_POST['feature'] ) && !empty( $_POST['feature'] ) ) {
    	$features = $_POST['feature'];
    }
    else {
	    $features = '';
    }
    wp_set_post_terms( $property_id, $features, 'property-features' );
    
    // Check If "OTHER" Location Is Selected
    $location = $_POST['property-location'];
    
	  if ( $location == "other" ) {
	    $location_other = $_POST['property_location_other'];			
			// Add New Taxonomy Term
			wp_insert_term( $location_other, 'property-location' );
			// Retrieve Term ID
			$new_location = get_term_by( 'name', $location_other, 'property-location' );
			$new_location_id = intval ( $new_location->term_id );
			// Assign New Term To Property
	    wp_set_post_terms( $property_id, $new_location_id, 'property-location' );
    }
    else {
	    wp_set_post_terms( $property_id, $location, 'property-location' );
    }
    
    // Check If "OTHER" Status Is Selected
	  $status = $_POST['property-status'];
	  
	  if ( $status == "other" ) {
	    $status_other = $_POST['property_status_other'];			
			// Add New Taxonomy Term
			wp_insert_term( $status_other, 'property-status' );
			// Retrieve Term ID
			$new_status = get_term_by( 'name', $status_other, 'property-status' );
			$new_status_id = intval ( $new_status->term_id );
			// Assign New Term To Property
	    wp_set_post_terms( $property_id, $new_status_id, 'property-status' );
    }
    else {
	    wp_set_post_terms( $property_id, $status, 'property-status' );
    }
    
    // Check If "OTHER" Type Is Selected
	  $type = $_POST['property-type'];
	  
	  if ( $type == "other" ) {
	    $type_other = $_POST['property_type_other'];			
			// Add New Taxonomy Term
			wp_insert_term( $type_other, 'property-type' );
			// Retrieve Term ID
			$new_type = get_term_by( 'name', $type_other, 'property-type' );
			$new_type_id = intval ( $new_type->term_id );
			// Assign New Term To Property
	    wp_set_post_terms( $property_id, $new_type_id, 'property-type' );
    }
    else {
	    wp_set_post_terms( $property_id, $type, 'property-type' );
    }
    
    $availability = $_POST['property-availability'];
    if ( isset( $availability ) && !empty( $availability ) ) {
			update_post_meta( $property_id, 'estate_property_available_from', $availability );
    }
    
    $price = $_POST['property-price'];
    if ( isset( $price ) && !empty( $price ) ) {
			update_post_meta( $property_id, 'estate_property_price', $price );
    }
    
    $price_text = $_POST['property-price-text'];
    if ( isset( $price_text ) && !empty( $price_text ) ) {
			update_post_meta( $property_id, 'estate_property_price_text', $price_text );
    }
    
    $size = $_POST['property-size'];
    if ( isset( $size ) && !empty( $size ) ) {
			update_post_meta( $property_id, 'estate_property_size', $size );
    }
    
    $size_unit = $_POST['property-size-unit'];
    if ( isset( $size_unit ) && !empty( $size_unit ) ) {
			update_post_meta( $property_id, 'estate_property_size_unit', $size_unit );
    }
    
    $rooms = $_POST['property-rooms'];
    if ( isset( $rooms ) && $rooms >= 0 ) {
			update_post_meta( $property_id, 'estate_property_rooms', $rooms );
    }
    
    $bedrooms = $_POST['property-bedrooms'];
    if ( isset( $bedrooms ) && $bedrooms >= 0 ) {
			update_post_meta( $property_id, 'estate_property_bedrooms', $bedrooms );
    }
    
    $bathrooms = $_POST['property-bathrooms'];
    if ( isset( $bathrooms ) && $bathrooms >= 0 ) {
			update_post_meta( $property_id, 'estate_property_bathrooms', $bathrooms );
    }
    
    $garages = $_POST['property-garages'];
    if ( isset( $garages ) && $garages >= 0 ) {
			update_post_meta( $property_id, 'estate_property_garages', $garages );
    }

    $address = $_POST['property-address'];
    if ( isset( $address ) && !empty( $address ) ) {
			update_post_meta( $property_id, 'estate_property_address', $address );
    }
    
    $coordinates = $_POST['property-coordinates'];
    if ( isset( $coordinates ) && !empty( $coordinates ) ) {
			update_post_meta( $property_id, 'estate_property_location', $coordinates );
    }
    
    $featured = $_POST['property-featured'];
    //if ( isset( $featured ) && $featured >= 0 ) {
			update_post_meta( $property_id, 'estate_property_featured', $featured );
    //}
    
    $contact_information = $_POST['contact_information'];
    if ( isset( $contact_information ) && !empty( $contact_information ) ) {
			update_post_meta( $property_id, 'estate_property_contact_information', $contact_information );
    }
    
    $assign_agent = $_POST['assign-agent'];
    if ( isset( $assign_agent ) ) {
			update_post_meta( $property_id, 'estate_property_custom_agent', $assign_agent );
    }
    
    // PROPERTY IMAGES
    // File Type Check
    $allowed_file_types = array( "image/gif", "image/jpeg", "image/jpg", "image/png" );
		$upload_errors = '';
					
    // Featured Image
    if ( !empty( $_FILES['property-featured-image']['name'] ) ) {

			// Min. dimension    
    	$featured_image_dimension = getimagesize( $_FILES['property-featured-image']['tmp_name'] );
			$featured_image_width = $featured_image_dimension[0];
			$featured_image_height = $featured_image_dimension[1];
			
			if ( $featured_image_width < 600 || $featured_image_height < 300 ) {
				$upload_errors .= '<p class="alert alert-danger">' . __( 'Featured image dimension of', 'tt' ) . ' ' . $featured_image_height . 'x' . $featured_image_width . ' ' . __( 'is too small', 'tt' ) . '. ' . __( 'Min. dimension is 600x300.', 'tt' ) . '</p>';
			}
			
    	if ( !in_array( $_FILES['property-featured-image']['type'], $allowed_file_types ) ) {
	    	$upload_errors .= '<p class="alert alert-danger">' . __( 'Invalid file type:', 'tt' ) . ' "' . $_FILES['property-featured-image']['type'] . '". ' . __( 'Supported file types: gif, jpg, jpeg, png.', 'tt' ) . '</p>';
    	}
    	
    	// Max. File Size 5 MB
			if ( $_FILES['property-featured-image']['size'] > 5000000 ) {
				$upload_errors .= '<p class="alert alert-danger">' . __( 'File is too large. Max. upload file size is 5 MB.', 'tt' ) . '</p>';
			}
			
			// No Erros -> Upload Image
			if ( empty( $upload_errors ) ) {
			
				// check to make sure its a successful upload
				if ( $_FILES['property-featured-image']['error'] !== UPLOAD_ERR_OK ) __return_false();
						  
				// These files need to be included as dependencies when on the front end.
				require_once( ABSPATH . 'wp-admin/includes/image.php' );
				require_once( ABSPATH . 'wp-admin/includes/file.php' );
				require_once( ABSPATH . 'wp-admin/includes/media.php' );
				
				$attachment_id = media_handle_upload( 'property-featured-image', $property_id );				
				
				if ( is_wp_error( $attachment_id ) ) {
					// There was an error uploading the image.
					echo '<p class="alert alert-danger">Upload failed. Please submit again.</p>';
				} else {
					update_post_meta( $property_id, '_thumbnail_id', $attachment_id ); // Set Image as Feaured
					// The image was uploaded successfully!
				}
			
			}
			
			// Upload Errors    
	    else {
				echo $upload_errors;
			}
    
    } // END If Featured Image
    
    // Gallery Images
    if ( !empty( $_FILES['property-featured-gallery'] ) ) {
			
			$gallery_files = $_FILES['property-featured-gallery'];
						
			foreach ( $gallery_files['name'] as $key => $value ) {
			
				if ( $gallery_files['name'][$key] ) {
					$file = array(
						'name'     => $gallery_files['name'][$key],
						'type'     => $gallery_files['type'][$key],
						'tmp_name' => $gallery_files['tmp_name'][$key],
						'error'    => $gallery_files['error'][$key],
						'size'     => $gallery_files['size'][$key]
					);
					
					if ( !in_array( $file['type'], $allowed_file_types ) ) {
			    	$upload_errors .= '<p class="alert alert-danger">' . __( 'Invalid file type:', 'tt' ) . ' "' . $file['type'] . '". ' . __( 'Supported file types: gif, jpg, jpeg, png.', 'tt' ) . '</p>';
		    	}
		    	
		    	// Max. File Size 5 MB
					if ( $file['size'] > 5000000 ) {
						$upload_errors .= '<p class="alert alert-danger">File is too large. Max. upload file size is 5 MB.</p>';
					}
					
					// No Erros -> Upload Image
					if ( empty( $upload_errors ) ) {
		 
						$_FILES = array( "property-featured-gallery" => $file );
						
						foreach ( $_FILES as $file => $array ) {
							// check to make sure its a successful upload
						  if ( $_FILES[$file]['error'] !== UPLOAD_ERR_OK ) __return_false();
						 
						  require_once(ABSPATH . "wp-admin" . '/includes/image.php');
						  require_once(ABSPATH . "wp-admin" . '/includes/file.php');
						  require_once(ABSPATH . "wp-admin" . '/includes/media.php');
						 
						  $attach_id = media_handle_upload( $file, $property_id );
						  add_post_meta( $property_id, 'estate_property_images', $attach_id );
						}
					
					}
					
					// Upload Errors    
			    else {
						echo $upload_errors;
					}
					
				}
				
			}
    
    }  // END If Gallery Images
      
  } // END If Submitted
  
  }

}
?>

</div><!-- .container -->
<?php tt_page_banner();	?>
<div class="container">
	
<div id="main-content" class="content-box">

	<?php	
	// Check If User Is Logged-In
	if ( is_user_logged_in() ) {
	
		// Theme Option: Is Property Submit For Subscribers Disabled?
		if ( $current_user_role != "subscriber" || !$realty_theme_option['property-submit-disabled-for-subscriber'] ) {
		
			// Submit Result Message, If No Errors
			if ( $submit_success && empty( $upload_errors ) ) { 
				echo $submit_result; 
			}
			
			$is_assigned_agent = false;
			
			if ( $update_property ) {
				$property_id = intval( $_GET['edit'] );
				// Check if user has beeen seleted as "Assigned Agent" in Property Settings
				$assigned_agent = get_post_meta( $property_id, 'estate_property_custom_agent', true );
				if ( get_current_user_id() == $assigned_agent ) {
					$is_assigned_agent = true;
				}
			}
			else {
				$property_id = 0;
			}
			$property = get_post( $property_id );
			
			// 1. Check If We Are Updating ( And If So, Check If Property Belongs To Logged-In User OR Assigned Agent ) | 2. Are We Adding A New Property? | 3. Admin Role?
			if ( ( $update_property && ( get_current_user_id() == $property->post_author ) || $is_assigned_agent ) || !$update_property || current_user_can( 'manage_options' ) ) {
			?>
			
			<?php if ( $current_user_role == "subscriber" && get_post_status( $property_id ) != 'publish' || !$property_id ) { ?>
			<p class="alert alert-info alert-dismissable property-payment-note">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				<?php 
				echo __( 'Publishing fee', 'tt' ) . ': ' . $realty_theme_option['paypal-currency-code'] . ' ' . $realty_theme_option['paypal-amount'];
				if ( doubleval($realty_theme_option['paypal-featured-amount']) > 0 ) {
					echo ' | ' . __( '"Featured" upgrade', 'tt' ) . ': ' . $realty_theme_option['paypal-currency-code'] . ' ' . $realty_theme_option['paypal-featured-amount'];
				}
				?>
			</p>
			<p class="alert alert-info alert-dismissable property-payment-note-2">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				<?php
				if ( $realty_theme_option['paypal-auto-publish'] ) {
					_e( 'Property will be published automatically after payment completion.', 'tt' );
				}
				else {
					_e( 'Property will be published manually after payment completion.', 'tt' );
				}
				?>
			</p>
			<?php } ?>
		
			<form id="property-submit" enctype="multipart/form-data" method="post">
			
				<?php if ( $update_property ) { ?>
				<div class="meta-data">
					<?php
					$property_data = get_post( $property_id, 'ARRAY_A' );
					echo __( 'Last edited:', 'tt' ) . ' ' . substr( $property_data['post_modified'], 0, 10 );
					if ( $property_data['post_status'] == "publish" ) {
					echo ' &middot; ' .__( 'Published:', 'tt' ) . ' ' . substr( $property_data['post_date'], 0, 10 );
					}
					?>
				</div>
				<?php } ?>
			
				<div class="row">
					
					<div class="col-sm-6">
					
						<div class="form-group">
							<label for="property-title"><?php _e( 'Title', 'tt' ); ?></label>
							<input type="text" name="property-title" id="property-title" class="form-control required" value="<?php echo $property->post_title; ?>" title="<?php _e( 'Please enter a property title.', 'tt' ); ?>" tabindex="1"  />
						</div>
						
						<div class="form-group">
							<label for="property-description"><?php _e( 'Description', 'tt' ); ?></label>
							<textarea name="property-description" id="property-description" class="form-control required" title="<?php _e( 'Please enter a property description.', 'tt' ); ?>" rows="8" tabindex="2"><?php echo $property->post_content; ?></textarea>
						</div>
		
						<div class="row">
						
							<div class="col-sm-6">
							
								<div class="form-group">
									<label for="property-location"><?php _e( 'Location', 'tt' ); ?></label>
									<select name="property-location" id="property-location" class="form-control required" title="<?php _e( 'Please select the property location.', 'tt' ); ?>" data-placeholder="<?php _e( 'Choose a location', 'tt' ); ?>" tabindex="3">
										<option value=""></option>
										<?php
										// Current Propertys' Location
										$property_locations = get_the_terms( $property_id , 'property-location' );
										
										if ( !empty( $property_locations ) ) {
											foreach ( $property_locations as $property_location ) {
												$location_id = $property_location->term_id;
												break;
											}
										}
										
										// Get All Locations
										$property_all_locations = get_terms( 'property-location', array( 'hide_empty' => false ) );
										$all_locations_id = array();
										
										foreach ( $property_all_locations as $property_all_location ) {
											$all_locations_id[] = $property_all_location->term_id; // Collect For Other Location Check
											$property_location_parent = $property_location->parent;
											if ( $property_location_parent ) {
												$property_location_parent_term = get_term_by( 'id', $property_location_parent, 'property-location' );
												$property_location_parent_text = $property_location_parent_term->name . ' - ';
											}
											else {
												$property_location_parent_text = false;
											}
											echo '<option value="' . $property_all_location->term_id . '" ' . selected( $property_all_location->term_id, $location_id ) . '>' . $property_location_parent_text . $property_all_location->name . '</option>';
										}
										// Check For Other Location
										$property_new_location = wp_get_post_terms( $property_id, 'property-location' );
										if ( !in_array( $property_new_location[0]->term_id, $all_locations_id ) ) {
											echo '<option value="' . $property_new_location[0]->term_id . '" ' . selected( $property_new_location[0]->term_id, $location_id ) . '>' . $property_new_location[0]->name . '</option>';
										}
										?>
										<option value="other"><?php _e( 'Other', 'tt' ); ?></option>
									</select>
									<input type="text" name="property_location_other" id="property-location-other" class="form-control other" placeholder="<?php _e( 'Enter New Property Location', 'tt' ); ?>" tabindex="3" />
								</div>
							
								<div class="form-group">
									<label for="property-status"><?php _e( 'Status', 'tt' ); ?></label>
									<select name="property-status" id="property-status" class="form-control required" title="<?php _e( 'Please select the property status.', 'tt' ); ?>" data-placeholder="<?php _e( 'Choose a status', 'tt' ); ?>" tabindex="5">
										<option value=""></option>
										<?php
										// Current Propertys' Status
										$property_statuss = get_the_terms( $property_id , 'property-status' );
										
										if ( !empty( $property_statuss ) ) {
											foreach ( $property_statuss as $property_status ) {
												$status_id = $property_status->term_id;
												break;
											}
										}
										
										// Get All Status'
										$property_all_statuss = get_terms( 'property-status', array( 'hide_empty' => false ) );
										$all_status_id = array();
										
										foreach ( $property_all_statuss as $property_all_status ) {
											$all_status_id[] = $property_all_status->term_id; // Collect For Other Location Check
											$property_status_parent = $property_status->parent;
											if ( $property_status_parent ) {
												$property_status_parent_term = get_term_by( 'id', $property_status_parent, 'property-status' );
												$property_status_parent_text = $property_status_parent_term->name . ' - ';
											}
											else {
												$property_status_parent_text = false;
											}
											echo '<option value="' . $property_all_status->term_id . '" ' . selected( $property_all_status->term_id, $status_id ) . '>' . $property_status_parent_text . $property_all_status->name . '</option>';
										}
										// Check For Other Status
										$property_new_status = wp_get_post_terms( $property_id, 'property-status' );
										if ( !in_array( $property_new_status[0]->term_id, $all_statuss_id ) ) {
											echo '<option value="' . $property_new_status[0]->term_id . '" ' . selected( $property_new_status[0]->term_id, $status_id ) . '>' . $property_new_status[0]->name . '</option>';
										}
										?>
										<option value="other"><?php _e( 'Other', 'tt' ); ?></option>
									</select>
									<input type="text" name="property_status_other" id="property-status-other" class="form-control other" placeholder="<?php _e( 'Enter New Property Status', 'tt' ); ?>" tabindex="5" />
								</div>
								
								<div class="form-group">
									<?php
									$currency = $realty_theme_option['currency-sign'];
									?>
									<label for="property-price"><?php echo __( 'Price', 'tt' ) . ' (' . $currency . ')'; ?></label>
									<input type="number" name="property-price" id="property-price" class="form-control required" value="<?php echo get_post_meta( $property_id, 'estate_property_price', true ); ?>" title="<?php _e( 'Please enter a property price.', 'tt' ); ?>" tabindex="7" />
								</div>
								
								<div class="form-group">
									<label for="property-size"><?php _e( 'Size', 'tt' ); ?></label>
									<input type="number" name="property-size" id="property-size" class="form-control required" value="<?php echo get_post_meta( $property_id, 'estate_property_size', true ); ?>" title="<?php _e( 'Please enter a property size.', 'tt' ); ?>" tabindex="9" />
								</div>
								
								<div class="form-group">
									<label for="property-rooms"><?php _e( 'Rooms', 'tt' ); ?></label>
									<input type="number" name="property-rooms" id="property-rooms" class="form-control required" value="<?php echo get_post_meta( $property_id, 'estate_property_rooms', true ); ?>" title="<?php _e( 'Please enter a number of rooms.', 'tt' ); ?>" tabindex="11" min="0" />
								</div>
								
								<div class="form-group">
									<label for="property-bathrooms"><?php _e( 'Bathrooms', 'tt' ); ?></label>
									<input type="number" name="property-bathrooms" id="property-bathrooms" class="form-control required" value="<?php echo get_post_meta( $property_id, 'estate_property_bathrooms', true ); ?>" title="<?php _e( 'Please enter a number of bathrooms.', 'tt' ); ?>" tabindex="13" min="0" />
								</div>
														
							</div>
							
							<div class="col-sm-6">
							
								<div class="form-group">
									<label for="property-type"><?php _e( 'Type', 'tt' ); ?></label>
									<select name="property-type" id="property-type" class="form-control required" title="<?php _e( 'Please select the property type.', 'tt' ); ?>" data-placeholder="<?php _e( 'Choose a type', 'tt' ); ?>" tabindex="4">
										<option value=""></option>
										<?php
										// Current Propertys' Type
										$property_types = get_the_terms( $property_id , 'property-type' );
										
										if ( !empty( $property_types ) ) {
											foreach ( $property_types as $property_type ) {
												$type_id = $property_type->term_id;
												break;
											}
										}
										
										// Get All Types
										$property_all_types = get_terms( 'property-type', array( 'hide_empty' => false ) );
										$all_types_id = array();
										
										foreach ( $property_all_types as $property_all_type ) {
											$all_types_id[] = $property_all_type->term_id; // Collect For Other Type Check
											$property_type_parent = $property_type->parent;
											if ( $property_type_parent ) {
												$property_type_parent_term = get_term_by( 'id', $property_type_parent, 'property-type' );
												$property_type_parent_text = $property_type_parent_term->name . ' - ';
											}
											else {
												$property_type_parent_text = false;
											}
											echo '<option value="' . $property_all_type->term_id . '" ' . selected( $property_all_type->term_id, $type_id ) . '>' . $property_type_parent_text . $property_all_type->name . '</option>';
										}
										// Check For Other Type
										$property_new_type = wp_get_post_terms( $property_id, 'property-type' );
										if ( !in_array( $property_new_type[0]->term_id, $all_types_id ) ) {
											echo '<option value="' . $property_new_type[0]->term_id . '" ' . selected( $property_new_type[0]->term_id, $type_id ) . '>' . $property_new_type[0]->name . '</option>';
										}
										?>
										<option value="other"><?php _e( 'Other', 'tt' ); ?></option>
									</select>
									<input type="text" name="property_type_other" id="property-type-other" class="form-control other" placeholder="<?php _e( 'Enter New Property Type', 'tt' ); ?>" tabindex="4" />
								</div>			
								
								<div class="form-group">
									<label for="property-availability"><?php _e( 'Available From', 'tt' ); ?></label>
									<div class="input-group">
										<input type="number" name="property-availability" id="property-availability" class="form-control datepicker" value="<?php echo get_post_meta( $property_id, 'estate_property_available_from', true ); ?>" placeholder="<?php _e( 'Available From:', 'tt' ); ?>" tabindex="6" /><span class="input-group-addon"><i class="fa fa-calendar-o"></i></span>
									</div>
								</div>
								
								<div class="form-group">
									<label for="property-price-text"><?php _e( 'Price Suffix', 'tt' ); ?></label>
									<select name="property-price-text" id="property-price-text" class="form-control" data-placeholder="<?php _e( 'Optional', 'tt' ); ?>" tabindex="8">
										<option value="">-</option>
										<?php
										$price_suffixes = $realty_theme_option['property-submit-price-suffix'];
										if ( !empty( $price_suffixes ) ) {
											foreach ( $price_suffixes as $price_suffix ) {
												echo '<option value="' . $price_suffix . '" ' . selected( get_post_meta( $property_id, 'estate_property_price_text', true ), $price_suffix ) . '>' . $price_suffix . '</option>';
											}
										}
										?>
									</select>
								</div>
								
								<div class="form-group">
									<label for="property-size-unit"><?php _e( 'Size Unit', 'tt' ); ?></label>
									<select name="property-size-unit" id="property-size-unit" class="form-control" tabindex="10">
										<?php
										$size_units = $realty_theme_option['property-submit-size-unit'];
										if ( !empty( $size_units ) ) {
											foreach ( $size_units as $size_unit ) {
												echo '<option value="' . $size_unit . '" ' . selected( get_post_meta( $property_id, 'estate_property_size_unit', true ), $size_unit ) . '>' . $size_unit . '</option>';
											}
										}
										?>
									</select>
								</div>
								
								<div class="form-group">
									<label for="property-bedrooms"><?php _e( 'Bedrooms', 'tt' ); ?></label>
									<input type="number" name="property-bedrooms" id="property-bedrooms" class="form-control required" value="<?php echo get_post_meta( $property_id, 'estate_property_bedrooms', true ); ?>" title="<?php _e( 'Please enter a number of bedrooms.', 'tt' ); ?>" tabindex="12" min="0" />
								</div>
								
								<div class="form-group">
									<label for="property-garages"><?php _e( 'Garages', 'tt' ); ?></label>
									<input type="number" name="property-garages" id="property-garages" class="form-control required" value="<?php echo get_post_meta( $property_id, 'estate_property_garages', true ); ?>" title="<?php _e( 'Please enter a number of garages.', 'tt' ); ?>" tabindex="14" min="0" />
								</div>
								
							</div>
						
						</div><!-- .row -->
						
						<div class="form-group">
							<label for="property-features"><?php _e( 'Features', 'tt' ); ?></label>
							<ul name="property-features" id="property-features" class="list-unstyled">
								<?php
								// Current Propertys' Features
								$property_features = get_the_terms( $property_id , 'property-features' );
								
								if ( !empty( $property_features ) ) {
									$feature_id = array();
									foreach ( $property_features as $property_feature ) {
										$feature_id[] = $property_feature->term_id;
									}
								}
								
								// Get All Features
								$property_all_features = get_terms( 'property-features', array( 'hide_empty' => false ) );
								
								if ( !empty( $property_all_features ) ) {							
									$property_feature_count = 1;
									
									// List All Property Features
									foreach ( $property_all_features as $property_all_feature ) {
										echo '<li>';
										if ( $update_property && in_array( $property_all_feature->term_id, $feature_id ) ) {
											echo '<input type="checkbox" name="feature[]" id="feature-' . $property_feature_count . '" value="' . $property_all_feature->term_id . '" checked />';
										}
										else {
											echo '<input type="checkbox" name="feature[]" id="feature-' . $property_feature_count . '" value="' . $property_all_feature->term_id . '" />';
										}
										echo '<label for="feature-' . $property_feature_count . '">' . $property_all_feature->name . '</label>';
										echo '</li>';
										$property_feature_count++;
									}
								}
								?>
							</ul>
						</div>
						
					</div>
					
					<div class="col-sm-6">
					
					<div class="form-group">
						<label for="property-address"><?php _e( 'Address', 'tt' ); ?></label>
						<input type="text" name="property-address" id="property-address" class="form-control required" value="<?php echo get_post_meta( $property_id, 'estate_property_address', true ); ?>" title="<?php _e( 'Please enter a property address.', 'tt' ); ?>" tabindex="15" />
					</div>
					
					<div class="form-group">
						<label for="property-coordinates"><?php _e( 'Once Address Entered Above, Drag & Drop Map Marker To Exact Location.', 'tt' ); ?></label>
						<input type="hidden" name="property-coordinates" id="property-coordinates" value="" />
					<?php
					//if ( $address || $google_maps ) {
						get_template_part( 'lib/inc/template/google-map-single-property' ); 
					//}
					?>
					</div>
					
					<?php if ( $current_user_role != "subscriber" || ( $current_user_role == "subscriber" && doubleval($realty_theme_option['paypal-featured-amount']) > 0 ) ) { ?>
					<div class="form-group">
						<label for="property-featured">
						<?php 
							echo __( 'Set property "Featured"', 'tt' );
							if ( $current_user_role == "subscriber" && get_post_status( $property_id ) != 'publish' || !$property_id  ) {
								echo ' ' . __( 'for an additional', 'tt' ) . ' ' . $realty_theme_option['paypal-currency-code'] . ' ' . $realty_theme_option['paypal-featured-amount'];
							}
							?>
						</label><br />
						<input type="checkbox" name="property-featured" id="property-featured" value="1" <?php checked( get_post_meta( $property_id, 'estate_property_featured', true ) ); ?> />
					</div>
					<?php } ?>
					
					<div class="form-group primary-tooltips">
						<label for="property-featured-image"><?php _e( 'Featured Image', 'tt' ); ?> <i class="fa fa-info-circle" data-toggle="tooltip" title="Min. dimension 600x300px. Max. file size 5MB."></i></label>
						<div class="clearfix"></div>
						<div id="preview-featured-image">
						<?php
						if( has_post_thumbnail( $property_id ) ) {
						 echo get_the_post_thumbnail( $property_id, 'medium' );
						}
						else {
							echo '<img src ="//placehold.it/250x150/eee/ccc/&text=' . __( 'No Image Selected', 'tt' ) . '" />';
						}
						?>
						</div>
						<input type="file" name="property-featured-image" id="property-featured-image" class="form-control" value="<?php _e( 'Select Files..', 'tt' ); ?>" title="<?php _e( 'Please select a featured image.', 'tt' ); ?>" tabindex="16" />
					</div>
					
					<div class="form-group">
						<label for="property-featured-gallery"><?php _e( 'Gallery Images', 'tt' ); ?></label>
						<div class="clearfix"></div>
						<?php
						$property_gallery_images = get_post_meta( $property_id, 'estate_property_images', false );
						if ( $property_gallery_images ) {
							echo '<ul class="gallery-images">';
							foreach ( $property_gallery_images as $gallery_image ) {
								echo '<li>';
								echo wp_get_attachment_image( $gallery_image, array( 120, 120 ), false, array( 'class' => 'gallery-image' ) );
								echo '<i class="fa fa-close delete-uploaded-image" data-property-id="' . $property_id . '" data-image-id="' . $gallery_image . '"></i>';
								echo '</li>';
							}
							echo '</ul>';
						}
						?>
						<p><small><?php _e( 'Hold down "Ctrl" or "Cmd" to select multiple images.', 'tt' ); ?></small></p>
						<input type="file" name="property-featured-gallery[]" id="property-featured-gallery" class="form-control" value="<?php _e( 'Select Files..', 'tt' ); ?>" multiple tabindex="17" />
					</div>
					
					<div id="contact-information" class="form-group">
						<label for="contact_information"><?php _e( 'Contact Information', 'tt' ); ?></label><br />
						<?php if ( $update_property ) { ?>
						<input type="radio" name="contact_information" id="contact-information-all" value="all" <?php checked( 'all', get_post_meta( $property_id, 'estate_property_contact_information', true ) ); ?> />
						<?php } else { ?>
						<input type="radio" name="contact_information" id="contact-information-all" value="all" checked />
						<?php } ?>
						<label for="contact-information-all" class="default-label"><?php _e( 'Show Profile Information & Contact Form.', 'tt' ); ?></label><br />
						<input type="radio" name="contact_information" id="contact-information-form" value="form" <?php checked( 'form', get_post_meta( $property_id, 'estate_property_contact_information', true ) ); ?> />
						<label for="contact-information-form" class="default-label"><?php _e( 'Show Contact Form Only.', 'tt' ); ?></label><br />
						<input type="radio" name="contact_information" id="contact-information-none" value="none" <?php checked( 'none', get_post_meta( $property_id, 'estate_property_contact_information', true ) ); ?> />
						<label for="contact-information-none" class="default-label"><?php _e( 'None.', 'tt' ); ?></label>
					</div>
					
					<?php if ( current_user_can( 'manage_options' ) ) { ?>
					<div class="form-group">
						<label for="assign-agent"><?php _e( 'Assign Property To Agent', 'tt' ); ?></label>
						<select name="assign-agent" id="assign-agent">
							<option value="">-</option>
							<?php
							$agents = array( '' => __( 'None', 'tt' ) );
							// Get all users with role "agent"
							$all_agents = get_users( array( 'role' => 'agent', 'fields' => 'ID' ) );
							foreach( $all_agents as $agent ) { 
								echo '<option value="' . $agent . '"' . selected( get_post_meta( $property_id, 'estate_property_custom_agent', true ), $agent ) . '>' . get_user_meta( $agent, 'first_name', true ) . ' ' . get_user_meta( $agent, 'last_name', true ) . '</option>';
							}
							?>
						</select>
					</div>
					<?php } ?>
					
					<?php if ( !$update_property && $current_user_role == "subscriber" ) { ?>
					<div class="form-group">
						<label for="message"><?php _e( 'Message To Reviewer', 'tt' ); ?></label>
						<textarea name="message" id="message" class="form-control" rows="5"></textarea>
					</div>
					<?php } ?>
					
					<?php wp_nonce_field( 'property_submit', 'nonce_property_submit' ); ?>
					<input type="hidden" name="property_id" value="<?php echo $_GET['edit']; ?>" />
					<?php if ( $update_property ) { ?>
					<input type="submit" name="submit" id="submit" value="<?php _e( 'Update Property', 'tt' ); ?>" tabindex="18" />
					<?php } else { ?>
					<input type="submit" name="submit" id="submit" value="<?php _e( $submit_button_text, 'tt' );; ?>" tabindex="18" />
					<?php } ?>	
					</div>
					
				</div>
			</form>
			
			<?php 
			}
			else {
				echo '<p class="alert alert-danger">' . __( 'This property doesn\'t belong to you.', 'tt' ) . '</p>';
			}
	
		} // END If Property Submit For Subscribers Disabled	
		else {
			_e( 'Property submit is not allowed.', 'tt' );
		}
	
	} // END If Logged-In
	
	else {
		echo '<p class="alert alert-danger">' . __( 'You have to be logged-in to submit properties.', 'tt' ) . '</p>';
	}
	?>

</div>

<?php get_footer(); ?>	