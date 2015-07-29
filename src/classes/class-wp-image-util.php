<?php

class WP_Image_Util {

    /* Properties
    ---------------------------------------------------------------------------------- */

    /**
     * Instance of the class.
     *
     * @var WP_Image_Util
     */
    protected static $instance = null;

    /* Public
    ---------------------------------------------------------------------------------- */

    /**
     * Echoes a thumbnail of the first image in the current post.
     *
     * Must be used inside of the loop.
     *
     * @param int $width Width of thumbnail output.
     * @param int $height Optional. Height of thumbnail output (will match width, if not supplied).
     * @param boolean $crop Optional. If thumbnail should be cropped.
     * @param string $default Optional. Path to default image, if there is no image in the post.
     */
    public function display_thumbnail( $width, $height = null, $crop = true, $default = null ) {

        // Display thumbnail.
        _e( $this->get_thumbnail( $width, $height, $crop, $default ) );

    }

    /**
     * Generates a datauri of an image.
     *
     * @param $absolute_path_to_file string Absolute path to an image file.
     * @return string Encoded datauri of image.
     */
    public function generate_datauri( $absolute_path_to_file ) {

        $wp_url_util = WP_Url_Util::get_instance();

        $datauri_header = '';

        $file_type = $wp_url_util->get_file_extension( $absolute_path_to_file );

        switch ( $file_type ) {

            case 'png':

                $datauri_header = 'data:image/png;base64,';

                break;

            case 'svg':

                $datauri_header = 'data:image/svg+xml;base64,';

                break;

        }

        $file_contents = file_get_contents( $absolute_path_to_file );

        return $datauri_header . base64_encode( $file_contents );

    }

    /**
     * Generate a thumbnail, at a specified size, from an image url.
     *
     * @param string $image_url URL to image to create a thumbnail with.
     * @param int $width Width of thumbnail to create.
     * @param int $height Optional. Height of thumbnail to create.
     * @param boolean $crop Optional. If thumbnail image should be cropped.
     * @return string Thumbnail image URL or original image URL.
     */
    public function generate_thumbnail( $image_url, $width, $height, $crop = true ) {

        $wp_url_util = WP_Url_Util::get_instance();

        // Check for external image.
        if ( $wp_url_util->is_external_file( $image_url ) ) {

            /*
             * We are not in possession of the image. We have no choice but to return
             * the original image.
             */

            return $image_url;

        }

        // Check if image is in the upload directory.
        if ( ! $wp_url_util->is_uploaded_file( $image_url ) ) {

            /*
             * We don't want to mess with images outside of the uploads, like smiley faces.
             * We have no choice but to return the original image.
             */

            return $image_url;

        }

        // Remove query string.
        $image_url = $wp_url_util->remove_query_string( $image_url );

        // Get image path.
        $image_path = $wp_url_util->convert_url_to_absolute_path( $image_url );

        // Generate thumbnail name.
        $thumbnail_name = $this->generate_thumbnail_name( $image_url, $width, $height );

        // Get path to generated thumbnail name.
        $thumbnail_path = trailingslashit( dirname( $image_path ) ) . $thumbnail_name;

        // Get url to proposed generated thumbnail.
        $thumbnail_url = $wp_url_util->convert_absolute_path_to_url( $thumbnail_path );

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
     * @param string $image Image path or url.
     * @param float $width Width of thumbnail.
     * @param float $height Height of thumbnail.
     * @return string Generated thumbnail name.
     */
    public function generate_thumbnail_name( $image, $width, $height ) {

        $wp_url_util = WP_Url_Util::get_instance();

        // Get file name.
        $file_name = $wp_url_util->get_file_name( $image );

        // Get file extension.
        $file_extension = $wp_url_util->get_file_extension( $image );

        // Generate thumbnail name.
        return $file_name . '-' . $width . 'x' . $height . '.' . $file_extension;

    }

    /**
     * Gets the first image inside of HTML.
     *
     * @param string $content Content with some markup, usually post content.
     * @param string $fallback Optional. URL of fallback image to use, if none are found in HTML.
     * @return string URL of first image.
     */
    public function get_first_image( $content, $fallback = '' ) {

        $wp_url_util = WP_Url_Util::get_instance();

        $images = $this->get_image_elements( $content );

        if ( $images->length ) {

            foreach ( $images as $image ) {

                $image_source = $image->getAttribute( 'src' );

                $image_source = $wp_url_util->remove_query_string( $image_source );

                if ( ! empty( $image_source ) ) {

                    return $image_source;

                }
            }

        }

        return $fallback;

    }

    /**
     * Gets the first WordPress image thumbnail id inside of content.
     *
     * @param string $content Content with some markup, usually post content.
     * @return int|string First WordPress image thumbnail id, if found. Otherwise, returns an empty string.
     */
    public function get_first_image_thumbnail_id( $content ) {

        $wp_image_class_prefix = 'wp-image-';

        $images = $this->get_image_elements( $content );

        if ( $images->length ) {

            foreach ( $images as $image ) {

                $image_class = $image->getAttribute( 'class' );

                $image_classes = explode( ' ', $image_class );

                foreach ( $image_classes as $class ) {

                    if ( false !== strpos( $class, $wp_image_class_prefix ) ) {

                        return intval( str_replace( $wp_image_class_prefix, '', $class ) );

                    }

                }
            }

        }

        return '';

    }

    /**
     * Gets the image DOM elements from content.
     *
     * @param string $content Content with some markup, usually post content.
     * @return DOMNodeList List of image elements found in content.
     */
    public function get_image_elements( $content ) {

        if ( ! empty( $content ) ) {

            $dom_util = WP_DOM_Util::get_instance();

            $dom = new DOMDocument();
            $dom->preserveWhiteSpace = false;
            $dom->LoadHTML( $dom_util->get_meta() . $content );

            return $dom->getElementsByTagName( 'img' );

        }

        return new DOMNodeList();

    }

    /**
     * Gets instance of class.
     *
     * @return WP_Recipe_Post_Type Instance of the class.
     */
    public static function get_instance() {

        if ( null == self::$instance ) {

            self::$instance = new self;

        }

        return self::$instance;

    }

    /**
     * Gets a post's featured image URL.
     *
     * @param string $post_id Post id.
     * @param mixed $size Size of featured image to return.
     * @return string Featured image URL.
     */
    public function get_the_post_thumbnail_uri( $post_id, $size ) {

        // Get featured image.
        $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), $size );

        // Return featured image URL.
        return $image[ 0 ];

    }

    /**
     * Returns a thumbnail of the first image in the current post.
     *
     * Must be used inside of the loop.
     *
     * @param int $width Width of thumbnail output.
     * @param int $height Optional. Height of thumbnail output (will match width, if not supplied).
     * @param boolean $crop Optional. If thumbnail should be cropped.
     * @param string $default Optional. Path to default image, if there is no image in the post.
     * @return string URL to thumbnail.
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

}
