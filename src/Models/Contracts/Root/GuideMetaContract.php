<?php

namespace Bonnier\Willow\Base\Models\Contracts\Root;

interface GuideMetaContract
{
    public function getDifficulty(): ?int;
    public function getTimeRequired(): ?string;
    public function getPrice(): ?string;
}
