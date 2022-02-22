<?php
/**
 * The file registers a taxonomy object and attaches it to a given post type list.
 *
 * @link          https://sixa.ch
 * @author        sixa AG
 * @since         1.7.0
 *
 * @package       Sixa_Snippets
 * @subpackage    Sixa_Snippets/Dashboard
 * @phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
 */

namespace Sixa_Snippets\Dashboard;

use Sixa_Snippets\Dashboard\Post_Type;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! class_exists( Taxonomy::class ) ) :

	/**
	 * The file that defines the core taxonomy class.
	 */
	class Taxonomy {

		/**
		 * Initialize the class and set its properties.
		 *
		 * @since     1.0.0
		 * @param     array $args    List of post type taxonomies to register.
		 * @return    void
		 */
		public function __construct( array $args = array() ) {
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
		 * @param     array $taxonomies    List of post type taxonomies to register.
		 * @return    void
		 */
		public function run( array $taxonomies ): void {
			foreach ( $taxonomies as $taxonomy ) {
				$key = strtolower( $taxonomy['key'] ?? '' );

				// Return when the key is not populated.
				if ( empty( $key ) ) {
					return;
				}

				$args          = $taxonomy['args'] ?? array();
				$post_type     = $taxonomy['post_type'] ?? 'post';
				$singular_name = $taxonomy['singular_name'] ?? _x( 'Category', 'taxonomy', 'sixa-snippets' );
				$plural_name   = $taxonomy['plural_name'] ?? _x( 'Categories', 'taxonomy', 'sixa-snippets' );
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
						'show_tagcloud'         => false,
						'show_in_rest'          => true,
						'rest_controller_class' => 'WP_REST_Terms_Controller',
						'show_in_quick_edit'    => true,
						'show_in_graphql'       => true,
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
		 * @param     string $singular_name    Singular name for the taxonomy.
		 * @param     string $plural_name      Plural name for the taxonomy.
		 * @return    array
		 */
		protected function get_labels( string $singular_name, string $plural_name ): array {
			$labels = apply_filters(
				'sixa_register_taxonomy_label_args',
				array(
					'name'                       => $plural_name,
					'singular_name'              => $singular_name,
					'menu_name'                  => $plural_name,
					/* translators: %s: General name for the taxonomy type, usually plural. */
					'all_items'                  => sprintf( _x( 'All %s', 'taxonomy', 'sixa-snippets' ), $plural_name ),
					/* translators: %s: Name for one object of this taxonomy type. */
					'edit_item'                  => sprintf( _x( 'Edit %s', 'taxonomy', 'sixa-snippets' ), $singular_name ),
					/* translators: %s: Name for one object of this taxonomy type. */
					'view_item'                  => sprintf( _x( 'View %s', 'taxonomy', 'sixa-snippets' ), $singular_name ),
					/* translators: %s: Name for one object of this taxonomy type. */
					'update_item'                => sprintf( _x( 'Update %s name', 'taxonomy', 'sixa-snippets' ), $singular_name ),
					/* translators: %s: Name for one object of this taxonomy type. */
					'add_new_item'               => sprintf( _x( 'Add new %s', 'taxonomy', 'sixa-snippets' ), $singular_name ),
					/* translators: %s: Name for one object of this taxonomy type. */
					'new_item_name'              => sprintf( _x( 'New %s name', 'taxonomy', 'sixa-snippets' ), $singular_name ),
					/* translators: %s: Name for one object of this taxonomy type. */
					'parent_item'                => sprintf( _x( 'Parent %s', 'taxonomy', 'sixa-snippets' ), $singular_name ),
					/* translators: %s: Name for one object of this taxonomy type. */
					'parent_item_colon'          => sprintf( _x( 'Parent %s:', 'taxonomy', 'sixa-snippets' ), $singular_name ),
					/* translators: %s: General name for the taxonomy type, usually plural. */
					'search_items'               => sprintf( _x( 'Search %s', 'taxonomy', 'sixa-snippets' ), $plural_name ),
					/* translators: %s: General name for the taxonomy type, usually plural. */
					'popular_items'              => sprintf( _x( 'Popular %s', 'taxonomy', 'sixa-snippets' ), $plural_name ),
					/* translators: %s: General name for the taxonomy type, usually plural. */
					'separate_items_with_commas' => sprintf( _x( 'Separate %s with commas', 'taxonomy', 'sixa-snippets' ), $plural_name ),
					/* translators: %s: General name for the taxonomy type, usually plural. */
					'add_or_remove_items'        => sprintf( _x( 'Add or remove %s', 'taxonomy', 'sixa-snippets' ), $plural_name ),
					/* translators: %s: General name for the taxonomy type, usually plural. */
					'choose_from_most_used'      => sprintf( _x( 'Choose from the most used %s', 'taxonomy', 'sixa-snippets' ), $plural_name ),
					/* translators: %s: General name for the taxonomy type, usually plural. */
					'not_found'                  => sprintf( _x( 'No %s found', 'taxonomy', 'sixa-snippets' ), $plural_name ),
					/* translators: %s: General name for the taxonomy type, usually plural. */
					'no_terms'                   => sprintf( _x( 'No %s', 'taxonomy', 'sixa-snippets' ), $plural_name ),
					/* translators: %s: General name for the taxonomy type, usually plural. */
					'items_list_navigation'      => sprintf( _x( '%s list navigation', 'taxonomy', 'sixa-snippets' ), $plural_name ),
					/* translators: %s: General name for the taxonomy type, usually plural. */
					'items_list'                 => sprintf( _x( '%s list', 'taxonomy', 'sixa-snippets' ), $plural_name ),
				)
			);

			return $labels;
		}

		/**
		 * Generate a list of publicly viewable taxonomies based on given post-type name.
		 *
		 * @since     1.7.0
		 * @param     string $post_type    Given post-type name/key.
		 * @return    array
		 */
		public static function list_viewables_by_post_type( string $post_type = 'post' ): array {
			$return = array();

			// Bail early, in case the post-type is not registered.
			if ( ! post_type_exists( $post_type ) || ! is_post_type_viewable( $post_type ) ) {
				return $return;
			}

			$post_type  = get_post_type_object( $post_type );
			$taxonomies = get_taxonomies(
				apply_filters(
					'sixa_list_viewable_post_type_taxonomies_args',
					array(
						'object_type'  => array( (string) $post_type->name ),
						'public'       => true,
						'show_in_rest' => true,
					)
				),
				'objects'
			);
			$return     = apply_filters(
				'sixa_list_viewable_post_type_taxonomies_options',
				array_map(
					function( $taxonomy ) {
						$taxonomy_name      = (string) $taxonomy->name;
						$taxonomy_rest_base = ! empty( $taxonomy->rest_base ) ? (string) $taxonomy->rest_base : $taxonomy_name;
						return array(
							'label' => (string) $taxonomy->labels->singular_name,
							'value' => $taxonomy_name . '|' . $taxonomy_rest_base,
						);
					},
					array_values( $taxonomies )
				)
			);

			return $return;
		}

		/**
		 * Retrieve REST base name/key for a given post-type.
		 *
		 * @since     1.7.0
		 * @param     string $taxonomy    Name of taxonomy object.
		 * @return    string
		 */
		public static function get_rest_base( string $taxonomy = 'category' ): ?string {
			$return = null;

			// Bail early, in case the post-type is not registered.
			if ( ! taxonomy_exists( $taxonomy ) || ! is_taxonomy_viewable( $taxonomy ) ) {
				return $return;
			}

			$taxonomy = get_taxonomy( $taxonomy );
			$return   = isset( $taxonomy->rest_base ) && ! empty( $taxonomy->rest_base ) ? $taxonomy->rest_base : $taxonomy->name;

			return $return;
		}

		/**
		 * Retrieves taxonomies associated with the given/selected post-type to query.
		 *
		 * @since     1.7.0
		 * @param     string $post_type    Post type name and rest-base key.
		 * @param     string $rest_base    Taxonomy REST-base key name.
		 * @return    null|array
		 */
		public static function get_by_post_type_name_tax_rest_base( string $post_type = 'post|posts', string $rest_base ): ?array {
			$post_types = Post_Type::list_viewables();
			$find_index = array_search( $post_type, array_column( $post_types, 'value' ), true );
			$taxonomies = false === $find_index ? null : $post_types[ $find_index ]['taxonomies'];

			if ( ! is_array( $taxonomies ) || empty( $taxonomies ) ) {
				return null;
			}

			return array_reduce(
				$taxonomies,
				function( $accumulator, $tax ) use ( $rest_base ) {
					$taxonomy                = $tax['value'] ?? '';
					$taxonomy_name_rest_base = Post_Type::split_name_from_rest_base( $taxonomy );
					if ( $rest_base === $taxonomy_name_rest_base['rest_base'] ?? '' ) {
						return $taxonomy_name_rest_base;
					}
					return $accumulator;
				}
			);
		}

	}
endif;
