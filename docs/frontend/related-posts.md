# Related Posts

```php
SixaSnippets\frontend\Related_Posts();
```

## Description

This method uses a given taxonomy to automatically generate and pull relevant content from the determined post-type and display it at the bottom of the single post view or any hook from the single page template.

## Import

```php
use SixaSnippets\frontend\Related_Posts;
```

!> Best place to initialize this class is inside the single post/page view template hook.

## Parameters

- **taxonomy**
    - *(string) (Required)* Post-type taxonomy slug.
    - *Default value: `category`*
- **number**
    - *(integer) (Required)* Total number of posts to retrieve and display.
    - *Default value: `3`*
- **show_date**
    - *(integer|bool) (Optional)* Whether to display post published date.
    - *Default value: `1 (true)`*
- **show_author**
    - *(integer|bool) (Optional)* Whether to display post author name.
    - *Default value: `1 (true)`*
- **show_thumb**
    - *(integer|bool) (Optional)* Whether to display post featured image.
    - *Default value: `1 (true)`*
- **show_categories**
    - *(integer|bool) (Optional)* Whether to display blog post category list.
    - *Default value: `1 (true)`*
- **show_excerpt**
    - *(integer|bool) (Optional)* Whether to display blog post excerpt.
    - *Default value: `1 (true)`*
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

## Usage

```php
// Without parameters.
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