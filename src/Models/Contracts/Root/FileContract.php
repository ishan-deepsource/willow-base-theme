<?php

namespace Bonnier\Willow\Base\Models\Contracts\Root;

interface FileContract
{
    public function getId(): ?int;

    public function getUrl(): ?string;

    public function getTitle(): ?string;

    public function getDescription(): ?string;

    public function getCaption(): ?string;

    public function getLanguage(): ?string;
}
