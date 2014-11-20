<?php get_header();
/*
Template Name: Property Search
*/

// Search Results Map
get_template_part( 'lib/inc/template/google-map-multiple-properties' );
?>

<div class="container">

<?php
// Property Search Form
get_template_part( 'lib/inc/template/search-form' );

// Build Property Search Query
$search_results_args = array();
$search_results_args = apply_filters( 'property_search_args', $search_results_args );

$query_search_results = new WP_Query( $search_results_args );

if ( !isset( $orderby ) || empty( $orderby ) ) {
	$orderby = "date-new";
}

if ( $query_search_results->have_posts() ) :	
$count_results = $query_search_results->found_posts;
?>
<div class="search-results-header clearfix">
	
	<h2 class="page-title"><?php echo __( 'Search Results', 'tt' ) . ' (<span>' . $count_results . '</span>)'; ?></h2>
	
	<?php the_content(); ?>
	
	<div class="search-results-view">
		<i class="fa fa-repeat <?php if ( !$orderby || $orderby != 'random' ) { echo 'hide'; } ?>" data-toggle="tooltip" title="<?php _e( 'Reload', 'tt' ); ?>"></i>
		<i class="fa fa-th-large active" data-view="grid-view" data-toggle="tooltip" title="<?php _e( 'Grid View', 'tt' ); ?>"></i>
		<i class="fa fa-th-list" data-view="list-view" data-toggle="tooltip" title="<?php _e( 'List View', 'tt' ); ?>"></i>
	</div>
	
	<div class="search-results-order clearfix">
		<div class="form-group select">
			<select name="orderby" id="orderby" class="form-control">
				<option value="date-new" <?php selected( 'date-new', $orderby ); ?>><?php _e( 'Sort by Date (Newest First)', 'tt' ); ?></option>
				<option value="date-old" <?php selected( 'date-old', $orderby ); ?>><?php _e( 'Sort by Date (Oldest First)', 'tt' ); ?></option>
				<option value="price-high" <?php selected( 'price-high', $orderby ); ?>><?php _e( 'Sort by Price (Highest First)', 'tt' ); ?></option>
				<option value="price-low" <?php selected( 'price-low', $orderby ); ?>><?php _e( 'Sort by Price (Lowest First)', 'tt' ); ?></option>
				<option value="random" <?php selected( 'random', $orderby ); ?>><?php _e( 'Random', 'tt' ); ?></option>
			</select>
		</div>
	</div>
	
</div>

<div id="property-search-results" data-view="grid-view">
	<div id="property-items">
	
		<ul class="row list-unstyled">
			<?php while ( $query_search_results->have_posts() ) : $query_search_results->the_post(); ?>	
			<?php
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
	
		echo paginate_links( array(
			'base' 				=> str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
			'format' 			=> '?page=%#%',
			'total' 			=> $query_search_results->max_num_pages,
			'show_all'		=> true,
			'type'				=> 'list',
			'current'     => $search_results_args['paged'],
			'prev_text' 	=> __( '<i class="btn btn-default fa fa-angle-left"></i>', 'tt' ),
			'next_text' 	=> __( '<i class="btn btn-default fa fa-angle-right"></i>', 'tt' ),
		) );
		?>
		</div>
		
		<?php
		else : ?>
		<p class="lead text-center text-muted"><?php _e( 'No Properties Match Your Search Criteria.', 'tt' ); ?></p>
		<?php
		endif;
		?>

	</div>
</div>

</div><!-- .container -->

<?php get_footer(); ?>