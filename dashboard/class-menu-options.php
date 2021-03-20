<?php
/**
 * The file registers menu specific custom input controls.
 *
 * @link       https://sixa.ch
 * @author     Mahdi Yazdani
 * @since      1.0.0
 *
 * @package    sixa-snippets
 * @subpackage sixa-snippets/dashboard
 */

namespace SixaSnippets\Dashboard;

/**
 * INSTRUCTIONS:
 *
 * 1. Update the namespace(s) used in this file.
 * 2. Search and replace text-domains `@@textdomain`.
 * 3. Initialize the class to register additional menu-item input fields when needed:
 *
 * add_action(
 *  'admin_init',
 *  function() {
 *      new Menu_Options(
 *          array(
 *              array(
 *                  'default'     => 'no',
 *                  'type'        => 'checkbox',                                        // Required: Type of the field.
 *                  'name'        => 'checkbox-choice',                                 // Required: Attribute used to reference the element.
 *                  'label'       => __( 'Do a thing?', '@@textdomain' ),
 *                  'description' => __( 'Enable to do something', '@@textdomain' ),
 *              ),
 *              array(
 *                  'show_option_none' => true,
 *                  'type'             => 'select',
 *                  'name'             => 'select-choice',
 *                  'label'            => __( 'Select an option?', '@@textdomain' ),
 *                  'options'          => array(
 *                      'option1' => __( 'Option 1', '@@textdomain' ),
 *                      'option2' => __( 'Option 2', '@@textdomain' ),
 *                      'option3' => __( 'Option 3', '@@textdomain' ),
 *                  ),
 *              ),
 *          )
 *      );
 *  }
 * );
 *
 * Note: Do not initialize this class before the `admin_menu` hook.
 * Note 2: This file requires the `Options` class to be imported and present in the project.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Menu_Options' ) ) :

	/**
	 * The file that defines the additional menu option’s class.
	 */
	class Menu_Options {

		/**
		 * Name of the option.
		 *
		 * @since    1.0.0
		 * @access   public
		 * @var      string $key     Name of the option to retrieve.
		 */
		public static $key = 'sixa_menu';

		/**
		 * Menu item fields.
		 *
		 * @since    1.0.0
		 * @access   protected
		 * @var      array $fields     Settings fieldset to register.
		 */
		protected static $fields = array();

		/**
		 * Initialize the class and set its properties.
		 *
		 * @since    1.0.0
		 * @param    array $fieldset     Fieldset.
		 * @return   void
		 */
		public function __construct( $fieldset = array() ) {
			// Bail early, in case there no option provided to register.
			if ( ! is_array( $fieldset ) || empty( $fieldset ) ) {
				return;
			}

			self::$fields = $fieldset;
			add_action( 'wp_nav_menu_item_custom_fields', array( $this, 'register' ) );
			add_action( 'save_post_nav_menu_item', array( $this, 'save' ) );
		}

		/**
		 * Register additional input controls for the menu-items.
		 *
		 * @since    1.0.0
		 * @param    int $item_id     Menu item ID.
		 * @return   void
		 */
		public function register( $item_id ) {
			$meta = (array) get_post_meta( $item_id, self::$key, true );

			foreach ( self::$fields as $field ) {
				$field['name'] = isset( $field['name'] ) ? $field['name'] : $field['id'];
				$field['type'] = isset( $field['type'] ) ? $field['type'] : 'text';

				call_user_func(
					array( sprintf( '%s\Options', __NAMESPACE__ ), sprintf( '%s_field', esc_attr( $field['type'] ) ) ),
					array_merge(
						$field,
						array(
							'wrapper_class'     => sprintf( 'field-%s description description-wide', esc_attr( $field['type'] ) ),
							'value'             => isset( $meta[ $field['name'] ] ) ? esc_attr( $meta[ $field['name'] ] ) : '',
							'id'                => sprintf( 'edit-menu-item-%d-%s', intval( $item_id ), esc_attr( $field['name'] ) ),
							'name'              => sprintf( '%s[%d][%s]', esc_attr( self::$key ), intval( $item_id ), esc_attr( $field['name'] ) ),
						)
					)
				);
			}
		}

		/**
		 * Save menu (post) meta-data.
		 *
		 * @since    1.0.0
		 * @param    int $item_id     Menu item ID.
		 * @return   void
		 */
		public function save( $item_id ) {
			$data = filter_input( INPUT_POST, self::$key, FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY );

			if ( isset( $data[ $item_id ] ) ) {
				update_post_meta( $item_id, self::$key, $data[ $item_id ] );
			}
		}

	}

endif;
