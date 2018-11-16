<?php

namespace Bonnier\Willow\Base\Models\Contracts\Root;

interface TranslationContract
{
    public function getId(): ?int;
    public function getTitle(): ?string;
    public function getLink(): ?string;
}
