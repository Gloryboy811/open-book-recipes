<?php
include_once('settings.php');
include_once('recipe-gallery.php');

function recipebook_amount_fraction_convert( $amount_decimal ) {
    $amount_decimal = trim( (string) $amount_decimal );
    if ( $amount_decimal === '' || ! is_numeric( $amount_decimal ) ) {
        return $amount_decimal;
    }
    $number = (float) $amount_decimal;
    if ( abs( $number ) < 0.000001 || strpos( $amount_decimal, '.' ) === false ) {
        return $amount_decimal;
    }
    return recipebook_float_to_frac( $number );
}

function recipebook_float_to_frac( $n, $tolerance = 1.e-6 ) {
    $n = (float) $n;
    if ( ! is_finite( $n ) || $n <= 0 ) {
        return (string) $n;
    }

    $h1 = 1; $h2 = 0;
    $k1 = 0; $k2 = 1;
    $b = 1 / $n;
    $iterations = 0;
    do {
        $b = 1 / $b;
        $a = floor( $b );
        $aux = $h1; $h1 = $a * $h1 + $h2; $h2 = $aux;
        $aux = $k1; $k1 = $a * $k1 + $k2; $k2 = $aux;
        $b = $b - $a;
        $iterations++;
    } while ( $k1 && abs( $n - $h1 / $k1 ) > $tolerance && $iterations < 100 );

    if ( ! $k1 ) {
        return (string) $n;
    }
    if ( $h1 > $k1 ) {
        $whole = floor( $h1 / $k1 );
        $remainder = $h1 - ( $whole * $k1 );
        return $remainder ? "$whole $remainder/$k1" : (string) $whole;
    }
    return "$h1/$k1";
}

class RecipeBook_Admin {
    static $instance;

    public function __construct() {
        add_action( 'admin_menu', [ $this, 'admin_menu_setup' ] );
        
    }
    
    public function admin_menu_setup() {
        add_submenu_page( 
            'edit.php?post_type=recipes',//parent
            'Settings', //title
            'Settings', //menu title
            'manage_options', //capabilities
            'settings', //slug
            [ $this, 'recipebook_main'] //function
        );
    
        add_action( 'add_meta_boxes', [ $this, 'products_add_meta_box'] );
        add_filter( 'save_post', [ $this, 'recipebook_recipe_save_meta'] );
    }

    
    public function recipebook_main() {
        include_once('settings-page.php');
    }

    public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
    }
    
    function products_add_meta_box() {

        $screens = array( 'product' );
    
        foreach ( $screens as $screen ) {
    
            add_meta_box(
               'recipebook_recipe_details',
                __( 'Recipe Details', 'recipebook' ),
                 [ $this, 'recipebook_recipe_details_box_callback'],
                'recipes', 'normal', 'high'
            );

            add_meta_box(
                'recipebook_recipe_content',
                 __( 'Recipe Content', 'recipebook' ),
                  [ $this, 'recipebook_recipe_content_box_callback'],
                 'recipes', 'normal', 'high'
             );
        }
    }

    function recipebook_recipe_details_box_callback( $post ) {
        include_once('recipe-details.php');
    }

    function recipebook_recipe_content_box_callback( $post ) {
        include_once('recipe-content.php');
    }

    function recipebook_recipe_meta_box_callback( $post ) {
        include_once('edit-recipe.php');
    }

    

    function recipebook_recipe_save_meta( $post_id ) {
        if ( ! isset( $_POST['recipebook_recipe_meta_box_nonce'] ) ||
            ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['recipebook_recipe_meta_box_nonce'] ) ), 'recipebook_recipe_save_meta' ) ) {
            return;
        }
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        if ( wp_is_post_revision( $post_id ) || get_post_type( $post_id ) !== 'recipes' ||
            ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        // Build and validate the replacement data before touching existing metadata.
        $components = array();
        $posted_components = isset( $_POST['rb_components'] ) && is_array( $_POST['rb_components'] )
            ? wp_unslash( $_POST['rb_components'] ) : array();

        foreach ( $posted_components as $key => $title ) {
            $key = sanitize_key( (string) $key );
            $component = array(
                'title' => sanitize_text_field( $title ),
                'ingredients' => array(),
            );
            $names = isset( $_POST['rb_component' . $key . '_rb_ingredient_name'] ) && is_array( $_POST['rb_component' . $key . '_rb_ingredient_name'] )
                ? wp_unslash( $_POST['rb_component' . $key . '_rb_ingredient_name'] ) : array();
            $amounts = isset( $_POST['rb_component' . $key . '_rb_ingredient_amount'] ) && is_array( $_POST['rb_component' . $key . '_rb_ingredient_amount'] )
                ? wp_unslash( $_POST['rb_component' . $key . '_rb_ingredient_amount'] ) : array();
            $denoms = isset( $_POST['rb_component' . $key . '_rb_ingredient_denominator'] ) && is_array( $_POST['rb_component' . $key . '_rb_ingredient_denominator'] )
                ? wp_unslash( $_POST['rb_component' . $key . '_rb_ingredient_denominator'] ) : array();
            $extras = isset( $_POST['rb_component' . $key . '_rb_ingredient_extra'] ) && is_array( $_POST['rb_component' . $key . '_rb_ingredient_extra'] )
                ? wp_unslash( $_POST['rb_component' . $key . '_rb_ingredient_extra'] ) : array();

            foreach ( $names as $i => $name ) {
                $name = sanitize_text_field( $name );
                if ( $name === '' ) {
                    continue;
                }
                $amount = isset( $amounts[$i] ) ? sanitize_text_field( $amounts[$i] ) : '';
                $component['ingredients'][] = array(
                    'title' => $name,
                    'amount' => recipebook_amount_fraction_convert( $amount ),
                    'denom' => isset( $denoms[$i] ) ? sanitize_text_field( $denoms[$i] ) : '',
                    'extra' => isset( $extras[$i] ) ? sanitize_text_field( $extras[$i] ) : '',
                );
            }
            $components[] = wp_json_encode( $component );
        }

        delete_post_meta( $post_id, '_rb_recipe_components' );
        foreach ( $components as $component_json ) {
            add_post_meta( $post_id, '_rb_recipe_components', $component_json, false );
        }

        $steps = isset( $_POST['rb_steps'] ) && is_array( $_POST['rb_steps'] ) ? wp_unslash( $_POST['rb_steps'] ) : array();
        delete_post_meta( $post_id, '_rb_recipe_steps' );
        foreach ( $steps as $step ) {
            $step = sanitize_textarea_field( $step );
            if ( $step !== '' ) {
                add_post_meta( $post_id, '_rb_recipe_steps', $step, false );
            }
        }

        update_post_meta( $post_id, '_rb_recipe_notes', isset( $_POST['rb_notes'] ) ? sanitize_textarea_field( wp_unslash( $_POST['rb_notes'] ) ) : '' );

        $time_fields = array( 'prepHrs' => 'rb_prep_hrs', 'prepMins' => 'rb_prep_mins', 'cookHrs' => 'rb_cook_hrs', 'cookMins' => 'rb_cook_mins', 'totalHrs' => 'rb_total_hrs', 'totalMins' => 'rb_total_mins' );
        $times = array();
        foreach ( $time_fields as $field => $posted_key ) {
            $times[$field] = isset( $_POST[$posted_key] ) ? absint( $_POST[$posted_key] ) : '';
        }
        update_post_meta( $post_id, '_rb_recipe_times', wp_json_encode( $times ) );

        $details = array(
            'cals' => isset( $_POST['rb_d_calories'] ) ? absint( $_POST['rb_d_calories'] ) : '',
            'serves' => isset( $_POST['rb_d_serves'] ) ? absint( $_POST['rb_d_serves'] ) : '',
            'url' => isset( $_POST['rb_d_url'] ) ? esc_url_raw( wp_unslash( $_POST['rb_d_url'] ) ) : '',
            'difficulty' => isset( $_POST['rb_d_difficulty'] ) ? sanitize_text_field( wp_unslash( $_POST['rb_d_difficulty'] ) ) : '',
        );
        update_post_meta( $post_id, '_rb_recipe_details', wp_json_encode( $details ) );

        update_post_meta( $post_id, '_rb_nut_titles', isset( $_POST['rb_n_t'] ) && is_array( $_POST['rb_n_t'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['rb_n_t'] ) ) : array() );
        update_post_meta( $post_id, '_rb_nut_amounts', isset( $_POST['rb_n_a'] ) && is_array( $_POST['rb_n_a'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['rb_n_a'] ) ) : array() );
    }
    
    
    
}

add_action( 'plugins_loaded', function () {
	RecipeBook_Admin::get_instance();
});