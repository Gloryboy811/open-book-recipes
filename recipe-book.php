<?php
/**
 * Plugin Name: Open Book Recipes
 * Plugin URI: https://github.com/Gloryboy811/open-book-recipes
 * Description: Open Book Recipes
 * Version: 0.1.9
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
		'label'             	=> __( 'recipes', 'recipebook' ),
		'description'       	=> __( 'Recipes', 'recipebook' ),
		'labels'            	=> $labels,
		'supports'          	=> array( 'title', 'editor', 'excerpt', 'thumbnail', 'taxonomies'),
		'hierarchical'      	=> false,
		'public'            	=> true,
		'show_ui'           	=> true,
		'show_in_menu'      	=> true,
		'show_in_nav_menus' 	=> true,
		'show_in_admin_bar'		=> true,
		'show_in_rest'			=> true,
		'query_var'				=> true,
		'menu_position'     	=> 7,
        'taxonomies'        	=> array('mealtime', 'cuisine'),
        'menu_icon'         	=> 'dashicons-book-alt',
		'can_export'			=> true,
		'has_archive'         	=> true,
		'exclude_from_search'	=> false,
		'publicly_queryable'  	=> true,
		'capability_type'     	=> 'page',
	);

	register_post_type( 'recipes', $args );

	/*add_filter('single_template', array('recipes','create_single_template'));
	add_filter('archive_template', array('recipes','create_archive_template'));*/
}

add_action( 'init', 'recipebook_custom_type', 0 );

function recipe_tax_init() {
    
	$args = array(
		'label' 				=> __( 'Mealtime' ),
		'labels' 				=> getTaxLabel('Meal Type'),
		'rewrite' 				=> array( 'slug' => 'mealtime' ),
		'hierarchical' 			=> true,
		'description'			=> 'What meal type is this recipe suited for?',
		'public' 				=> true,
		'show_in_rest'			=> true,
		'show_ui'				=> true,
		'show_admin_column'		=> true,
		'query_var'				=> true,
		'rewrite'				=> [ 'slug' => 'mealtime' ],
	);
	register_taxonomy( 'mealtime', [ 'recipes' ], $args );


	$args2 = array(
			'label'				 => __( 'Cuisine' ),
            'labels' 			=> getTaxLabel('Cuisine'),
			'rewrite' 			=> array( 'slug' => 'cuisine' ),
			'hierarchical' 		=> true,
            'description' 		=> 'What cuisine is this recipe part of?',
            'public' 			=> true,
			'show_in_rest'		=> true,
			'show_ui'			=> true,
			'show_admin_column'	=> true,
			'query_var'			=> true,
	);
    register_taxonomy('cuisine', [ 'recipes' ], $args2 );

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

/**
 * Mark modern recipe pages so the active theme's duplicate title and
 * featured-image presentation can be hidden without affecting other layouts.
 */
function recipebook_modern_recipe_body_class( $classes ) {
    if ( ! is_singular( 'recipes' ) ) {
        return $classes;
    }

    $options = get_option( 'recipebook_settings', array() );
    $theme = isset( $options['layout_theme'] ) ? sanitize_key( $options['layout_theme'] ) : 'basic';

    if ( 'modern' === $theme ) {
        $classes[] = 'rb-modern-recipe';
    }

    return $classes;
}
add_filter( 'body_class', 'recipebook_modern_recipe_body_class' );


function recipebook_load_archive_template( $template ) {
    if ( is_post_type_archive( 'recipes' ) ) {
        $plugin_template = plugin_dir_path( __FILE__ ) . 'archive-recipes.php';
        if ( file_exists( $plugin_template ) ) {
            return $plugin_template;
        }
    }
    return $template;
}
add_filter( 'template_include', 'recipebook_load_archive_template' );

function add_recipe_info($content) {
	global $post;

	if ( is_singular() && in_the_loop() && is_main_query() && $post->post_type == 'recipes' ) {

		$options = get_option( 'recipebook_settings' );
		$theme = 'basic';

		// check layout theme
		$content_file =  dirname( __FILE__ )  . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'single-'.$theme.'.php';

		if (!file_exists($content_file  ) ) {
			return $content;
		}

		$the_content = $content;

		ob_start();
		include_once($content_file);
		echo '<style>' . ( isset( $options['custom_css'] ) ? wp_strip_all_tags( $options['custom_css'] ) : '' ) . '</style>';
		$content = ob_get_clean();
		$content = str_replace("    ", '', $content);
		$content = str_replace(array("<p></p>", "\r", "\n"), '', $content);
	}
	
	return $content;
}


/**
 * Apply public archive search and filters to the main recipes archive query.
 * Search uses a dedicated query-string key so WordPress keeps the CPT archive
 * template instead of switching to the global search-results template.
 */
function recipebook_filter_recipes_archive( $query ) {
    if ( is_admin() || ! $query->is_main_query() || ! $query->is_post_type_archive( 'recipes' ) ) {
        return;
    }

    $query->set( 'post_type', 'recipes' );
    $query->set( 'posts_per_page', 12 );

    if ( isset( $_GET['recipe_search'] ) ) {
        $search = sanitize_text_field( wp_unslash( $_GET['recipe_search'] ) );
        if ( $search !== '' ) {
            $query->set( 's', $search );
        }
    }

    $tax_query = array();
    foreach ( array( 'meal_type' => 'mealtime', 'cuisine' => 'cuisine' ) as $request_key => $taxonomy ) {
        if ( empty( $_GET[$request_key] ) ) {
            continue;
        }
        $slug = sanitize_title( wp_unslash( $_GET[$request_key] ) );
        if ( $slug !== '' ) {
            $tax_query[] = array(
                'taxonomy' => $taxonomy,
                'field'    => 'slug',
                'terms'    => $slug,
            );
        }
    }
    if ( count( $tax_query ) > 1 ) {
        $tax_query['relation'] = 'AND';
    }
    if ( $tax_query ) {
        $query->set( 'tax_query', $tax_query );
    }

    if ( isset( $_GET['difficulty'] ) && $_GET['difficulty'] !== '' ) {
        $query->set( 'rb_archive_difficulty', sanitize_text_field( wp_unslash( $_GET['difficulty'] ) ) );
    }
}
add_action( 'pre_get_posts', 'recipebook_filter_recipes_archive' );

/**
 * Match difficulty using dedicated metadata, with JSON fallback for older recipes.
 */
function recipebook_archive_difficulty_where( $where, $query ) {
    global $wpdb;
    if ( is_admin() || ! $query->is_main_query() || ! $query->is_post_type_archive( 'recipes' ) ) {
        return $where;
    }
    $difficulty = $query->get( 'rb_archive_difficulty' );
    if ( ! is_string( $difficulty ) || $difficulty === '' ) {
        return $where;
    }
    $where .= $wpdb->prepare(
        " AND EXISTS (
            SELECT 1 FROM {$wpdb->postmeta} AS rb_difficulty_meta
            WHERE rb_difficulty_meta.post_id = {$wpdb->posts}.ID
              AND (
                ( rb_difficulty_meta.meta_key = '_rb_recipe_difficulty' AND rb_difficulty_meta.meta_value = %s )
                OR
                ( rb_difficulty_meta.meta_key = '_rb_recipe_details'
                  AND JSON_UNQUOTE(JSON_EXTRACT(rb_difficulty_meta.meta_value, '$.difficulty')) = %s )
              )
        )",
        $difficulty,
        $difficulty
    );
    return $where;
}
add_filter( 'posts_where', 'recipebook_archive_difficulty_where', 10, 2 );



/**
 * Extend archive text search to recipe ingredients, method steps and notes.
 */
function recipebook_archive_search_meta( $search, $query ) {
    global $wpdb;
    if ( is_admin() || ! $query->is_main_query() || ! $query->is_post_type_archive( 'recipes' ) ) { return $search; }
    $term = $query->get( 's' );
    if ( ! is_string( $term ) || $term === '' ) { return $search; }
    $like = '%' . $wpdb->esc_like( $term ) . '%';
    $sql = ' AND ( ' . $wpdb->posts . '.post_title LIKE %s OR ' . $wpdb->posts . '.post_excerpt LIKE %s OR ' . $wpdb->posts . '.post_content LIKE %s OR EXISTS ( SELECT 1 FROM ' . $wpdb->postmeta . ' AS rb_search_meta WHERE rb_search_meta.post_id = ' . $wpdb->posts . '.ID AND rb_search_meta.meta_key IN ( "_rb_recipe_components", "_rb_recipe_steps", "_rb_recipe_notes" ) AND rb_search_meta.meta_value LIKE %s ) )';
    return $wpdb->prepare( $sql, $like, $like, $like, $like );
}
add_filter( 'posts_search', 'recipebook_archive_search_meta', 10, 2 );

function recipebook_clear_archive_difficulty_cache( $post_id ) {
    if ( 'recipes' === get_post_type( $post_id ) ) { delete_transient( 'recipebook_archive_difficulties' ); }
}
add_action( 'save_post', 'recipebook_clear_archive_difficulty_cache' );


function my_javascripts() {
    if ( ! is_singular( 'recipes' ) ) {
        return;
    }
    $script_file = recipebook_url . 'script/recipe-book-front.js';
    wp_enqueue_script( 'recipe-book-front', $script_file, array(), '0.1.9', true );
}
add_action( 'wp_enqueue_scripts', 'my_javascripts' );


/* Filter the single_template with our custom function*/

ob_get_contents();// - Return the contents of the output buffer
ob_clean();// - Clean (erase) the contents of the active output buffer
ob_end_clean();// - Clean (erase) the contents of the active output buffer and turn it off
ob_get_flush();// - F


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
	$num = str_replace($str, $frac, $num);

	return $num;
}

/**
 * Change the Continue Reading text on Blog Post Entries
 */
function myprefix_post_readmore_link_text() {
	return 'View Recipe';
}
add_filter( 'ocean_post_continue_reading', 'myprefix_post_readmore_link_text' );

// include_once('admin/settings.php');
include_once('admin/admin-main.php');