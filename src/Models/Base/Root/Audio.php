<?php

namespace Bonnier\Willow\Base\Models\Base\Root;

use Bonnier\Willow\Base\Models\Contracts\Root\AudioContract;

class Audio implements AudioContract
{
    protected $audio;

    public function __construct(AudioContract $audio)
    {
        $this->audio = $audio;
    }

    public function getId(): ?int
    {
        return $this->audio->getId();
    }

    public function getUrl(): ?string
    {
        return $this->audio->getUrl();
    }

    public function getTitle(): ?string
    {
        return $this->audio->getTitle();
    }

    public function getDescription(): ?string
    {
        return $this->audio->getDescription();
    }

    public function getCaption(): ?string
    {
        return $this->audio->getCaption();
    }

    public function getLanguage(): ?string
    {
        return $this->audio->getLanguage();
    }
}
