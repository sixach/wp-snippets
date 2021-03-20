# Custom Post Type

## Initialization

```php
add_action(
	'init',
	function() {
		new Post_Type(
			array(
				array(
					'key'           => 'docs',
					'singular_name' => 'Document',
					'plural_name'   => 'Documents',
					'args'          => array(
						'menu_icon' => 'dashicons-book',
					),
					'taxonomies'    => array(
						array(
							'key' => 'category',
						),
					),
				),
			),
		);
	}
);
```