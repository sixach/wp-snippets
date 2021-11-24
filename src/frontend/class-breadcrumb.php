<?php
/**
 * The file generates a list of breadcrumb trails.
 *
 * @link          https://sixa.ch
 * @author        sixa AG
 * @since         1.4.3
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

if ( ! class_exists( 'Breadcrumb' ) ) :

	/**
	 * The file that defines the core breadcrumb class.
	 */
	class Breadcrumb {

		/**
		 * Breadcrumb trail.
		 *
		 * @access    protected
		 * @var       array        $crumbs    List of breadcrumbs trail.
		 */
		protected $crumbs = array();

		/**
		 * Add a crumb so we don't get lost.
		 *
		 * @since     1.0.0
		 * @param     string    $name    Name.
		 * @param     string    $link    Link.
		 * @return    void
		 */
		public function add_crumb( $name, $link = '' ) {
			$this->crumbs[] = array(
				wp_strip_all_tags( $name ),
				$link,
			);
		}

		/**
		 * Reset crumbs.
		 *
		 * @since     1.0.0
		 * @return    void
		 */
		public function reset() {
			$this->crumbs = array();
		}

		/**
		 * Get the breadcrumb.
		 *
		 * @since     1.0.0
		 * @return    array
		 */
		public function get_breadcrumb() {
			return apply_filters( 'sixa_get_breadcrumb', $this->crumbs, $this );
		}

		/**
		 * Generate breadcrumb trail.
		 *
		 * @since     1.0.0
		 * @return    array
		 */
		public function generate() {
			$conditionals = array(
				'is_home',
				'is_404',
				'is_attachment',
				'is_single',
				'is_product_category',
				'is_product_tag',
				'is_shop',
				'is_page',
				'is_post_type_archive',
				'is_category',
				'is_tag',
				'is_author',
				'is_date',
				'is_tax',
			);

			if ( ! is_front_page() || is_paged() ) {
				foreach ( $conditionals as $conditional ) {
					if ( function_exists( $conditional ) && call_user_func( $conditional ) ) {
						call_user_func( array( $this, sprintf( 'add_crumbs_%s', substr( $conditional, 3 ) ) ) );
						break;
					}
				}

				$this->search_trail();
				$this->paged_trail();

				return $this->get_breadcrumb();
			}

			return array();
		}

		/**
		 * Prepend the shop page to shop breadcrumbs.
		 *
		 * @since     1.0.0
		 * @return    void
		 */
		protected function prepend_shop_page() {
			// Proceed if the given function has been defined.
			if ( function_exists( 'wc_get_page_id' ) ) {
				$permalinks   = wc_get_permalink_structure();
				$shop_page_id = wc_get_page_id( 'shop' );
				$shop_page    = get_post( $shop_page_id );

				// If permalinks contain the shop page in the URI prepend the breadcrumb with shop.
				if ( $shop_page_id && $shop_page && isset( $permalinks['product_base'] ) && strstr( $permalinks['product_base'], sprintf( '/%s', $shop_page->post_name ) ) && intval( get_option( 'page_on_front' ) ) !== $shop_page_id ) {
					$this->add_crumb( get_the_title( $shop_page ), get_permalink( $shop_page ) );
				}
			}
		}

		/**
		 * Is home trail?
		 *
		 * @since     1.0.0
		 * @return    void
		 */
		protected function add_crumbs_home() {
			$this->add_crumb( single_post_title( '', false ) );
		}

		/**
		 * 404 trail.
		 *
		 * @since     1.0.0
		 * @return    void
		 */
		protected function add_crumbs_404() {
			$this->add_crumb( _x( 'Error 404', 'breadcrumb', 'sixa-snippets' ) );
		}

		/**
		 * Attachment trail.
		 *
		 * @since     1.0.0
		 * @return    void
		 */
		protected function add_crumbs_attachment() {
			global $post;

			$this->add_crumbs_single( $post->post_parent, get_permalink( $post->post_parent ) );
			$this->add_crumb( get_the_title(), get_permalink() );
		}

		/**
		 * Single post trail.
		 *
		 * @since     1.4.3
		 *            Display the general name for the post type, usually plural.
		 * @since     1.0.0
		 * @param     int       $post_id      Post ID.
		 * @param     string    $permalink    Post permalink.
		 * @return    void
		 */
		protected function add_crumbs_single( $post_id = 0, $permalink = '' ) {
			if ( ! $post_id ) {
				global $post;
			} else {
				$post = get_post( $post_id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			}

			if ( ! $permalink ) {
				$permalink = get_permalink( $post );
			}

			if ( 'product' === get_post_type( $post ) ) {
				$this->prepend_shop_page();

				$terms = wc_get_product_terms(
					$post->ID,
					'product_cat',
					array(
						'orderby' => 'parent',
						'order'   => 'DESC',
					)
				);

				if ( $terms ) {
					$main_term = $terms[0];
					$this->term_ancestors( $main_term->term_id, 'product_cat' );
					$this->add_crumb( $main_term->name, get_term_link( $main_term ) );
				}
			} elseif ( 'post' !== get_post_type( $post ) ) {
				$post_type = get_post_type_object( get_post_type( $post ) );

				if ( ! empty( $post_type->has_archive ) ) {
					$this->add_crumbs_post_type_archive( $post );
				}
			} else {
				$page_for_posts = get_option( 'page_for_posts', false );

				if ( $page_for_posts ) {
					$this->add_crumb( get_the_title( $page_for_posts ), get_the_permalink( $page_for_posts ) );
				}

				$cat = current( get_the_category( $post ) );
				if ( $cat ) {
					$this->term_ancestors( $cat->term_id, 'category' );
					$this->add_crumb( $cat->name, get_term_link( $cat ) );
				}
			}

			$this->add_crumb( get_the_title( $post ), $permalink );
		}

		/**
		 * Page trail.
		 *
		 * @since     1.0.0
		 * @return    void
		 */
		protected function add_crumbs_page() {
			global $post;

			if ( $post->post_parent ) {
				$parent_crumbs = array();
				$parent_id     = $post->post_parent;

				while ( $parent_id ) {
					$page            = get_post( $parent_id );
					$parent_id       = $page->post_parent;
					$parent_crumbs[] = array( get_the_title( $page->ID ), get_permalink( $page->ID ) );
				}

				$parent_crumbs = array_reverse( $parent_crumbs );

				foreach ( $parent_crumbs as $crumb ) {
					$this->add_crumb( $crumb[0], $crumb[1] );
				}
			}

			$this->add_crumb( get_the_title(), get_permalink() );
			$this->endpoint_trail();
		}

		/**
		 * Product category trail.
		 *
		 * @since     1.0.0
		 * @return    void
		 */
		protected function add_crumbs_product_category() {
			$current_term = $GLOBALS['wp_query']->get_queried_object();

			$this->prepend_shop_page();
			$this->term_ancestors( $current_term->term_id, 'product_cat' );
			$this->add_crumb( $current_term->name, get_term_link( $current_term, 'product_cat' ) );
		}

		/**
		 * Product tag trail.
		 *
		 * @since     1.0.0
		 * @return    void
		 */
		protected function add_crumbs_product_tag() {
			$current_term = $GLOBALS['wp_query']->get_queried_object();

			$this->prepend_shop_page();

			/* translators: %s: product tag */
			$this->add_crumb( sprintf( _x( 'Products tagged &ldquo;%s&rdquo;', 'breadcrumb', 'sixa-snippets' ), $current_term->name ), get_term_link( $current_term, 'product_tag' ) );
		}

		/**
		 * Shop breadcrumb.
		 *
		 * @since     1.0.0
		 * @return    void
		 */
		protected function add_crumbs_shop() {
			if ( intval( get_option( 'page_on_front' ) ) === wc_get_page_id( 'shop' ) ) {
				return;
			}

			$shop_title = wc_get_page_id( 'shop' ) ? get_the_title( wc_get_page_id( 'shop' ) ) : '';

			if ( ! $shop_title ) {
				$product_post_type = get_post_type_object( 'product' );
				$shop_title        = $product_post_type->labels->name;
			}

			$this->add_crumb( $shop_title, get_post_type_archive_link( 'product' ) );
		}

		/**
		 * Post type archive trail.
		 *
		 * @since     1.4.3
		 *            Optionally, provide a post-object as an argument.
		 * @since     1.0.0
		 * @param     null|object    $post    Post object.
		 * @return    void
		 */
		protected function add_crumbs_post_type_archive( $post = null ) {
			$post_type = get_post_type_object( get_post_type( $post ) );

			if ( $post_type ) {
				$this->add_crumb( $post_type->labels->name, get_post_type_archive_link( get_post_type( $post ) ) );
			}
		}

		/**
		 * Category trail.
		 *
		 * @since     1.0.0
		 * @return    void
		 */
		protected function add_crumbs_category() {
			$this_category = get_category( $GLOBALS['wp_query']->get_queried_object() );

			if ( 0 !== intval( $this_category->parent ) ) {
				$this->term_ancestors( $this_category->term_id, 'category' );
			}

			$this->add_crumb( single_cat_title( '', false ), get_category_link( $this_category->term_id ) );
		}

		/**
		 * Tag trail.
		 *
		 * @since     1.0.0
		 * @return    void
		 */
		protected function add_crumbs_tag() {
			$queried_object = $GLOBALS['wp_query']->get_queried_object();

			/* translators: %s: tag name */
			$this->add_crumb( sprintf( _x( 'Posts tagged &ldquo;%s&rdquo;', 'breadcrumb', 'sixa-snippets' ), single_tag_title( '', false ) ), get_tag_link( $queried_object->term_id ) );
		}

		/**
		 * Add crumbs for date based archives.
		 *
		 * @since     1.0.0
		 * @return    void
		 */
		protected function add_crumbs_date() {
			if ( is_year() || is_month() || is_day() ) {
				$this->add_crumb( get_the_time( 'Y' ), get_year_link( get_the_time( 'Y' ) ) );
			}
			if ( is_month() || is_day() ) {
				$this->add_crumb( get_the_time( 'F' ), get_month_link( get_the_time( 'Y' ), get_the_time( 'm' ) ) );
			}
			if ( is_day() ) {
				$this->add_crumb( get_the_time( 'd' ) );
			}
		}

		/**
		 * Add crumbs for taxonomies.
		 *
		 * @since     1.0.0
		 * @return    void
		 */
		protected function add_crumbs_tax() {
			$this_term = $GLOBALS['wp_query']->get_queried_object();
			$taxonomy  = get_taxonomy( $this_term->taxonomy );

			$this->add_crumb( $taxonomy->labels->name );

			if ( 0 !== intval( $this_term->parent ) ) {
				$this->term_ancestors( $this_term->term_id, $this_term->taxonomy );
			}

			$this->add_crumb( single_term_title( '', false ), get_term_link( $this_term->term_id, $this_term->taxonomy ) );
		}

		/**
		 * Add a breadcrumb for author archives.
		 *
		 * @since     1.0.0
		 * @return    void
		 */
		protected function add_crumbs_author() {
			global $author;

			$userdata = get_userdata( $author );

			/* translators: %s: author name */
			$this->add_crumb( sprintf( _x( 'Author: %s', 'breadcrumb', 'sixa-snippets' ), $userdata->display_name ) );
		}

		/**
		 * Add crumbs for a term.
		 *
		 * @since     1.0.0
		 * @param     int       $term_id     Term ID.
		 * @param     string    $taxonomy    Taxonomy.
		 * @return    void
		 */
		protected function term_ancestors( $term_id, $taxonomy ) {
			$ancestors = get_ancestors( $term_id, $taxonomy );
			$ancestors = array_reverse( $ancestors );

			foreach ( $ancestors as $ancestor ) {
				$ancestor = get_term( $ancestor, $taxonomy );

				if ( ! is_wp_error( $ancestor ) && $ancestor ) {
					$this->add_crumb( $ancestor->name, get_term_link( $ancestor ) );
				}
			}
		}

		/**
		 * WooCommerce my-account endpoints.
		 *
		 * @since     1.0.0
		 * @return    void
		 */
		protected function endpoint_trail() {
			if ( function_exists( 'is_wc_endpoint_url' ) && ! ! is_wc_endpoint_url() ) {
				$endpoint_title = WC()->query->get_endpoint_title( WC()->query->get_current_endpoint(), filter_input( INPUT_GET, 'action', FILTER_SANITIZE_STRING ) );

				if ( $endpoint_title ) {
					$this->add_crumb( $endpoint_title );
				}
			}
		}

		/**
		 * Add a breadcrumb for search results.
		 *
		 * @since     1.0.0
		 * @return    void
		 */
		protected function search_trail() {
			if ( is_search() ) {
				/* translators: %s: search term */
				$this->add_crumb( sprintf( _x( 'Search results for &ldquo;%s&rdquo;', 'breadcrumb', 'sixa-snippets' ), get_search_query() ), remove_query_arg( 'paged' ) );
			}
		}

		/**
		 * Add a breadcrumb for pagination.
		 *
		 * @since     1.0.0
		 * @return    void
		 */
		protected function paged_trail() {
			if ( get_query_var( 'paged' ) ) {
				// See what is going to display in the loop.
				if ( function_exists( 'woocommerce_get_loop_display_mode' ) && 'subcategories' === woocommerce_get_loop_display_mode() ) {
					return;
				}

				/* translators: %d: page number */
				$this->add_crumb( sprintf( _x( 'Page %d', 'breadcrumb', 'sixa-snippets' ), get_query_var( 'paged' ) ) );
			}
		}

		/**
		 * Breadcrumb markup arguments.
		 *
		 * @since     1.0.0
		 * @return    array
		 */
		protected function crumb_args() {
			$args = apply_filters(
				'sixa_breadcrumb_args',
				array(
					'delimiter'   => '&nbsp;&#47;&nbsp;',
					'wrap_before' => '<ol class="sixa-breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList">',
					'wrap_after'  => '</ol>',
					'before'      => '<li itemscope itemprop="itemListElement" itemtype="https://schema.org/ListItem">',
					'after'       => '</li>',
					'home'        => _x( 'Home', 'breadcrumb', 'sixa-snippets' ),
				)
			);

			return $args;
		}

		/**
		 * Run this class and output generated crumbs on the page.
		 *
		 * @since     1.0.0
		 * @param     boolean    $echo    Optional. Echo the output or return it.
		 * @return    mixed
		 */
		public function run( $echo = true ) {
			$return = '';
			$args   = $this->crumb_args();

			if ( ! empty( $args['home'] ) ) {
				$this->add_crumb( $args['home'], apply_filters( 'sixa_breadcrumb_home_url', home_url() ) );
			}

			$breadcrumbs = $this->generate();

			if ( ! empty( $breadcrumbs ) ) {
				$return .= $args['wrap_before'];

				foreach ( $breadcrumbs as $key => $crumb ) {
					$position = $key + 1;
					$return  .= $args['before'];

					if ( ! empty( $crumb[1] ) && count( $breadcrumbs ) !== $position ) {
						$return .= sprintf( '<a href="%1$s" itemprop="item"><span itemprop="name">%2$s</span></a>', esc_url( $crumb[1] ), esc_html( $crumb[0] ) );
					} else {
						$return .= sprintf( '<span itemprop="name">%s</span>', esc_html( $crumb[0] ) );
					}

					$return .= sprintf( '<meta itemprop="position" content="%d" />', absint( $position ) );
					$return .= $args['after'];

					if ( count( $breadcrumbs ) !== $position ) {
						$return .= $args['delimiter'];
					}
				}

				$return .= $args['wrap_after'];
			}

			if ( ! $echo ) {
				return $return;
			}

			echo $return; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

	}
endif;
