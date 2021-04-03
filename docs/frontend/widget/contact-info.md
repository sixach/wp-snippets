# Contact Info Widget

```php
SixaSnippets\Frontend\Widget\Contact_Info( array $args = array() );
```

## Description

This widget allows you to display your location (address), and contact information along with an optional map view.

## Import

```php 
use SixaSnippets\Frontend\Widget\Contact_Info;
```

!> **Note:** Should be hooked to the [widgets_init](http://developer.wordpress.org/reference/hooks/widgets_init/) action hook.

## Parameters

- **args**
    - **label**
        - *(string) (Optional)* Formatted label of the widget component.
		- *Default value: `Contact Info`*
	- **description**
        - *(string) (Optional)* A help text will be shown below the widget title.
		- *Default value: `Display a link to your location, and contact information.`*
	- **defaults**
		- *(array) (Optional)* Default values for the widget properties.
		- **title**
			- *(string) (Optional)* Widget title.
			- *Default value: empty string*
		- **address**
			- *(string) (Optional)* Address location.
			- *Default value: empty string*
        - **showmap**
			- *(string) (Optional)* Whether to display a map along with the address location.
			- *Default value: `0 (false)`*
        - **phone**
			- *(string) (Optional)* Telephone number.
			- *Default value: empty string*
        - **email**
			- *(string) (Optional)* Email address.
			- *Default value: empty string*

## Usage

```php
add_action(
	'widgets_init',
	function() {
		register_widget(
			new Contact_Info(
				array(
					'label'       => __( 'Contact Info', '@@textdomain' ),
					'description' => __( 'Display a link to your location, and contact information.', '@@textdomain' ),
				)
			)
		);
	}
);
```

## Screenshot

![](../../assets/contact-info-widget.png ':size=30%')