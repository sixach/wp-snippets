# Taxonomy

```php
SixaSnippets\Dashboard\Taxonomy( array $args = array() );
```

## Description

A simple wrapper function for creating a taxonomy object based on the parameters given, and attaching it to a list of [post types](dashboard/post-type.md).

## Import

```php 
use SixaSnippets\Dashboard\Taxonomy;
```

!> **Note:** Should not be hooked before the [init](http://developer.wordpress.org/reference/hooks/init/) action hook.

## Parameters

- **$args**
	- **key**:
        - *(string) (Required)* Taxonomy key, must not exceed 32 characters.
	- **post_type**
        - *(string|array) (Optional)* Post type or array of post types with which the taxonomy should be associated.
        - *Default value: `post`*
	- **singular_name**
        - *(string) (Optional)* Name for one term of this taxonomy.
        - *Default value: `Category`*
	- **plural_name**
        - *(string) (Optional)* General name for the taxonomy, usually plural.
        - *Default value: `Categories`*
	- **args**
		- *(array) (Optional)* [All the parameters](http://developer.wordpress.org/reference/functions/register_taxonomy/) from the original registration method could be overwritten as needed.

## Example

```php
add_action(
	'init',
	function() {
		new Taxonomy(
			array(
				array(
					'key'           => 'brand',
					'post_type'     => 'product',
					'singular_name' => __( 'Brand', '@@textdomain' ),
					'plural_name'   => __( 'Brands', '@@textdomain' ),
				),
				array(
					'key'           => 'log',
					'post_type'     => array( 'post', 'page' ),
					'singular_name' => __( 'Log', '@@textdomain' ),
					'plural_name'   => __( 'Logs', '@@textdomain' ),
					'args'          => array(
						'publicly_queryable' => false,
					),
				),
			)
		);
	}
);
```