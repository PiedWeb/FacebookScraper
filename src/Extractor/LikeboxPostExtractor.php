<?php

namespace PiedWeb\FacebookScraper\Extractor;

use PiedWeb\FacebookScraper\Client;
use Symfony\Component\DomCrawler\Crawler;

class LikeboxPostExtractor extends PostExtractor
{
    const POST_SELECTOR = 'div[role="feed"] > div';

    protected function getImages(): array
    {
        $images = [];
        $jsonImages = (new Crawler($this->dom))->filter('script[type="application/ld+json"]');
        foreach ($jsonImages as $i) {
            $json = json_decode($i->nodeValue, true);
            if (! is_array($json) || ! isset($json['image']) || ! isset($json['image']['contentUrl'])) {
                continue;
            }
            Client::get($json['image']['contentUrl']);
            $images[] = Client::getCacheFilePath($json['image']['contentUrl']);
        }

        return $images;
    }

    protected function getImagesThumb(): array
    {
        return $this->getImages();
        /*
        $imgs = (new Crawler($this->dom))->filter('.scaledImageFitWidth');

        $return = [];
        foreach ($imgs as $img) {
            Client::get($img->getAttribute('src'));
            $return[] = Client::getCacheFilePath($img->getAttribute('src'));
        }

        return $return;/**/
    }

    protected function getLikeNumber(): int
    {
        $likes = (new Crawler($this->dom))->filter('.embeddedLikeButton');
        foreach ($likes as $like) {
            return intval($like->nodeValue);
        }
        return 0;
    }

    protected function getCommentNumber() : int
    {
        $likes = (new Crawler($this->dom))->filter('[title="Commenter"]');
        foreach ($likes as $like) {
            return intval($like->nodeValue);
        }

        return 0;
    }

    protected function getDataStore(): array
    {
        $dataStore = $this->dom->getAttribute('data-store');
        $dataStore = json_decode($dataStore, true);

        return $dataStore;
    }

    protected function getPublishTime(): int
    {
        preg_match('/abbr data-utime="([0-9]+)"/', $this->dom->ownerDocument->saveXML($this->dom), $match);

        return (int) ($match[1] ?? 0);
    }

    protected function getPostId(): int
    {
        preg_match('/posts\/([0-9]+)"/', $this->dom->ownerDocument->saveXML($this->dom), $match);

        return (int) ($match[1] ?? 0);
    }
}
