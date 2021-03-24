# Recent Posts Widget

```php
SixaSnippets\Frontend\Widget\Recent_Posts( array $args = array() );
```

## Description

This widget displays your most recent posts in the sidebar, making it easy for the readers to see what’s new on published on the blog.

## Import

```php 
use SixaSnippets\Frontend\Widget\Recent_Posts;
```

!> **Note:** Should be hooked to the [widgets_init](http://developer.wordpress.org/reference/hooks/widgets_init/) action hook.

## Parameters

- **args**
    - **label**
        - *(string) (Optional)* Formatted label of the widget component.
		- *Default value: `Recent Posts`*
	- **description**
        - *(string) (Optional)* A help text will be shown below the widget title.
		- *Default value: `Your site’s recent blog posts.`*
	- **defaults**
		- *(array) (Optional)* Default values for the widget properties.
		- **title**
			- *(string) (Optional)* Widget title.
			- *Default value: `Recent Posts`*
		- **number**
			- *(integer) (Required)* Total number of posts to retrieve and display.
			- *Default value: `4`*
		- **show_date**
			- *(bool) (Optional)* Whether to display post published date.
			- *Default value: `0 (false)`*
		- **show_author**
			- *(bool) (Optional)* Whether to display post author name.
			- *Default value: `0 (false)`*
		- **show_thumb**
			- *(bool) (Optional)* Whether to display post featured image.
			- *Default value: `0 (false)`*

## Example

```php
add_action(
	'widgets_init',
	function() {
		register_widget(
			new Recent_Posts(
				array(
					'label'       => __( 'Recent Posts', '@@textdomain' ),
					'description' => __( 'Display your most recent posts in your sidebar.', '@@textdomain' ),
				)
			)
		);
	}
);
```

## Screenshot

![](../../assets/recent-posts-widget.png ':size=30%')