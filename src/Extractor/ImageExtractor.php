<?php

namespace PiedWeb\FacebookScraper\Extractor;

use PiedWeb\FacebookScraper\Client;

class ImageExtractor
{
    public static function extractFromUrl($url)
    {
        $response = Client::get($url);

        preg_match('/property="og:image" content="([^"]+)"/', $response, $matches);

        if ($matches && isset($matches[1])) {
            $url = $matches[1];
            $url = str_replace('&amp;', '&', $url);
            if (Client::get($url)) {
                return Client::getCacheFilePath($url);
            }
        }
    }
}
