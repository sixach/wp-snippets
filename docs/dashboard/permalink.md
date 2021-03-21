# Permalink

```php
SixaSnippets\Dashboard\Permalink( array $args = array() );
```

## Description

Adds additional input controls to the `Settings` → `Permalinks` settings page.

## Import

```php 
use SixaSnippets\Dashboard\Permalink;
```

!> **Note:** Do not initialize this class before the [admin_menu](http://developer.wordpress.org/reference/hooks/admin_menu/) action hook.

## Parameters

- **$args**
	- *(array) (Required)* List of controls for generating permalink base controls, where keys will be used in populating both input field `id` and `name` attributes, and values are the field labels.
	- *Default value: empty array*

## Example

```php
add_action(
	'admin_menu',
	function() {
		new Permalink(
			array(
				'portfolio'   => __( 'Portfolio base', '@@textdomain' ),
				'testimonial' => __( 'Testimonial base', '@@textdomain' ),
			)
		);
	}
);
```

## Screenshot

![](../assets/permalink.png ':size=30%')