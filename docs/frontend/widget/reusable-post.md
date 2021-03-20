# Reusable Post Widget

## Initialization

```php
add_action(
	'widgets_init',
	function() {
		register_widget( Reusable_Post::class );
	}
);
```