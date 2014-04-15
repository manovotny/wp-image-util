<?php
/**
 * WP_Image.
 *
 * @package     WP_Image
 * @author      Michael Novotny <manovotny@gmail.com>
 * @license     GPL-3.0+
 * @link        https://github.com/manovotny/wp-gist
 * @copyright   2014 Michael Novotny
 */

/*
/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\ CONTENTS /\/\/\/\/\/\/\/\/\/\/\/\/\/\//\/\/\/\/\

    1. Properties
    2. Constructor
    3. Helpers

/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\//\/\/\/\/\/\/\/\/\/\
*/

class WP_Image {

    /* Properties
    ---------------------------------------------------------------------------------- */

    /* Instance
    ---------------------------------------------- */

    /**
     * Instance of the WP_Image class.
     *
     * @access      protected static
     * @var         WP_Image
     *
     * @since       0.1.0
     * @version     0.1.0
     */
    protected static $instance = null;

    /**
     * Get accessor method for instance property.
     *
     * @return      WP_Image  Instance of the WP_Image class.
     *
     * @since       0.1.0
     * @version     0.1.0
     */
    public static function get_instance() {

        // Check if an instance has not been created yet.
        if ( null == self::$instance ) {

            // Set instance of class.
            self::$instance = new self;

        } // end if

        // Return instance.
        return self::$instance;

    } // end get_instance

    /* Constructor
    ---------------------------------------------------------------------------------- */
    /**
     * Initializes plugin.
     *
     * @since       0.1.0
     * @version     0.1.0
     */
    public function __construct() {

        // Adds theme support for WordPress Featured Images.
        add_theme_support( 'post-thumbnails' );

    } // end constructor

    /* Helpers
    ---------------------------------------------------------------------------------- */

    /* Public
    ---------------------------------------------- */

    /**
     * Echoes a thumbnail of the first image in the current post.
     *
     * Must be used inside of the loop.
     *
     * @param   int         $width                  Width of thumbnail output.
     * @param   int         $height (optional)      Height of thumbnail output (will match width, if not supplied).
     * @param   boolean     $crop (optional)        If thumbnail should be cropped.
     * @param   string      $default (optional)     Path to default image, if there is no image in the post.
     * 
     * @since       0.1.0
     * @version     0.1.0
     */
    public function display_thumbnail( $width, $height = null, $crop = true, $default = null ) {

        // Display thumbnail.
        _e( $this->get_thumbnail( $width, $height, $crop, $default ) );

    } // end display_thumbnail

    /**
     * Extracts file extension from a file name, path or URL.
     *
     * @param   string  $file_string    A file name, path, or URL.
     * @return  string                  The file extension.
     * 
     * @since       0.1.0
     * @version     0.1.0
     */
    public function get_file_extension( $file_string )  {

        return pathinfo( $file_string, PATHINFO_EXTENSION );

    } // end get_file_extension

    /**
     * Extracts file name from a file, path, or URL.
     *
     * @param   string  $file_string    A file, path, or URL.
     * @return  string                  The file name.
     * 
     * @since       0.1.0
     * @version     0.1.0
     */
    public function get_file_name( $file_string ) {

        return pathinfo( $file_string, PATHINFO_FILENAME );

    } // end get_file_name

    /**
     * Gets the first image inside of HTML.
     *
     * @param   string  $content                HTML (usually post content).
     * @param   string  $fallback (optional)    URL of fallback image to use, if none are found in HTML.
     * @return  string                          URL of first image.
     * 
     * @since       0.1.0
     * @version     0.1.0
     */
    public function get_first_image( $content, $fallback = '' ) {

        // Check for content.
        if ( ! empty( $content ) ) {

            // Find all images.
            $pattern = '~<img (?:.*?)src(?:\s*)=(?:\s*)"(.*?)"(?:.*?)/>~';
            preg_match( $pattern, $content, $matches );

            // Check for matches.
            if ( ! empty( $matches ) ) {

                // Return first image.
                return $matches[ 1 ];

            } // end if

        } // end if

        // No images found, return fallback image.
        return $fallback;

    } // end get_first_image

    /**
     * Returns a thumbnail of the first image in the current post.
     *
     * Must be used inside of the loop.
     *
     * @param       int         $width                  Width of thumbnail output.
     * @param       int         $height (optional)      Height of thumbnail output (will match width, if not supplied).
     * @param       boolean     $crop (optional)        If thumbnail should be cropped.
     * @param       string      $default (optional)     Path to default image, if there is no image in the post.
     * @return      string                              URL to thumbnail.
     * 
     * @since       0.1.0
     * @version     0.1.0
     */
    public function get_thumbnail( $width, $height = null, $crop = true, $default = null ) {

        global $post;

        // Check for height.
        if ( is_null( $height ) ) {

            // Match width.
            $height = $width;

        } // end if

        // Check for featured image.
        if ( has_post_thumbnail( $post->ID ) ) {

            // Return URL of featured image.
            return $this->get_the_post_thumbnail_uri( $post->ID, array( $width, $height ) );

        } // end if

        // Get post content.
        $content = apply_filters( 'the_content', $post->post_content );

        // Get the URL of the first image in the post content.
        $image_url = $this->get_first_image( $content );

        // Check for image url.
        if ( ! empty( $image_url ) ) {

            // Use a generated thumbnail of the first image of the post.
            return $this->generate_thumbnail( $image_url, $width, $height, $crop );

        } // end if

        // Check for default image.
        if ( ! empty( $default ) ) {

            // Use default image.
            return $default;

        } // end if

        // No featured image, no image in post, no default, so return empty.
        return '';

    } // end get_thumbnail

    /* Private
    ---------------------------------------------- */

    /**
     * Generate a thumbnail, at a specified size, from an image url.
     *
     * @param   string      $image_url          URL to image to create a thumbnail with.
     * @param   int         $width              Width of thumbnail to create.
     * @param   int         $height (optional)  Height of thumbnail to create.
     * @param   boolean     $crop (optional)    If thumbnail image should be cropped.
     * @return  string                          Thumbnail image URL or original image URL.
     * 
     * @since       0.1.0
     * @version     0.1.0
     */
    private function generate_thumbnail( $image_url, $width, $height, $crop = true ) {

        // Remove query string.
        $image_url = substr( $image_url, 0, strpos( $image_url, '?' ) );

        // Parse image URL.
        $parsed_image_url = parse_url( $image_url );

        // Parse site URL.
        $parsed_site_url = parse_url( site_url() );

        // Check if hosts match, which means we have control of the image to resize it.
        if ( $parsed_image_url['host'] !== $parsed_site_url['host'] ) {

            /*
             * Hosts do not match, so we cannot resize the image. We have no choice but to
             * return the original image.
             */

            return $image_url;

        } // end if

        // Get image path (ie. "wp-content/uploads/yyyy/mm/image.jpg").
        $image_path = substr( $image_url, strpos( $image_url, 'wp-content' ) );

        // Generate thumbnail name.
        $thumbnail_name = $this->get_file_name( $image_path ) . '-' . $width . 'x' . $height . '.' . $this->get_file_extension( $image_path );

        // Explode image path.
        $image_path_exploded = explode( '/', $image_path );

        // Replace image URL with requested thumbnail image name.
        $image_path_exploded[ count( $image_path_exploded ) - 1 ] = $thumbnail_name;

        // Implode to set thumbnail image URL.
        $thumbnail_path = implode( '/', $image_path_exploded );

        // Check if thumbnail already exists.
        if ( is_file( $thumbnail_path ) ) {

            // Return existing thumbnail.
            return site_url() . '/' .  $thumbnail_path;

        } // end if

        // Get image for editing.
        $thumbnail = wp_get_image_editor( $image_path );

        // Check for errors.
        if ( ! is_wp_error( $thumbnail ) ) {

            // Resize thumbnail to specified thumbnail dimensions.
            $thumbnail->resize( $width, $height, $crop );

            // Save thumbnail.
            $thumbnail->save( $thumbnail_path );

        } // end if

        // Something went wrong with finding or creating the thumbnail, so return original image.
        return $image_url;

    } // end generate_thumbnail

    /**
     * Gets a post's featured image URL.
     *
     * @param   string  $post_id    Post id.
     * @param   mixed   $size       Size of featured image to return.
     * @return  string              Featured image URL.
     * 
     * @since       0.1.0
     * @version     0.1.0
     */
    private function get_the_post_thumbnail_uri( $post_id, $size ) {

        // Get featured image.
        $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), $size );

        // Return featured image URL.
        return $image[ 0 ];

    } // end get_the_post_thumbnail_uri

} // end class
