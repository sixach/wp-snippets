<?php
/**
 * A single post based on given/selected post-id.
 *
 * @link       https://sixa.ch
 * @author     Mahdi Yazdani
 * @since      1.0.0
 *
 * @package    sixa-snippets
 * @subpackage sixa-snippets/frontend/shortcode
 */

namespace SixaSnippets\Frontend\Shortcode;

/**
 * INSTRUCTIONS:
 *
 * 1. Update the namespace(s) used in this file.
 * 2. Initialize the class to register the shortcode when needed:
 *
 * add_action( 'init', function() {
 *     add_shortcode( 'sixa_post', array( 'SixaSnippets\Frontend\Shortcode\Post', 'Run' ) );
 * } );
 *
 * Note: Do not initialize this class before the `init` hook.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Post' ) ) :

	/**
	 * The file that outputs the single post content.
	 */
	class Post {

		/**
		 * Outputs post content based on the given shortcode attributes.
		 *
		 * @since    1.0.0
		 * @param    array $atts     Optional. Shortcode attributes.
		 * @return   mixed
		 */
		public static function run( $atts ) {
			$atts = shortcode_atts(
				array(
					'id'    => 0,
					'class' => '',
				),
				$atts
			);

			$return  = '';
			$post_id = $atts['id'];
			$class   = $atts['class'];
			$post    = sanitize_post( get_post( $post_id ), 'display' );

			// Proceed, if the post object is of the `WP_Post` class.
			if ( $post instanceof \WP_Post ) {
                // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
				$return = sprintf( '<div class="%s">%s</div>', implode( ' ', get_post_class( sanitize_html_class( $class ), $post_id ) ), apply_filters( 'the_content', $post->post_content, $post, $atts ) );
			}

			return $return;
		}

	}
endif;
