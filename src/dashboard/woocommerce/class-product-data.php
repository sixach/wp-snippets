<?php
/**
 * The file registers custom product specific meta-data controls.
 *
 * @link          https://sixa.ch
 * @author        Mahdi Yazdani
 * @since         1.0.0
 *
 * @package       sixa-snippets
 * @subpackage    sixa-snippets/dashboard
 * @phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
 */

namespace Sixa_Snippets\Dashboard\WooCommerce;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Product_Data' ) ) :

	/**
	 * The file that adds additional controls to the `product-data` table.
	 */
	class Product_Data {

		/**
		 * Meta-data controls to register.
		 *
		 * @since     1.0.0
		 * @access    private
		 * @var       array      $controls    List of controls to register as part of the product-data table.
		 */
		private static $controls = array();

		/**
		 * Name of the meta-data.
		 *
		 * @since     1.0.0
		 * @access    public
		 * @var       string    $key    Name of the meta-data to retrieve.
		 */
		public static $key = 'sixa_product_data';

		/**
		 * Initialize the class and set its properties.
		 *
		 * @since    1.0.0
		 * @param    array    $args    Product data setting arguments.
		 * @return   void
		 */
		public function __construct( $args = array() ) {
			// Bail early, in case there no option provided to register.
			if ( ! is_array( $args ) || empty( $args ) ) {
				return;
			}

			self::$controls = $args;
			add_action( 'woocommerce_product_data_tabs', array( $this, 'tabs' ) );
			add_action( 'woocommerce_product_data_panels', array( $this, 'panels' ) );
			add_action( 'woocommerce_admin_process_product_object', array( $this, 'save' ) );
		}

		/**
		 * Create/append custom tab(s) to the product data table.
		 *
		 * @since     1.0.0
		 * @param     array    $tabs    Existing product data meta-box tabs.
		 * @return    array
		 */
		public function tabs( $tabs ) {
			foreach ( self::$controls as $key => $args ) {
				$tabs[ $key ] = array(
					'label'    => isset( $args['label'] ) ? $args['label'] : _x( 'Sixa Options', 'product data', '@@textdomain' ),
					'class'    => isset( $args['class'] ) ? $args['class'] : '',
					'target'   => strtolower( trim( $key ) ),
					'priority' => 81,
				);
			}

			return $tabs;
		}

		/**
		 * Renders custom tab(s) content.
		 *
		 * @since     1.0.0
		 * @return    void
		 */
		public function panels() {
			global $post;

			$return  = '';
			$options = (array) get_post_meta( $post->ID, self::$key, true );

			foreach ( self::$controls as $key => $args ) {
				$fields  = isset( $args['fields'] ) ? $args['fields'] : array();
				$return .= sprintf( '<div id="%s" class="panel woocommerce_options_panel hidden"><div class="options_group">', strtolower( trim( $key ) ) );

				if ( ! empty( $fields ) ) {
					foreach ( $fields as $field ) {
						$field['name'] = isset( $field['name'] ) ? $field['name'] : $field['id'];
						$field['type'] = isset( $field['type'] ) ? $field['type'] : 'text';

						$return .= call_user_func(
							array( 'SixaSnippets\Dashboard\Options', sprintf( '%s_field', esc_attr( $field['type'] ) ) ),
							array_merge(
								$field,
								array(
									'value'             => isset( $options[ $field['name'] ] ) ? esc_attr( $options[ $field['name'] ] ) : '',
									'id'                => sprintf( 'product-data-item-%d-%s', intval( $post->ID ), esc_attr( $field['name'] ) ),
									'name'              => sprintf( '%s[%s]', esc_attr( self::$key ), esc_attr( $field['name'] ) ),
								)
							),
							false
						);
					}
				}

				$return .= '</div></div>';
			}

			echo $return; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		/**
		 * Fires after a product has been updated or published.
		 *
		 * @since     1.0.0
		 * @param     WC_Product    $product    Product object.
		 * @return    void
		 */
		public function save( $product ) {
			$product->update_meta_data( self::$key, filter_input( INPUT_POST, self::$key, FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY ) );
		}

	}
endif;
