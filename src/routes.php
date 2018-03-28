<?php

Route::group([
    'prefix'    => config('static-image-cache.cache_path_prefix'),
    'namespace' => 'CampaigningBureau\LaravelStaticImageCache\Http\Controllers',
], function ()
{
    Route::get('/{slug}', ['as' => 'static-image-cache.image-proxy', 'uses' => 'ProxyController@image'])
         ->where('slug', '.*');
});
