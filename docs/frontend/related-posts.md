# Related Posts

```php
SixaSnippets\Frontend\Related_Posts();
```

## Description

This method generates 

## Import

```php 
use SixaSnippets\Frontend\Widget\Related_Posts;
```

!> Best place to initialize this class is inside the single post/page view template hook.

## Usage

```php
add_action( 'sixa_single_post_bottom', array( 'SixaSnippets\Frontend\Related_Posts', 'run' ) );
```

```php
// With parameters being customized.
add_action(
	'sixa_single_post_bottom',
	function() {
		Related_Posts::run(
			array(
				'show_date'     => 1,
				'show_author'   => 1,
				'show_thumb'    => 1,
				'number'        => 3,
				'orderby'       => 'date',
				'order'         => 'desc',
				'taxonomy'      => 'category',
			)
		);
	}
);
```