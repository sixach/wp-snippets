<?php
/**
 * The file registers custom product specific meta-data controls.
 *
 * @link       https://sixa.ch
 * @author     Mahdi Yazdani
 * @since      1.0.0
 *
 * @package    sixa-snippets
 * @subpackage sixa-snippets/dashboard
 */

namespace SixaSnippets\Dashboard\WooCommerce;

/**
 * INSTRUCTIONS:
 *
 * 1. Update the namespace(s) used in this file.
 * 2. Search and replace text-domains `@@textdomain`.
 * 3. Initialize the class to register custom product data controls when needed:
 *
 * add_action(
 *  'woocommerce_init',
 *  function() {
 *      add_action(
 *          'add_meta_boxes_product',
 *          function() {
 *              global $post;
 *
 *              $options = (array) get_post_meta( $post->ID, Product_Data::$key, true );
 *              new Product_Data(
 *                  array(
 *                      // New tab.
 *                      'data' => array(
 *                          'label'  => __( 'Custom Panel', '@@textdomain' ),        // Optional: Formatted title of the panel.
 *                          'class'  => 'show_if_simple',                            // Optional: CSS class names, conditional class names such as show_if_simple, hide_if_variable could be passed.
 *                          'fields' => array(                                       // Required: Fieldset.
 *                              // Text.
 *                              Options::text_field(
 *                                  array(
 *                                      'id'    => 'sixa_product_data_text',
 *                                      'label' => __( 'Text field', '@@textdomain' ),
 *                                      'name'  => sprintf( '%s[text-input]', Product_Data::$key ),
 *                                      'value' => isset( $options['text-input'] ) ? $options['text-input'] : '',
 *                                  ),
 *                                  false                                            // Required: Avoid echoing the generated markup.
 *                              ),
 *                              // Checkbox.
 *                              Options::checkbox_field(
 *                                  array(
 *                                      'id'          => 'sixa_product_data_checkbox',
 *                                      'label'       => __( 'Checkbox', '@@textdomain' ),
 *                                      'name'        => sprintf( '%s[checkbox-choice]', Product_Data::$key ),
 *                                      'value'       => isset( $options['checkbox-choice'] ) ? 'yes' : 'no',
 *                                      'description' => __( 'Check me out', '@@textdomain' ),
 *                                  ),
 *                                  false
 *                              ),
 *                          ),
 *                      ),
 *                      // Another tab.
 *                      'extras' => array(
 *                          'fields' => array(
 *                              // Textarea.
 *                              Options::textarea_field(
 *                                  array(
 *                                      'id'    => 'sixa_product_data_textarea',
 *                                      'label' => __( 'Textarea field', '@@textdomain' ),
 *                                      'name'  => sprintf( '%s[textarea-input]', Product_Data::$key ),
 *                                      'value' => isset( $options['textarea-input'] ) ? $options['textarea-input'] : '',
 *                                  ),
 *                                  false
 *                              ),
 *                          ),
 *                      ),
 *                  ),
 *              );
 *          }
 *      );
 *
 *      // Save and sanitize the submitted data.
 *      add_action( 'woocommerce_admin_process_product_object', array( 'SixaSnippets\Dashboard\WooCommerce\Product_Data', 'save' ) );
 *  }
 * );
 *
 * Note: Do not initialize this class before the `woocommerce_init` hook.
 * Note 2: This file requires the `Options` class to be imported and present in the project.
 */

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
		 * @since    1.0.0
		 * @access   private
		 * @var      array $controls     List of controls to register as part of the product-data table.
		 */
		private static $controls = array();

		/**
		 * Name of the meta-data.
		 *
		 * @since    1.0.0
		 * @access   public
		 * @var      string $key     Name of the meta-data to retrieve.
		 */
		public static $key = 'sixa_product_data';

		/**
		 * Initialize the class and set its properties.
		 *
		 * @since    1.0.0
		 * @param    array $list     Product data setting arguments.
		 * @return   void
		 */
		public function __construct( $list = array() ) {
			// Bail early, in case there no option provided to register.
			if ( ! is_array( $list ) || empty( $list ) ) {
				return;
			}

			self::$controls = $list;
			add_action( 'woocommerce_product_data_tabs', array( $this, 'tabs' ) );
			add_action( 'woocommerce_product_data_panels', array( $this, 'panels' ) );
		}

		/**
		 * Create/append custom tab(s) to the product data table.
		 *
		 * @since    1.0.0
		 * @param    array $tabs     Existing product data meta-box tabs.
		 * @return   array
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
		 * @since    1.0.0
		 * @return   void
		 */
		public function panels() {
			$return = '';

			foreach ( self::$controls as $key => $args ) {
				$fields  = isset( $args['fields'] ) ? $args['fields'] : array();
				$return .= sprintf( '<div id="%s" class="panel woocommerce_options_panel hidden"><div class="options_group">', strtolower( trim( $key ) ) );

				if ( ! empty( $fields ) ) {
					foreach ( $fields as $field ) {
						$return .= $field;
					}
				}

				$return .= '</div></div>';
			}

			echo $return; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		/**
		 * Fires after a product has been updated or published.
		 *
		 * @since    1.0.0
		 * @param    WC_Product $product    Product object.
		 * @return   void
		 */
		public static function save( $product ) {
			$product->update_meta_data( self::$key, filter_input( INPUT_POST, self::$key, FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY ) );
		}

	}
endif;
