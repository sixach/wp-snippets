# Permalink

## Initialization

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