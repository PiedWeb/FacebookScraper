<?php

namespace PiedWeb\FacebookScraper\Extractor;

interface ExtractorInterface
{
    public function get(): ?array;
}
