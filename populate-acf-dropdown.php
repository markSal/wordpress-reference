<?php

//NOTE: custom_field is used as a stand in for the acf field name in this example

/* ADD BELOW TO FUNCTIONS FILE*/
// Dynamically add choices to ACF drop-down
function acf_load_custom_field_choices($field){
	// reset choices
	$field['choices'] = array();

	// get logos
	global $wpdb;
	$query = "
		SELECT      id, post_title as 'name'
		FROM        $wpdb->posts
		WHERE       $wpdb->posts.post_type='sp_logo_carousel'
		ORDER BY    $wpdb->posts.post_title
	";
	$logos = $wpdb->get_results($query);

	// populate drop-down options
	foreach($logos as $logo){
		$field['choices'][$logo['name']] = $logo['id'];
	}

	// return the field
	return $field;
}
add_filter('acf/load_field/name=custom_field', 'acf_load_custom_field_choices');

/* ADD BELOW TO NEWS POST DISPLAY */
// Display logo in News Post
$custom_field_id = get_field('custom_field'); 						// Get data from ACF field
$logo_name = get_the_title($custom_field_id); 						// Get organization name from logo carousel
$logo_src = get_the_post_thumbnail_url($custom_field_id);  				// Get logo url form logo carousel
$logo_href = current(get_post_meta($custom_field_id, 'sp_logo_carousel_link_option')); 	// Get url array attached to logo in logo carousel
$logo_href = $logo_href['lcp_logo_link'];  						// Get url value from url array

// Display logo with link to organization from logo carousel
echo '<a href="' . $logo_href . '" target="_blank"><img src="'. $logo_src .'"  title="' . $logo_name . '"></img></a>';
?>
