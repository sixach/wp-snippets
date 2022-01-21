<?php
/**
 * The file registers input text-field(s)
 * to the `Settings` → `Permalinks` settings/page.
 *
 * @link          https://sixa.ch
 * @author        sixa AG
 * @since         1.4.3
 *
 * @package       Sixa_Snippets
 * @subpackage    Sixa_Snippets/Dashboard
 * @phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
 */

namespace Sixa_Snippets\Dashboard;

use Sixa_Snippets\Dashboard\Options as Options;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! class_exists( Permalink::class ) ) :

	/**
	 * The file that defines the permalink option’s class.
	 */
	class Permalink {

		/**
		 * List of permalink bases.
		 *
		 * @since     1.0.0
		 * @access    private
		 * @var       array $bases    List of URI bases to register a text-field control for it.
		 */
		private static $bases = array();

		/**
		 * Name of the option.
		 *
		 * @since     1.0.0
		 * @access    public
		 * @var       string $key    Name of the option to retrieve.
		 */
		public static $key = 'sixa_permalink';

		/**
		 * Initialize the class and set its properties.
		 *
		 * @since     1.4.3
		 *            Flush rewrite rules when custom permalink options created/updated.
		 * @since     1.0.0
		 * @param     array $args    Permalink base list.
		 * @return    void
		 */
		public function __construct( array $args = array() ) {
			// Bail early, in case there no option provided to register.
			if ( ! is_array( $args ) || empty( $args ) ) {
				return;
			}

			self::$bases = $args;
			$this->register();
			$this->save();
			add_action( sprintf( 'add_option_%s', self::$key ), array( $this, 'flush_rules_on_save' ) );
			add_action( sprintf( 'update_option_%s', self::$key ), array( $this, 'flush_rules_on_save' ) );
		}

		/**
		 * Registers permalink base input text-fields.
		 *
		 * @since     1.0.0
		 * @return    void
		 */
		public function register(): void {
			$options = get_option( self::$key, array() );

			foreach ( self::$bases as $key => $name ) {
				$key = strtolower( trim( $key ) );
				Options::add_field(
					sprintf( 'sixa_permalink_%s', $key ),
					$name,
					function() use ( $options, $key, $name ) {
						Options::text_field(
							array(
								'style' => 'width:25em;',
								'class' => 'regular-text code',
								'id'    => sprintf( 'sixa_permalink_%s', $key ),
								'name'  => sprintf( '%s[%s]', self::$key, $key ),
								'value' => $options[ $key ] ?? '',
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
		 * @since     1.0.0
		 * @return    void
		 * @phpcs:disable WordPress.Security.NonceVerification.Missing
		 */
		public function save(): void {
			// Bail early, if the current request is for the administrative interface page.
			if ( ! is_admin() || ! isset( $_POST['permalink_structure'] ) ) {
				return;
			}

			update_option( self::$key, filter_input( INPUT_POST, self::$key, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY ), false );
		}

		/**
		 * Flush permalinks when option is created or updated.
		 *
		 * @since     1.4.3
		 * @return    void
		 */
		public function flush_rules_on_save(): void {
			flush_rewrite_rules();
		}

	}
endif;
