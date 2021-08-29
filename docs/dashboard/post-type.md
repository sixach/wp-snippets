# Post Type

```php
SixaSnippets\dashboard\Post_Type( array $args = array() );
```

## Description

A simple wrapper function for registering a custom post type object based on the parameters given. Also, any taxonomy connections should be registered via the [taxonomies](dashboard/taxonomy.md) argument to ensure consistency when hooks such as `parse_query` or `pre_get_posts` are used.

## Import

```php 
use SixaSnippets\Dashboard\Post_Type;
```

!> **Note:** Should not be hooked before the [init](http://developer.wordpress.org/reference/hooks/init/) action hook.

## Parameters

- **args**
	- **key**:
        - *(string) (Required)* Post type key. Must not exceed 20 characters and may only contain lowercase alphanumeric characters, dashes, and underscores.
	- **singular_name**
        - *(string) (Optional)* Name for one post of this post type.
        - *Default value: `Post`*
	- **plural_name**
        - *(string) (Optional)* General name for the post type, usually plural.
        - *Default value: `Posts`*
	- **args**
		- *(array) (Optional)* [All the parameters](http://developer.wordpress.org/reference/functions/register_post_type/) from the original registration method could be overwritten as needed.
	- **taxonomies**
		- *(array) (Optional)* Attaching additional [taxonomy](dashboard/taxonomy.md) objects to this post type.

## Usage

```php
add_action(
	'init',
	function() {
		new Post_Type(
			array(
				array(
					'key'           => 'docs',
					'singular_name' => __( 'Document', '@@textdomain' ),
					'plural_name'   => __( 'Documents', '@@textdomain' ),
					'args'          => array(
						'menu_icon' => 'dashicons-book',
					),
					'taxonomies'    => array(
						array(
							'key'  => 'category',
							'args' => array(
								'publicly_queryable' => false,
							),
						),
					),
				),
				array(
					'key'           => 'logs',
					'singular_name' => __( 'Log', '@@textdomain' ),
					'plural_name'   => __( 'Logs', '@@textdomain' ),
				),
			)
		);
	}
);
```