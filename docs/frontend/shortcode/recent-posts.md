# Recent Posts Shortcode

## Initialization

```php
add_action( 'init', function() {
    add_shortcode( 'sixa_recent_posts', array( 'SixaSnippets\Frontend\Shortcode\Recent_Posts', 'Run' ) );
} );
```