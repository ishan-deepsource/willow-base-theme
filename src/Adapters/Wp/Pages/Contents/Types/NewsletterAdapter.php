<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Pages\Contents\Types;

use Bonnier\Willow\Base\Adapters\Wp\Pages\Contents\AbstractContentAdapter;
use Bonnier\Willow\Base\Models\Contracts\Pages\Contents\Types\NewsletterContract;

class NewsletterAdapter extends AbstractContentAdapter implements NewsletterContract
{
    public function getSourceCodeCheckbox(): ?bool
    {
        return array_get($this->acfArray, 'manual_source_code') ?: null;
    }

    public function getSourceCode(): ?int
    {
        return $this->getManualSourceCodeEnabled() ? array_get($this->acfArray, 'source_code') : null;
    }

    public function getPermissionText(): ?string
    {
        return $this->getManualSourceCodeEnabled() ? array_get($this->acfArray, 'permission_text') : null;
    }
}
