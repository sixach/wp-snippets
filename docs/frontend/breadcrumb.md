# Breadcrumb

```php
Sixa_Snippets\Frontend\Breadcrumb();
```

## Description

This method generates secondary navigation links back to each previous page the user navigated through and shows the user’s current location in a website.

## Import

```php 
use Sixa_Snippets\Frontend\Widget\Breadcrumb;
```

!> Best place to initialize this class is inside a template hook.

## Parameters

- **echo**
    - *(bool) (Optional)* Whether to echo or just return the output.
    - *Default value: `true`*

## Usage

```php
add_action( 'sixa_single_post_top', function() {
	$breadcrumb = new Breadcrumb();
	$breadcrumb->run(); // $echo = true;
} );
```