# Plugin Options

```php
SixaSnippets\Dashboard\Options( array $args = array() );
```

## Description

This method output nonce, action, and a hook for adding additional fieldset for a plugin settings page.

## Import

```php 
use SixaSnippets\Dashboard\Options;
```

!> **Note:** Should not be hooked before the [admin_menu](http://developer.wordpress.org/reference/hooks/admin_menu/) action hook.

## Parameters

- **$args**
	- **labels**
	- *(array) (Optional)* An array of labels for the plugin options page.
	- *Default value: empty array*
		- **page_title**
			- *(string) (Optional)* The text to be displayed in the title tags of the page when the menu is selected.
			- *Default value: `Plugin Options`*
		- **menu_title**
			- *(string) (Optional)* The text to be used for the menu.
			- *Default value: `Sixa Options`*
	- **parent_slug**
		- *(string) (Optional)* The file name of a standard WordPress admin page. E.g. `options-general.php`, `themes.php`, etc.
		- *Default value: empty string*

> Not passing a valid `parent_slug` argument while initializing the class object, will register the menu as a top-level page instead.

## Example

```php
add_action(
	'admin_menu',
	function() {
		new Options(
			array(
				'labels'      => array(
					'page_title' => __( 'Plugin Options', '@@textdomain' ),
					'menu_title' => __( 'Sixa Options', '@@textdomain' ),
				),
				'parent_slug' => '',
			)
		);
	}
);
```

## Screenshot

![](../assets/options.png ':size=30%')