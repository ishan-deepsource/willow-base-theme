<?php

namespace Bonnier\Willow\Base\Models\Contracts\Root;

interface AuthorContract
{
    public function getId(): ?int;

    public function getName(): ?string;

    public function getTitle(): ?string;

    public function getBiography(): ?string;

    public function getAvatar(): ?ImageContract;

    public function getUrl(): ?string;
}
