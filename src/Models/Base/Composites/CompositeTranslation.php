<?php

namespace Bonnier\Willow\Base\Models\Base\Composites;

use Bonnier\Willow\Base\Models\Contracts\Composites\CompositeTranslationContract;

class CompositeTranslation implements CompositeTranslationContract
{
    protected $compositeTranslation;

    public function __construct(CompositeTranslationContract $compositeTranslation)
    {
        $this->compositeTranslation = $compositeTranslation;
    }

    public function getId(): ?int
    {
        return $this->compositeTranslation->getId();
    }

    public function getTitle(): ?string
    {
        return $this->compositeTranslation->getTitle();
    }

    public function getLink(): ?string
    {
        return $this->compositeTranslation->getLink();
    }
}
