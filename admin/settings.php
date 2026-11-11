<?php
/*
 * Register the settings
 */
add_action('admin_init', 'recipebook_register_settings');
function recipebook_register_settings(){
    //this will save the option in the wp_options table as 'recipebook_settings'
    //the third parameter is a function that will validate your input values
    register_setting('recipebook_settings', 'recipebook_settings', 'recipebook_settings_validate');
}

function recipebook_settings_validate($args){
    if ( ! is_array( $args ) ) {
        return array();
    }

    $allowed = array(
        'layout_theme' => array( 'basic', 'modern' ),
        'title_card' => array( 'icons', 'heading', 'excerpt' ),
        'image_size' => array( 'corner', 'half', 'wide' ),
        'rating_place' => array( 'over', 'side' ),
    );
    foreach ( $allowed as $key => $values ) {
        if ( isset( $args[$key] ) && ! in_array( $args[$key], $values, true ) ) {
            unset( $args[$key] );
        }
    }
    foreach ( array( 'accent_color', 'accent_color_base' ) as $color_key ) {
        if ( isset( $args[$color_key] ) ) {
            $color = sanitize_hex_color( $args[$color_key] );
            if ( $color ) {
                $args[$color_key] = $color;
            } else {
                unset( $args[$color_key] );
            }
        }
    }
    if ( isset( $args['custom_css'] ) ) {
        $args['custom_css'] = wp_strip_all_tags( $args['custom_css'] );
    }
    return $args;
}

//Display the validation errors and update messages
/*
 * Admin notices
 */
add_action('admin_notices', 'recipebook_admin_notices');
function recipebook_admin_notices(){
   settings_errors();
}

//The markup for your plugin settings page
function recipebook_admin_page_callback(){ 
    include_once('settings-page.php');
 }
?>