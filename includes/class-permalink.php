<?php
/**
 * The file registers input text-field(s)
 * to the `Settings` → `Permalinks` settings/page.
 *
 * @link       https://sixa.ch
 * @author     Mahdi Yazdani
 * @since      1.0.0
 *
 * @package    sixa-snippets
 * @subpackage sixa-snippets/includes
 */

namespace SixaSnippets\Includes;

use SixaSnippets\Includes\Options;

/**
 * INSTRUCTIONS:
 *
 * 1. Update the namespace(s) used in this file.
 * 2. Search and replace text-domains `@@textdomain`.
 * 3. Initialize the class to register a series of text-input fields when needed:
 *
 * add_action(
 *  'admin_menu',
 *  function() {
 *      new Permalink(
 *          array(
 *              'portfolio'   => __( 'Portfolio base', '@@textdomain' ),
 *              'testimonial' => __( 'Testimonial base', '@@textdomain' ),
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

if ( ! class_exists( 'Permalink' ) ) :

	/**
	 * The file that defines the permalink option’s class.
	 */
	class Permalink {

		/**
		 * List of permalink bases.
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      array $bases     List of URI bases to register a text-field control for it.
		 */
		private static $bases = array();

		/**
		 * Name of the option.
		 *
		 * @since    1.0.0
		 * @access   public
		 * @var      string $key     Name of the option to retrieve.
		 */
		public static $key = 'sixa_permalink';

		/**
		 * Initialize the class and set its properties.
		 *
		 * @since    1.0.0
		 * @param    array $list     Permalink base list.
		 * @return   void
		 */
		public function __construct( $list = array() ) {
			// Bail early, in case there no option provided to register.
			if ( ! is_array( $list ) || empty( $list ) ) {
				return;
			}

			self::$bases = $list;
			$this->register();
			$this->save();
		}

		/**
		 * Registers permalink base input text-fields.
		 *
		 * @since    1.0.0
		 * @return   void
		 */
		public function register() {
			$options = get_option( self::$key, array() );

			foreach ( self::$bases as $key => $name ) {
				$key = strtolower( trim( $key ) );
				Options::add_field(
					sprintf( 'sixa_permalink_%s', $key ),
					$name,
					function() use ( $options, $key, $name ) {
						Options::text_field(
							array(
								'style'            => 'width:25em;',
								'class'            => 'regular-text code',
								'id'               => sprintf( 'sixa_permalink_%s', $key ),
								'name'             => sprintf( '%s[%s]', self::$key, $key ),
								'value'            => isset( $options[ $key ] ) ? $options[ $key ] : '',
							)
						);
					},
					'permalink',
					'optional'
				);
			}
		}

		/**
		 * Save settings.
		 *
		 * @since    1.0.0
		 * @return   void
		 * @phpcs:disable WordPress.Security.NonceVerification.Missing
		 */
		public function save() {
			// Bail early, if the current request is for the administrative interface page.
			if ( ! is_admin() || ! isset( $_POST['permalink_structure'] ) ) {
				return;
			}

			update_option( self::$key, filter_input( INPUT_POST, self::$key, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY ), false );
		}

	}
endif;
