<?php

namespace PiedWeb\FacebookScraper\Extractor;

use DOMElement;
use PiedWeb\Curl\Request;
use Symfony\Component\DomCrawler\Crawler;
use League\HTMLToMarkdown\HtmlConverter;
use PiedWeb\FacebookScraper\FacebookScraper;
use PiedWeb\FacebookScraper\Client;

class PostExtractor
{
    protected DOMElement $dom;

    public function __construct(DOMElement $dom)
    {
        $this->dom = $dom;
    }

    public function get()
    {
        return [
            'publish_time' => $this->getPublishTime(),
            'post_id' => $this->getPostId(),
            'text' => $this->getText(),
            'comment_number' => $this->getCommentNumber(),
            'like_number' => $this->getLikeNumber(),
            'images' => $this->getImages(),
        ];
    }

    protected function getImages()
    {

        $imgs = (new Crawler($this->dom))->filter('.img[width=320]');

        $return = [];
        foreach ($imgs as $img)
        {
            Client::get($img->getAttribute('src'));
            $return[] = Client::getCacheFilePath($img->getAttribute('src'));
        }

        return $return;
    }
    protected function getText()
    {
        $paragraphs = (new Crawler($this->dom))->filter('p');

        $converter = new HtmlConverter();

        $return = '';
        foreach ($paragraphs as $paragraph) {
            $return .= $converter->convert($paragraph->nodeValue).PHP_EOL.PHP_EOL;
        }

        return trim($return);
    }

    protected function getLikeNumber()
    {
        $likes = (new Crawler($this->dom))->filter('.like_def');
            $like = $likes? $likes->eq(0)->text() : '0';

        preg_match('/[0-9]+/', $like, $match);

        return $match[0];
    }

    protected function getCommentNumber()
    {
        $likes = (new Crawler($this->dom))->filter('.cmt_def');
        $like = $likes ? $likes->eq(0)->text() : '0';

        preg_match('/[0-9]+/', $like, $match);

        return $match[0];
    }

    protected function getDataStore(): array
    {
        $dataStore = $this->dom->getAttribute('data-store');
        $dataStore = json_decode($dataStore, true);

        return $dataStore;
    }

    protected function getPublishTime()
    {
        preg_match('/\"publish_time\\"\:([0-9]*)/', $this->getDataStore()['linkdata'], $match);

        return $match[1];
    }

    protected function getPostId()
    {
        preg_match('/\"post_id\\"\:([0-9]*)/', $this->getDataStore()['linkdata'], $match);

        return $match[1];
    }
}
