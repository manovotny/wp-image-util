<?php
/**
 * @package WP_Image_Util
 *
 * @wordpress-plugin
 * Plugin Name: WP Image Util
 * Plugin URI: https://github.com/manovotny/wp-image-util
 * Description: A collection of helpful utilities for working with images in WordPress.
 * Version: 1.1.0
 * Author: Michael Novotny
 * Author URI: http://manovotny.com
 * License: GPL-3.0+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Domain Path: /lang
 * Text Domain: wp-image-util
 * GitHub Plugin URI: https://github.com/manovotny/wp-image-util
 */

/* Composer
---------------------------------------------------------------------------------- */

if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {

    require_once __DIR__ . '/vendor/autoload.php';

}

/* Initialization
---------------------------------------------------------------------------------- */

require_once __DIR__ . '/src/initialize.php';