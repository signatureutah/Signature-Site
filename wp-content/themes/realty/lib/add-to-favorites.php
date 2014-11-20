<?php
/* AJAX - Favorites
============================== */
function tt_ajax_add_remove_favorites() {
	$user_id = $_GET['user'];
	$property_id = $_GET['property'];
			
	// Get Favorites Meta Data		
	$get_user_meta_favorites = get_user_meta( $user_id, 'realty_user_favorites', false ); // false = array()

	// No User Meta Data Favorites Found -> Add Data
	if ( !$get_user_meta_favorites ) {
		$create_favorites = array($property_id);
		add_user_meta( $user_id, 'realty_user_favorites', $create_favorites );		
	}
	// Meta Data Found -> Update Data
	else {
		// Add New Favorite
		if ( !in_array( $property_id, $get_user_meta_favorites[0] ) ) {
			array_unshift( $get_user_meta_favorites[0], $property_id ); // Add To Beginning Of Favorites Array
			update_user_meta( $user_id, 'realty_user_favorites', $get_user_meta_favorites[0] );		
		}
		// Remove Favorite
		else {
			$removeFavoriteFromPosition = array_search( $property_id, $get_user_meta_favorites[0] );
			unset($get_user_meta_favorites[0][$removeFavoriteFromPosition]);
			update_user_meta( $user_id, 'realty_user_favorites', $get_user_meta_favorites[0] );		
		}
	}
}
add_action('wp_ajax_tt_ajax_add_remove_favorites', 'tt_ajax_add_remove_favorites');


/* Favorites - Click
============================== */
function tt_add_remove_favorites() {
	
	global $realty_theme_option;
	$add_favorites_temporary = $realty_theme_option['property-favorites-temporary'];

	$property_id = get_the_ID();
	
	// Logged-In User
	if ( is_user_logged_in() ) {		
		$user_id = get_current_user_id();		
		$get_user_meta_favorites = get_user_meta( $user_id, 'realty_user_favorites', false ); // false = array()					
		
		// Property Is Already In Favorites
		if ( !empty( $get_user_meta_favorites ) && in_array( $property_id, $get_user_meta_favorites[0] ) ) {
			$favicon = '<i class="add-to-favorites fa fa-heart" data-fav-id="' . $property_id . '" data-toggle="tooltip" title="' . __( 'Remove From Favorites', 'tt' ) . '"></i>';	
		}
		// Property Isn't In Favorites
		else {
			$favicon = '<i class="add-to-favorites fa fa-heart-o" data-fav-id="' . $property_id . '" data-toggle="tooltip" title="' . __( 'Add To Favorites', 'tt' ) . '"></i>';
		}	
	}
	// Not Logged-In Visitor
	else {
		$favicon = '<i class="add-to-favorites fa fa-heart-o" data-fav-id="' . $property_id . '" data-toggle="tooltip" title="' . __( 'Add To Favorites', 'tt' ) . '"></i>';
	}
	
	return $favicon;
	
}


/* Favorites - Footer Script
============================== */
function tt_favorites_script() {

	global $realty_theme_option;
	$add_favorites_temporary = $realty_theme_option['property-favorites-temporary'];
	
	// Logged-In User Or Temporary Favorites Enabled
	if ( is_user_logged_in() || $add_favorites_temporary ) { ?>
	<script>
	jQuery('.add-to-favorites').click(function() {
		
		// Toggle Favorites Tooltips
		if ( jQuery(this).hasClass('fa-heart') ) {
			jQuery(this).attr('data-original-title', '<?php _e( 'Remove From Favorites', 'tt' ); ?>');
		}
		
		jQuery(this).find('i').toggleClass('fa-heart fa-heart-o');
		jQuery(this).closest('i').toggleClass('fa-heart fa-heart-o');
		
		<?php 
		// Loggen-In User
		if ( is_user_logged_in() ) {
			$user_id = get_current_user_id();
			?>
			jQuery.ajax({			
			  type: 'GET',
			  url: ajaxURL,
			  data: {
			    'action'        :   'tt_ajax_add_remove_favorites', // WP Function
			    'user'					: 	<?php echo $user_id; ?>,
			    'property'			: 	jQuery(this).attr('data-fav-id')
			  },
			  success: function (response) { },
			  error: function () { }			  
			});
			<?php
		}
		
		// Not Logged-In Visitor
		else {
		
			// Login Not Required: Save Favorites Temporary
			if ( $add_favorites_temporary ) {
			?>

			// Check If Browser Supports LocalStorage			
			if (!store.enabled) {
		    alert('<?php echo __( 'Local storage is not supported by your browser. Please disable "Private Mode", or upgrade to a modern browser.', 'tt' ); ?>');
				return;
		  }
			// Check For Temporary Favorites (store.js plugin)
			if ( store.get('favorites') ) {
				
				// Check If item Already In Favorites Array
				function inArray(needle, haystack) {
			    var length = haystack.length;
			    for( var i = 0; i < length; i++ ) {
		        if(haystack[i] == needle) return true;
			    }
			    return false;
				}

				var getFavs = store.get('favorites');
				var newFav = jQuery(this).attr('data-fav-id');

				// Remove Old Favorite
				if ( inArray( newFav, getFavs ) ) {
					var index = getFavs.indexOf(newFav);
					getFavs.splice(index, 1);
				}
				// Add New Favorite
				else {
					getFavs.push( newFav );
				}
				store.set( 'favorites', getFavs );
				
			}
			else {				
				var arrayFav = [];
				arrayFav.push( jQuery(this).attr('data-fav-id') );				
				store.set( 'favorites', arrayFav );				
			}
			
			console.log( store.get('favorites') );
			
			//store.remove('favorites');
			//store.clear();
			
			<?php
			}
			// Login Required: Show Modal
			else {
			?>
			jQuery('a[href="#tab-login"]').tab('show');
			jQuery('#login-modal').modal();
			jQuery('#msg-login-to-add-favorites').removeClass('hide');
			jQuery('#msg-login-to-add-favorites').addClass('hide');
			<?php
			}
			
		}
		?>
		
	});
	</script>
	
	<?php 
	}
}
add_action( 'wp_footer', 'tt_favorites_script', 20 );