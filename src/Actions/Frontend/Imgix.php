<?php

namespace Bonnier\Willow\Base\Actions\Frontend;

class Imgix
{
    public function __construct()
    {
        add_action('wp_head', [$this, 'init']);
    }
    
    public function init()
    {
        echo '<!-- Imgix Metadata -->';
        $this->disableAutoInit();
        $this->setHost();
        $this->setClientHints();
        echo '<!-- /Imgix Metadata -->';
    }
    
    /**
     * Set the Imgix host to allow the use of parameters as an image attribute
     * instead of setting them as GET parameters on the image URL.
     */
    private function setHost()
    {
        echo sprintf("<meta property='ix:host' content='%s'>", getenv('AWS_S3_DOMAIN'));
    }
    
    /**
     * Make the browser send DPR, Width and Viewport-Width to Imgix.
     * This only works in Chrome as of February 2018.
     */
    private function setClientHints()
    {
        echo "<meta http-equiv='Accept-CH' content='DPR, Width, Viewport-Width'>";
    }
    
    private function disableAutoInit()
    {
        echo "<meta property='ix:autoInit' content='false'>";
    }
}
