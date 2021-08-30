# Post Shortcode

```php
Sixa_Snippets\Frontend\Shortcode\Post( array $atts = array() );
```

## Description

This shortcode retrieves and displays a given post ID’s content on the page.

## Import

```php 
use Sixa_Snippets\Frontend\Shortcode\Post;
```

!> **Note:** Should not be hooked before the [init](http://developer.wordpress.org/reference/hooks/init/) action hook.

## Parameters

- **id**
    - *(integer) (Required)* Post id.
    - *Default value: empty string*
- **class**
    - *(string) (Optional)* Custom CSS class name to be added to the wrapper tag.
    - *Default value: empty string*

## Register

```php
add_action( 'init', function() {
    add_shortcode( 'sixa_post', array( 'SixaSnippets\Frontend\Shortcode\Post', 'run' ) );
} );
```

## Usage

```html
[sixa_post id="1788" class="class-name"]
```