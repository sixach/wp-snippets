<?php
/**
 * The plugin bootstrap file.
 *
 * @link                 https://sixa.ch
 * @author               sixa AG
 * @since                1.0.0
 * @package              wp-snippets
 *
 * @wordpress-plugin
 * Plugin Name:          Sixa Snippets
 * Plugin URI:           https://sixa.ch
 * Description:          A plugin containing factory classes or methods for the Sixa projects.
 * Version:              1.7.2
 * Requires at least:    5.3
 * Requires PHP:         7.4
 * Author:               sixa AG
 * Author URI:           https://sixa.ch
 * License:              GPL-3.0+
 * License URI:          http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:          sixa-snippets
 * Domain Path:          /languages
 */

namespace Sixa_Snippets;

/**
 * Composer autoload is needed in this package, even if
 * it doesn't use any libraries, to autoload the classes
 * from this package.
 *
 * @see    https://getcomposer.org/doc/01-basic-usage.md#autoloading
 */
require __DIR__ . '/vendor/autoload.php';
