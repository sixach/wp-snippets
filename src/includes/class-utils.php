<?php
/**
 * Helper functions.
 *
 * @link          https://sixa.ch
 * @author        sixa AG
 * @since         1.0.0
 *
 * @package       Sixa_Snippets
 * @subpackage    Sixa_Snippets/Includes/Utils
 */

namespace Sixa_Snippets\Includes;


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( Utils::class ) ) :

	/**
	 * Utils Class.
	 */
	final class Utils {

		/**
		 * Return `true` if WooCommerce is installed and `false` otherwise.
		 *
		 * @since     1.3.0
		 * @return    bool
		 */
		public static function is_woocommerce_activated(): bool {
			// This statement prevents from producing fatal errors,
			// in case the WooCommerce plugin is not activated on the site.
			$woocommerce_plugin     = apply_filters( 'sixa_snippets_woocommerce_path', 'woocommerce/woocommerce.php' );
			$subsite_active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );
			$network_active_plugins = apply_filters( 'active_plugins', get_site_option( 'active_sitewide_plugins' ) );

			// Bail early in case the plugin is not activated on the website.
			if ( ( empty( $subsite_active_plugins ) || ! in_array( $woocommerce_plugin, $subsite_active_plugins ) ) && ( empty( $network_active_plugins ) || ! array_key_exists( $woocommerce_plugin, $network_active_plugins ) ) ) {
				return false;
			}

			return true;
		}

	}

endif;