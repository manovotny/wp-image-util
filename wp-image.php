<?php
/**
 * WP Image.
 *
 * An image utility for WordPress.
 *
 * @link https://github.com/manovotny/wp-image
 * @since 0.1.0
 *
 * @package WP_Images
 *
 * @author Michael Novotny <manovotny@gmail.com>
 * @copyright 2014 Michael Novotny
 * @license GPL-3.0+
 *
 * @wordpress-plugin
 * Plugin Name: WP Image
 * Plugin URI: https://github.com/manovotny/wp-image
 * Description: An image utility for WordPress.
 * Version: 0.2.0
 * Author: Michael Novotny
 * Author URI: http://manovotny.com
 * Text Domain: English
 * License: GPL-3.0+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Domain Path: /lang
 */

/*
/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\ CONTENTS /\/\/\/\/\/\/\/\/\/\/\/\/\/\//\/\/\/\/\

    1. Access
    2. Plugin
    3. Admin

/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\//\/\/\/\/\/\/\/\/\/\
*/

/* Access
---------------------------------------------------------------------------------- */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {

    die;

} // end if

/* Plugin
---------------------------------------------------------------------------------- */

// Include plugin classes.
require_once( __DIR__ . '/classes/class-wp-image.php' );

// Load plugin.
add_action( 'plugins_loaded', array( 'WP_Image', 'get_instance' ) );