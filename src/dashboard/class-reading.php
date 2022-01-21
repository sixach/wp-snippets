<?php
/**
 * The file registers static page dropdown field(s)
 * to the `Settings` → `Reading` settings/page.
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

use Sixa_Snippets\Dashboard\Options as Options;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! class_exists( Reading::class ) ) :

	/**
	 * The file that defines the plugin option’s class.
	 */
	class Reading {

		/**
		 * List of page options.
		 *
		 * @since     1.0.0
		 * @access    private
		 * @var       array $dropdowns    List of pages to register a dropdown option for it.
		 */
		private static $dropdowns = array();

		/**
		 * Name of the option.
		 *
		 * @since     1.0.0
		 * @access    public
		 * @var       string $key    Name of the option to retrieve.
		 */
		public static $key = 'sixa_reading';

		/**
		 * Initialize the class and set its properties.
		 *
		 * @since     1.0.0
		 * @param     array $args    Dropdown list.
		 * @return    void
		 */
		public function __construct( array $args = array() ) {
			// Bail early, in case there no option provided to register.
			if ( ! is_array( $args ) || empty( $args ) ) {
				return;
			}

			self::$dropdowns = $args;
			$this->register();
			add_filter( 'display_post_states', array( $this, 'post_states' ), 10, 2 );
		}

		/**
		 * Registers static page dropdown menus.
		 *
		 * @since     1.0.0
		 * @return    void
		 */
		public function register(): void {
			register_setting( 'reading', self::$key, array( sprintf( '%s\Options', __NAMESPACE__ ), 'sanitize' ) );
			add_settings_section( self::$key, _x( 'Additional Settings', 'reading', 'sixa-snippets' ), '', 'reading' );
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
								'value'            => $options[ $key ] ?? '',
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
		 * @since     1.0.0
		 * @param     array  $post_states    An array of post display states.
		 * @param     object $post           The current post object.
		 * @return    array
		 */
		public function post_states( array $post_states, object $post ): array {
			$options = get_option( self::$key, array() );

			foreach ( $options as $key => $name ) {
				$post_id = isset( $options[ $key ] ) ? intval( $options[ $key ] ) : 0;

				if ( $post->ID === $post_id ) {
					$post_states[] = self::$dropdowns[ $key ] ?? '';
				}
			}

			return (array) $post_states;
		}

	}
endif;
