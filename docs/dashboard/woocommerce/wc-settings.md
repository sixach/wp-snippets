# WooCommerce Settings

```php
SixaSnippets\Dashboard\WooCommerce\WC_Settings( array $args = array() );
```
## Description

Registers additional input controls divided into different sections to the `WooCommerce` → `Settings` settings page.

## Import

```php 
use SixaSnippets\Dashboard\WooCommerce\WC_Settings;
```

## Retrieve

Retrieve the stored additional WooCommerce Settings option values.

```php
$options = get_option( WC_Settings::$key, array() );
```

## Parameters

- **args**
	- **id**
    	- *(string) (Optional)* Slug-name to identify the settings tab. Used in the `href` attribute of the tab.
		- *Default value: `sixa_wc_settings`*
	- **label**
    	- *(string) (Optional)* Formatted title of the settings tab.
		- *Default value: `Sixa Options`*
	- **sections**
    	- *(array) (Required)* Settings tab sections.
		- *Default value: empty array*
			- **id**
    			- *(string) (Required)* Slug-name to identify the section. Used in the `href` attribute of the section.
				- *Default value: empty string*
			- **label**
    			- *(string) (Required)* Formatted title of the settings section.
				- *Default value: empty string*
			- **fields**
				- *(array) (Required)* List of the section specific fields.
				- *Default value: empty array*

## Example

```php
add_filter(
	'woocommerce_get_settings_pages',
	function( $settings ) {
		$settings[] = new WC_Settings(
			array(
				'sections' => array(
					
					// Section 1.
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
					// Section 2.
					array(
						'id'     => 'second_section',
						'label'  => __( 'Section 2', '@@textdomain' ),
						'fields' => array(
							array(
								'type' => 'title',
								'id'   => 'sixa_wc_settings_important_options',
								'name' => __( 'Important Stuff', '@@textdomain' ),
							),
							array(
								'type'     => 'select',
								'id'       => sprintf( '%s[select-choice]', WC_Settings::$key ),
								'name'     => __( 'Choose your favorite', '@@textdomain' ),
								'options'  => array(
									'vanilla'        => __( 'Vanilla', '@@textdomain' ),
									'chocolate'      => __( 'Chocolate', '@@textdomain' ),
									'strawberry'     => __( 'Strawberry', '@@textdomain' ),
								),
								'class'    => 'wc-enhanced-select',
								'desc_tip' => __( 'Be honest!', '@@textdomain' ),
								'default'  => 'vanilla',
							),
							array(
								'type' => 'sectionend',
								'id'   => 'sixa_wc_settings_important_options',
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

## Screenshot

![](../../assets/wc-settings.png ':size=30%')