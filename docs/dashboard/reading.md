# Reading

```php
SixaSnippets\Dashboard\Reading( array $args = array() );
```

## Description

Adds additional static page dropdown (select) controls to the `Settings` → `Reading` settings page.

## Import

```php 
use SixaSnippets\Dashboard\Reading;
```

!> **Note:** Should not be hooked before the [admin_menu](http://developer.wordpress.org/reference/hooks/admin_menu/) action hook.

## Retrieve

Retrieve the stored additional option values.

```php
$options = get_option( Reading::$key, array() );
```

## Parameters

- **$args**
	- *(array) (Required)* List of controls for generating dropdown page controls, where keys will be used in populating both input field `id` and `name` attributes, and values are the field labels.
	- *Default value: empty array*

## Example

```php
add_action(
	'admin_menu',
	function() {
		new Reading(
			array(
				'testimonial' => __( 'Testimonial', '@@textdomain' ),
				'notfound'    => __( '404 Notfound', '@@textdomain' ),
			)
		);
	}
);
```

## Screenshot

![](../assets/reading.png ':size=30%')