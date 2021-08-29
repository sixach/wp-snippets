# Select Field

```php
SixaSnippets\dashboard\Options::select_field( array $args = array(), bool $echo = true );
```

## Description

This method outputs a `<select />` tag that is used to create a dropdown list. The element is most often used in a form, to collect user input.

!> The `id` attribute is needed to associate the dropdown list with a label.

?> The `name` attribute is needed to reference the form data after the form is submitted. If you omit this attribute, no data from the dropdown list will be submitted.

## Import

```php 
use SixaSnippets\Dashboard\Options;
```

## Parameters

- **args**
    - **id**:
        - *(string) (Required)* Slug-name to identify the field. Used in the `id` attribute of tags.
    - **options**
        - *(array) (Required)* List of options for the dropdown menu control, where values are the keys, and labels are the values.
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
        - *Default value: `select short`*
    - **wrapper_class**
        - *(string) (Optional)* Custom CSS class names to be added to the input field wrapper paragraph tag.
    - **style**
        - *(string) (Optional)* Inline style may be used to apply a unique style to the input field.
    - **value**
        - *(string) (Optional)* The current value of the input field.
    - **show_option_none**
        - *(bool) (Optional)* A placeholder option to display for showing no option is selected.
        - *Default value: `false`*
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
		'label'                         => __( 'Select field', '@@textdomain' ),
		'description'                   => __( 'More information about this field.', '@@textdomain' ),
		'class'                         => 'select short',
		'style'                         => 'color:#A8A8A8;',
		'wrapper_class'                 => 'wrapper-class-name',
		'value'                         => 'option1',
		'id'                            => 'sixa_options_select',
		'name'                          => 'select-input',
		'show_option_none'              => true,
		'custom_attributes'             => array(
			'data-attr' => 'attr-value',
		),
		'options'                       => array(
			'option1' => __( 'Option 1', '@@textdomain' ),
			'option2' => __( 'Option 2', '@@textdomain' ),
			'option3' => __( 'Option 3', '@@textdomain' ),
		),
	)
);
```