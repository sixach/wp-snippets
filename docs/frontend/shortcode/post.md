# Post Shortcode

## Initialization

```php
add_action( 'init', function() {
    add_shortcode( 'sixa_post', array( 'SixaSnippets\Frontend\Shortcode\Post', 'Run' ) );
} );
```