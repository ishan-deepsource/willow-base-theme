<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Root;

use Bonnier\Willow\Base\Models\Contracts\Root\HyperlinkContract;

class HyperlinkAdapter implements HyperlinkContract
{
    protected $image;

    public function __construct(\WP_Post $image)
    {
        $this->image = $image;
    }

    public function getTitle(): ?string
    {
        return get_post_meta($this->image->ID, '_wp_attachment_image_alt', true) ?: null;
    }

    public function getUrl(): ?string
    {
        return wp_get_attachment_image_url($this->image->ID, 'original') ?: null;
    }

    public function getRelationship(): ?string
    {
        return null;
    }

    public function getTarget(): ?string
    {
        return null;
    }
}
