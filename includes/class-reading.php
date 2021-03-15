<?php
/**
 * The file registers static page dropdown field(s)
 * to the `Dashboard` → `Reading` settings/page.
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
 * 3. Initialize the class to register a series of dropdown menus when needed:
 *
 * add_action(
 *  'admin_menu',
 *  function() {
 *      new Reading(
 *          array(
 *              'testimonial' => __( 'Testimonial', '@@textdomain' ),
 *              'notfound'    => __( '404 Notfound', '@@textdomain' ),
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

if ( ! class_exists( 'Reading' ) ) :

	/**
	 * The file that defines the plugin option’s class.
	 */
	class Reading {

		/**
		 * List of page options.
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      array $dropdowns     List of pages to register a dropdown option for it.
		 */
		private static $dropdowns = array();

		/**
		 * Name of the option.
		 *
		 * @since    1.0.0
		 * @access   public
		 * @var      string $key     Name of the option to retrieve.
		 */
		public static $key = 'sixa_reading';

		/**
		 * Initialize the class and set its properties.
		 *
		 * @since    1.0.0
		 * @param    array $list     Dropdown list.
		 * @return   void
		 */
		public function __construct( $list = array() ) {
			// Bail early, in case there no option provided to register.
			if ( ! is_array( $list ) || empty( $list ) ) {
				return;
			}

			self::$dropdowns = $list;
			$this->register();
			add_filter( 'display_post_states', array( $this, 'post_states' ), 10, 2 );
		}

		/**
		 * Registers static page dropdown menus.
		 *
		 * @since    1.0.0
		 * @return   void
		 */
		public function register() {
			register_setting( 'reading', self::$key, array( 'SixaSnippets\Includes\Options', 'sanitize' ) );
			add_settings_section( self::$key, _x( 'Additional Settings', 'reading', '@@textdomain' ), '', 'reading' );
			$options = get_option( self::$key, array() );

			foreach ( self::$dropdowns as $key => $name ) {
				$key = strtolower( trim( $key ) );
				Options::add_field(
					sprintf( 'sixa_reading_%s', $key ),
					$name,
					function() use ( $options, $key, $name ) {
						Options::select_field(
							array(
								'show_option_none' => true,
								'id'               => sprintf( 'sixa_reading_%s', $key ),
								'name'             => sprintf( '%s[%s]', self::$key, $key ),
								'value'            => isset( $options[ $key ] ) ? $options[ $key ] : '',
								'options'          => wp_list_pluck( get_pages( array( 'child_of' => 0 ) ), 'post_title', 'ID' ),
							)
						);
					},
					'reading',
					self::$key
				);
			}
		}

		/**
		 * Filters the default post display states used in the posts list table.
		 *
		 * @since    1.0.0
		 * @param    array   $post_states    An array of post display states.
		 * @param    WP_Post $post           The current post object.
		 * @return   array
		 */
		public function post_states( $post_states, $post ) {
			$options = get_option( self::$key, array() );

			foreach ( $options as $key => $name ) {
				$post_id = isset( $options[ $key ] ) ? intval( $options[ $key ] ) : 0;

				if ( $post->ID === $post_id ) {
					$post_states[] = isset( self::$dropdowns[ $key ] ) ? self::$dropdowns[ $key ] : '';
				}
			}

			return $post_states;
		}

	}
endif;
