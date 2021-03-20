# Reading

## Initialization

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