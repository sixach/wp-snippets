<?php
/**
 * The file generates a list of related posts.
 *
 * @link          https://sixa.ch
 * @author        sixa AG
 * @since         1.1.0
 *
 * @package       Sixa_Snippets
 * @subpackage    Sixa_Snippets/Frontend
 * @phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
 */

namespace Sixa_Snippets\Frontend;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Related_Posts' ) ) :

	/**
	 * The file that defines the core related-posts class.
	 */
	class Related_Posts {

		/**
		 * CSS class name.
		 *
		 * @access    private
		 * @var       string     $class    CSS class name used for this component.
		 */
		private static $class = 'sixa-related-posts';

		/**
		 * Run this class and output generated posts on the page.
		 *
		 * @since    1.0.0
		 * @since    1.1.0
		 *           Added `sixa_related_posts_before_post` and `sixa_related_posts_before_post` actions
		 *           Added `show_readmore` to display readmore button
		 * @param    array   $args    Post query arguments.
		 * @param    boolean $echo    Optional. Echo the output or return it.
		 * @return   string
		 */
		public static function run( $args = array(), $echo = true ) {
			/* translators: 1: Open `div` tag, 2: Close H3 tag. */
			$return = sprintf( esc_html_x( '%1$sRelated Posts%2$s', 'related posts', 'sixa-snippets' ), sprintf( '<div class="%s"><h3>', sanitize_html_class( self::$class ) ), '</h3>' );

			// Bail early, in case the query is NOT for an existing single post of any post type.
			if ( is_singular() ) {
				$args = wp_parse_args(
					$args,
					array(
						'show_date'       => 1,
						'show_author'     => 1,
						'show_thumb'      => 1,
						'show_categories' => 1,
						'show_excerpt'    => 1,
						'show_readmore'   => 0,
					)
				);

				$show_date       = ( 1 === intval( $args['show_date'] ) || 'true' === $args['show_date'] ) ? true : false;
				$show_author     = ( 1 === intval( $args['show_author'] ) || 'true' === $args['show_author'] ) ? true : false;
				$show_thumb      = ( 1 === intval( $args['show_thumb'] ) || 'true' === $args['show_thumb'] ) ? true : false;
				$show_categories = ( 1 === intval( $args['show_categories'] ) || 'true' === $args['show_categories'] ) ? true : false;
				$show_excerpt    = ( 1 === intval( $args['show_excerpt'] ) || 'true' === $args['show_excerpt'] ) ? true : false;
				$show_readmore   = ( 1 === intval( $args['show_readmore'] ) || 'true' === $args['show_readmore'] ) ? true : false;
				$get_posts       = self::generate( $args );

				// Bail early, if the query has no posts to loop over.
				if ( is_array( $get_posts ) && ! empty( $get_posts ) ) {
					$return .= '<ul>';

					foreach ( $get_posts as $post ) {
						$return .= sprintf( '<li class="%s">', implode( ' ', get_post_class( '', $post ) ) );
						$return .= apply_filters( 'sixa_related_posts_before_post', __return_empty_string(), $post );

						// Thumbnail.
						if ( $show_thumb && has_post_thumbnail( $post ) ) {
							$return .= sprintf( '<figure class="%s__thumbnail"><a href="%s">%s</a></figure>', sanitize_html_class( self::$class ), esc_url( get_permalink( $post ) ), get_the_post_thumbnail( $post ) );
						}

						// Title.
						$return .= sprintf( '<a href="%s" class="%s__title" rel="bookmark">%s</a>', esc_url( get_permalink( $post ) ), sanitize_html_class( self::$class ), wp_kses_post( get_the_title( $post ) ) );

						// Author.
						if ( $show_author ) {
							/* translators: 1: Open span tag, 2: Author anchor tag, 3: Close span tag. */
							$return .= sprintf( _x( '%1$sby %2$s%3$s', 'related posts', 'sixa-snippets' ), sprintf( '<span class="%s__author">', sanitize_html_class( self::$class ) ), sprintf( '<a href="%s" class="url fn" rel="author">%s</a>', esc_url( get_author_posts_url( get_the_author_meta( 'ID', $post->post_author ) ) ), get_the_author_meta( 'display_name', $post->post_author ) ), '</span>' );
						}

						// Date.
						if ( $show_date ) {
							$return .= sprintf( '<time class="%s__date published" datetime="%s">%s</time>', sanitize_html_class( self::$class ), esc_attr( get_the_date( 'c' ) ), esc_html( get_the_date() ) );
						}

						// Categories.
						if ( $show_categories && is_single() ) {
							$return .= sprintf( '<div class="%s__categories">%s</div>', sanitize_html_class( self::$class ), get_the_category_list() );
						}

						// Excerpt.
						if ( $show_excerpt ) {
							$return .= sprintf( '<div class="%s__excerpt">%s</div>', sanitize_html_class( self::$class ), wpautop( get_the_excerpt( $post ) ) );
						}

						// Read more.
						if ( $show_readmore ) {
							/* translators: 1: Open anchor link, 2: Close anchor tag.  */
							$return .= sprintf( esc_html__( '%1$sRead more%2$s', 'sixa-snippets' ), sprintf( '<a href="%s">', esc_url( get_permalink( $post ) ) ), '</a>' );
						}

						$return .= apply_filters( 'sixa_related_posts_after_post', __return_empty_string(), $post );
						$return .= '</li>';
					}

					$return .= '</ul>';
				}
			}

			$return .= '</div>';

			if ( ! $echo ) {
				return $return;
			}

			echo $return; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		/**
		 * Generate posts query.
		 *
		 * @since     1.0.0
		 * @param     array           $args    Post query arguments.
		 * @return    bool|WP_Post
         * @phpcs:disable WordPress.DB.SlowDBQuery.slow_db_query_tax_query
		 */
		public static function generate( $args = array() ) {
			global $post;

			$args = wp_parse_args(
				$args,
				apply_filters(
					'sixa_related_posts_query_args',
					array(
						'number'    => 3,
						'orderby'   => 'date',
						'order'     => 'desc',
						'taxonomy'  => 'category',
						'post_id'   => ! empty( $post ) ? $post->ID : '',
						'post_type' => ! empty( $post ) ? $post->post_type : 'post',
					)
				)
			);

			if ( ! taxonomy_exists( $args['taxonomy'] ) ) {
				return false;
			}

			// Retrieves the terms for this post.
			$taxonomies = wp_get_post_terms( $args['post_id'], $args['taxonomy'], array( 'fields' => 'ids' ) );

			if ( empty( $taxonomies ) ) {
				return false;
			}

			return get_posts(
				apply_filters(
					'sixa_related_posts_query_args',
					array(
						'post__not_in'   => array( $args['post_id'] ),
						'post_type'      => $args['post_type'],
						'posts_per_page' => $args['number'],
						'orderby'        => $args['orderby'],
						'order'          => $args['order'],
						'tax_query'      => array(
							array(
								'field'    => 'term_id',
								'terms'    => $taxonomies,
								'taxonomy' => $args['taxonomy'],
							),
						),
					)
				)
			);
		}

	}
endif;
