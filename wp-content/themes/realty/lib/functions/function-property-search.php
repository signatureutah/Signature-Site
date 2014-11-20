<?php
/* PROPERTY SEARCH QUERY ARGUMENTS
============================== */

function tt_property_search_args($search_results_args) {
	
	$search_results_args['post_type'] = 'property';
	$search_results_args['post_status'] = 'publish';
	$search_results_args['paged'] = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
	
	global $realty_theme_option;
	$search_results_per_page = $realty_theme_option['search-results-per-page'];
	
	// Search Results Per Page: Check for Theme Option
	if ( $search_results_per_page ) {
		$search_results_args['posts_per_page'] = $search_results_per_page;
	}
	else {
		$search_results_args['posts_per_page'] = 10;
	}
	
	// Search Results Order
	if( !empty( $_GET[ 'orderby' ] ) ) {
		
		$orderby = $_GET[ 'orderby' ];
		
		// By Date (Newest First)
		if ( $orderby == 'date-new' ) {
			$search_results_args['orderby'] = 'date';
			$search_results_args['order'] = 'DESC';
		}
		
		// By Date (Oldest First)
		if ( $orderby == 'date-old' ) {
			$search_results_args['orderby'] = 'date';
			$search_results_args['order'] = 'ASC';
		}
		
		// By Price (Highest First)
		if ( $orderby == 'price-high' ) {
			$search_results_args['meta_key'] = 'estate_property_price';
			$search_results_args['orderby'] = 'meta_value_num';
			$search_results_args['order'] = 'DESC';
		}
		
		// By Price (Lowest First)
		if ( $orderby == 'price-low' ) {
			$search_results_args['meta_key'] = 'estate_property_price';
			$search_results_args['orderby'] = 'meta_value_num';
			$search_results_args['order'] = 'ASC';
		}
		
		// Random
		if ( $orderby == 'random' ) {
			$search_results_args['orderby'] = 'rand';
		}
		
	}
	else {
		$orderby = '';
	}
	
	/* META QUERIES: 
	============================== */
	
	$meta_query = array();
		
	foreach ( $_GET as $search_key => $search_value ) {
	
		// Check If Key Has A Value
		if ( !empty( $search_value ) ) {
		
			//$position = 0;
							
			//while ( $position < 8 ) :	
			
			//$position++;

			$pos = 0;
			while ( $pos < 8 ) :
				$pos++;
				if ( $search_key == $realty_theme_option['property-search-field-'.$pos] ) {
					$compare = $realty_theme_option['property-search-compare-'.$pos];
				}
			endwhile;
			
			switch ( $compare ) {
				
				case 'equal' : case '' 	: $compare = '='; break;
				case 'greather_than' 		: $compare = '<='; break;
				case 'less_than' 				: $compare = '>='; break;
				
			}
			
			// Default Fields
			switch ( $search_key ) {
			
				case 'estate_property_id' : 
				$meta_query[] = array(
					'key' 			=> 'estate_property_id',
					'value' 		=> $search_value
				);				
				break;

				case 'estate_property_price' :
				$search_value = number_format( $search_value, 0, '', '' );
				$meta_query[] = array(
					'key' 			=> 'estate_property_price',
					'value' 		=> $search_value,
					'type' 			=> 'NUMERIC',
			    'compare' 	=> $compare
				);				
				break;
				
				case 'price_range_min' :
				$search_value = number_format( $search_value, 0, '', '' );
				$meta_query[] = array(
					'key' 			=> 'estate_property_price',
					'value' 		=> array( $_GET['price_range_min'], $_GET['price_range_max'] ),
					'type' 			=> 'NUMERIC',
			    'compare' 	=> 'BETWEEN'
				);				
				break;
				
				case 'price_range_max' :
				$search_value = number_format( $search_value, 0, '', '' );
				$meta_query[] = array(
					'key' 			=> 'estate_property_price',
					'value' 		=> array( $_GET['price_range_min'], $_GET['price_range_max'] ),
					'type' 			=> 'NUMERIC',
			    'compare' 	=> 'BETWEEN'
				);				
				break;
				
				case 'estate_property_size' :
				$search_value = number_format( $search_value, 0, '', '' );
				$meta_query[] = array(
					'key' 			=> 'estate_property_size',
					'value' 		=> $search_value,
					'type' 			=> 'NUMERIC',
			    'compare' 	=> $compare
				);				
				break;
				
				case 'estate_property_rooms' : 
				$search_value = number_format( $search_value, 0, '', '' );
				$meta_query[] = array(
					'key' 			=> 'estate_property_rooms',
					'value' 		=> $search_value,
					'type' 			=> 'NUMERIC',
			    'compare' 	=> $compare
				);				
				break;
				
				case 'estate_property_bedrooms' : 
				$search_value = number_format( $search_value, 0, '', '' );
				$meta_query[] = array(
					'key' 			=> 'estate_property_bedrooms',
					'value' 		=> $search_value,
					'type' 			=> 'NUMERIC',
			    'compare' 	=> $compare
				);				
				break;
				
				case 'estate_property_bathrooms' : 
				$search_value = number_format( $search_value, 0, '', '' );
				$meta_query[] = array(
					'key' 			=> 'estate_property_bathrooms',
					'value' 		=> $search_value,
					'type' 			=> 'NUMERIC',
			    'compare' 	=> $compare
				);				
				break;
				
				case 'estate_property_garages' : 
				$search_value = number_format( $search_value, 0, '', '' );
				$meta_query[] = array(
					'key' 			=> 'estate_property_garages',
					'value' 		=> $search_value,
					'type' 			=> 'NUMERIC',
			    'compare' 	=> $compare
				);				
				break;
				
				case 'estate_property_availability' : 
				$meta_query[] = array(
					'key' 			=> 'estate_property_available_from',
					'value' 		=> $search_value,
					'type' 			=> 'DATE',
			    'compare' 	=> $compare
				);				
				break;
			
			} // switch
		
			// Advanced Custom Fields (ACF plugin)
			if ( in_array( $search_key, tt_acf_fields_name( tt_acf_group_id_property() ) ) ) {
			
				// Get Field Type	
				$acf_field_position = array_search( $search_key, tt_acf_fields_name( tt_acf_group_id_property() ) );
				$acf_field_type = tt_acf_fields_type( tt_acf_group_id_property() )[$acf_field_position];
				
				$type = '';
				
				switch ( $acf_field_type ) {		
					case ( 'number' ) : $type = 'NUMERIC'; break;
					case ( 'date_picker' ) : $type = 'DATE'; break;		
				}
			
				if ( $type != '' ) {
					
					$meta_query[] = array(
						'key' 			=> $search_key,
						'value' 		=> $search_value,
						'type' 			=> $type,
			    	'compare' 	=> $compare
					);				
				}
		
				// No type, so no comparison needed
				else {
					$meta_query[] = array(
						'key' 			=> $search_key,
						'value' 		=> $search_value
					);
				}
			
			}
			
			//endwhile;
			
		}
		
	}
	
	// Count Meta Queries + set their relation for search query
	$meta_count = count( $meta_query );
	if ( $meta_count > 1 ) {
	  $meta_query['relation'] = 'AND';
	}
	
	if ( $meta_count > 0 ) {
		$search_results_args['meta_query'] = $meta_query;
	}
			
	/* TAX QUERIES: 
	============================== */
	
	$tax_query = array();
	
	// Property Location
	if( !empty( $_GET[ 'estate_property_location' ] ) ) {
		$search_location = $_GET['estate_property_location'];
		if ( $search_location != "all" ) {
			$tax_query[] = array(
				'taxonomy' 	=> 'property-location',
				'field' 		=> 'slug',
				'terms'			=> $search_location
			);
		}
	}
	
	// Property Type		
	if( !empty( $_GET[ 'estate_property_type' ] ) ) {
		$search_type = $_GET['estate_property_type'];
		if ( $search_type != "all" ) {
			$tax_query[] = array(
				'taxonomy' 	=> 'property-type',
				'field' 		=> 'slug',
				'terms'			=> $search_type
			);
		}				
	}
	
	// Property Status
	if( !empty( $_GET[ 'estate_property_status' ] ) ) {
		$search_status = $_GET['estate_property_status'];		
		if (  $search_status != "all" ) {
			$tax_query[] = array(
				'taxonomy' 	=> 'property-status',
				'field' 		=> 'slug',
				'terms'			=> $search_status
			);
		}
	}
	
	// Property Features - NEW v1.4.3
	if( !empty( $_GET[ 'feature' ] ) ) {
		$search_features = $_GET['feature'];		
		foreach( $search_features as $search_feature ) {
			if ( $search_feature != "all" ) {
				$tax_query[] = array(
					'taxonomy' 	=> 'property-features',
					'field' 		=> 'slug',
					'terms'			=> $search_feature
				);
			}
		}		
	}
	
	// Count Taxonomy Queries + set their relation for search query
	$tax_count = count( $tax_query );
	if ( $tax_count > 1 ) {
		$tax_query['relation'] = 'AND';
	}
	
	if ( $tax_count > 0 ) {
		$search_results_args['tax_query'] = $tax_query;
	}
	
	return $search_results_args;

}
add_filter( 'property_search_args', 'tt_property_search_args' );


/* AJAX - Search
============================== */
function tt_ajax_search() {
	
	// Build Property Search Query
	$search_results_args = array();
	$search_results_args = apply_filters( 'property_search_args', $search_results_args );
	$count_results = "0";
	
	$query_search_results = new WP_Query( $search_results_args );
	
	if ( !isset( $orderby ) || empty( $orderby ) ) {
		$orderby = "date-new";
	}
	
	if ( $query_search_results->have_posts() ) :
	
	$count_results = $query_search_results->found_posts;
	// template-property-search.php
	?>
	<ul class="row list-unstyled">	
		<?php
		while ( $query_search_results->have_posts() ) : $query_search_results->the_post();
		global $realty_theme_option;
		$columns = $realty_theme_option['property-listing-columns'];
		if ( empty($columns) ) {
			$columns = "col-md-6";
		}
		?>
		<li class="<?php echo $columns; ?>">
			<?php get_template_part( 'lib/inc/template/property', 'item' );	?>
		</li>
		<?php endwhile; ?>
		
	</ul>
	<?php wp_reset_query(); ?>

	<div id="pagination">
	<?php
	// Built Property Pagination
	$big = 999999999; // need an unlikely integer
	
	/* http://stackoverflow.com/questions/20150653/wordpress-pagination-not-working-with-ajax
	$original_request_uri = $_SERVER['REQUEST_URI'];
	$base = $_GET['base'];
	$_SERVER['REQUEST_URI'] = $base;
	*/
	
	echo paginate_links( array(
		'base' 				=> str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
		'format' 			=> '%#%',
		'total' 			=> $query_search_results->max_num_pages,
		'show_all'		=> true,
		'type'				=> 'list',
		'current'     => $search_results_args['paged'],
		'prev_text' 	=> __( '<i class="btn btn-default fa fa-angle-left"></i>', 'tt' ),
		'next_text' 	=> __( '<i class="btn btn-default fa fa-angle-right"></i>', 'tt' ),
	) );
	
	//$_SERVER['REQUEST_URI'] = $original_request_uri;
	?>
	</div>
	
	<?php
	else : ?>
	<p class="lead text-center text-muted"><?php _e( 'No Properties Match Your Search Criteria.', 'tt' ); ?></p>
	<?php
	endif;
	?>
	
	<script>
	jQuery('.search-results-header, #property-search-results').fadeOut(0);
	<?php 
	// No Results Found
	if ( $count_results == "0" ) { ?>
	jQuery('#map-overlay-no-results, #property-search-results').fadeIn();
	<?php }
	// Results Found
	else {
	// AJAX Refresh Property Map Markers
	$search_results_args['posts_per_page'] = -1;
	$query_search_results = new WP_Query( $search_results_args );
	
	if ( $query_search_results->have_posts() ) :
	
	$property_string = '';
	$i = 0;

	while ( $query_search_results->have_posts() ) : $query_search_results->the_post(); 
	global $post;
	$google_maps = get_post_meta( $post->ID, 'estate_property_location', true );
	
	// Check For Map Coordinates
	if ( $google_maps ) {	
		
		$coordinate = explode( ',', $google_maps );	
		$property_string .= '{ ';	
		$property_string .= 'permalink:"' . get_permalink() . '", ';
		$property_string .= 'title:"' . get_the_title() . '", ';
		$property_string .= 'price:"' . tt_property_price() . '", ';
		$property_string .= 'latLng: new google.maps.LatLng(' . $coordinate[0] . ', ' . $coordinate[1] . '), ';
		if ( has_post_thumbnail() ) { 
			$property_thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'medium' );
			$property_string .= 'thumbnail: "' . $property_thumbnail[0] . '"';
		}	
		else { 
			$property_string .= 'thumbnail: "//placehold.it/300x100/eee/ccc/&text=.."';
		}
		$property_string .= ' },' . "\n";
	}
	
	$i++;
	endwhile;
	wp_reset_query();
	endif;
	?>	
	bounds = new google.maps.LatLngBounds();
	
	initMarkers(map, [ <?php echo $property_string; ?> ]);
	markerCluster = new MarkerClusterer(map, newMarkers, markerClusterOptions);
	
	google.maps.event.addListenerOnce(map, 'bounds_changed', function(event) {
		map.fitBounds(bounds);
		if (this.getZoom() > 13) {
	    this.setZoom(13);
	  }
	});
	
	jQuery('#map-overlay-no-results').fadeOut();
	jQuery('.search-results-header, #property-search-results').fadeIn();
	jQuery('.page-title span').html(<?php echo $count_results; ?>);
	<?php } ?>
	</script>
	
	<?php
	
	die();
	
}
add_action('wp_ajax_tt_ajax_search', 'tt_ajax_search');
add_action('wp_ajax_nopriv_tt_ajax_search', 'tt_ajax_search');