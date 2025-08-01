<?php
include_once('settings.php');
include_once('recipe-gallery.php');

function recipebook_amount_fraction_convert( $amount_decimal ) {
    if (strpos($amount_decimal, '.') == false) 
    { return $amount_decimal; }

    $TheFloatVal = floatval($amount_decimal);
    return recipebook_float_to_frac($TheFloatVal);
 }

function recipebook_float_to_frac($n, $tolerance = 1.e-6) {
    
    $h1=1; $h2=0;
    $k1=0; $k2=1;
    $b = 1/$n;
    do {
        $b = 1/$b;
        $a = floor($b);
        $aux = $h1; $h1 = $a*$h1+$h2; $h2 = $aux;
        $aux = $k1; $k1 = $a*$k1+$k2; $k2 = $aux;
        $b = $b-$a;
    } while (abs($n-$h1/$k1) > $n*$tolerance);

    if ($h1 > $k1) {
        $h3 = floor($h1 / $k1);
        $h1 = $h1 - ($h3 * $k1);

        return "$h3 $h1/$k1";

    } else {
        return "$h1/$k1";
    }

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

        // Check if our nonce is set.
        if ( ! isset( $_POST['recipebook_recipe_meta_box_nonce'] ) ) {
            return;
        }
        
        // Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $_POST['recipebook_recipe_meta_box_nonce'], 'recipebook_recipe_save_meta' ) ) {
            return;
        }
        
        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
    
        // Check the user's permissions.
        if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {
    
            if ( ! current_user_can( 'edit_page', $post_id ) ) {
                return;
            }
    
        } else {
    
            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                return;
            }
        }
    
            delete_post_meta($post_id, '_rb_recipe_components');
            if (!isset($_POST['rb_components'])) {
                return;
            }
            foreach ($_POST['rb_components'] as $key => $value) {
                //do something
                $newComponentJson = '{ "title": "'.$value.'", "ingredients": [';
                        
                foreach ($_POST['rb_component'.$key.'_rb_ingredient_name'] as $i => $title) {
                    //do something
                    if ($title == '') continue;

                    $amountConv = recipebook_amount_fraction_convert($_POST['rb_component'.$key.'_rb_ingredient_amount'][$i]);


                    $ingredientJson = 
                    '{ "title": "'.escapeJsonString($title).
                        '", "amount": "'.$amountConv.
                        '", "denom": "'.$_POST['rb_component'.$key.'_rb_ingredient_denominator'][$i].
                        '", "extra": "'.$_POST['rb_component'.$key.'_rb_ingredient_extra'][$i].
                        '"}';
                        if ($i > 0) {
                            $ingredientJson = ','.$ingredientJson;
                        }
                    
                    $newComponentJson .= $ingredientJson;
                }

                $newComponentJson .= ']}';
                // var_dump($newComponentJson);
                add_post_meta( $post_id, '_rb_recipe_components', $newComponentJson, FALSE );
            }

            delete_post_meta($post_id, '_rb_recipe_steps');
            foreach ($_POST['rb_steps'] as $i => $stepText) { 
                if ($stepText == '') continue;
                add_post_meta( $post_id, '_rb_recipe_steps', $stepText, FALSE );
            }

            delete_post_meta($post_id, '_rb_recipe_notes'); 
            add_post_meta( $post_id, '_rb_recipe_notes', $_POST['rb_notes'], FALSE );
    
            // times
            $times = '{ "prepHrs": "'.$_POST['rb_prep_hrs'].'", "prepMins": "'.$_POST['rb_prep_mins'].
                '", "cookHrs": "'.$_POST['rb_cook_hrs'].'", "cookMins": "'.$_POST['rb_cook_mins'].
                '", "totalHrs": "'.$_POST['rb_total_hrs'].'", "totalMins": "'.$_POST['rb_total_mins'].'" }';

            update_post_meta( $post_id, '_rb_recipe_times', $times );

            $dets = '{ "cals": "'.$_POST['rb_d_calories'].
                '", "serves": "'.$_POST['rb_d_serves'].
                '", "url": "'.$_POST['rb_d_url'].
                '", "difficulty": "'.$_POST['rb_d_difficulty'].'" }';

            update_post_meta( $post_id, '_rb_recipe_details', $dets );    

            //recipe nut info
            //rb_n_t //rb_n_a
            if (isset($_POST['rb_n_t'])) {
               update_post_meta( $post_id, '_rb_nut_titles', $_POST['rb_n_t'] );   
            }
            if (isset($_POST['rb_n_a'])) {
               update_post_meta( $post_id, '_rb_nut_amounts', $_POST['rb_n_a'] );   
            }
              

    }
    
    
    
}

add_action( 'plugins_loaded', function () {
	RecipeBook_Admin::get_instance();
});