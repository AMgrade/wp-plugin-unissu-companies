<?php
// Add Scripts
function unw_add_scripts(){

	// Add Main CSS
	wp_enqueue_style('unw-main-style', plugin_dir_url( __FILE__ ) . '../css/style.css');

	if(is_single()) {
		// Add Main JS
		wp_enqueue_script('unw-main-script', plugin_dir_url( __FILE__ ) . '../js/main.js', array('jquery'), true);
	}
}
add_action('wp_enqueue_scripts', 'unw_add_scripts');