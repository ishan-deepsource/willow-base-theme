<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Root;

use Bonnier\Willow\Base\Models\Contracts\Root\TeaserContract;

abstract class AbstractTeaserAdapter implements TeaserContract
{
    const DEFAULT_TEASER = 'default';
    const SEO_TEASER = 'seo';
    const FACEBOOK_TEASER = 'facebook';
    const TWITTER_TEASER = 'twitter';

    protected $type;

    public function __construct($type)
    {
        switch (strtolower($type)) {
            case 'google':
            case self::SEO_TEASER:
                $this->type = 'seo_';
                break;
            case 'og':
            case 'fb':
            case self::FACEBOOK_TEASER:
                $this->type = 'fb_';
                break;
            case 'tw':
            case self::TWITTER_TEASER:
                $this->type = 'tw_';
                break;
            default:
                $this->type = '';
                break;
        }
    }

    public function getType(): string
    {
        switch ($this->type) {
            case 'seo_':
                return self::SEO_TEASER;
            case 'fb_':
                return self::FACEBOOK_TEASER;
            case 'tw_':
                return self::TWITTER_TEASER;
            default:
                return self::DEFAULT_TEASER;
        }
    }
}
