<?php

namespace Piedweb\FacebookScraper\Tests;

use PHPUnit\Framework\TestCase;
use PiedWeb\FacebookScraper\Client;
use PiedWeb\FacebookScraper\FacebookScraper;

class FacebookScraperTest extends TestCase
{
    public function testIt()
    {
        Client::$cacheDir = sys_get_temp_dir().'/com.github.piedweb.facebookscraper';
        if (! file_exists(Client::$cacheDir)) {
            mkdir(Client::$cacheDir);
        }

        $fbScraper = new FacebookScraper('Google');

        $posts = $fbScraper->getPosts();

        $this->assertTrue($posts[0]['publish_time'] > 0);
    }
}
