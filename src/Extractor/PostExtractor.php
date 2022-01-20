<?php

namespace PiedWeb\FacebookScraper\Extractor;

use DOMElement;
use League\HTMLToMarkdown\HtmlConverter;
use PiedWeb\FacebookScraper\Client;
use PiedWeb\FacebookScraper\FacebookScraper;
use Symfony\Component\DomCrawler\Crawler;

class PostExtractor implements ExtractorInterface
{
    const POST_SELECTOR = 'article';
    protected DOMElement $dom;
    protected string $pageId;

    public function __construct(DOMElement $dom, string $pageId)
    {
        $this->dom = $dom;
        $this->pageId = $pageId;
    }

    public function get(): ?array
    {
        return ! $this->getPostId() ? null : [
            'publish_time' => $this->getPublishTime(),
            'post_id' => $this->getPostId(),
            'text' => $this->getText(),
            'comment_number' => $this->getCommentNumber(),
            'like_number' => $this->getLikeNumber(),
            'images' => $this->getImagesThumb(),
            'images_hd' => $this->getImages(),
        ];
    }

    protected function getImages(): array
    {
        $imgs = (new Crawler($this->dom))->filterXPath("//*[contains(@href,'photos')]");

        $return = [];

        foreach ($imgs as $img) {
            if (! $img instanceof DOMElement)
                continue;
            $img = ImageExtractor::extractFromUrl('https://m.facebook.com'.$img->getAttribute('href'));
            if ($img) {
                $return[] = $img;
            }
        }

        return $return;
    }

    protected function getImagesThumb(): array
    {
        $imgs = (new Crawler($this->dom))->filter('.img[width=320]');

        $return = [];
        foreach ($imgs as $img) {
            if (! $img instanceof DOMElement)
                continue;
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
        $like = $likes ? $likes->eq(0)->text('0') : '0';

        preg_match('/[0-9]+/', $like, $match);

        return $match[0];
    }

    protected function getCommentNumber()
    {
        $likes = (new Crawler($this->dom))->filter('.cmt_def');
        $like = $likes ? $likes->eq(0)->text('0') : '0';

        preg_match('/[0-9]+/', $like, $match);

        return $match[0];
    }

    protected function getDataStore(): array
    {
        $dataStore = $this->dom->getAttribute('data-store');
        $dataStore = json_decode($dataStore, true);

        return $dataStore;
    }

    protected function getPublishTime() :int
    {
        preg_match('/\"publish_time\\"\:([0-9]*)/', $this->getDataStore()['linkdata'], $match);

        return (int) (isset($match[1]) ? $match[1] :  0);
    }

    protected function getPostId(): int
    {
        preg_match('/\"post_id\\"\:([0-9]+)/', $this->getDataStore()['linkdata'], $match);

        return (int) (isset($match[1]) ? $match[1] :  0);
    }
}
