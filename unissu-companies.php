<?php
/**
 * Plugin Name: Unissu Companies Plugin
 * Plugin URI: https://www.propmodo.com/
 * Description: This plugin get companies from Unissu API and view in the posts
 * Version: 1.0
 * Author: Promodo
 * Author URI: http://propmodo.com/
 **/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

require_once plugin_dir_path( __FILE__ ) . '/inc/index.php';

register_activation_hook( __FILE__, 'uwc_install' );
register_activation_hook( __FILE__, 'uwc_install_data' );