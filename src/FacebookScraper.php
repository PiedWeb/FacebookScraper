<?php

namespace PiedWeb\FacebookScraper;

use PiedWeb\FacebookScraper\Extractor\PostExtractor;
use Symfony\Component\DomCrawler\Crawler;

class FacebookScraper
{
    protected string $facebookUrl = 'https://m.facebook.com';
    protected string $cacheDir = '/tmp';
    protected int $cacheExpir = 6000; // 100 minutes;
    protected $pageId;

    public function __construct(string $id)
    {
        $this->pageId = $id;

        Client::bootClient();
    }

    protected function get($page = 'posts/'): Crawler
    {
        $url = $this->facebookUrl.'/'.$this->pageId.'/'.$page;

        $response = Client::get($url);

        return new Crawler($response);
    }

    public function getPosts(): array
    {
        $crawler = $this->get('posts/');
        $posts = $crawler->filter('article');

        $return = [];
        foreach ($posts as $post) {
            $return[] = (new PostExtractor($post))->get();
        }

        return $return;
    }
}
