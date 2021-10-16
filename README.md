# WP Snippets

This repository is a collection of useful functions for WordPress projects.

## Introduction

At Sixa, we strive to provide a top-notch and solid code base for all WordPress projects. 
In order to improve both our efficiency and consistency, we need to standardize what we 
use and how we use it.

This repository allows us to reuse initial functions and classes to make sure all projects 
can get up and running as quickly as possible while closing adhering to Sixa’s high-quality
coding standards.

## Installation

```bash
composer install sixach/wp-snippets
```

such that in `composer.json` you `require` the package
```JSON
{
    "require": {
        "sixach/wp-snippets": "^1.4.1"
    }
}
```

Make sure to use the latest version, the version in the example above might be outdated.

## Usage

In the file in which you wish to call a function or factory class from WP Snippets, simply
import the class and call it subsequently, e.g.

```PHP
<?php
namespace Package\Subpackage;

use Sixa_Snippets\Dashboard\Menu_Options as Menu_Options;

class My_Class {

    public function some_cool_function(): void {
        // The class imported from Sixa_Snippets
        new Menu_Options (
            array (
                array (
                    'default'     => 'no',
                    'type'        => 'checkbox',
                    'id'          => 'edit-menu-item-is-button',
                    'name'        => 'is_button',
                    'description' => __( 'Style this item as a CTA button? (Top-level only)', '@@textdomain' ),
                ),
            )
        );
    }
    
}
?>
```

### Documentation

For a more detailed documentation on the functionality included in this package, please refer
to the [about page of this repository](https://sixach.github.io/wp-snippets/#/).

## Notes

**Note 1**: Much of the functionality in this repository is intended to be optional depending on the needs of the project. E.g. `Breadcrumb` class.

**Note 2**: Presentation should be kept in the theme. Separating functionality from aesthetics makes long-term development, maintenance, and extensibility much easier.