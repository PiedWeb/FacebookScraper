<?php

namespace Piedweb\FacebookScraper\Tests;

use PHPUnit\Framework\TestCase;
use PiedWeb\FacebookScraper\FacebookScraper;

class FacebookScraperTest extends TestCase
{
    public function testIt()
    {
        $fbScraper = new FacebookScraper('Google');

        $posts = $fbScraper->getPosts();

        $this->assertTrue($posts[0]['publish_time'] > 0);
    }
}
