<?php

if (!function_exists('static_image')) {
    /**
     * @param string $url
     *
     * @return string
     */
    function static_image(string $url): string
    {
        return app('static-image-cache')->getUrl($url);
    }
}

if (!function_exists('statify_text')) {
    /**
     * @param string $text
     *
     * @return string
     */
    function statify_text(string $text): string
    {
        return app('static-image-cache')->statifyText($text);
    }
}