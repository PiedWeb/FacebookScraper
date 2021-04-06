<?php

namespace PiedWeb\FacebookScraper;

use PiedWeb\FacebookScraper\Extractor\PostExtractor;
use Symfony\Component\DomCrawler\Crawler;

class FacebookScraper
{
    public static string $facebookUrl = 'https://m.facebook.com';
    protected $pageId;

    public function __construct(string $id)
    {
        $this->pageId = $id;

        Client::bootClient();
    }

    protected function get($page = 'posts/'): Crawler
    {
        $url = self::$facebookUrl.'/'.$this->pageId.'/'.$page;

        $response = Client::get($url);

        return new Crawler($response);
    }

    public function getPosts(int $limit = 0): array
    {
        $crawler = $this->get('posts/');
        $posts = $crawler->filter('article');

        $return = [];
        foreach ($posts as $post) {
            if (! $currentPost = (new PostExtractor($post))->get()) {
                continue;
            }

            $return[] = $currentPost;


            if ($limit !== 0 && count($return) == $limit) {
                break;
            }
        }

        return $return;
    }
}
