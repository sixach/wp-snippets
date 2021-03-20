# WooCommerce Settings

## Initialization

```php
add_filter(
	'woocommerce_get_settings_pages',
	function( $settings ) {
		$settings[] = new WC_Settings(
			array(
				'sections' => array(
					array(
						'id'     => '',
						'label'  => __( 'Section 1', '@@textdomain' ),
						'fields' => array(
							array(
								'type' => 'title',
								'id'   => 'sixa_wc_settings_group1_title',
								'name' => __( 'Group 1', '@@textdomain' ),
							),
							array(
								'default'  => 'no',
								'type'     => 'checkbox',
								'id'       => sprintf( '%s[checkbox-choice]', WC_Settings::$key ),
								'name'     => __( 'Do a thing?', '@@textdomain' ),
								'desc'     => __( 'Enable to do something', '@@textdomain' ),
							),
							array(
								'type' => 'sectionend',
								'id'   => 'sixa_wc_settings_group1_sectionend',
							),
						),
					),
				),
			)
		);

		return $settings;
	},
	15
);
```