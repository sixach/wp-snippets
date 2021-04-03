# Social Network Widget

```php
SixaSnippets\Frontend\Widget\Social_Network( array $args = array() );
```

## Description

This widget lets you easily add icons for the most popular social networks in the sidebar.

## Import

```php 
use SixaSnippets\Frontend\Widget\Social_Network;
```

!> **Note:** Should be hooked to the [widgets_init](http://developer.wordpress.org/reference/hooks/widgets_init/) action hook.

## Parameters

- **args**
    - **label**
        - *(string) (Optional)* Formatted label of the widget component.
		- *Default value: `Social Network`*
	- **description**
        - *(string) (Optional)* A help text will be shown below the widget title.
		- *Default value: `Display a list of social media link icons in your sidebar.`*
	- **defaults**
		- *(array) (Optional)* Default values for the widget properties.
		- **title**
			- *(string) (Optional)* Widget title.
			- *Default value: empty string*
		- **target**
			- *(integer) (Optional)* Whether to open linked address(es) in a new window/tab.
			- *Default value: `0`*

## Usage

```php
add_action(
	'widgets_init',
	function() {
		register_widget(
			new Social_Network(
				array(
					'label'       => __( 'Social Network', '@@textdomain' ),
					'description' => __( 'Display a list of social media link icons in your sidebar.', '@@textdomain' ),
				)
			)
		);
	}
);
```

## Screenshot

![](../../assets/social-network-widget.png ':size=30%')

## Icons

Linking to any of the following sites from the social network widget will automatically display its icon in the sidebar area.

!> **Note:** Custom icons can be added with the use of the [sixa_social_network_widget_supported_icons](https://github.com/sixach/sixa-wp-snippets/blob/main/frontend/widget/class-social-network.php#L279) filter hook.

|            	|           	|            	|
|------------	|-----------	|------------	|
| 500px      	| Goodreads 	| Skype      	|
| Amazon     	| Google    	| Snapchat   	|
| Behance    	| GitHub    	| SoundCloud 	|
| CodePen    	| Instagram 	| Spotify    	|
| DeviantArt 	| Last.fm   	| Tumblr     	|
| Dribbble   	| LinkedIn  	| Twitch     	|
| Dropbox    	| Email     	| Twitter    	|
| Etsy       	| Meetup    	| Vimeo      	|
| Facebook   	| Medium    	| VK         	|
| RSS Feed   	| Pinterest 	| WordPress  	|
| Flickr     	| Pocket    	| Yelp       	|
| Foursquare 	| Reddit    	| YouTube    	|

<!-- Remove table heading. -->
<style>th{display:none;}</style>