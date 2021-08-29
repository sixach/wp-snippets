# Reusable Post Widget

```php
SixaSnippets\frontend\widget\Reusable_Post( array $args = array() );
```

## Description

This widget displays a selected [Reusable Blocks](https://wordpress.org/news/2021/02/gutenberg-tutorial-reusable-blocks/) post in the sidebar.

## Import

```php 
use SixaSnippets\Frontend\Widget\Reusable_Post;
```

!> **Note:** Should be hooked to the [widgets_init](http://developer.wordpress.org/reference/hooks/widgets_init/) action hook.

## Parameters

- **args**
    - **label**
        - *(string) (Optional)* Formatted label of the widget component.
		- *Default value: `Reusable Post`*
	- **description**
        - *(string) (Optional)* A help text will be shown below the widget title.
		- *Default value: `Your site’s reusable blocks post.`*
	- **defaults**
		- *(array) (Optional)* Default values for the widget properties.
		- **title**
			- *(string) (Optional)* Widget title.
			- *Default value: empty string*
		- **post_id**
			- *(integer) (Required)* Reusable post ID.
			- *Default value: empty string*

## Usage

```php
add_action(
	'widgets_init',
	function() {
		register_widget(
			new Reusable_Post(
				array(
					'label'       => __( 'Reusable Post', '@@textdomain' ),
					'description' => __( 'Display a selected "Reusable Blocks" post in your sidebar.', '@@textdomain' ),
				)
			)
		);
	}
);
```

## Screenshot

![](../../assets/reusable-post-widget.png ':size=30%')