<?php

namespace Bonnier\Willow\Base\Models\Contracts\Pages;

interface PageTranslationContract
{
    public function getId(): ?int;
    public function getTitle(): ?string;
    public function getLink(): ?string;
}
