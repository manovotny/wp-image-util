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

        }

        // Return instance.
        return self::$instance;

    }

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

    }

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

    }

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

    }

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

    }

    /**
     * Extracts file name and extension from a file, path, or URL.
     *
     * @param   string  $file_string    A file, path, or URL.
     * @return  string                  The file name.
     *
     * @since       0.2.0
     * @version     0.1.0
     */
    public function get_file_name_and_extension( $file_string ) {

        return pathinfo( $file_string, PATHINFO_BASENAME );

    }

    /**
     * Gets the first image inside of HTML.
     *
     * @param   string  $content                Content with some markup, usually post content.
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

            }

        }

        // No images found, return fallback image.
        return $fallback;

    }

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

        }

        // Check for featured image.
        if ( has_post_thumbnail( $post->ID ) ) {

            // Return URL of featured image.
            return $this->get_the_post_thumbnail_uri( $post->ID, array( $width, $height ) );

        }

        // Get the URL of the first image in the post content.
        $image_url = $this->get_first_image( $post->post_content );

        // Check for image url.
        if ( ! empty( $image_url ) ) {

            // Use a generated thumbnail of the first image of the post.
            return $this->generate_thumbnail( $image_url, $width, $height, $crop );

        }

        // Check for default image.
        if ( ! empty( $default ) ) {

            // Use default image.
            return $default;

        }

        // No featured image, no image in post, no default, so return empty.
        return '';

    }

    /* Private
    ---------------------------------------------- */

    /**
     * Converts a path to an image to the url of the image.
     *
     * @param   string      $image_path     Image path.
     * @return  string                      Image url.
     *
     * @since       0.2.0
     * @version     0.1.0
     */
    private function convert_image_path_to_url( $image_path ) {

        // Get WordPress upload directory information.
        $wp_upload_directory = wp_upload_dir();

        // Remove upload base directory from image path.
        $image_url = str_replace( $wp_upload_directory[ 'basedir' ], '', $image_path );

        // Add upload url to image.
        $image_url = $wp_upload_directory[ 'baseurl' ] . $image_url;

        // Return image url.
        return $image_url;

    }

    /**
     * Converts a url to an image to the path of the image.
     *
     * @param   string      $image_url      Image url.
     * @return  string                      Image path.
     *
     * @since       0.2.0
     * @version     0.1.0
     */
    private function convert_image_url_to_path( $image_url ) {

        // Get WordPress upload directory information.
        $wp_upload_directory = wp_upload_dir();

        // Remove upload base url from image url.
        $image_path = str_replace( $wp_upload_directory[ 'baseurl' ], '', $image_url );

        // Add upload path to image.
        $image_path = $wp_upload_directory[ 'basedir' ] . $image_path;

        // Return image path.
        return $image_path;

    }

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
     * @version     0.2.0
     */
    private function generate_thumbnail( $image_url, $width, $height, $crop = true ) {

        // Check for external image.
        if ( $this->is_external_image( $image_url ) ) {

            /*
             * We are not in possession of the image. We have no choice but to return
             * the original image.
             */

            return $image_url;

        }

        // Check if image is in the upload directory.
        if ( ! $this->is_uploaded_image( $image_url ) ) {

            /*
             * We don't want to mess with images outside of the uploads, like smiley faces.
             * We have no choice but to return the original image.
             */

            return $image_url;

        }

        // Remove query string.
        $image_url = $this->remove_query_string( $image_url );

        // Get image path.
        $image_path = $this->convert_image_url_to_path( $image_url );

        // Generate thumbnail name.
        $thumbnail_name = $this->generate_thumbnail_name( $image_url, $width, $height );

        // Get path to generated thumbnail name.
        $thumbnail_path = trailingslashit( dirname( $image_path ) ) . $thumbnail_name;

        // Get url to proposed generated thumbnail.
        $thumbnail_url = $this->convert_image_path_to_url( $thumbnail_path );

        // Check if thumbnail already exists.
        if ( is_file( $thumbnail_path ) ) {

            // Return existing thumbnail.
            return $thumbnail_url;

        }

        // Get image for editing.
        $thumbnail = wp_get_image_editor( $image_path );

        // Check for errors.
        if ( ! is_wp_error( $thumbnail ) ) {

            // Resize thumbnail to specified thumbnail dimensions.
            $thumbnail->resize( $width, $height, $crop );

            // Save thumbnail.
            $thumbnail->save( $thumbnail_path );

            // Return generated thumbnail.
            return $thumbnail_url;

        }

        // Something went wrong with finding or creating the thumbnail, so return original image.
        return $image_url;

    }

    /**
     * Generates a thumbnail name.
     *
     * @param   string  $image_string   Image path or url.
     * @param   float   $width          Width of thumbnail.
     * @param   float   $height         Height of thumbnail.
     * @return  string                  Generated thumbnail name.
     *
     * @since       0.2.0
     * @version     0.1.0
     */
    private function generate_thumbnail_name( $image_string, $width, $height ) {

        // Get file name.
        $file_name = $this->get_file_name( $image_string );

        // Get file extension.
        $file_extension = $this->get_file_extension( $image_string );

        // Generate thumbnail name.
        return $file_name . '-' . $width . 'x' . $height . '.' . $file_extension;

    }

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

    }

    /**
     * @param   string      $image_url  Image url.
     * @return  boolean                 Whether an image is internally or externally hosted.
     *
     * @since       0.2.0
     * @version     0.1.0
     */
    private function is_external_image( $image_url ) {

        // Parse url.
        $parsed_image_url = parse_url( $image_url );

        // Parse site url.
        $parsed_site_url = parse_url( site_url() );

        // Check if hosts match, which means we own / possess the image.
        return ( $parsed_image_url[ 'host' ] !== $parsed_site_url[ 'host' ] );

    }

    /**
     * Gets the path of an image based on image url.
     *
     * @param   string  $image_url  Image url.
     * @return  string              Image path.
     *
     * @since       0.2.0
     * @version     0.1.0
     */
    private function is_uploaded_image( $image_url ) {

        // Get WordPress upload directory information.
        $wp_upload_directory = wp_upload_dir();

        // Remove upload base url from image url.
        return ( false !== strpos( $image_url, $wp_upload_directory[ 'baseurl' ] ) );

    }

    /**
     * Removes query string from a url.
     *
     * @param   string  $url    Any url.
     * @return  string          Url with the query string removed.
     *
     * @since       0.2.0
     * @version     0.1.0
     */
    private function remove_query_string( $url ) {

        // Remove query string.
        return strtok( $url, '?' );

    }

}
