<?php get_header();
/*
Template Name: Property Submit Listing
*/
?>

</div><!-- .container -->
<?php tt_page_banner();	?>
<div class="container">

<?php
if ( is_user_logged_in() ) {
	
	global $realty_theme_option;
  $columns_theme_option = $realty_theme_option['property-listing-columns'];
	$search_results_per_page = $realty_theme_option['search-results-per-page'];
	
	// Is admin? If so, show all properties
	if ( current_user_can( 'manage_options' ) ) {
	
		$property_args_admin = array(
			'post_type' 				=> 'property',
			'paged' 					=> get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1,
		);
		
		// Search Results Per Page: Check for Theme Option
		if ( $search_results_per_page ) {
			$property_args_admin['posts_per_page'] = $search_results_per_page;
		}
		else {
			$property_args_admin['posts_per_page'] = 10;
		}
		
		$query_combined_results = new WP_Query( $property_args_admin );
		
	}
	// Query for non-admins
	else {
		
		global $current_user;
		get_currentuserinfo();
	  
	  // Query: Has user any published properties?
		$property_args = array(
			'post_type' 				=> 'property',
			'posts_per_page' 		=> -1,
			'author' 						=> $current_user->ID,
	    'post_status'				=> array( 'publish', 'pending' )
		);
		
		// Query 2: Is agent assigned to any properties?
		$property_args_agent_assigned = array(
			'post_type' 				=> 'property',
			'posts_per_page' 		=> -1,
			'author__not_in'		=> $current_user->ID,
	    'post_status'				=> array( 'publish', 'pending' ),
			'meta_query' 				=> array(
				array(
					'key' 		=> 'estate_property_custom_agent',
					'value' 	=> $current_user->ID,
					'compare'	=> '='
				)
			)
		);
		
		// Create two queries
		$query_property = new WP_Query( $property_args );
		$query_property_assigned_agent = new WP_Query( $property_args_agent_assigned );
		$query_combined_results = new WP_Query();
		
		// Set posts and post_count
		$query_combined_results->posts = array_merge( $query_property->posts, $query_property_assigned_agent->posts );
		$query_combined_results->post_count = $query_property->post_count + $query_property_assigned_agent->post_count;
  
  }
	
  if ( $query_combined_results->have_posts() ) :
  
  echo '<div id="property-items"><ul class="row list-unstyled">';
  
  while ( $query_combined_results->have_posts() ) : $query_combined_results->the_post();
  
  	echo '<li class="' . $columns_theme_option . '">';
  	get_template_part( 'lib/inc/template/property', 'item' );
		echo '</li>';
		
  endwhile;
  
  echo '</ul></div>';
  
  wp_reset_query();
  
  if ( current_user_can( 'manage_options' ) ) :
  
	  echo '<div id="pagination">';
		// Built Property Pagination
		$big = 999999999; // need an unlikely integer
		
		echo paginate_links( array(
			'base' 				=> str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
			'format' 			=> '%#%',
			'total' 			=> $query_combined_results->max_num_pages,
			'show_all'		=> true,
			'type'				=> 'list',
			'current'     => $property_args_admin['paged'],
			'prev_text' 	=> __( '<i class="btn btn-default fa fa-angle-left"></i>', 'tt' ),
			'next_text' 	=> __( '<i class="btn btn-default fa fa-angle-right"></i>', 'tt' ),
		) );
		
		echo '</div>';

	endif;
	
  else:
  	echo __( 'You haven\'t submitted any properties yet.', 'tt' );
  endif;
	
}
else {
	echo '<p class="text-danger">' . __( 'You have to be logged-in to view your submitted properties.', 'tt' ) . '</p>';
}

get_footer(); ?>	