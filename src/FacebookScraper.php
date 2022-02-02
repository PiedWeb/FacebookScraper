<?php

namespace PiedWeb\FacebookScraper;

use PiedWeb\FacebookScraper\Extractor\ExtractorInterface;
use Symfony\Component\DomCrawler\Crawler;

class FacebookScraper
{
    protected string $pageId;
    /** @var class-string<ExtractorInterface> */
    protected string $extractor;

    public function __construct(string $id, string $extractor = '\PiedWeb\FacebookScraper\Extractor\PostExtractor')
    {
        $this->pageId = $id;

        if (! class_exists($extractor) || false === is_subclass_of($extractor, ExtractorInterface::class)) {
            throw new \Exception('extractor must implement ExtractorInterface');
        }
        $this->extractor = $extractor;

        Client::bootClient();
    }

    protected function getPageUrl(): string
    {
        return  'https://m.facebook.com/'.$this->pageId.'/posts';
    }

    protected function get(): Crawler
    {
        $url = $this->getPageUrl();

        $response = Client::get($url);

        return new Crawler($response);
    }

    /**
     * @return array[]
     */
    public function getPosts(int $limit = 0): array
    {
        $crawler = $this->get();
        $posts = $crawler->filter($this->extractor::POST_SELECTOR);

        $return = [];
        foreach ($posts as $post) {
            if (! $currentPost = (new $this->extractor($post, $this->pageId))->get()) {
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
