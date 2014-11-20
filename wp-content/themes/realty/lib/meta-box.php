<?php
function estate_register_meta_boxes( $meta_boxes ) {

            
	$prefix = 'estate_';
	
	$agents = array( '' => __( 'None', 'tt' ) );
	// Get all users with role "agent"
	$all_agents = get_users( array( 'role' => 'agent', 'fields' => 'ID' ) );
	foreach( $all_agents as $agent ) { 
		$agents[$agent] = get_user_meta($agent, 'first_name', true ) . ' ' . get_user_meta($agent, 'last_name', true );
	}

	/* PROPERTY
	============================== */
	$meta_boxes[] = array(		
		'id' 						=> 'property_settings',
		'title' 				=> __( 'Property Settings', 'tt' ),
		'pages' 				=> array( 'property' ),
		'context' 			=> 'normal',
		'priority' 			=> 'high',
		'autosave' 			=> true,
		'fields' 				=> array(
			array(
				'name' 					=> __( 'Property Layout', 'tt' ),
				'id'   					=> "{$prefix}property_layout",
				'desc'  				=> __( 'Choose Property Layout.', 'tt' ),
				'type' 					=> 'select',
				'options'  => array(
					'theme_option_setting' 	=> __( 'Theme Option Setting', 'tt' ),
					'full_width' 						=> __( 'Full Width', 'tt' ),
					'boxed' 								=> __( 'Boxed', 'tt' ),
				),
				'std'  					=> 'theme_option_setting',
			),
			array(
				'name' 					=> __( 'Property Status Update', 'tt' ),
				'id'   					=> "{$prefix}property_status_update",
				'desc'  				=> __( 'E.g. "Sold", "Rented Out" etc.', 'tt' ),
				'type' 					=> 'text',
				'std'   				=> __( '', 'tt' ),
			),
			array(
				'name' 					=> __( 'Featured Property', 'tt' ),
				'id'   					=> "{$prefix}property_featured",
				'type' 					=> 'checkbox',
				'std'  					=> 0,
			),
			/*
			array(
				'name' 					=> __( 'Property Video', 'tt' ),
				'id'   					=> "{$prefix}property_video",
				'desc'  				=> __( 'Insert full video URL.', 'tt' ),
				'type' 					=> 'text',
				'std'   				=> __( '', 'tt' ),
			),
			*/
			array(
				'name'             => __( 'Property Images', 'tt' ),
				'id'               => "{$prefix}property_images",
				'type'             => 'image_advanced',
				'max_file_uploads' => 100,
			),
			array(
				'name'  				=> __( 'Property ID', 'tt' ),
				'id'    				=> "{$prefix}property_id",
				'desc'  				=> __( '', 'tt' ),
				'type'  				=> 'text',
				'std'   				=> __( '', 'tt' ),
			),
			array(
				'name'  				=> __( 'Address', 'tt' ),
				'id'    				=> "{$prefix}property_address",
				'desc'  				=> __( '', 'tt' ),
				'type'  				=> 'text',
				'std'   				=> __( '', 'tt' ),
			),
			array(
        'id'            => "{$prefix}property_location",
        'name'          => __( 'Google Maps' , 'tt' ),
        'desc'          => __( 'Enter Property Address Above, Then Click "Find Address" To Search For Exact Location On The Map. Drag & Drop Map Marker If Necessary.' , 'tt' ),
        'type'          => 'map',
        'std'           => '', // 'latitude,longitude[,zoom]' (zoom is optional)
        'style'         => 'width: 400px; height: 200px; margin-bottom: 1em',
        'address_field' => "{$prefix}property_address", // Name of text field where address is entered. Can be list of text fields, separated by commas (for ex. city, state)
      ),
			array(
				'name' => __( 'Available From', 'tt' ),
				'id'   => "{$prefix}property_available_from",
				'type' => 'date',
				// jQuery date picker options. See here http://api.jqueryui.com/datepicker
				'js_options' => array(
					'appendText'      => __( '(YYYYMMDD)', 'tt' ),
					'dateFormat'      => __( 'yymmdd', 'tt' ),
					'changeMonth'     => true,
					'changeYear'      => true,
					'showButtonPanel' => false,
				),
			),
			/*
			array(
				'name' => __( 'Available Until', 'tt' ),
				'id'   => "{$prefix}property_available_until",
				'type' => 'date',
				// jQuery date picker options. See here http://api.jqueryui.com/datepicker
				'js_options' => array(
					'appendText'      => __( '(YYYYMMDD)', 'tt' ),
					'dateFormat'      => __( 'yymmdd', 'tt' ),
					'changeMonth'     => true,
					'changeYear'      => true,
					'showButtonPanel' => false,
				),
			),
			*/
			array(
				'name'  				=> __( 'Property Price', 'tt' ),
				'id'    				=> "{$prefix}property_price",
				'desc'  				=> __( 'Property Sale or Rent Price (Digits Only, i.e. "1000")', 'tt' ),
				'type'  				=> 'number',
				'std'   				=> __( '1000', 'tt' ),
				'step'  				=> 0.01,
			),
			array(
				'name'  				=> __( 'Property Price Text', 'tt' ),
				'id'    				=> "{$prefix}property_price_text",
				'desc'  				=> __( 'Text After Property Price (i.e. "per month")', 'tt' ),
				'type'  				=> 'text',
				'std'   				=> __( '', 'tt' ),
			),
			array(
				'name'  				=> __( 'Size', 'tt' ),
				'id'    				=> "{$prefix}property_size",
				'desc'  				=> __( 'Property Size (Digits Only, i.e. "250")', 'tt' ),
				//'type'  				=> 'text',
				'type'  				=> 'number',
				'std'   				=> __( '250', 'tt' ),
				'step'  				=> 0.01,
			),
			array(
				'name'  				=> __( 'Size Unit', 'tt' ),
				'id'    				=> "{$prefix}property_size_unit",
				'desc'  				=> __( 'Unit Appears After Property Size (i.e. "sq ft")', 'tt' ),
				'type'  				=> 'text',
				'std'   				=> __( 'sq ft', 'tt' ),
			),
			array(
				'name' 					=> __( 'Rooms', 'tt' ),
				'id'   					=> "{$prefix}property_rooms",
				'type' 					=> 'number',
				'prefix' 				=> __( '', 'tt' ),
				'suffix' 				=> __( '', 'tt' ),
				'js_options' 		=> array(
					'min'   					=> 0,
					'max'   					=> 100,
					'step'  					=> 1,
				),
			),
			array(
				'name' 					=> __( 'Bedrooms', 'tt' ),
				'id'   					=> "{$prefix}property_bedrooms",
				'type' 					=> 'number',
				'prefix' 				=> __( '', 'tt' ),
				'suffix' 				=> __( '', 'tt' ),
				'js_options' 		=> array(
					'min'   					=> 0,
					'max'   					=> 100,
					'step'  					=> 1,
				),
			),
			array(
				'name' 					=> __( 'Bathrooms', 'tt' ),
				'id'   					=> "{$prefix}property_bathrooms",
				'type' 					=> 'number',
				'prefix' 				=> __( '', 'tt' ),
				'suffix' 				=> __( '', 'tt' ),
				'min'   				=> 0,
				'max'   				=> 100,
				'step'  				=> 0.5,
			),
			array(
				'name' 					=> __( 'Garages', 'tt' ),
				'id'   					=> "{$prefix}property_garages",
				'type' 					=> 'number',
				'prefix' 				=> __( '', 'tt' ),
				'suffix' 				=> __( '', 'tt' ),
				'js_options' 		=> array(
					'min'   					=> 0,
					'max'   					=> 100,
					'step'  					=> 1,
				),
			),
			array(
				'name'     			=> __( 'Contact Information', 'tt' ),
				'id'       			=> "{$prefix}property_contact_information",
				'type'     			=> 'select',
				'options'  => array(
					'all' 						=> __( 'Profile Information & Contact Form', 'tt' ),
					'form' 						=> __( 'Contact Form Only', 'tt' ),
					'none' 						=> __( 'None', 'tt' ),
				),
				'std'  					=> 'all',
			),
			array(
				'name'     			=> __( 'Assign Agent', 'tt' ),
				'id'       			=> "{$prefix}property_custom_agent", // Until Realty 1.2 "property_agent"
				'desc'          => __( 'Selected agent will be able to edit this property.' , 'tt' ),
				'type'     			=> 'select',
				'options'  			=> $agents,
			),
		)
	);
	
	
	/* TESTIMONIAL
	============================== */
	$meta_boxes[] = array(		
		'id' 						=> 'testimonial_settings',
		'title' 				=> __( 'Testimonial', 'tt' ),
		'pages' 				=> array( 'testimonial' ),
		'context' 			=> 'normal',
		'priority' 			=> 'high',
		'autosave' 			=> true,
		'fields' 				=> array(
			array(
				'name' 					=> __( 'Testimonial Text', 'tt' ),
				'id'   					=> "{$prefix}testimonial_text",
				'type' 					=> 'textarea',
				'std'  					=> __( '', 'tt' ),
			),
		)
	);
	
	
	/* POST TYPE "GALLERY"
	============================== */
	$meta_boxes[] = array(		
		'id' 						=> 'post_type_gallery',
		'title' 				=> __( 'Gallery Settings', 'tt' ),
		'pages' 				=> array( 'post' ),
		'context' 			=> 'normal',
		'priority' 			=> 'high',
		'autosave' 			=> true,
		'fields' 				=> array(
			array(
				'name'             => __( 'Gallery Images', 'tt' ),
				'id'               => "{$prefix}post_gallery",
				'type'             => 'image_advanced',
				'max_file_uploads' => 100,
			),
		)
	);
	
	
	/* POST TYPE "VIDEO"
	============================== */
	$meta_boxes[] = array(		
		'id' 						=> 'post_type_video',
		'title' 				=> __( 'Video Settings', 'tt' ),
		'pages' 				=> array( 'post' ),
		'context' 			=> 'normal',
		'priority' 			=> 'high',
		'autosave' 			=> true,
		'fields' 				=> array(
			array(
			'name'	=> 'Full Video URL',
			'id'	=> "{$prefix}post_video_url",
			'desc'	=> 'Insert Full Video URL (i.e. <strong>http://vimeo.com/99370876</strong>)',
			'type' 	=> 'text',
			'std' 	=> ''
		)
		)
	);
	
	
	/* PAGE SETTINGS
	============================== */
	$meta_boxes[] = array(		
		'id' 						=> 'pages_settings',
		'title' 				=> __( 'Page Settings', 'tt' ),
		'pages' 				=> array( 'post', 'page', 'property', 'agent' ),
		'context' 			=> 'normal',
		'priority' 			=> 'high',
		'autosave' 			=> true,
		'fields' 				=> array(
			array(
				'name' 					=> __( 'Hide Sidebar', 'tt' ),
				'id'   					=> "{$prefix}page_hide_sidebar",
				'type' 					=> 'checkbox',
				'std'  					=> 0,
			),
			// Intro Page Only
			array(
				'name'             => __( 'Intro Fullscreen Background Slideshow Images', 'tt' ),
				'id'               => "{$prefix}intro_fullscreen_background_slideshow_images",
				'class'						 => 'intro-only',
				'type'             => 'image_advanced',
				'max_file_uploads' => 100,
			),
			array(
				'name' 					=> __( 'Property Slideshow: Type', 'tt' ),
				'id'   					=> "{$prefix}property_slideshow_type",
				'class'					=> 'property-slideshow-meta-box',
				'desc'  				=> __( '', 'tt' ),
				'type' 					=> 'select',
				'options'  			=> array(
					'featured' 				=> __( 'Featured Properties', 'tt' ),
					'latest' 					=> __( 'Latest Three Properties', 'tt' ),
					'selected' 				=> __( 'Selected Properties (choose below)', 'tt' ),
				),
				'std'  					=> 'latest',
			),
			array(
				'name'    			=> __( 'Property Slideshow: Selected Properties', 'tt' ),
				'id'      			=> "{$prefix}property_slideshow_selected_properties",
				'class'					=> 'property-slideshow-meta-box',
				'type'    			=> 'post',
				'post_type' 		=> 'property',
				'field_type' 		=> 'select_advanced',
				'multiple'    	=> true,
				// Query arguments (optional). No settings means get all published posts
				'query_args' 		=> array(
					'post_status' 		=> 'publish',
					'posts_per_page' 	=> '-1',
				)
			),
			array(
				'name' 					=> __( 'Property Slideshow: Show Mini Search', 'tt' ),
				'id'   					=> "{$prefix}property_slideshow_mini_search",
				'class'					=> 'property-slideshow-meta-box',
				'type' 					=> 'checkbox',
				'std'  					=> 0,
			),
			array(
				'name' 					=> __( 'Property Slideshow: Fullscreen', 'tt' ),
				'id'   					=> "{$prefix}property_slideshow_fullscreen",
				'class'					=> 'property-slideshow-meta-box',
				'type' 					=> 'checkbox',
				'std'  					=> 0,
			),
			/* XXX
			array(
				'name'             => __( 'Intro Fullscreen Background Video URL', 'tt' ),
				'id'               => "{$prefix}intro_fullscreen_background_video_url",
				'class'						 => 'intro-only',
				'type'             => 'text',
				'desc'						 => 'Insert Full Video URL (i.e. <strong>https://www.youtube.com/watch?v=0q_oXY0thxo</strong>)',
			),
			*/
		)
	);

	return $meta_boxes;
}
add_filter( 'rwmb_meta_boxes', 'estate_register_meta_boxes' );