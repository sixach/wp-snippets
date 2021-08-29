# Product Data

```php
SixaSnippets\dashboard\woocommerce\Product_Data( array $args = array() );
```

## Description

Registers additional input controls divided into different panels to the `Product data` table.

## Import

```php 
use SixaSnippets\Dashboard\WooCommerce\Product_Data;
```

!> **Note:** Should not be hooked before the [woocommerce_init](http://hookr.io/actions/woocommerce_init/) action hook.

## Retrieve

Retrieve the stored additional product meta-data values.

```php
global $product;
$meta = (array) $product->get_meta( Product_Data::$key, true );
```

## Parameters

- **args**:
	- *(array) (Required)* List of panels, where values are the properties of the panel, and keys are used in the `id` attribute of tags.
	- *Default value: empty array*
		- **label**:
			- *(string) (Optional)* Formatted title of the panel tab.
			- *Default value: `Sixa Options`*
		- **class**:
        	- *(string) (Optional)* Custom CSS class names to be added to the panel warpper tag.
        	- *Default value: empty string*
		- **fields**:
			- *(array) (Required)* Panel tab fields.
			- *Default value: empty array*
			- *See available field types: [text](fields/text-field.md), [textarea](ields/textarea-field.md), [hidden](fields/hidden-field.md), [select](fields/select-field.md), [checkbox](fields/checkbox-field.md), [radio](fields/radio-field.md).*

## Usage

```php
add_action(
	'woocommerce_init',
	function() {
		new Product_Data(
			array(
				// New panel.
				'data' => array(
					'label'  => __( 'Sixa Panel', '@@textdomain' ),
					'class'  => 'show_if_simple',
					'fields' => array(
						array(
							'type'        => 'checkbox',
							'id'          => 'checkbox-choice',
							'label'       => __( 'Do a thing?', '@@textdomain' ),
							'description' => __( 'Enable to do something', '@@textdomain' ),
						),
						array(
							'type'        => 'select',
							'id'          => 'select-choice',
							'label'       => __( 'Choose your favorite', '@@textdomain' ),
							'options'     => array(
								'vanilla'        => __( 'Vanilla', '@@textdomain' ),
								'chocolate'      => __( 'Chocolate', '@@textdomain' ),
								'strawberry'     => __( 'Strawberry', '@@textdomain' ),
							),
							'class'       => 'wc-enhanced-select',
							'style'       => 'width:400px;',
							'description' => __( 'Be honest!', '@@textdomain' ),
						),
					),
				),
				// Another panel.
				'text' => array(
					'label'  => __( 'Sixa Panel (2)', '@@textdomain' ),
					'fields' => array(
						array(
							'type'        => 'text',
							'id'          => 'text-input',
							'label'       => __( 'Text field', '@@textdomain' ),
						),
						array(
							'rows'        => '5',
							'style'       => 'height:auto;',
							'type'        => 'textarea',
							'id'          => 'textarea-input',
							'label'       => __( 'Textarea field', '@@textdomain' ),
						),
					),
				),
			)
		);
	}
);
```

## Screenshot

![](../../assets/product-data.png ':size=30%')