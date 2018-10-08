<?php

namespace Bonnier\Willow\Base\Models\Base\Root;

use Bonnier\Willow\Base\Models\Contracts\Root\NativeVideoContract;

class NativeVideo implements NativeVideoContract
{
    protected $nativeVideo;

    public function __construct(NativeVideoContract $nativeVideo)
    {
        $this->nativeVideo = $nativeVideo;
    }

    public function getId(): ?int
    {
        return $this->nativeVideo->getId();
    }

    public function getUrl(): ?string
    {
        return $this->nativeVideo->getUrl();
    }

    public function getTitle(): ?string
    {
        return $this->nativeVideo->getTitle();
    }

    public function getDescription(): ?string
    {
        return $this->nativeVideo->getDescription();
    }

    public function getCaption(): ?string
    {
        return $this->nativeVideo->getCaption();
    }

    public function getLanguage(): ?string
    {
        return $this->nativeVideo->getLanguage();
    }
}
