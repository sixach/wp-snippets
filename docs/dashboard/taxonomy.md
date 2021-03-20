# Custom Taxonomy

## Initialization

```php
add_action(
	'init',
	function() {
		new Taxonomy(
			array(
				array(
					'key'           => 'brand',
					'post_type'     => 'product',
					'plural_name'   => 'Brands',
					'singular_name' => 'Brand',
				),
			)
		);
	}
);
```