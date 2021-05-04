# WP Snippets

This scaffold is the starting point for all Sixa WordPress projects.

## Introduction

At Sixa, we strive to provide a top-notch and solid code base for all WordPress projects. In order to improve both our efficiency and consistency, we need to standardize what we use and how we use it.

This repository allows us to reuse initial functions and classes to make sure all projects can get up and running as quickly as possible while closing adhering to Sixa’s high-quality coding standards.

## Installation / Composer Setup

This repository can be installed as a package using Composer. To do so, add the
full name `sixa/wp-snippets` to the dependencies in your `composer.json`. Additionally,
you will need to define the repository in `composer.json` because we are installing the
package from GitHub.
```json
{
    "name": "sixa/your-project",
    "require": {
        "sixa/wp-snippets": "dev-main"
    },
    "repositories": [
        {
            "type": "vcs",
            "url":  "git@github.com:sixach/sixa-wp-snippets.git"
        }
    ]
}
```

The repository can be installed from [*tagged*](https://git-scm.com/book/en/v2/Git-Basics-Tagging)
as well as *untagged* branches. A tagged branch is typically a release branch, 
e.g. `1.0.0`, `1.0.0-beta`, and so on.
An untagged branch uses the name of the branch prefixed with `dev-` as its version.
For instance, to use the `main` branch, you need to use version `dev-main` in your
dependency in your `composer.json` (see example above).

## Notes

**Note 1**: Much of the functionality in this repository is intended to be optional depending on the needs of the project. E.g. [Breadcrumb](frontend/breadcrumb.md) class.

**Note 2**: Presentation should be kept in the theme. Separating functionality from aesthetics makes long-term development, maintenance, and extensibility much easier.