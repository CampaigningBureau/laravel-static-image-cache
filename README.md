# laravel-static-image-cache
> store/cache external images as a static file

## Setup

Add the service provider to the `app.php` provider array
```php
[
    CampaigningBureau\LaravelStaticImageCache\Provider\LaravelStaticImageCacheProvider::class,
]
```

## Documentation

Replaces URIs by a Caching Route.

When the caching route is called, the original image is cached and returned.

If the requested file was already cached, it is instead directly returned by the webserver.


## Usage

This Package provides two helper functions: `static_image` and `statify_text`

### static_image

Just use the `static_image`-helper to generate the static file url for a given image url.

```blade
<img src="{{ static_image('https://images.domain.com/my-image.jpg') }}" alt="An external image">
```

### statify_text

## Clear the files
To clear all the files manually you can use an artisan task.
```bash
php artisan static-image-cache:clear
```

