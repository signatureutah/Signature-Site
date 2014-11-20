<?php 
get_header(); 
global $wp_query;
?>

<div class="taxonomy-results">

	<div class="search-results-header clearfix">
	
		<h2 class="page-title">
			<?php 
			if ( is_tax( 'property-location' ) ) {
				echo __( 'Location:', 'tt' ) . ' ' . str_replace( '-', ' ', get_query_var( 'property-location' ) );
			}
			if ( is_tax( 'property-status' ) ) {
				echo __( 'Status:', 'tt' ) . ' ' . str_replace( '-', ' ', get_query_var( 'property-status' ) );
			}
			if ( is_tax( 'property-type' ) ) {
				echo __( 'Type:', 'tt' ) . ' ' . str_replace( '-', ' ', get_query_var( 'property-type' ) );
			}
			if ( is_tax( 'property-features' ) ) {
				echo __( 'Feature:', 'tt' ) . ' ' . str_replace( '-', ' ', get_query_var( 'property-features' ) );
			}
			echo ' (' . $wp_query->found_posts . ')';
			?>
		</h2>
		
		<div class="taxonomy-description">
			<?php echo term_description(); ?>
		</div>
	
		<div class="search-results-view">
			<i class="fa fa-th-large active" data-view="grid-view" data-toggle="tooltip" title="<?php _e( 'Grid View', 'tt' ); ?>"></i>
			<i class="fa fa-th-list" data-view="list-view" data-toggle="tooltip" title="<?php _e( 'List View', 'tt' ); ?>"></i>
		</div>
		
	</div>
	
	<?php if ( have_posts() ) : ?>
	<div id="property-search-results" data-view="grid-view">
		<div id="property-items">
		
			<ul class="row list-unstyled">
				<?php while ( have_posts() ) : the_post(); ?>	
				<li class="col-sm-6">
					<?php get_template_part( 'lib/inc/template/property', 'item' );	?>
				</li>
				<?php endwhile; ?>
			</ul>
			
			<div id="pagination">
			<?php
			// Built Property Pagination
			$big = 999999999; // need an unlikely integer
			$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
			
			echo paginate_links( array(
				'base' 				=> str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
				'format' 			=> '?page=%#%',
				'total' 			=> $wp_query->max_num_pages,
				'show_all'		=> true,
				'type'				=> 'list',
				'current'     => $paged,
				'prev_text' 	=> __( '<i class="btn btn-default fa fa-angle-left"></i>', 'tt' ),
				'next_text' 	=> __( '<i class="btn btn-default fa fa-angle-right"></i>', 'tt' ),
			) );
			?>
			</div>
			
			<?php
			else : ?>
			<p class="lead text-center text-muted"><?php _e( 'Nothing Matches Your Criteria.', 'tt' ); ?></p>
			<?php
			endif;
			?>
			
		</div>
	</div>
		
</div>

<?php get_footer(); ?>