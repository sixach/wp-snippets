<?php
/**
 * A list of FAQ items generated via shortcode attributes.
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

if ( ! class_exists( 'FAQ' ) ) :

	/**
	 * The file that outputs the single post content.
	 */
	class FAQ {

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
					'category'                   => '',
					'number'                     => 2,
					'order'                      => 'desc',
					'order_by'                   => 'date',
				),
				$atts
			);

			$query_args = apply_filters(
				'sixa_faq_shortcode_args',
				array(
					'post_type'        => apply_filters( 'sixa_faq_post_type', 'faq-item' ),
					'order'            => $atts['order'],
					'orderby'          => $atts['order_by'],
					'posts_per_page'   => $atts['number'],
					'post_status'      => 'publish',
					'suppress_filters' => false,
					'tax_query'        => array(), // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				)
			);

			if ( $atts['category'] ) {
				array_push(
					$query_args['tax_query'],
					array(
						'taxonomy' => apply_filters( 'sixa_faq_category_endpoint', 'faq-item-category' ),
						'field'    => 'term_id',
						'terms'    => (array) $atts['category'],
					)
				);
			}

			$return    = '';
			$class     = empty( $atts['class'] ) ? 'wp-block-sixa-faq' : '';
			$get_posts = get_posts( $query_args );
			// Bail early, if the query has no posts to loop over.
			if ( is_array( $get_posts ) && ! empty( $get_posts ) ) {
				foreach ( $get_posts as $post ) {
					$return .= sprintf(
						'<details class="%s">',
						implode(
							' ',
							get_post_class(
								sprintf(
									'%s__item',
									sanitize_html_class( $class )
								),
								$post
							)
						)
					);

					// Title.
					$title = get_the_title( $post );
					if ( ! $title ) {
						$title = __( '(no title)', 'sixa-snippets' );
					}
					$return .= sprintf( '<summary class="%s">%s</summary>', sprintf( '%s__title', sanitize_html_class( $class ) ), wp_kses_post( $title ) );

					// Content.
					$return .= sprintf(
						'<div class="%1$s">%2$s</div>',
						sprintf( '%s__content', sanitize_html_class( $class ) ),
						wp_kses_post( html_entity_decode( $post->post_content, ENT_QUOTES, get_option( 'blog_charset' ) ) )
					);

					$return .= '</details>';
				}
			}

			return apply_filters( 'sixa_faq_shortcode_output', $return );
		}

	}
endif;
