<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Root;

use Bonnier\Willow\Base\Models\Contracts\Root\SitemapItemContract;
use Bonnier\WP\Sitemap\Models\Sitemap;
use DateTime;

class SitemapAdapter implements SitemapItemContract
{
    private $sitemap;

    public function __construct(Sitemap $sitemap)
    {
        $this->sitemap = $sitemap;
    }


    public function getUrl(): ?string
    {
        return $this->sitemap->getUrl();
    }

    public function getLastModified(): DateTime
    {
        return $this->sitemap->getModifiedAt();
    }
}
