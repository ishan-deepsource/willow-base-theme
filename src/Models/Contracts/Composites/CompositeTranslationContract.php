<?php

namespace Bonnier\Willow\Base\Models\Contracts\Composites;

interface CompositeTranslationContract
{
    public function getId(): ?int;
    public function getTitle(): ?string;
    public function getLink(): ?string;
}
