<form class="property-search-form" action="<?php if ( tt_page_id_template_search() ) { echo get_permalink( tt_page_id_template_search() ); } ?>">	
	<div class="row">
	
		<?php
		global $realty_theme_option;
		$property_search_features = $realty_theme_option['property-search-features'];

		$property_search_i = 0;
					
		while ( $property_search_i < 8 ) :
						
		$property_search_i++;
		
		$search_field = $realty_theme_option['property-search-field-'.$property_search_i];
		$search_label = $realty_theme_option['property-search-label-'.$property_search_i];
		$default_search_fields_array = array( 
			'estate_property_id', 
			'estate_property_location', 
			'estate_property_type', 
			'estate_property_status', 
			'estate_property_price', 
			'estate_property_pricerange',
			'estate_property_size',
			'estate_property_rooms',
			'estate_property_bedrooms',
			'estate_property_bathrooms',
			'estate_property_garages',
			'estate_property_availability'
		);
		
		// Check If Search Field Is Filled Out
		if ( !empty( $search_field ) ) {
		
			// Default Property Field
			if ( in_array( $search_field, $default_search_fields_array ) ) {
				
				switch ( $search_field ) {
					
					case 'estate_property_id' : ?>
					<div class="col-xs-6 col-sm-4 col-md-3 form-group">
						<input type="text" name="estate_property_id" id="property-search-id" value="<?php echo isset( $_GET[ 'id' ])?$_GET[ 'id' ]:''; ?>" placeholder="<?php echo $search_label; ?>" class="form-control" />
					</div>
					<?php
					break;
					
					case 'estate_property_location' : ?>
					<div class="col-xs-6 col-sm-4 col-md-3 form-group select">	
					<?php // http://wordpress.stackexchange.com/questions/14652/how-to-show-a-hierarchical-terms-list#answer-14658 ?>
					<select name="estate_property_location" id="property-search-location" class="form-control">
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
	            <optgroup>
	              <?php foreach( $location2 as $key => $location2 ) : ?>
	                  <option value="<?php echo $location2->slug; ?>" class="level2" <?php selected( $location2->slug, $get_location ); ?>>
	                  	<?php 
	                  	echo $location2->name;
	                  	$location3 = get_terms( 'property-location', array( 'orderby' => 'slug', 'parent' => $location2->term_id ) );
	                  	if( $location3 ) : ?>
	                  	<optgroup>
	                  		<?php foreach( $location3 as $key => $location3 ) : ?>
	                    		<option value="<?php echo $location3->slug; ?>" class="level3" <?php selected( $location3->slug, $get_location ); ?>>
	                    		<?php 
	                    		echo $location3->name;
		                    	$location4 = get_terms( 'property-location', array( 'orderby' => 'slug', 'parent' => $location3->term_id ) );
		                    	if( $location4 ) :
	                    		?>
	                    		<optgroup>
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
					</div>
					<?php
					break;
					
					case 'estate_property_type' : ?>
					<div class="col-xs-6 col-sm-4 col-md-3 form-group select">
					<select name="estate_property_type" id="property-search-type" class="form-control">
						<option value="all"><?php _e( 'Any Type', 'tt' ); ?></option>
						<?php
						$property_types = get_terms( 'property-type' );
						if ( isset( $_GET['estate_property_type'] ) ) {
							$get_type = $_GET['estate_property_type'];
						}
						else {
							$get_type = '';
						}
						foreach ( $property_types as $property_type ) {
							echo '<option value="' . $property_type->slug . '" ' . selected( $property_type->slug, $get_type ) . '>' . $property_type->name . '</option>';
						}
						?>
					</select>
				</div>
					<?php
					break;
					
					case 'estate_property_status' :?>
					<div class="col-xs-6 col-sm-4 col-md-3 form-group select">
					<select name="estate_property_status" id="property-search-status" class="form-control">
						<option value="all"><?php _e( 'Any Status', 'tt' ); ?></option>
						<?php
						$property_status_array = get_terms( 'property-status' );
						if ( isset( $_GET['estate_property_status'] ) ) {
							$get_status = $_GET['estate_property_status'];
						}
						else {
							$get_status = '';
						}
						foreach ( $property_status_array as $property_status ) {
							echo '<option value="' . $property_status->slug . '" ' . selected( $property_status->slug, $get_status ) . '>' . $property_status->name . '</option>';
						}
						?>
					</select>
				</div>
					<?php
					break;
					
					case 'estate_property_price' : ?>
					<div class="col-xs-6 col-sm-4 col-md-3 form-group">
						<input type="number" name="<?php echo $search_field; ?>" id="property-search-price" value="<?php echo isset( $_GET[$search_field])?$_GET[$search_field]:''; ?>" placeholder="<?php echo $search_label; ?>" min="0" class="form-control" />
					</div>
					<?php
					break;
					
					case 'estate_property_size' : ?>
					<div class="col-xs-6 col-sm-4 col-md-3 form-group">
						<input type="number" name="<?php echo $search_field; ?>" id="property-search-size" value="<?php echo isset( $_GET[$search_field])?$_GET[$search_field]:''; ?>" placeholder="<?php echo $search_label; ?>" min="0" class="form-control" />
					</div>
					<?php
					break;
					
					case 'estate_property_rooms' : ?>
					<div class="col-xs-6 col-sm-4 col-md-3 form-group">
						<input type="number" name="<?php echo $search_field; ?>" id="property-search-rooms" value="<?php echo isset( $_GET[$search_field])?$_GET[$search_field]:''; ?>" placeholder="<?php echo $search_label; ?>" min="0" class="form-control" />
					</div>
					<?php
					break;
					
					case 'estate_property_bedrooms' : ?>
					<div class="col-xs-6 col-sm-4 col-md-3 form-group">
						<input type="number" name="<?php echo $search_field; ?>" id="property-search-bedrooms" value="<?php echo isset( $_GET[$search_field])?$_GET[$search_field]:''; ?>" placeholder="<?php echo $search_label; ?>" min="0" class="form-control" />
					</div>
					<?php
					break;
					
					case 'estate_property_bathrooms' : ?>
					<div class="col-xs-6 col-sm-4 col-md-3 form-group">
						<input type="number" name="<?php echo $search_field; ?>" id="property-search-bathrooms" value="<?php echo isset( $_GET[$search_field])?$_GET[$search_field]:''; ?>" placeholder="<?php echo $search_label; ?>" min="0" class="form-control" />
					</div>
					<?php
					break;
					
					case 'estate_property_garages' : ?>
					<div class="col-xs-6 col-sm-4 col-md-3 form-group">
						<input type="number" name="<?php echo $search_field; ?>" id="property-search-garages" value="<?php echo isset( $_GET[$search_field])?$_GET[$search_field]:''; ?>" placeholder="<?php echo $search_label; ?>" min="0" class="form-control" />
					</div>
					<?php
					break;
					
					case 'estate_property_availability' : ?>
					<div class="col-xs-6 col-sm-4 col-md-3 form-group">
						<div class="input-group">
							<input type="number" name="<?php echo $search_field; ?>" id="property-search-availability" class="form-control datepicker" value="<?php echo isset( $_GET[$search_field])?$_GET[$search_field]:''; ?>" placeholder="<?php echo $search_label; ?>" /><span class="input-group-addon"><i class="fa fa-calendar-o"></i></span>
						</div>
					</div>
					<?php 
					break;
					
					case 'estate_property_pricerange' : 
					$pricerange_min = $realty_theme_option['property-search-price-range-min'];
					$pricerange_max = $realty_theme_option['property-search-price-range-max'];
					?>
					<div class="col-xs-6 col-sm-4 col-md-3 form-group prince-range">
						<input type="number" name="price_range_min" id="property-search-price-range-min" value="<?php if ( $_GET['price_range_min'] ) { echo $_GET['price_range_min']; } else { echo $pricerange_min; } ?>" class="hide" />
						<input type="number" name="price_range_max" id="property-search-price-range-max" value="<?php if ( $_GET['price_range_max'] ) { echo $_GET['price_range_max']; } else { echo $pricerange_max; } ?>" class="hide" />
						<label><?php echo $search_label; ?> <span id="price-range-min"></span> <?php _e( 'to', 'tt' ); ?> <span id="price-range-max"></span></label>
						<div id="price-range"></div>
					</div>
					<?php
					break;
					
				}
				
			}
			
			// ACF: Custom Property Field
			else if ( tt_acf_active() ) {
			
				// Get ACF Field Type
				$acf_field_position = array_search( $search_field, tt_acf_fields_name( tt_acf_group_id_property() ) );
				$acf_field_type = tt_acf_fields_type( tt_acf_group_id_property() )[$acf_field_position];
				
				$acf_field_type_final = 'text';
				$datepicker_class = '';
				
				switch ( $acf_field_type ) {
					case 'number' : $acf_field_type_final = 'number'; break;
					case 'date_picker' : $acf_field_type_final = 'number'; $datepicker_class = 'datepicker'; break;
				}
				
				echo '<div class="col-xs-6 col-sm-4 col-md-3 form-group">';		
				if ( $acf_field_type == 'date_picker' ) {
				echo '<div class="input-group">';
				}
				
				$value = '';
				if ( isset ( $_GET[ $search_field ] ) ) {
					$value = $_GET[ $search_field ];
				}
				
				echo '<input type="' . $acf_field_type_final . '" name="' . $search_field . '" value="' . $value . '" placeholder="' . $search_label . '" class="form-control ' . $datepicker_class . '" />';
				if ( $acf_field_type == 'date_picker' ) {
				echo '<span class="input-group-addon"><i class="fa fa-calendar-o"></i></span>';
				echo '</div>';
				}
				echo '</div>';
			
			}
		
		}
		
		endwhile;

		?>	
		
		<!-- Default Order: Newest Properties First -->
		<input type="hidden" name="orderby" value="date-new" />
		
		<div class="col-xs-6 col-sm-4 col-md-3 form-group">
				<input type="submit" value="<?php _e( 'Search', 'tt' ); ?>" class="btn btn-primary btn-block form-control" />
			</div>
	
	</div>
		
	<?php if ( $property_search_features ) { ?>
	<p>
		<a href="#" id="toggle-property-search-features">
			<span class="more"><?php _e( 'Show more search options', 'tt' ); ?></span>
			<span class="less hide"><?php _e( 'Hide additional search options', 'tt' ); ?></span>
		</a>
	</p>
	<div class="property-search-features">
		<h6><?php _e( 'Property features:', 'tt' ); ?></h6>
		<div class="row">			
		<?php
			foreach ( $property_search_features as $property_search_feature ) {
			$feature = get_term_by( 'id', $property_search_feature, 'property-features' );
			
			if ( isset( $_GET['feature'] ) ) {
				$get_feature = $_GET['feature'];
				if ( is_array( $get_feature ) && in_array( $feature->slug, $get_feature ) ) {
					$get_feature = $feature->slug;
				}
			}
			else {
				$get_feature = '';
			}
			?>
			<div class="col-xs-6 col-sm-4 col-md-3 form-group">
				<input name="feature[]" id="property-search-feature-<?php echo $property_search_feature; ?>" class="property-search-feature" type="checkbox" value="<?php echo $feature->slug; ?>" <?php checked( $feature->slug, $get_feature ); ?> />
				<label for="property-search-feature-<?php echo $property_search_feature; ?>"><?php echo $feature->name; ?></label>
			</div>
			<?php } ?>
		
		</div>
	</div>
	<?php } ?>
	
</form>