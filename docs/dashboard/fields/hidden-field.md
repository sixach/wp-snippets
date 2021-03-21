# Hidden Field

```php
SixaSnippets\Dashboard\Options::hidden_field( array $args = array(), bool $echo = true );
```

## Description

This method outputs an `<input type="hidden" />` tag that defines a hidden input field. A hidden field lets developers include data that cannot be seen or modified by users when a form is submitted.

This field type often stores what database record that needs to be updated when the form is submitted.

## Import

```php 
use SixaSnippets\Dashboard\Options;
```

## Parameters

- **$args**
    - **id**:
        - *(string) (Required)* Slug-name to identify the field. Used in the 'id' attribute of tags.
    - **name**:
        - *(string) (Optional)* Can be used to reference the element in other places.
        - *Default value: `$id`*
    - **class**:
        - *(string) (Optional)* Custom CSS class names to be added to the input field.
    - **value**:
        - *(string) (Optional)* The current value of the input field.
- **$echo**
    - *(bool) (Optional)* Whether to echo or just return the output.
    - *Default value: `true`*

## Example

```php
Options::hidden_field(
	array(
		'class'     => 'class-name',
		'value'     => 'hidden value',
		'id'        => 'sixa_options_hidden',
		'name'      => 'hidden-input',
	)
);
```