<?php
/**
 * The file registers custom control(s), (field(s))
 * to the `WooCommerce` → `Settings` settings/page.
 *
 * @link          https://sixa.ch
 * @author        sixa AG
 * @since         1.0.0
 *
 * @package       Sixa_Snippets
 * @subpackage    Sixa_Snippets/Dashboard/WooCommerce
 * @phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
 */

namespace Sixa_Snippets\Dashboard\WooCommerce;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! class_exists( WC_Settings::class ) ) :

	/**
	 * The file that defines the extra WooCommerce’s class.
	 */
	class WC_Settings {

		/**
		 * Name of the option.
		 *
		 * @since     1.0.0
		 * @access    public
		 * @var       string $key    Name of the option to retrieve.
		 */
		public static $key = 'sixa_wc_options';

		/**
		 * Setting page id.
		 *
		 * @since     1.0.0
		 * @access    protected
		 * @var       string $id     Settings tab id to register.
		 */
		protected $id = 'sixa_wc_settings';

		/**
		 * Setting page label.
		 *
		 * @since     1.0.0
		 * @access    protected
		 * @var       string $label    Settings tab label to register.
		 */
		protected $label = '';

		/**
		 * Setting page sections.
		 *
		 * @since     1.0.0
		 * @access    protected
		 * @var       array $fields    Settings sections to register.
		 */
		protected $sections = array();

		/**
		 * Setting page fields.
		 *
		 * @since     1.0.0
		 * @access    protected
		 * @var       array $fields    Settings fieldset to register.
		 */
		protected $fields = array();

		/**
		 * Initialize the class and set its properties.
		 *
		 * @since     1.0.0
		 * @param     array $args    Settings arguments.
		 * @return    void
		 */
		public function __construct( array $args = array() ) {
			$this->id       = $args['id'] ?? $this->id;
			$this->label    = $args['label'] ?? _x( 'Sixa Options', 'wc settings', 'sixa-snippets' );
			$this->sections = isset( $args['sections'] ) ? wp_list_pluck( $args['sections'], 'label', 'id' ) : array();
			$this->fields   = ! empty( $this->sections ) ? wp_list_pluck( $args['sections'], 'fields', 'id' ) : array();

			add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
			add_action( sprintf( 'woocommerce_sections_%s', $this->id ), array( $this, 'output_sections' ) );
			add_action( sprintf( 'woocommerce_settings_%s', $this->id ), array( $this, 'output' ) );
			add_action( sprintf( 'woocommerce_settings_save_%s', $this->id ), array( $this, 'save' ) );
		}

		/**
		 * Get settings page ID.
		 *
		 * @since     1.0.0
		 * @return    string
		 */
		public function get_id(): string {
			return $this->id;
		}

		/**
		 * Get settings page label.
		 *
		 * @since     1.0.0
		 * @return    string
		 */
		public function get_label(): string {
			return $this->label;
		}

		/**
		 * Add this page to settings.
		 *
		 * @since     1.0.0
		 * @param     array $tabs    Existing settings tabs.
		 * @return    array
		 */
		public function add_settings_page( array $tabs ): array {
			$tabs[ $this->id ] = $this->label;
			return $tabs;
		}

		/**
		 * Get settings array.
		 *
		 * @since     1.0.0
		 * @return    array
		 */
		public function get_settings(): array {
			$current_section = filter_input( INPUT_GET, 'section', FILTER_SANITIZE_STRING );
			$current_section = is_null( $current_section ) ? array_key_first( $this->fields ) : $current_section; // phpcs:ignore PHPCompatibility.FunctionUse.NewFunctions.array_key_firstFound
			$fields          = $this->fields[ $current_section ] ?? array();

			return $fields;
		}

		/**
		 * Get sections.
		 *
		 * @since     1.0.0
		 * @return    array
		 */
		public function get_sections(): array {
			return $this->sections;
		}

		/**
		 * Output sections.
		 *
		 * @since     1.0.0
		 * @return    void
		 */
		public function output_sections(): void {
			global $current_section;

			$return   = '';
			$sections = $this->get_sections();

			if ( empty( $sections ) || 1 === count( $sections ) ) {
				return;
			}

			$return    .= '<ul class="subsubsub">';
			$array_keys = array_keys( $sections );

			foreach ( $sections as $id => $label ) {
				$return .= sprintf( '<li><a href="%s" class="%s">%s</a>%s</li>', admin_url( sprintf( 'admin.php?page=wc-settings&tab=%s&section=%s', $this->id, sanitize_title( $id ) ) ), $current_section === $id ? 'current' : '', $label, end( $array_keys ) === $id ? '' : '|' );
			}

			$return .= '</ul><br class="clear" />';

			echo $return; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		/**
		 * Output the settings.
		 *
		 * @since     1.0.0
		 * @return    void
		 */
		public function output(): void {
			$settings = $this->get_settings();
			\WC_Admin_Settings::output_fields( $settings );
		}

		/**
		 * Save settings.
		 *
		 * @since     1.0.0
		 * @return    void
		 */
		public function save(): void {
			$settings = $this->get_settings();
			\WC_Admin_Settings::save_fields( $settings );
		}

	}
endif;
