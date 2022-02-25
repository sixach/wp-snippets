<?php
/**
 * The file registers custom post types.
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

use Sixa_Snippets\Dashboard\Taxonomy;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! class_exists( Post_Type::class ) ) :

	/**
	 * The file that defines the core post-type class.
	 */
	class Post_Type {

		/**
		 * Initialize the class and set its properties.
		 *
		 * @since     1.0.0
		 * @param     array $args    List of post type post-types to register.
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
		 * Creates a post-types object.
		 *
		 * @since     1.0.0
		 * @param     array $post_types    List of post type post-types to register.
		 * @return    void
		 */
		public function run( array $post_types ): void {
			foreach ( $post_types as $post_type ) {
				$key = strtolower( $post_type['key'] ?? '' );

				// Return when the key is not populated or post type is already registered.
				if ( empty( $key ) || post_type_exists( $key ) ) {
					return;
				}

				if ( isset( $post_type['taxonomies'] ) ) {
					$taxonomies = $post_type['taxonomies'];
					unset( $post_type['taxonomies'] );
				} else {
					$taxonomies = array();
				}

				$args          = $post_type['args'] ?? array();
				$singular_name = $post_type['singular_name'] ?? _x( 'Post', 'post type', 'sixa-snippets' );
				$plural_name   = $post_type['plural_name'] ?? _x( 'Posts', 'post type', 'sixa-snippets' );
				$defaults      = apply_filters(
					'sixa_register_post_type_args',
					array(
						'label'                 => $plural_name,
						'labels'                => $this->get_labels( $singular_name, $plural_name ),
						'public'                => true,
						'publicly_queryable'    => true,
						'show_ui'               => true,
						'show_in_rest'          => true,
						'has_archive'           => false,
						'show_in_menu'          => true,
						'show_in_nav_menus'     => true,
						'delete_with_user'      => false,
						'exclude_from_search'   => true,
						'map_meta_cap'          => true,
						'hierarchical'          => false,
						'rewrite'               => true,
						'query_var'             => true,
						'can_export'            => true,
						'show_in_graphql'       => true,
						'menu_position'         => 50,
						'taxonomies'            => wp_list_pluck( $taxonomies, 'key' ),
						'capability_type'       => 'post',
						'menu_icon'             => 'dashicons-admin-generic',
						'rest_controller_class' => 'WP_REST_Posts_Controller',
						'supports'              => array( 'title', 'editor', 'excerpt', 'custom-fields', 'author', 'revisions', 'page-attributes' ),
					)
				);

				// Merge user defined arguments into defaults array.
				$args = wp_parse_args( $args, $defaults );
				register_post_type( $key, $args );

				if ( ! empty( $taxonomies ) ) {
					new Taxonomy( $this->attach_taxonomy( $key, $taxonomies ) );
				}
			}
		}

		/**
		 * Attaches post-type to the taxonomy list.
		 *
		 * @since     1.0.0
		 * @param     string $post_type     Post type name.
		 * @param     array  $taxonomies    List of post type taxonomies.
		 * @return    array
		 */
		protected function attach_taxonomy( string $post_type, array $taxonomies ): array {
			$taxonomies_count = count( $taxonomies );
			for ( $i = 0; $i < $taxonomies_count; ++$i ) {
				$taxonomies[ $i ]['post_type'] = $post_type;
			}

			return $taxonomies;
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
				'sixa_register_post_type_label_args',
				array(
					'name'                     => $plural_name,
					'singular_name'            => $singular_name,
					'menu_name'                => $plural_name,
					'name_admin_bar'           => $singular_name,
					'add_new'                  => _x( 'Add new', 'post type', 'sixa-snippets' ),
					/* translators: %1$s: General name for the post type, usually plural. */
					'all_items'                => sprintf( _x( 'All %1$s', 'post type', 'sixa-snippets' ), $plural_name ),
					/* translators: %1$s: Name for one object of this post type. */
					'add_new_item'             => sprintf( _x( 'Add new %1$s', 'post type', 'sixa-snippets' ), $singular_name ),
					/* translators: %1$s: Name for one object of this post type. */
					'edit_item'                => sprintf( _x( 'Edit %1$s', 'post type', 'sixa-snippets' ), $singular_name ),
					/* translators: %1$s: Name for one object of this post type. */
					'new_item'                 => sprintf( _x( 'New %1$s', 'post type', 'sixa-snippets' ), $singular_name ),
					/* translators: %1$s: Name for one object of this post type. */
					'view_item'                => sprintf( _x( 'View %1$s', 'post type', 'sixa-snippets' ), $singular_name ),
					/* translators: %1$s: General name for the post type, usually plural. */
					'view_items'               => sprintf( _x( 'View %1$s', 'post type', 'sixa-snippets' ), $plural_name ),
					/* translators: %1$s: General name for the post type, usually plural. */
					'search_items'             => sprintf( _x( 'Search %1$s', 'post type', 'sixa-snippets' ), $plural_name ),
					/* translators: %1$s: General name for the post type, usually plural. */
					'not_found'                => sprintf( _x( 'No %1$s found', 'post type', 'sixa-snippets' ), $plural_name ),
					/* translators: %1$s: General name for the post type, usually plural. */
					'not_found_in_trash'       => sprintf( _x( 'No %1$s found in trash', 'post type', 'sixa-snippets' ), $plural_name ),
					/* translators: %1$s: Name for one object of this post type. */
					'parent'                   => sprintf( _x( 'Parent %1$s:', 'post type', 'sixa-snippets' ), $singular_name ),
					/* translators: %1$s: Name for one object of this post type. */
					'featured_image'           => sprintf( _x( 'Featured image for this %1$s', 'post type', 'sixa-snippets' ), $singular_name ),
					/* translators: %1$s: Name for one object of this post type. */
					'set_featured_image'       => sprintf( _x( 'Set featured image for this %1$s', 'post type', 'sixa-snippets' ), $singular_name ),
					/* translators: %1$s: Name for one object of this post type. */
					'remove_featured_image'    => sprintf( _x( 'Remove featured image for this %1$s', 'post type', 'sixa-snippets' ), $singular_name ),
					/* translators: %1$s: Name for one object of this post type. */
					'use_featured_image'       => sprintf( _x( 'Use as featured image for this %1$s', 'post type', 'sixa-snippets' ), $singular_name ),
					/* translators: %1$s: Name for one object of this post type. */
					'archives'                 => sprintf( _x( '%1$s archives', 'post type', 'sixa-snippets' ), $singular_name ),
					/* translators: %1$s: Name for one object of this post type. */
					'insert_into_item'         => sprintf( _x( 'Insert into %1$s', 'post type', 'sixa-snippets' ), $singular_name ),
					/* translators: %1$s: Name for one object of this post type. */
					'uploaded_to_this_item'    => sprintf( _x( 'Upload to this %1$s', 'post type', 'sixa-snippets' ), $singular_name ),
					/* translators: %1$s: General name for the post type, usually plural. */
					'filter_items_list'        => sprintf( _x( 'Filter %1$s list', 'post type', 'sixa-snippets' ), $plural_name ),
					/* translators: %1$s: General name for the post type, usually plural. */
					'items_list_navigation'    => sprintf( _x( '%1$s list navigation', 'post type', 'sixa-snippets' ), $plural_name ),
					/* translators: %1$s: General name for the post type, usually plural. */
					'items_list'               => sprintf( _x( '%1$s list', 'post type', 'sixa-snippets' ), $plural_name ),
					/* translators: %1$s: General name for the post type, usually plural. */
					'attributes'               => sprintf( _x( '%1$s attributes', 'post type', 'sixa-snippets' ), $plural_name ),
					/* translators: %1$s: Name for one object of this post type. */
					'item_published'           => sprintf( _x( '%1$s published', 'post type', 'sixa-snippets' ), $singular_name ),
					/* translators: %1$s: Name for one object of this post type. */
					'item_published_privately' => sprintf( _x( '%1$s published privately.', 'post type', 'sixa-snippets' ), $singular_name ),
					/* translators: %1$s: Name for one object of this post type. */
					'item_reverted_to_draft'   => sprintf( _x( '%1$s reverted to draft.', 'post type', 'sixa-snippets' ), $singular_name ),
					/* translators: %1$s: Name for one object of this post type. */
					'item_scheduled'           => sprintf( _x( '%1$s scheduled', 'post type', 'sixa-snippets' ), $singular_name ),
					/* translators: %1$s: Name for one object of this post type. */
					'item_updated'             => sprintf( _x( '%1$s updated.', 'post type', 'sixa-snippets' ), $singular_name ),
					/* translators: %1$s: Name for one object of this post type. */
					'parent_item_colon'        => sprintf( _x( 'Parent %1$s:', 'post type', 'sixa-snippets' ), $singular_name ),
				)
			);

			return $labels;
		}

		/**
		 * Retrieves a string of CSS class names for the post container element.
		 *
		 * @since     1.7.0
		 * @param     object $post       The post object.
		 * @param     bool   $echo       Optional. Echo the string.
		 * @param     array  $classes    Optional. An array of class names to add to the class list.
		 * @return    string
		 */
		public static function post_class_names( object $post, bool $echo = true, $classes = array() ) {
			$return = implode( ' ', array_values( get_post_class( $classes, $post ) ) );

			if ( $echo ) {
				echo $return; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

			return $return;
		}

		/**
		 * Generate a list of publicly viewable registered post-types.
		 *
		 * @since     1.7.0
		 * @param     bool $attach_taxonomies    Whether to include list of viewable taxonomies as well.
		 * @return    array
		 */
		public static function list_viewables( $attach_taxonomies = true ): array {
			$return     = array();
			$post_types =
				get_post_types(
					apply_filters(
						'sixa_list_viewable_post_types_args',
						array(
							'public'       => true,
							'show_in_rest' => true,
						)
					),
					'objects'
				);

			if ( is_array( $post_types ) && ! empty( $post_types ) ) {
				foreach ( $post_types as $post_type ) {
					$post_type_name = (string) $post_type->name;
					$rest_base      = ! empty( $post_type->rest_base ) ? (string) $post_type->rest_base : $post_type_name;
					$taxonomies     = array();

					if ( $attach_taxonomies ) {
						$taxonomies = array( 'taxonomies' => Taxonomy::list_viewables_by_post_type( $post_type_name ) );
					}

					$return[] = array_merge(
						array(
							'label'      => (string) $post_type->labels->singular_name,
							'value'      => $post_type_name . '|' . $rest_base,
						),
						$taxonomies
					);

				}
			}

			return $return;
		}

		/**
		 * Generate a list of taxonomy attached viewable post-types (names).
		 *
		 * @since     1.7.0
		 * @param     bool $names_only    Whether to only return post-type names/keys.
		 * @return    null|array
		 */
		public static function list_viewables_with_taxonomy( $names_only = false ): ?array {
			$return = array_filter(
				self::list_viewables(),
				function( $post_type ) {
					return isset( $post_type['taxonomies'] ) && ! empty( $post_type['taxonomies'] );
				}
			);

			if ( $names_only ) {
				$return = array_values(
					array_map(
						function( $post_type ) {
							$post_type_name_and_rest_base = self::split_name_from_rest_base( $post_type['value'] );
							return $post_type_name_and_rest_base['name'];
						},
						$return
					)
				);
			}

			return $return;
		}

		/**
		 * Retrieve REST base name/key for a given post-type.
		 *
		 * @since     1.7.0
		 * @param     string $post_type    Name of post-type object.
		 * @return    string
		 */
		public static function get_rest_base( string $post_type = 'post' ): ?string {
			$return = null;

			// Bail early, in case the post-type is not registered.
			if ( ! post_type_exists( $post_type ) || ! is_post_type_viewable( $post_type ) ) {
				return $return;
			}

			$post_type = get_post_type_object( $post_type );
			$return    = isset( $post_type->rest_base ) && ! empty( $post_type->rest_base ) ? $post_type->rest_base : $post_type->name;

			return $return;
		}

		/**
		 * Separates post-type name from its REST-API base key.
		 *
		 * @since     1.7.0
		 * @param     string $post_type    Post type name and rest-base key.
		 * @return    array
		 */
		public static function split_name_from_rest_base( string $post_type = 'post|posts' ): array {
			$post_type = (array) explode( '|', $post_type );

			return array(
				'name'      => (string) $post_type[0],
				'rest_base' => (string) $post_type[1],
			);
		}

		/**
		 * Joins given object name and rest-base key into a string separated by a pipe separator.
		 *
		 * @since     1.7.0
		 * @param     array      $name_and_rest_base    Post type name and rest-base key.
		 * @return    null|string
		 */
		public static function glue_name_and_rest_base( array $name_and_rest_base ): ?string {
			$return = null;

			if ( ! is_array( $name_and_rest_base ) || ! isset( $name_and_rest_base['name'], $name_and_rest_base['rest_base'] ) ) {
				return $return;
			}

			$return = implode( '|', array_map( 'esc_html', $name_and_rest_base ) );

			return $return;
		}
	}
endif;
