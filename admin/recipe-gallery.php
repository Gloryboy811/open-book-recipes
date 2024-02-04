<?php
/*
 * @param string $name Name of option or name of post custom field.
 * @param string $value Optional Attachment IDs
 * @return string HTML of the Upload Button
 */
function recipe_image_uploader_field( $name, $values = '') {
	$image_size = 'thumbnail'; // it would be better to use thumbnail size here (150x150 or so)
	$display = 'none'; // display state ot the "Remove image" button
	$image = '';

	if (is_array($values)) {
		foreach($values as $key => $value) {
			$image_attributes = wp_get_attachment_image_src( $value, $image_size ); 

			// $image_attributes[0] - image URL
			// $image_attributes[1] - image width
			// $image_attributes[2] - image height
	
			$images .= '
				<li id="rb-gallery-' . $key . '">
					<img src="' . $image_attributes[0] . '" style="max-width:95%;display:block;" />
					<input type="hidden" name="rb_gallery['. $key.']" value="' . esc_attr( $value ) . '" />
					<a href="#" data-rbgid="' . $key . '" class="recipe_remove_image_button">Remove</a>
				</li>';
			$display = 'inline-block';
		} 
	}

	if (isset($images)) {
		return '<div><ul id="recipe-images">' . $images . '</ul><a href="#" class="recipe_upload_image_button button">Select Images</a> <a href="#" class="recipe_clear_gallery_button button" style="display:'.$display.'">Remove All</a></div>';
	}
	return '<div><a href="#" class="recipe_upload_image_button button">Select Images</a> <a href="#" class="recipe_clear_gallery_button button" style="display:'.$display.'">Remove All</a></div>';
	
}

function recipe_include_myuploadscript() {
	if ( ! did_action( 'wp_enqueue_media' ) ) {
		wp_enqueue_media();
	}
}
 
add_action( 'admin_enqueue_scripts', 'recipe_include_myuploadscript' );
/*
 * Add a meta box
 */
add_action( 'admin_menu', 'recipe_meta_box_add' );
 
function recipe_meta_box_add() {
	add_meta_box('recipediv', // meta box ID
		'Recipe Gallery', // meta box title
		'recipe_print_box', // callback function that prints the meta box HTML 
		'recipes', // post type where to add it
		'side', // priority
		'default' ); // position
}
 
/*
 * Meta Box HTML
 */
function recipe_print_box( $post ) {
	$meta_key = 'gallery_images';
	echo recipe_image_uploader_field( $meta_key, get_post_meta($post->ID, $meta_key, true) );
}
 
/*
 * Save Meta Box data
 */
add_action('save_post', 'recipe_save');
 
function recipe_save( $post_id ) {
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
		return $post_id;
	$meta_key = 'gallery_images';
	if (isset($_POST['rb_gallery'])) {
		update_post_meta( $post_id, $meta_key, $_POST['rb_gallery']); 
	}
	return $post_id;
}

