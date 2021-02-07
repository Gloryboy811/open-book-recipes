<?php
get_header(); 

$options = get_option( 'recipebook_settings' );
$theme = $options['layout_theme'] ? $options['layout_theme'] : 'basic';

include_once('templates/single-'.$theme.'.php');
// include_once('templates/single-magazine.php');
// include_once('templates/single-olive.php');


echo '<style>'.$options['custom_css'].'</style>';

// get_sidebar( 'content' );
// get_sidebar();
get_footer();