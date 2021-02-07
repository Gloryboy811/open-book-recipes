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
    //$args will contain the values posted in your settings form, you can validate them as no spaces allowed, no special chars allowed or validate emails etc.
    if(!isset($args['recipebook_email']) || !is_email($args['recipebook_email'])){
        //add a settings error because the email is invalid and make the form field blank, so that the user can enter again
        $args['recipebook_email'] = '';
        add_settings_error('recipebook_settings', 'recipebook_invalid_email', 'Please enter a valid email!', $type = 'error');   
    }

    //make sure you return the args
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