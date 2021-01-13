<?php

namespace PiedWeb\FacebookScraper;

use PiedWeb\Curl\Request;
use PiedWeb\FacebookScraper\Extractor\PostExtractor;
use Symfony\Component\DomCrawler\Crawler;

class Client
{
    protected static string $cacheDir = '/tmp';
    protected static int $cacheExpir = 6000; // 100 minutes;
    protected static Request $client;
    protected static string $userAgent = 'Chrome/76.0.3809.87 Safari/537.36';

    public static function bootClient()
    {
        self::$client = (new Request())
            ->setDefaultGetOptions()
            ->setUserAgent(self::$userAgent);
    }

    public static function getCacheFilePath(string $url): string
    {
        return self::$cacheDir.'/'.sha1('fbs'.$url);
    }

    public static function get(string $url): string
    {
        $cacheFile = self::getCacheFilePath($url);

        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < self::$cacheExpir) {
            return file_get_contents($cacheFile);
        }

        $response = self::$client->get($url);

        file_put_contents($cacheFile, $response);

        return $response;
    }
}
