# Recent Posts Widget

## Initialization

```php
add_action(
	'widgets_init',
	function() {
		register_widget( Recent_Posts::class );
	}
);
```