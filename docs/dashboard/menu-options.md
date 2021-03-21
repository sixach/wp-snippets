# Menu Options

```php
SixaSnippets\Dashboard\Menu_Options( array $args = array() );
```

## Description

Adds additional input controls before the move buttons of a nav menu item in the menu editor.

## Import

```php 
use SixaSnippets\Dashboard\Menu_Options;
```

!> **Note:** Should not be hooked before the [admin_menu](http://developer.wordpress.org/reference/hooks/admin_menu/) action hook.

## Parameters

- **$args**
    - **type**:
        - *(string) (Required)* Input field type.
        - *See available field types: [text](dashboard/fields/text-field.md), [textarea](dashboard/fields/textarea-field.md), [hidden](dashboard/fields/hidden-field.md), [select](dashboard/fields/select-field.md), [checkbox](dashboard/fields/checkbox-field.md), [radio](dashboard/fields/radio-field.md).*
    - **id**:
        - *(string) (Required)* Slug-name to identify the field. Used in the 'id' attribute of tags.
    - **name**:
        - *(string) (Optional)* Can be used to reference the element in other places.
        - *Default value: `$id`*
    - **label**:
        - *(string) (Optional)* Formatted title of the field.
    - **description**:
        - *(string) (Optional)* A help text will be shown below the input field.
    - **default**:
        - *(string) (Optional)* The default value of the input field.

## Example

```php
add_action(
	'admin_init',
	function() {
		new Menu_Options(
			array(
				array(
					'default'     => 'no',
					'type'        => 'checkbox',
					'name'        => 'checkbox-input',
					'label'       => __( 'Do a thing?', '@@textdomain' ),
					'description' => __( 'Enable to do something', '@@textdomain' ),
				),
				array(
                    'type'             => 'select',
					'name'             => 'select-input',
					'label'            => __( 'Select an option?', '@@textdomain' ),
					'show_option_none' => true,
					'options'          => array(
						'option1' => __( 'Option 1', '@@textdomain' ),
						'option2' => __( 'Option 2', '@@textdomain' ),
						'option3' => __( 'Option 3', '@@textdomain' ),
					),
				),
			)
		);
	}
);
```

## Screenshot

![](../assets/menu-options.png ':size=30%')