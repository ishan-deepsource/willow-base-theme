<?php

namespace Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\ContentContract;

interface LeadParagraphContract extends ContentContract
{
    public function getTitle(): ?string;

    public function getDescription(): ?string;

    public function getTextBlock(): ?string;

    public function getDisplayHint(): string;
}
