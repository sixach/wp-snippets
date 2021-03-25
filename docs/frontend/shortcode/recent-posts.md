# Recent Posts Shortcode

```php
SixaSnippets\Frontend\Shortcode\Recent_Posts( array $atts = array() );
```

## Description

This shortcode displays your most recent posts in anywhere that the shortcode could be rendered, making it easy for the readers to see what’s new on published on the blog.

## Import

```php 
use SixaSnippets\Frontend\Shortcode\Recent_Posts;
```

!> **Note:** Should not be hooked before the [init](http://developer.wordpress.org/reference/hooks/init/) action hook.

## Parameters

- **number**
    - *(integer) (Required)* Total number of posts to retrieve and display.
    - *Default value: `4`*
- **categories**
    - *(array) (Optional)* Comma-separated list of category ids.
    - *Default value: empty array*
- **show_date**
    - *(bool) (Optional)* Whether to display post published date.
    - *Default value: `0 (false)`*
- **show_author**
    - *(bool) (Optional)* Whether to display post author name.
    - *Default value: `0 (false)`*
- **show_thumb**
    - *(bool) (Optional)* Whether to display post featured image.
    - *Default value: `0 (false)`*
- **order**
    - *(string) (Optional)* States whether the product order is ascending `ASC` *(lowest to highest)* or descending `DESC` *(highest to lowest)*.
    - *Default value: `desc`*
- **orderby**
    - *(string) (Optional)* Sorts the posts displayed by the entered option.
    - *Default value: `date`*
    - Available options:
        - `none` — No order.
        - `ID` — Order by post id.
        - `author` — Order by author.
        - `title` — Order by title.
        - `name` — Order by post name.
        - `type` — Order by post type.
        - `date` — Order by date.
        - `modified` — Order by last modified date.
        - `parent` — Order by post/page parent id.
        - `rand` — Random order.
        - `comment_count` — Order by number of comments.

## Register

```php
add_action( 'init', function() {
    add_shortcode( 'sixa_recent_posts', array( 'SixaSnippets\Frontend\Shortcode\Recent_Posts', 'run' ) );
} );
```

## Usage

```html
[sixa_recent_posts categories="10,13" number="4" show_date="1" show_author="1" show_thumb="1" order="desc" orderby="date"]
```