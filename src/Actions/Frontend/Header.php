<?php

namespace Bonnier\Willow\Base\Actions\Frontend;

use Bonnier\Willow\Base\Helpers\TrackingHelper;
use Bonnier\Willow\Base\Helpers\SocialMediaHelper;

class Header
{
    public function __construct()
    {
        TrackingHelper::tnsScripts();
        SocialMediaHelper::facebookMetaTags();
        SocialMediaHelper::facebookScript();
    }
}
