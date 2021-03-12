<?php
/**
 * The file registers custom post types.
 *
 * @link       https://sixa.ch
 * @author     Mahdi Yazdani
 * @since      1.0.0
 *
 * @package    sixa-snippets
 * @subpackage sixa-snippets/includes
 */

namespace SixaSnippets\Includes;

/**
 * INSTRUCTIONS:
 *
 * 1. Update the namespace(s) used in this file.
 * 2. Search and replace text-domains `@@textdomain`.
 * 3. Initialize the class to register a series of post-types when needed:
 *
 * new Post_Type(
 *      array(
 *          array(
 *              'key'           => 'docs',              // Required. Post type key. Must not exceed 20 characters.
 *              'plural_name'   => 'Documents',         // Optional. Plural name for the post-type.
 *              'singular_name' => 'Document',          // Optional. Singular name for the post-type.
 *              'args'          => array(               // Optional. Array of arguments for registering a post type.
 *                  'publicly_queryable' => false,
 *                  'menu_icon'          => 'dashicons-book',
 *              ),
 *              'taxonomies'    => array(               // Note: This would require including `Taxonomy` class in the project as well.
 *                  array(
 *                      'key' => 'cat',                 // Required. Taxonomy key, must not exceed 32 characters.
 *                  ),
 *                  array(
 *                      'key'           => 'type',      // Required. Taxonomy key, must not exceed 32 characters.
 *                      'plural_name'   => 'Types',     // Optional. Plural name for the taxonomy.
 *                      'singular_name' => 'Type',      // Optional. Singular name for the taxonomy.
 *                  ),
 *              ),
 *          ),
 *          array(
 *              'key'           => 'logs',              // Required. Post type key. Must not exceed 20 characters.
 *              'plural_name'   => 'Logs',              // Optional. Plural name for the post-type.
 *              'singular_name' => 'Log',               // Optional. Singular name for the post-type.
 *          ),
 *      )
 *  );
 *
 * Note: Do not initialize this class before the `init` hook.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Post_Type' ) ) :

	/**
	 * The file that defines the core post-type class.
	 */
	class Post_Type {

		/**
		 * Taxonomy register class.
		 *
		 * @access   protected
		 * @var      boolean    $can_tax    Whether the taxonomy factory class is present.
		 */
		protected $can_tax = false;

		/**
		 * Initialize the class and set its properties.
		 *
		 * @since    1.0.0
		 * @param    array $post_types     List of post type post-types to register.
		 * @return   void
		 */
		public function __construct( $post_types = array() ) {
			// Bail early, if there is no argument passed to register.
			if ( empty( $post_types ) ) {
				return;
			}

			$this->can_tax = ! ! class_exists( 'SixaSnippets\Includes\Taxonomy' );
			$this->run( $post_types );
		}

		/**
		 * Creates a post-types object.
		 *
		 * @since    1.0.0
		 * @param    array $post_types     List of post type post-types to register.
		 * @return   void
		 */
		public function run( $post_types ) {
			foreach ( $post_types as $post_type ) {
				$key = isset( $post_type['key'] ) ? strtolower( $post_type['key'] ) : '';

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

				$args          = isset( $post_type['args'] ) ? $post_type['args'] : array();
				$singular_name = isset( $post_type['singular_name'] ) ? $post_type['singular_name'] : _x( 'Post', 'post type', '@@textdomain' );
				$plural_name   = isset( $post_type['plural_name'] ) ? $post_type['plural_name'] : _x( 'Posts', 'post type', '@@textdomain' );
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

				if ( $this->can_tax ) {
					new Taxonomy( $this->attach_taxonomy( $key, $taxonomies ) );
				}
			}
		}

		/**
		 * Attaches post-type to the taxonomy list.
		 *
		 * @since    1.0.0
		 * @param    array $post_type     Post type name.
		 * @param    array $taxonomies    List of post type taxonomies.
		 * @return   array
		 */
		protected function attach_taxonomy( $post_type, $taxonomies ) {
			$taxonomies_count = count( $taxonomies );
			for ( $i = 0; $i < $taxonomies_count; ++$i ) {
				$taxonomies[ $i ]['post_type'] = $post_type;
			}

			return $taxonomies;
		}

		/**
		 * An array of labels for registering taxonomy.
		 *
		 * @since    1.0.0
		 * @param    string $singular_name     Singular name for the taxonomy.
		 * @param    string $plural_name       Plural name for the taxonomy.
		 * @return   array
		 */
		protected function get_labels( $singular_name, $plural_name ) {
			$labels = apply_filters(
				'sixa_register_post_type_label_args',
				array(
					'name'                     => $plural_name,
					'singular_name'            => $singular_name,
					'menu_name'                => $plural_name,
					'name_admin_bar'           => $singular_name,
					'add_new'                  => _x( 'Add new', 'post type', '@@textdomain' ),
					/* translators: %1$s: General name for the post type, usually plural. */
					'all_items'                => sprintf( _x( 'All %1$s', 'post type', '@@textdomain' ), $plural_name ),
					/* translators: %1$s: Name for one object of this post type. */
					'add_new_item'             => sprintf( _x( 'Add new %1$s', 'post type', '@@textdomain' ), $singular_name ),
					/* translators: %1$s: Name for one object of this post type. */
					'edit_item'                => sprintf( _x( 'Edit %1$s', 'post type', '@@textdomain' ), $singular_name ),
					/* translators: %1$s: Name for one object of this post type. */
					'new_item'                 => sprintf( _x( 'New %1$s', 'post type', '@@textdomain' ), $singular_name ),
					/* translators: %1$s: Name for one object of this post type. */
					'view_item'                => sprintf( _x( 'View %1$s', 'post type', '@@textdomain' ), $singular_name ),
					/* translators: %1$s: General name for the post type, usually plural. */
					'view_items'               => sprintf( _x( 'View %1$s', 'post type', '@@textdomain' ), $plural_name ),
					/* translators: %1$s: General name for the post type, usually plural. */
					'search_items'             => sprintf( _x( 'Search %1$s', 'post type', '@@textdomain' ), $plural_name ),
					/* translators: %1$s: General name for the post type, usually plural. */
					'not_found'                => sprintf( _x( 'No %1$s found', 'post type', '@@textdomain' ), $plural_name ),
					/* translators: %1$s: General name for the post type, usually plural. */
					'not_found_in_trash'       => sprintf( _x( 'No %1$s found in trash', 'post type', '@@textdomain' ), $plural_name ),
					/* translators: %1$s: Name for one object of this post type. */
					'parent'                   => sprintf( _x( 'Parent %1$s:', 'post type', '@@textdomain' ), $singular_name ),
					/* translators: %1$s: Name for one object of this post type. */
					'featured_image'           => sprintf( _x( 'Featured image for this %1$s', 'post type', '@@textdomain' ), $singular_name ),
					/* translators: %1$s: Name for one object of this post type. */
					'set_featured_image'       => sprintf( _x( 'Set featured image for this %1$s', 'post type', '@@textdomain' ), $singular_name ),
					/* translators: %1$s: Name for one object of this post type. */
					'remove_featured_image'    => sprintf( _x( 'Remove featured image for this %1$s', 'post type', '@@textdomain' ), $singular_name ),
					/* translators: %1$s: Name for one object of this post type. */
					'use_featured_image'       => sprintf( _x( 'Use as featured image for this %1$s', 'post type', '@@textdomain' ), $singular_name ),
					/* translators: %1$s: Name for one object of this post type. */
					'archives'                 => sprintf( _x( '%1$s archives', 'post type', '@@textdomain' ), $singular_name ),
					/* translators: %1$s: Name for one object of this post type. */
					'insert_into_item'         => sprintf( _x( 'Insert into %1$s', 'post type', '@@textdomain' ), $singular_name ),
					/* translators: %1$s: Name for one object of this post type. */
					'uploaded_to_this_item'    => sprintf( _x( 'Upload to this %1$s', 'post type', '@@textdomain' ), $singular_name ),
					/* translators: %1$s: General name for the post type, usually plural. */
					'filter_items_list'        => sprintf( _x( 'Filter %1$s list', 'post type', '@@textdomain' ), $plural_name ),
					/* translators: %1$s: General name for the post type, usually plural. */
					'items_list_navigation'    => sprintf( _x( '%1$s list navigation', 'post type', '@@textdomain' ), $plural_name ),
					/* translators: %1$s: General name for the post type, usually plural. */
					'items_list'               => sprintf( _x( '%1$s list', 'post type', '@@textdomain' ), $plural_name ),
					/* translators: %1$s: General name for the post type, usually plural. */
					'attributes'               => sprintf( _x( '%1$s attributes', 'post type', '@@textdomain' ), $plural_name ),
					/* translators: %1$s: Name for one object of this post type. */
					'item_published'           => sprintf( _x( '%1$s published', 'post type', '@@textdomain' ), $singular_name ),
					/* translators: %1$s: Name for one object of this post type. */
					'item_published_privately' => sprintf( _x( '%1$s published privately.', 'post type', '@@textdomain' ), $singular_name ),
					/* translators: %1$s: Name for one object of this post type. */
					'item_reverted_to_draft'   => sprintf( _x( '%1$s reverted to draft.', 'post type', '@@textdomain' ), $singular_name ),
					/* translators: %1$s: Name for one object of this post type. */
					'item_scheduled'           => sprintf( _x( '%1$s scheduled', 'post type', '@@textdomain' ), $singular_name ),
					/* translators: %1$s: Name for one object of this post type. */
					'item_updated'             => sprintf( _x( '%1$s updated.', 'post type', '@@textdomain' ), $singular_name ),
					/* translators: %1$s: Name for one object of this post type. */
					'parent_item_colon'        => sprintf( _x( 'Parent %1$s:', 'post type', '@@textdomain' ), $singular_name ),
				)
			);

			return $labels;
		}
	}
endif;
