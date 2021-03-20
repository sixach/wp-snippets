<?php
/**
 * A list of recent posts generated via custom shortcode attributes.
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
 *     add_shortcode( 'sixa_recent_posts', array( 'SixaSnippets\Frontend\Shortcode\Recent_Posts', 'Run' ) );
 * } );
 *
 * Note: Do not initialize this class before the `init` hook.
 * Usage: [sixa_recent_posts categories="31,32,40" number="4" show_date="1" show_author="1" show_thumb="1"]
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Recent_Posts' ) ) :

	/**
	 * The file that outputs the single post content.
	 */
	class Recent_Posts {

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
					'categories'  => '',
					'number'      => 4,
					'show_date'   => 0,
					'show_author' => 0,
					'show_thumb'  => 0,
					'order'       => 'desc',
					'orderby'     => 'date',
				),
				$atts
			);

			$return      = '';
			$categories  = ! empty( $atts['categories'] ) ? explode( ',', $atts['categories'] ) : array();
			$number      = intval( $atts['number'] );
			$order       = esc_html( $atts['order'] );
			$orderby     = esc_html( $atts['orderby'] );
			$show_date   = ( 1 === intval( $atts['show_date'] ) || 'true' === $atts['show_date'] ) ? true : false;
			$show_author = ( 1 === intval( $atts['show_author'] ) || 'true' === $atts['show_author'] ) ? true : false;
			$show_thumb  = ( 1 === intval( $atts['show_thumb'] ) || 'true' === $atts['show_thumb'] ) ? true : false;
			$get_posts   = get_posts(
				apply_filters(
					'sixa_recent_posts_shortcode_args',
					array(
						'order'            => $order,
						'orderby'          => $orderby,
						'category__in'     => $categories,
						'numberposts'      => $number,
						'post_status'      => 'publish',
						'suppress_filters' => false,
					)
				)
			);

			// Bail early, if the query has no posts to loop over.
			if ( is_array( $get_posts ) && ! empty( $get_posts ) ) {
				$return = '<ul>';

				foreach ( $get_posts as $post ) {
					$return .= sprintf( '<li class="%s">', implode( ' ', get_post_class( '', $post ) ) );

					// Thumbnail.
					if ( ! ! $show_thumb && has_post_thumbnail( $post ) ) {
						$return .= sprintf( '<figure class="entry-thumbnail"><a href="%s">%s</a></figure>', esc_url( get_permalink( $post ) ), get_the_post_thumbnail( $post ) );
					}

					// Title.
					$return .= sprintf( '<a href="%s" class="entry-title" rel="bookmark">%s</a>', esc_url( get_permalink( $post ) ), wp_kses_post( get_the_title( $post ) ) );

					// Author.
					if ( ! ! $show_author ) {
						/* translators: 1: Open span tag, 2: Author anchor tag, 3: Close span tag. */
						$return .= sprintf( _x( '%1$sby %2$s%3$s', 'recent posts shortcode', '@@textdomain' ), '<span class="post-author">', sprintf( '<a href="%s" class="url fn" rel="author">%s</a>', esc_url( get_author_posts_url( get_the_author_meta( 'ID', $post->post_author ) ) ), get_the_author_meta( 'display_name', $post->post_author ) ), '</span>' );
					}

					// Date.
					if ( ! ! $show_date ) {
						$return .= sprintf( '<a href="%s" rel="bookmark"><time class="entry-date published" datetime="%s">%s</time></a>', esc_url( get_permalink( $post ) ), esc_attr( get_the_date( 'c' ) ), esc_html( get_the_date() ) );
					}

					$return .= '</li>';
				}

				$return .= '</ul>';
			}

			return apply_filters( 'sixa_recent_posts_shortcode_output', $return );
		}

	}
endif;
