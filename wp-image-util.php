<?php
/**
 * An image utility for WordPress.
 *
 * A collection of helpful utilities for working with images in WordPress.
 *
 * @package WP_Image_Util
 * @author Michael Novotny <manovotny@gmail.com>
 * @license GPL-3.0+
 * @link https://github.com/manovotny/wp-image-util
 * @copyright 2014 Michael Novotny
 *
 * @wordpress-plugin
 * Plugin Name: WP Image Util
 * Plugin URI: https://github.com/manovotny/wp-image-util
 * Description: An image utility for WordPress.
 * Version: 0.5.0
 * Author: Michael Novotny
 * Author URI: http://manovotny.com
 * License: GPL-3.0+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */


/*
/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\ CONTENTS /\/\/\/\/\/\/\/\/\/\/\/\/\/\//\/\/\/\/\

    1. Access
    2. Includes
    3. Plugin

/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\//\/\/\/\/\/\/\/\/\/\
*/

/* Access
---------------------------------------------------------------------------------- */

if ( ! defined( 'WPINC' ) ) {

    die;

}

/* Includes
---------------------------------------------------------------------------------- */

require_once __DIR__ . '/inc/querypath/qp.php';

/* Plugin
---------------------------------------------------------------------------------- */

if ( ! class_exists( 'WP_Image_Util' ) ) {

    require_once __DIR__ . '/classes/class-wp-image-util.php';

}
