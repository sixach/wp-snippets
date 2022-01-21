<?php
/**
 * The file registers menu specific custom input controls.
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

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! class_exists( Menu_Options::class ) ) :

	/**
	 * The file that defines the additional menu option’s class.
	 */
	class Menu_Options {

		/**
		 * Name of the option.
		 *
		 * @since     1.0.0
		 * @access    public
		 * @var       string $key    Name of the option to retrieve.
		 */
		public static $key = 'sixa_menu';

		/**
		 * Menu item fields.
		 *
		 * @since     1.0.0
		 * @access    protected
		 * @var       array $fields    Settings fieldset to register.
		 */
		protected static $fields = array();

		/**
		 * Initialize the class and set its properties.
		 *
		 * @since     1.0.0
		 * @param     array $args    Fieldset.
		 * @return    void
		 */
		public function __construct( array $args = array() ) {
			// Bail early, in case there no option provided to register.
			if ( ! is_array( $args ) || empty( $args ) ) {
				return;
			}

			self::$fields = $args;
			add_action( 'wp_nav_menu_item_custom_fields', array( $this, 'register' ) );
			add_action( 'save_post_nav_menu_item', array( $this, 'save' ) );
		}

		/**
		 * Register additional input controls for the menu-items.
		 *
		 * @since     1.0.0
		 * @param     int $item_id    Menu item ID.
		 * @return    void
		 */
		public function register( int $item_id ): void {
			$meta = (array) get_post_meta( $item_id, self::$key, true );

			foreach ( self::$fields as $field ) {
				$field['name'] = $field['name'] ?? $field['id'];
				$field['type'] = $field['type'] ?? 'text';

				call_user_func(
					array( sprintf( '%s\Options', __NAMESPACE__ ), sprintf( '%s_field', esc_attr( $field['type'] ) ) ),
					array_merge(
						$field,
						array(
							'wrapper_class' => sprintf( 'field-%s description description-wide', esc_attr( $field['type'] ) ),
							'value'         => isset( $meta[ $field['name'] ] ) ? esc_attr( $meta[ $field['name'] ] ) : '',
							'id'            => sprintf( 'edit-menu-item-%d-%s', intval( $item_id ), esc_attr( $field['name'] ) ),
							'name'          => sprintf( '%s[%d][%s]', esc_attr( self::$key ), intval( $item_id ), esc_attr( $field['name'] ) ),
						)
					)
				);
			}
		}

		/**
		 * Save menu (post) meta-data.
		 *
		 * @since     1.0.0
		 * @param     int $item_id    Menu item ID.
		 * @return    void
		 */
		public function save( int $item_id ): void {
			$data = filter_input( INPUT_POST, self::$key, FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY );

			if ( isset( $data[ $item_id ] ) ) {
				update_post_meta( $item_id, self::$key, $data[ $item_id ] );
			} else {
				delete_post_meta( $item_id, self::$key );
			}
		}

	}

endif;
