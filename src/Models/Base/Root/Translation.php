<?php

namespace Bonnier\Willow\Base\Models\Base\Root;

use Bonnier\Willow\Base\Models\Contracts\Root\TranslationContract;

class Translation implements TranslationContract
{
    private $translation;

    public function __construct(TranslationContract $translation)
    {
        $this->translation = $translation;
    }

    public function getId(): ?int
    {
        return $this->translation->getId();
    }

    public function getTitle(): ?string
    {
        return $this->translation->getTitle();
    }

    public function getLink(): ?string
    {
        return $this->translation->getLink();
    }
}
