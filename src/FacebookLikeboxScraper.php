<?php

namespace PiedWeb\FacebookScraper;

class FacebookLikeboxScraper extends FacebookScraper
{
    protected function getPageUrl(): string
    {
        return  'https://www.facebook.com/plugins/likebox.php?href=https%3A%2F%2Fwww.facebook.com%2F'.$this->pageId.'&width=600&colorscheme=light&show_faces=true&border_color&stream=true&header=true&height=435&_fb_noscript=1';
    }

    public function __construct(string $id, string $extractor = '\PiedWeb\FacebookScraper\Extractor\LikeboxPostExtractor')
    {
        parent::__construct($id, $extractor);
    }
}
