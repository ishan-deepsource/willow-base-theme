<?php

namespace Bonnier\Willow\Base\Models\Base\Root;

use Bonnier\Willow\Base\Models\Contracts\Root\FileContract;

class File implements FileContract
{
    protected $file;

    public function __construct(FileContract $file)
    {
        $this->file = $file;
    }

    public function getId(): ?int
    {
        return $this->file->getId();
    }

    public function getUrl(): ?string
    {
        return $this->file->getUrl();
    }

    public function getTitle(): ?string
    {
        return $this->file->getTitle();
    }

    public function getDescription(): ?string
    {
        return $this->file->getDescription();
    }

    public function getCaption(): ?string
    {
        return $this->file->getCaption();
    }

    public function getLanguage(): ?string
    {
        return $this->file->getLanguage();
    }
}
