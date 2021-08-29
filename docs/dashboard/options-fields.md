# Options Fields

```php
SixaSnippets\dashboard\Options::add_field( string $id = null, string $title = null, function $callback = null );
```

## Description

Add a new field to a section of the plugin’s settings page.

## Import

```php 
use SixaSnippets\Dashboard\Options;
```

!> **Note:** Should be hooked to the `sixa_options_fieldset` action hook.

## Retrieve

Retrieve the stored additional option values.

```php
$options = get_option( Options::$key, array() );
```

## Parameters

- **id**
    - *(string) (Required)* Slug-name to identify the field. Used in the `id` attribute of tags.
- **title**
    - *(string) (Required)* Formatted title of the field. Shown as the label for the field during output.
- **callback**
    - *(callable) (Required)* Function that fills the field with the desired form inputs. The function should echo its output.

## Usage

```php
add_action(
	'sixa_options_fieldset',
	function( $slug ) {
		$options = get_option( Options::$key, array() );

		// Text.
		Options::add_field(
			'sixa_options_text',
			__( 'Text field', '@@textdomain' ),
			function() use ( $options ) {
				Options::text_field(
					array(
						'id'    => 'sixa_options_text',
						'name'  => sprintf( '%s[text-input]', Options::$key ),
						'value' => isset( $options['text-input'] ) ? $options['text-input'] : '',
					)
				);
			}
		);
		// Checkbox.
		Options::add_field(
			'sixa_options_checkbox',
			__( 'Checkbox', '@@textdomain' ),
			function() use ( $options ) {
				Options::checkbox_field(
					array(
						'id'          => 'sixa_options_checkbox',
						'name'        => sprintf( '%s[checkbox-choice]', Options::$key ),
						'value'       => isset( $options['checkbox-choice'] ) ? 'yes' : 'no',
						'description' => __( 'Check me out', '@@textdomain' ),
					)
				);
			}
		);
		// Select.
		Options::add_field(
			'sixa_options_select',
			__( 'Select', '@@textdomain' ),
			function() use ( $options ) {
				Options::select_field(
					array(
						'id'      => 'sixa_options_select',
						'name'    => sprintf( '%s[select-choice]', Options::$key ),
						'value'   => isset( $options['select-choice'] ) ? $options['select-choice'] : '',
						'options' => array(
							'option1' => __( 'Option 1', '@@textdomain' ),
							'option2' => __( 'Option 2', '@@textdomain' ),
							'option3' => __( 'Option 3', '@@textdomain' ),
						),
					)
				);
			}
		);
	}
);
```

## Screenshot

![](../assets/options-fields.png ':size=30%')