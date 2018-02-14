# laravel-static-image-cache
> store/cache external images as a static file

## Setup

Add the service provider to the `app.php` provider array
```php
[
    CampaigningBureau\LaravelStaticImageCache\Provider\LaravelStaticImageCacheProvider::class,
]
```


## Usage
Just use the `staticImage`-helper to generate the static file url

```blade
<img src="{{ staticImage('https://images.domain.com/my-image.jpg') }}" alt="An external image">
```

## Clear the files
To clear all the files manually you can use an artisan task.
```bash
php artisan static-image-cache:clear
```
