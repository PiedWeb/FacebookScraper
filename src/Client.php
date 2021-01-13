<?php

namespace PiedWeb\FacebookScraper;

use \Exception;
use PiedWeb\Curl\Request;

class Client
{
    public static string $cacheDir = ''; // default sys_get_temp_dir
    public static int $cacheExpir = 6000; // 100 minutes;
    protected static Request $client;
    public static string $userAgent = '';

    public static function bootClient()
    {
        if (! self::$cacheDir) {
            self::$cacheDir = sys_get_temp_dir();
        }

        self::$client = (new Request())
            ->setDefaultGetOptions()
            ->setDefaultSpeedOptions()
            ->setNoFollowRedirection();

        if (self::$userAgent) {
            self::$client->setUserAgent(self::$userAgent);
        } else {
            self::$client->setDesktopUserAgent();
        }
    }

    public static function getCacheFilePath(string $url, $cacheExpir = null): string
    {
        if (! self::$cacheDir) {
            throw new Exception('You must define `Client::$cacheDir`');
        }

        return self::$cacheDir.'/'.sha1(
            'fbs'.$url.($cacheExpir === null ? self::$cacheExpir : $cacheExpir).self::$userAgent
        );
    }

    public static function get(string $url): string
    {
        $cacheFile = self::getCacheFilePath($url);

        if (file_exists($cacheFile) && (self::$cacheExpir === 0 || (time() - filemtime($cacheFile)) < self::$cacheExpir)) {
            return file_get_contents($cacheFile);
        }

        self::$client->setUrl($url);

        $response = self::$client->exec();

        if (is_int($response)) {
            return '';
        }

        $response = $response->getContent();

        if (self::$client->getInfo(CURLINFO_HTTP_CODE) != 200) {
            return '';
        }
        //dd($cacheFile);
        file_put_contents($cacheFile, $response);
        file_put_contents(self::getCacheFilePath($url, 0), $response);

        return $response;
    }
}
