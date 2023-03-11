<?php

//Add options to ACF drop-down
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


// Display post thumbnail
$post_thumb_src = get_the_post_thumbnail($post_id);

?>
