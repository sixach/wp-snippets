# Radio Field

```php
Sixa_Snippets\Dashboard\Options::radio_field( array $args = array(), bool $echo = true );
```

## Description

This method outputs an `<input type="radio" />` tag that defines a radio button. Radio buttons are normally presented in radio groups, and only one radio button in a group can be selected at the same time.

!> Always add the `<label />` tag for best accessibility practices!

?> The `value` attribute defines the unique value associated with each radio button. The value is not shown to the user but is the value that is sent to the server on submission to identify which radio button was selected.

## Import

```php 
use Sixa_Snippets\Dashboard\Options;
```

## Parameters

- **args**
    - **id**:
        - *(string) (Required)* Slug-name to identify the field. Used in the `id` attribute of tags.
    - **choices**
        - *(array) (Required)* List of choices for the radio group control, where values are the keys, and labels are the values.
        - *Default value: empty array*
    - **name**
        - *(string) (Optional)* Can be used to reference the element in other places.
        - *Default value: `$id`*
    - **label**
        - *(string) (Optional)* Formatted title of the field.
    - **description**
        - *(string) (Optional)* A help text will be shown below the input field.
    - **class**
        - *(string) (Optional)* Custom CSS class names to be added to the input field.
        - *Default value: `radio`*
    - **wrapper_class**
        - *(string) (Optional)* Custom CSS class names to be added to the input field wrapper paragraph tag.
    - **style**
        - *(string) (Optional)* Inline style may be used to apply a unique style to the input field.
    - **value**
        - *(string) (Optional)* The current value of the input field.
    - **custom_attributes**
        - *(array) (Optional)* Attributes consist of two parts:
            - The attribute name should not contain any uppercase letters, and must be at least one character long after the prefix.
            - The attribute value can be any thing.
- **echo**
    - *(bool) (Optional)* Whether to echo or just return the output.
    - *Default value: `true`*

## Usage

```php
Options::select_field(
	array(
		'label'                         => __( 'Radio field', '@@textdomain' ),
		'description'                   => __( 'More information about this field.', '@@textdomain' ),
		'class'                         => 'radio',
		'style'                         => 'color:#A8A8A8;',
		'wrapper_class'                 => 'wrapper-class-name',
		'value'                         => 'radio1',
		'id'                            => 'sixa_options_radio',
		'name'                          => 'radio-input',
		'show_option_none'              => true,
		'custom_attributes'             => array(
			'data-attr' => 'attr-value',
		),
		'choices'                       => array(
			'radio1' => __( 'Radio 1', '@@textdomain' ),
			'radio2' => __( 'Radio 2', '@@textdomain' ),
			'radio3' => __( 'Radio 3', '@@textdomain' ),
		),
	)
);
```