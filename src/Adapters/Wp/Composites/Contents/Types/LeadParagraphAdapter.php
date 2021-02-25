<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types;

use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\AbstractContentAdapter;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\LeadParagraphContract;

class LeadParagraphAdapter extends AbstractContentAdapter implements LeadParagraphContract
{
    public function getTitle(): ?string
    {
        return array_get($this->acfArray, 'title') ?: null;
    }

    public function getDescription(): ?string
    {
        return array_get($this->acfArray, 'description') ?: null;
    }

    public function getTextBlock(): ?string
    {
        return array_get($this->acfArray, 'text_block') ?: null;
    }

    public function getDisplayHint(): string
    {
        return array_get($this->acfArray, 'display_hint') ?: 'default';
    }
}
