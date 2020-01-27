<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types;

use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\AbstractContentAdapter;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\NewsletterContract;

/**
 * Class VideoAdapter
 *
 * @package \Bonnier\Willow\Base\Adapters\Wp
 */
class NewsletterAdapter extends AbstractContentAdapter implements NewsletterContract
{
    public function getTitle(): ?string
    {
        return array_get($this->acfArray, 'title') ?: null;
    }

    public function getDescription(): ?string
    {
        return array_get($this->acfArray, 'description') ?: null;
    }

    public function getSourceCode(): ?int
    {
        return array_get($this->acfArray, 'source_code') ?: null;
    }

    public function getPermissionText(): ?string
    {
        return array_get($this->acfArray, 'permission_text') ?: null;
    }
}
