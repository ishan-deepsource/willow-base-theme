<?php

namespace Bonnier\Willow\Base\Commands;

use Bonnier\Willow\Base\Helpers\Utils;
use Bonnier\Willow\Base\Repositories\SiteManager\SiteRepository;
use Bonnier\Willow\MuPlugins\Helpers\LanguageProvider;
use Illuminate\Support\Collection;
use WP_CLI_Command;

/**
 * Class BaseCmd
 */
class BaseCmd extends WP_CLI_Command
{
    protected function mapSites($callable)
    {
        $this->getSites()->each($callable);
    }

    protected function getSites(): Collection
    {

        return collect(LanguageProvider::getLanguageList())
            ->pluck('home_url')
            ->map(function ($homeUrl) {
                $domain = Utils::removeApiSubdomain($homeUrl);
                return SiteRepository::find_by_domain(parse_url($domain, PHP_URL_HOST));
            })->reject(function ($site) {
                return is_null($site);
            });
    }

    protected function getSite()
    {
        return $this->getSites()->first();
    }
}
