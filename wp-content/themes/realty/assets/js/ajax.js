/* Ajax - Search Results
-------------------------*/
jQuery(window).load(function() {

	function tt_ajax_search_results() {
	  "use strict";
	  
	  /* GET Parameters
	  function getUrlVars() {
			var map = {};
			var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
				map[key] = value;
			});
			return map;
		}
		
		var urlParameter = getUrlVars();
		var ajaxMeta = {
			'feature'					: 	feature,
			'action'          :   'tt_ajax_search', // WP Function
			'base'						: 	window.location.pathname
		};
		
		// Check If Get Param Object Is Empty. If so, serialize form elements for AJAX data
		if ( jQuery.isEmptyObject( urlParameter ) ) {
			var ajaxData = jQuery('.property-search-form').first().serialize() + "&feature=feature&action=tt_ajax_search&base=" + window.location.pathname
		}
		else {
			var ajaxData = jQuery.extend( urlParameter, ajaxMeta );
		}
		*/
		
		if ( jQuery('#price-range').length ) {
	  	
	  	var price_range, min_price, max_price;
	  	
	  	price_range			=   jQuery('#price-range').val();
	  	min_price 			= 	priceFormat.from( price_range[0] );
	  	max_price 			= 	priceFormat.from( price_range[1] );
	  	
	  	jQuery('#property-search-price-range-min').val(min_price);
			jQuery('#property-search-price-range-max').val(max_price);
	
	  }
		
		if ( jQuery('.property-search-feature') ) {	
			//var feature = new Array;
			var feature = [];
			jQuery('.property-search-feature:checked').each(function() {
			  feature.push( jQuery(this).val() );
			});
		}
		
		var ajaxData = jQuery('.property-search-form').first().serialize() + "&action=tt_ajax_search&base=" + window.location.pathname;
	
	  jQuery.ajax({
	    
	    type: 'GET',
	    url: ajaxURL,
	    data: ajaxData,
	    success: function (response) {
	      jQuery('#property-items').html(response); // Show response from function tt_ajax_search()
	    },
	    error: function () {
	    	console.log( 'failed' );
	    }
	    
	  });
	
	}
	
	// Remove Map Markers & Marker Cluster
	function removeMarkers() {
		// http://google-maps-utility-library-v3.googlecode.com/svn/trunk/markerclusterer/examples/speed_test.js
	  for( i = 0; i < newMarkers.length; i++ ) {
	  	newMarkers[i].setMap(null);
			// Close Infoboxes
	  	if ( newMarkers[i].infobox.getVisible() ) {
	    	newMarkers[i].infobox.hide();
	  	}
	  }
	  if ( markerCluster ) { 
	  	markerCluster.clearMarkers();
	  }
	  markers = [];
	  newMarkers = [];
	  bounds = [];
	}
		
	// Fire Search Results Ajax On Serach Field Change
	jQuery('#price-range, .property-search-form select, .property-search-form input').change(function() {
	
		tt_ajax_search_results();
		removeMarkers();	
		
	});
	
});