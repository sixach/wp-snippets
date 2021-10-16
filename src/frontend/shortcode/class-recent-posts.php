<?php
/**
 * A list of recent posts generated via custom shortcode attributes.
 *
 * @link          https://sixa.ch
 * @author        sixa AG
 * @since         1.0.0
 *
 * @package       Sixa_Snippets
 * @subpackage    Sixa_Snippets/Frontend/Shortcode
 * @phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
 */

namespace Sixa_Snippets\Frontend\Shortcode;

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
		 * @since     1.0.0
		 * @param     array    $atts    Optional. Shortcode attributes.
		 * @return    mixed
		 */
		public static function run( $atts ) {
			$atts = shortcode_atts(
				array(
					'class'                      => '',
					'categories'                 => array(),
					'selected_author'            => '',
					'number'                     => 5,
					'display_post_content'       => 0,
					'display_post_content_radio' => 'excerpt',
					'excerpt_length'             => 55,
					'display_author'             => 0,
					'display_post_date'          => 0,
					'display_categories'         => 1,
					'columns'                    => 3,
					'order'                      => 'desc',
					'order_by'                   => 'date',
					'display_featured_image'     => 0,
					'featured_image_size_slug'   => 'thumbnail',
					'featured_image_size_width'  => '',
					'featured_image_size_height' => '',
				),
				$atts
			);

			// Update the maximum number of words in a post excerpt.
			$excerpt_length = $atts['excerpt_length'];
			add_filter(
				'excerpt_length',
				function() use ( $excerpt_length ) {
					return $excerpt_length;
				},
				20
			);

			$query_args = apply_filters(
				'sixa_recent_posts_shortcode_args',
				array(
					'order'            => $atts['order'],
					'orderby'          => $atts['order_by'],
					'posts_per_page'   => $atts['number'],
					'post_status'      => 'publish',
					'suppress_filters' => false,
				)
			);

			if ( ! empty( $atts['categories'] ) ) {
				$query_args['category__in'] = explode( ',', $atts['categories'] );
			}
			if ( ! empty( $atts['selected_author'] ) ) {
				$query_args['author'] = $atts['selected_author'];
			}

			$return    = '';
			$class     = empty( $atts['class'] ) ? sprintf( 'wp-block-sixa-recent-posts' ) : '';
			$get_posts = get_posts( $query_args );

			// Bail early, if the query has no posts to loop over.
			if ( is_array( $get_posts ) && ! empty( $get_posts ) ) {
				$return = '';

				foreach ( $get_posts as $post ) {
					$return .= sprintf( '<div class="%s__post %s">', sanitize_html_class( $class ), implode( ' ', get_post_class( '', $post ) ) );

					// Thumbnail.
					if ( $atts['display_featured_image'] && has_post_thumbnail( $post ) ) {
						$image_style = '';
						if ( isset( $atts['featured_image_size_width'] ) ) {
							$image_style .= sprintf( 'max-width:%spx;', $atts['featured_image_size_width'] );
						}
						if ( isset( $atts['featured_image_size_height'] ) ) {
							$image_style .= sprintf( 'max-height:%spx;', $atts['featured_image_size_height'] );
						}

						$featured_image = sprintf(
							'<a href="%1$s">%2$s</a>',
							esc_url( get_permalink( $post ) ),
							get_the_post_thumbnail(
								$post,
								$atts['featured_image_size_slug'],
								array(
									'style' => $image_style,
								)
							)
						);
						$return        .= sprintf(
							'<figure class="%1$s">%2$s</figure>',
							sprintf( '%s__post-thumbnail', sanitize_html_class( $class ) ),
							$featured_image
						);
					}

					// Title.
					$title = get_the_title( $post );
					if ( ! $title ) {
						$title = __( '(no title)', 'sixa-snippets' );
					}
					$return .= sprintf( '<a href="%s" class="%s" rel="bookmark">%s</a>', esc_url( get_permalink( $post ) ), sprintf( '%s__post-title', sanitize_html_class( $class ) ), wp_kses_post( $title ) );

					// Author.
					if ( isset( $atts['display_author'] ) && $atts['display_author'] ) {
						$author_display_name = get_the_author_meta( 'display_name', $post->post_author );

						/* translators: byline. %s: current author. */
						$byline = sprintf( __( 'by %s', 'sixa-snippets' ), $author_display_name );

						if ( ! empty( $author_display_name ) ) {
							$return .= sprintf(
								'<div class="%1$s">%2$s</div>',
								sprintf( '%s__post-author', sanitize_html_class( $class ) ),
								esc_html( $byline )
							);
						}
					}

					// Date.
					if ( isset( $atts['display_post_date'] ) && $atts['display_post_date'] ) {
						$return .= sprintf(
							'<time class="%1$s" datetime="%2$s">%3$s</time>',
							sprintf( '%s__post-date', sanitize_html_class( $class ) ),
							esc_attr( get_the_date( 'c', $post ) ),
							esc_html( get_the_date( '', $post ) )
						);
					}

					// Content.
					if ( isset( $atts['display_post_content'], $atts['display_post_content_radio'] ) && $atts['display_post_content'] ) {
						// Excerpt.
						if ( 'excerpt' === $atts['display_post_content_radio'] ) {
							$trimmed_excerpt = get_the_excerpt( $post );
							$readmore        = '';

							if ( $atts['excerpt_length'] < count( explode( ' ', get_the_content( '', '', $post ) ) ) ) {
								/* translators: 1: Open anchor link, 2: Close anchor tag.  */
								$readmore = sprintf( esc_html__( '%1$sRead more%2$s', 'sixa-snippets' ), sprintf( '<a href="%s">', esc_url( get_permalink( $post ) ) ), '</a>' );
							}

							$return .= sprintf(
								'<div class="%1$s">%2$s%3$s</div>',
								sprintf( '%s__post-excerpt', sanitize_html_class( $class ) ),
								get_the_excerpt( $post ),
								$readmore
							);
						} else {
							$return .= sprintf(
								'<div class="%1$s">%2$s</div>',
								sprintf( '%s__post-content', sanitize_html_class( $class ) ),
								wp_kses_post( html_entity_decode( $post->post_content, ENT_QUOTES, get_option( 'blog_charset' ) ) )
							);
						}
					}

					if ( $atts['display_featured_image'] ) {
						$return .= sprintf(
							'<div class="%s__post-categories">%s</div>',
							sanitize_html_class( $class ),
							get_the_category_list( ' ', '', $post->ID )
						);
					}

					$return .= '</div>';
				}
			}

			return apply_filters( 'sixa_recent_posts_shortcode_output', $return );
		}

	}
endif;
