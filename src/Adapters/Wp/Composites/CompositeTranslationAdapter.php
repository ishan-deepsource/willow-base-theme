<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Composites;

use Bonnier\Willow\Base\Repositories\WpModelRepository;
use Bonnier\Willow\Base\Models\Contracts\Composites\CompositeTranslationContract;
use Bonnier\Willow\Base\Traits\UrlTrait;

class CompositeTranslationAdapter implements CompositeTranslationContract
{
    use UrlTrait;

    protected $composite;

    public function __construct(\WP_Post $composite)
    {
        $this->composite = $composite;
    }

    public function getId(): ?int
    {
        return data_get($this->composite, 'ID') ?: null;
    }

    public function getTitle(): ?string
    {
        return data_get($this->composite, 'post_title') ?: null;
    }

    public function getLink(): ?string
    {
        return $this->getFullUrl(WpModelRepository::instance()->getPermalink($this->getId())) ?: null;
    }
}
