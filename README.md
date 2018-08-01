# Willow Base Theme
A RESTful [WordPress](https://wordpress.org/) API Theme.

[League/Fractal](https://fractal.thephpleague.com/) is used to transform the data output in the API.

## API
There is extensive API documentation for the RESTful API here:
[Postman Documentation](https://documenter.getpostman.com/view/329406/RWMHLmwD)

The API is an extension of [WordPress REST API](https://developer.wordpress.org/rest-api/)

## Requirements
* PHP >= 7.1
* Composer

## WordPress installation
In your [Roots](https://roots.io/) [Bedrock](https://roots.io/bedrock/) installation
you can install the theme with composer.

Because Advanced Custom Fields isn't part of
[Packagist](https://packagist.org/) or [WordPress Packagist](https://wpackagist.org/)
you will need to add the repository to your main composer file, before requiring this theme.

You can find a guide here:
[ACF as a Composer Dependency](https://roots.io/guides/acf-pro-as-a-composer-dependency-with-encrypted-license-key/)

You will also need to add the github repository as a composer repository:
```
"repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/BenjaminMedia/willow-base-theme"
    }
]
```

Once that is set up, you can require the plugin as a composer package:
```
composer require bonnier/willow-base-theme
```

## Standalone installation
For development purposes, it can be useful to set the theme up on its own.

1. Install via git: `git clone https://github.com/BenjaminMedia/willow-base-theme`
or through your git gui (SourceTree, GitHub Desktop or other)
2. Navigate into the project folder: `cd willow-base-theme`
3. Copy the environment file: `cp .env.example .env`
4. Enter the ACF PRO license key into the environment file
5. Run `composer install`
6. All done

## Tests
Tests are using [Codeception](https://codeception.com/for/wordpress)'s framework for WordPress.

To run tests, execute the command `composer unit`
