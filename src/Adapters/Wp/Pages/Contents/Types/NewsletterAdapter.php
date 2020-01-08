<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Pages\Contents\Types;

use Bonnier\Willow\Base\Adapters\Wp\Pages\Contents\AbstractContentAdapter;
use Bonnier\Willow\Base\Models\Contracts\Pages\Contents\Types\NewsletterContract;

class NewsletterAdapter extends AbstractContentAdapter implements NewsletterContract
{
    public function getSourceCode(): ?int
    {
        return array_get($this->acfArray, 'source_code') ?: null;
    }

    public function getPermissionText(): ?string
    {
        return array_get($this->acfArray, 'permission_text') ?: null;
    }
}
