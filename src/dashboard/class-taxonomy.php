<?php
/**
 * The file registers a taxonomy object and attaches it to a given post type list.
 *
 * @link          https://sixa.ch
 * @author        sixa AG
 * @since         1.0.0
 *
 * @package       Sixa_Snippets
 * @subpackage    Sixa_Snippets/Dashboard
 * @phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
 */

namespace Sixa_Snippets\Dashboard;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Taxonomy' ) ) :

	/**
	 * The file that defines the core taxonomy class.
	 */
	class Taxonomy {

		/**
		 * Initialize the class and set its properties.
		 *
		 * @since     1.0.0
		 * @param     array    $args    List of post type taxonomies to register.
		 * @return    void
		 */
		public function __construct( $args = array() ) {
			// Bail early, if there is no argument passed to register.
			if ( empty( $args ) ) {
				return;
			}

			$this->run( $args );
		}

		/**
		 * Creates a taxonomy object.
		 *
		 * @since     1.0.0
		 * @param     array    $taxonomies    List of post type taxonomies to register.
		 * @return    void
		 */
		public function run( $taxonomies ) {
			foreach ( $taxonomies as $taxonomy ) {
				$key = isset( $taxonomy['key'] ) ? strtolower( $taxonomy['key'] ) : '';

				// Return when the key is not populated.
				if ( empty( $key ) ) {
					return;
				}

				$args          = isset( $taxonomy['args'] ) ? $taxonomy['args'] : array();
				$post_type     = isset( $taxonomy['post_type'] ) ? $taxonomy['post_type'] : 'post';
				$singular_name = isset( $taxonomy['singular_name'] ) ? $taxonomy['singular_name'] : _x( 'Category', 'taxonomy', '@@textdomain' );
				$plural_name   = isset( $taxonomy['plural_name'] ) ? $taxonomy['plural_name'] : _x( 'Categories', 'taxonomy', '@@textdomain' );
				$defaults      = apply_filters(
					'sixa_register_taxonomy_args',
					array(
						'label'                 => $plural_name,
						'labels'                => $this->get_labels( $singular_name, $plural_name ),
						'public'                => true,
						'publicly_queryable'    => true,
						'hierarchical'          => true,
						'show_ui'               => true,
						'show_in_menu'          => true,
						'show_in_nav_menus'     => true,
						'query_var'             => true,
						'rewrite'               => false,
						'show_admin_column'     => true,
						'show_in_rest'          => true,
						'rest_controller_class' => 'WP_REST_Terms_Controller',
						'show_in_quick_edit'    => true,
					)
				);

				// Merge user defined arguments into defaults array.
				$args = wp_parse_args( $args, $defaults );
				register_taxonomy( $key, $post_type, $args );
			}
		}

		/**
		 * An array of labels for registering taxonomy.
		 *
		 * @since     1.0.0
		 * @param     string    $singular_name    Singular name for the taxonomy.
		 * @param     string    $plural_name      Plural name for the taxonomy.
		 * @return    array
		 */
		protected function get_labels( $singular_name, $plural_name ) {
			$labels = apply_filters(
				'sixa_register_taxonomy_label_args',
				array(
					'name'                       => $plural_name,
					'singular_name'              => $singular_name,
					'menu_name'                  => $plural_name,
					/* translators: %s: General name for the taxonomy type, usually plural. */
					'all_items'                  => sprintf( _x( 'All %s', 'taxonomy', '@@textdomain' ), $plural_name ),
					/* translators: %s: Name for one object of this taxonomy type. */
					'edit_item'                  => sprintf( _x( 'Edit %s', 'taxonomy', '@@textdomain' ), $singular_name ),
					/* translators: %s: Name for one object of this taxonomy type. */
					'view_item'                  => sprintf( _x( 'View %s', 'taxonomy', '@@textdomain' ), $singular_name ),
					/* translators: %s: Name for one object of this taxonomy type. */
					'update_item'                => sprintf( _x( 'Update %s name', 'taxonomy', '@@textdomain' ), $singular_name ),
					/* translators: %s: Name for one object of this taxonomy type. */
					'add_new_item'               => sprintf( _x( 'Add new %s', 'taxonomy', '@@textdomain' ), $singular_name ),
					/* translators: %s: Name for one object of this taxonomy type. */
					'new_item_name'              => sprintf( _x( 'New %s name', 'taxonomy', '@@textdomain' ), $singular_name ),
					/* translators: %s: Name for one object of this taxonomy type. */
					'parent_item'                => sprintf( _x( 'Parent %s', 'taxonomy', '@@textdomain' ), $singular_name ),
					/* translators: %s: Name for one object of this taxonomy type. */
					'parent_item_colon'          => sprintf( _x( 'Parent %s:', 'taxonomy', '@@textdomain' ), $singular_name ),
					/* translators: %s: General name for the taxonomy type, usually plural. */
					'search_items'               => sprintf( _x( 'Search %s', 'taxonomy', '@@textdomain' ), $plural_name ),
					/* translators: %s: General name for the taxonomy type, usually plural. */
					'popular_items'              => sprintf( _x( 'Popular %s', 'taxonomy', '@@textdomain' ), $plural_name ),
					/* translators: %s: General name for the taxonomy type, usually plural. */
					'separate_items_with_commas' => sprintf( _x( 'Separate %s with commas', 'taxonomy', '@@textdomain' ), $plural_name ),
					/* translators: %s: General name for the taxonomy type, usually plural. */
					'add_or_remove_items'        => sprintf( _x( 'Add or remove %s', 'taxonomy', '@@textdomain' ), $plural_name ),
					/* translators: %s: General name for the taxonomy type, usually plural. */
					'choose_from_most_used'      => sprintf( _x( 'Choose from the most used %s', 'taxonomy', '@@textdomain' ), $plural_name ),
					/* translators: %s: General name for the taxonomy type, usually plural. */
					'not_found'                  => sprintf( _x( 'No %s found', 'taxonomy', '@@textdomain' ), $plural_name ),
					/* translators: %s: General name for the taxonomy type, usually plural. */
					'no_terms'                   => sprintf( _x( 'No %s', 'taxonomy', '@@textdomain' ), $plural_name ),
					/* translators: %s: General name for the taxonomy type, usually plural. */
					'items_list_navigation'      => sprintf( _x( '%s list navigation', 'taxonomy', '@@textdomain' ), $plural_name ),
					/* translators: %s: General name for the taxonomy type, usually plural. */
					'items_list'                 => sprintf( _x( '%s list', 'taxonomy', '@@textdomain' ), $plural_name ),
				)
			);

			return $labels;
		}

	}
endif;
