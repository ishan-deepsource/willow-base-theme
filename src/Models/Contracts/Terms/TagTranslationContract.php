<?php

namespace Bonnier\Willow\Base\Models\Contracts\Terms;

interface TagTranslationContract
{
    public function getId(): ?int;
    public function getTitle(): ?string;
    public function getLink(): ?string;
}
