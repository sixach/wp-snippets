<?php
/**
 * The plugin bootstrap file.
 *
 * @wordpress-plugin
 * Plugin Name:       Sixa Snippets
 * Plugin URI:        https://sixa.ch
 * Description:       A plugin containing factory classes or methods for the Sixa projects.
 * Version:           1.0.0
 * Requires at least: 5.3
 * Requires PHP:      7.2
 * Author:            sixa AG
 * Author URI:        https://sixa.ch
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       @@textdomain
 * Domain Path:       /languages
 */

namespace SixaSnippets;

/**
 * Loads the PSR-4 autoloader implementation.
 *
 * @since    1.0.0
 * @return   void
 */
require_once sprintf( '%s/autoloader.php', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
