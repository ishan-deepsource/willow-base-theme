<?php

namespace Bonnier\Willow\Base\Models\Contracts\Root;

use DateTime;

interface SitemapItemContract
{
    public function getUrl(): string;

    public function getLastModified(): DateTime;
}
