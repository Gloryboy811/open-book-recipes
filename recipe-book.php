<?php
/**
 * Plugin Name: Open Book Recipes
 * Plugin URI: https://github.com/Gloryboy811/open-book-recipes
 * Description: Open Book Recipes
 * Version: 0.0.1
 * Author: Matthew Hughes
 * Author URI: http://www.MatthewHughes.co.za
 */

defined( 'ABSPATH' ) or die( 'No tearing out pages from the Recipe Book!' );
define( 'recipebook_url', plugin_dir_url( __FILE__ ) );
define( 'recipebook_admin_url',  get_site_url().'/wp-admin/admin.php?page=' );


/* Add the menu item */
//add_action( 'admin_menu', 'recipebook_menu' );

function recipebook_custom_type() {
    $labels = array(
		'name'                => _x( 'Recipes', 'Post Type General Name', 'recipebook' ),
		'singular_name'       => _x( 'Recipe', 'Post Type Singular Name', 'recipebook' ),
		'menu_name'           => __( 'Recipe Book', 'recipebook' ),
		'parent_item_colon'   => __( 'Parent Recipe', 'recipebook' ),
		'all_items'           => __( 'View Recipes', 'recipebook' ),
		'view_item'           => __( 'View Recipe', 'recipebook' ),
		'add_new_item'        => __( 'Add New Recipe', 'recipebook' ),
		'add_new'             => __( 'Add Recipe', 'recipebook' ),
		'edit_item'           => __( 'Edit Recipe', 'recipebook' ),
		'update_item'         => __( 'Update Recipe', 'recipebook' ),
		'search_items'        => __( 'Search Recipes', 'recipebook' ),
		'not_found'           => __( 'Not Found', 'recipebook' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'recipebook' ),
	);

	$args = array(
		'label'               => __( 'recipes', 'recipebook' ),
		'description'         => __( 'Recipes', 'recipebook' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail'),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 7,
        'taxonomies'          => array('mealtime','occasion','cuisine'),
        'menu_icon'           => 'dashicons-book-alt',
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'page',
	);

	register_post_type( 'recipes', $args );
}

add_action( 'init', 'recipebook_custom_type', 0 );

function recipe_tax_init() {
    
    register_taxonomy(
		'mealtime',
		'recipes',
		array(
			'label' => __( 'Mealtime' ),
            'labels' => getTaxLabel('Meal Type'),
			'rewrite' => array( 'slug' => 'mealtime' ),
			'hierarchical' => true,
            'description' => 'What meal type is this recipe suited for?',
            'public' => true
		)
	);

    register_taxonomy(
		'cuisine',
		'recipes',
		array(
			'label' => __( 'Cuisine' ),
            'labels' => getTaxLabel('Cuisine'),
			'rewrite' => array( 'slug' => 'cuisine' ),
			'hierarchical' => true,
            'description' => 'What cuisine is this recipe part of?',
            'public' => true
		)
	);

    // register_taxonomy(
	// 	'ingredient',
	// 	'recipes',
	// 	array(
	// 		'label' => __( 'Ingredient' ),
    //         'labels' => getTaxLabel('Ingredient'),
	// 		'rewrite' => array( 'slug' => 'ingredient' ),
	// 		'hierarchical' => true,
    //         'description' => 'What ingredients feature in this recipe?',
    //         'public' => false
	// 	)
	// );

    //  register_taxonomy(
	// 	'occasion',
	// 	'recipes',
	// 	array(
	// 		'label' => __( 'Occasion' ),
    //         'labels' => getTaxLabel('Occasion'),
	// 		'rewrite' => array( 'slug' => 'occasion' ),
	// 		'hierarchical' => true,
    //         'description' => 'For what occasions would you make this recipe?',
    //         'public' => true
	// 	)
	// );

	
}
add_action( 'init', 'recipe_tax_init' );


add_filter( 'the_content', 'add_recipe_info', 1 );

function add_recipe_info($content) {
	global $post;

	if ( is_singular() && in_the_loop() && is_main_query() && $post->post_type == 'recipes' ) {

		$options = get_option( 'recipebook_settings' );
		$theme = $options['layout_theme'] ? $options['layout_theme'] : 'basic';

		// check layout theme
		$content_file =  dirname( __FILE__ )  . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'single-'.$theme.'.php';

		if (!file_exists($content_file  ) ) {
			return $content;
		}

		$the_content = $content;

		ob_start();
			include_once($content_file);
			echo '<style>'.$options['custom_css'].'</style>';
		$content = ob_get_clean();
		$content = str_replace("    ", '', $content);
		$content = str_replace(array("<p></p>", "\r", "\n"), '', $content);
		//echo '<this>'.$content.'</this>';

	}
	
	return $content;
}


function my_javascripts() {
    /*$script_file =  dirname( __FILE__ )  . DIRECTORY_SEPARATOR . 'script' . DIRECTORY_SEPARATOR . 'recipe-book-front.js';
	echo "<script>";
		include_once($script_file);
	echo "</script>";*/

	$script_fil2e =  recipebook_url . 'script' . DIRECTORY_SEPARATOR . 'recipe-book-front.js';
    wp_enqueue_script( 'recipe-book-front', $script_fil2e);
}
add_action( 'wp_enqueue_scripts', 'my_javascripts' );


/* Filter the single_template with our custom function*/
/*add_filter('single_template', 'my_custom_template');

ob_get_contents() - Return the contents of the output buffer
ob_clean() - Clean (erase) the contents of the active output buffer
ob_end_clean() - Clean (erase) the contents of the active output buffer and turn it off
ob_get_flush() - F

function my_custom_template($single) {

    global $post;

    // Checks for single template by post type 
    if ( $post->post_type == 'recipes' ) {
		$file =  dirname( __FILE__ )  . DIRECTORY_SEPARATOR .'single-recipes.php';
        if ( file_exists($file  ) ) {
            return $file ;
        } else {
			die($file.'NOT FOUND');
		}
    }
    return $single;

}*/

/*add_filter('template_include', 'recipes_template');

function recipes_template( $template ) {
  if ( is_post_type_archive('recipes') ) {
    $theme_files = array('archive-recipes.php', 'recipe-book/archive-recipes.php');
    $exists_in_theme = locate_template($theme_files, false);
    if ( $exists_in_theme != '' ) {
      return $exists_in_theme;
    } else {
      return plugin_dir_path(__FILE__) . 'archive-recipes.php';
    }
  }
  return $template;
}
*/


function my_rewrite_flush() {
    recipe_tax_init();
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'my_rewrite_flush' );

function getTaxLabel($taxName) {
    $labels = array(
        'name' => _x( $taxName.'s', 'taxonomy general name' ),
        'singular_name' => _x( $taxName, 'taxonomy singular name' ),
        'search_items' =>  __( 'Search '.$taxName.'s' ),
        'all_items' => __( 'All '.$taxName.'s' ),
        'parent_item' => __( 'Parent '.$taxName ),
        'parent_item_colon' => __( 'Parent '.$taxName.':' ),
        'edit_item' => __( 'Edit '.$taxName ), 
        'update_item' => __( 'Update '.$taxName ),
        'add_new_item' => __( 'Add New '.$taxName ),
        'new_item_name' => __( 'New '.$taxName.' Name' ),
        'menu_name' => __( $taxName.'s' ),
      ); 

      return $labels;
}

function escapeJsonString($value) {
	$result = str_replace("\n","\\n", $value);
    return $result;
}    

function showJsonString($value) {
	$result = str_replace("\\n","\n", $value);
    return $result;
}

function toFrac($num) {
	$frac = ['½', '⅓','⅔','¼','¾','⅛','⅜','⅝','⅞'];
	$str = ['1/2', '1/3','2/3','1/4','3/4','1/8','3/8','5/8','7/8'];
	/*
	$s = array_search($num, $str);
	if ($s !== false) {
		return $frac[$s];
	}*/

	$num = str_replace($str, $frac, $num);

	return $num;
}
// include_once('admin/settings.php');
include_once('admin/admin-main.php');