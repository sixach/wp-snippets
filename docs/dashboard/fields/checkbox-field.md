# Checkbox Field

```php
SixaSnippets\dashboard\Options::checkbox_field( array $args = array(), bool $echo = true );
```

## Description

This method outputs an `<input type="checkbox" />` tag that defines a checkbox. The checkbox is shown as a square box that is ticked (checked) when activated.

Checkboxes are often used to let a user select one or more options of a limited number of choices.

!> Always add the `<label />` tag for best accessibility practices!

## Import

```php 
use SixaSnippets\Dashboard\Options;
```

## Parameters

- **args**
    - **id**:
        - *(string) (Required)* Slug-name to identify the field. Used in the `id` attribute of tags.
    - **name**
        - *(string) (Optional)* Can be used to reference the element in other places.
        - *Default value: $id*
    - **label**
        - *(string) (Optional)* Formatted title of the field.
    - **description**
        - *(string) (Optional)* A help text will be shown below the input field.
    - **class**
        - *(string) (Optional)* Custom CSS class names to be added to the input field.
        - *Default value: `checkbox`*
    - **wrapper_class**
        - *(string) (Optional)* Custom CSS class names to be added to the input field wrapper paragraph tag.
    - **style**
        - *(string) (Optional)* Inline style may be used to apply a unique style to the input field.
    - **value**
        - *(string) (Optional)* The current value of the input field. Could be either `yes` or `no`.
        - *Default value: `no`*
    - **custom_attributes**
        - *(array) (Optional)* Attributes consist of two parts:
            - The attribute name should not contain any uppercase letters, and must be at least one character long after the prefix.
            - The attribute value can be any thing.
- **echo**
    - *(bool) (Optional)* Whether to echo or just return the output.
    - *Default value: `true`*

## Usage

```php
Options::checkbox_field(
	array(
		'label'                         => __( 'Checkbox field', '@@textdomain' ),
		'description'                   => __( 'More information about this field.', '@@textdomain' ),
		'class'                         => 'checkbox',
		'style'                         => 'color:#A8A8A8;',
		'wrapper_class'                 => 'wrapper-class-name',
		'value'                         => 'no',
		'id'                            => 'sixa_options_checkbox',
		'name'                          => 'checkbox-input',
		'custom_attributes'             => array(
			'data-attr' => 'attr-value',
		),
	)
);
```