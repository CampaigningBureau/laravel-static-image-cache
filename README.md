# laravel-static-image-cache

## Changelog

v7.0 requires Laravel 7 and at least PHP 7.2.5

v5.0 requires Laravel 6.0 and at least PHP 7.2

Use `"campaigningbureau/laravel-static-image-cache": "^4.0"` for Laravel < 6.0

---

v4.0 requires Laravel 5.8 and at least PHP 7.1.3.

Use `"campaigningbureau/laravel-static-image-cache": "^3.0"` for Laravel >= 5.6 and Laravel < 5.8

---

v3.0 requires Laravel 5.6 and at least PHP 7.1.3.

Use `"campaigningbureau/laravel-static-image-cache": "^2.0"` for Laravel < 5.6

---

> store/cache external images as a static file

Replaces URIs by a proxy route.

When this proxy route is called, the original image is cached inside the configured directory and returned.

If the requested file was already cached, it is instead directly returned by the webserver.

## Installation

```
composer require campaigningbureau/laravel-static-image-cache
```

## Setup

Add the service provider to the `app.php` provider array
```php
/*
 * Package Service Providers...
 */
CampaigningBureau\LaravelStaticImageCache\Provider\LaravelStaticImageCacheProvider::class,
```

## Usage

This Package provides two helper functions: `static_image` and `statify_text`

### static_image

The `static_image`-helper can be used to generate the static file url for a given image url.

```blade
<img src="{{ static_image('https://images.domain.com/my-image.jpg') }}" alt="An external image">
```

### statify_text

This helper function automatically statifies images from all domains that are configured in the `statify_domains` config entry inside the given string.

Usage:

```blade
if (function_exists('statify_text')) {
    $text = statify_text($text);
}
```

## Clear the files
To clear all cached files manually you can use an artisan task.
```bash
php artisan static-image-cache:clear
```

## Configuration

- `enabled`: defines, if proxying and storing of the images is activated. Can be set to `true`, `false` or `debug` (If `debug` the package will synchronise the flag with the `app.debug` config value)

- `cache_path_prefix`: The path prefix relative to `public_path`. This is where the images will be stored. This path will also be used as the proxy-url prefix.

- `statify_domains`: Holds an array of all domains that will be statified when calling the `statify_text` function.

## Upgrade guide

### Fom v1.x to v2.0

all usages of `staticImage()` need to be replaced by the new `static_image()` function.
