<?php

namespace CampaigningBureau\LaravelStaticImageCache\Classes;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Filesystem\Filesystem;

class ImageProxy
{
    private $enabled;

    function __construct()
    {
        if (config('static-image-cache.enabled') === 'debug') {
            $this->enabled = !config('app.debug');
        } else {
            $this->enabled = config('static-image-cache.enabled');
        }
    }

    /**
     * get the url to the caching service
     *
     * @param string $url
     *
     * @return string
     */
    public function getUrl(string $url): string
    {
        if (!$this->enabled) {
            return $url;
        }
        // split the url into its parts
        $parts = parse_url($url);

        // collect the slug parts
        $slug = collect([]);
        // first use the host
        $slug->push($parts['host']);

        // extract the uri directory path
        $slug->push(dirname($parts['path']));

        // if available extract the query
        if (isset($parts['query'])) {
            $slug->push("q64_" . base64_encode($parts['query']));
        }

        // add the filename
        $slug->push(basename($parts['path']));

        // remove slashed at the beginning and end of the string
        $slug = $slug->map(function (string $part)
        {
            return trim($part, '/');
        })
                     ->implode('/');    // create an uri

        // create the laravel route
        return route('static-image-cache.image-proxy', ['slug' => $slug]);
    }

    /**
     * compute and return the original url from the given slug.
     * returns false if the slug could not be parsed as expected
     *
     * @param string $slug
     *
     * @return string|boolean
     */
    public function getOriginalUrl(string $slug): string
    {
        $matches = [];
        // extract the url parts from the uri
        preg_match('#^((?:.(?!q64_))+)(?:\/q64_([^\/]+))?\/(.+)$#', $slug, $matches);

        if (count($matches) < 4) {
            return false;
        }

        $url = "https://" . $matches[1];
        $url .= '/' . $matches[3];

        if (!empty($matches[2])) {
            $url .= '?' . base64_decode($matches[2]);
        }

        return $url;
    }

    /**
     * download the image from the web and return the response
     *
     * @param string $url
     *
     * @return bool|\Psr\Http\Message\ResponseInterface
     */
    private function downloadImage(string $url)
    {
        $guzzleClient = new Client();

        try {
            $response = $guzzleClient->get($url);

            return $response;
        } catch (ClientException $exception) {
            return false;
        }
    }

    /**
     * download the image, cache it and return it
     *
     * @param string $slug
     *
     * @return \Illuminate\Http\Response
     */
    public function getImageResponse(string $slug): \Illuminate\Http\Response
    {
        // compute original url from the slug
        $url = $this->getOriginalUrl($slug);

        if (!$url) {
            return response('invalid image');
        }

        // get image
        $response = $this->downloadImage($url);

        // check for a valid response
        if (!$response) {
            return response('could not download image');
        }

        // cache the image
        $this->cacheImage($slug, $response);

        return response((string)$response->getBody(), $response->getStatusCode(), $response->getHeaders());
    }

    /**
     * cache the image inside the defined caching directory.
     *
     * @param string                              $slug
     * @param \Psr\Http\Message\ResponseInterface $response
     */
    private function cacheImage($slug, $response)
    {
        $filesystem = \App::make(Filesystem::class);

        $filename = public_path(config('static-image-cache.cache_path_prefix') . "/" . $slug);

        $file = $response->getBody();
        $path = dirname($filename);

        if (!$filesystem->isDirectory($path)) {
            $filesystem->makeDirectory($path, 0777, true);
        }

        $filesystem->put($filename, $file);
    }

    /**
     * statifies all image urls, whose domain matches one of the configured.
     *
     * @param string $text
     *
     * @return mixed|string
     */
    public function statifyText($text)
    {
        $text = collect(config('static-image-cache.statify_domains'))->reduce(function ($text, $domain)
        {
            return preg_replace_callback(
                '#(https?:)?\/\/' . str_replace('.', '\.', $domain) . '\/(.+)\.(jpe?g|png|webp|gif|svg)#i',
                function ($matches)
                {
                    return $this->getUrl($matches[0]);
                },
                $text
            );
        }, $text);

        return $text;
    }
}
