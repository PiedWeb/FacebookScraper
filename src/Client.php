<?php

namespace PiedWeb\FacebookScraper;

use \Exception;
use PiedWeb\Curl\Request;

class Client
{
    public static string $cacheDir = ''; // default sys_get_temp_dir
    public static int $cacheExpir = 6000; // 100 minutes;
    protected static Request $client;
    public static string $userAgent = 'Chrome/76.0.3809.87 Safari/537.36';

    public static function bootClient()
    {
        if (! self::$cacheDir) {
            self::$cacheDir = sys_get_temp_dir();
        }

        self::$client = (new Request())
            ->setDefaultGetOptions()
            ->setUserAgent(self::$userAgent);
    }

    public static function getCacheFilePath(string $url): string
    {
        if (! self::$cacheDir) {
            throw new Exception('You must define `Client::$cacheDir`');
        }
        
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
