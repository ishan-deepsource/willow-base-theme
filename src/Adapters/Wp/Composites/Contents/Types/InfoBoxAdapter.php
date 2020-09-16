<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types;

use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\AbstractContentAdapter;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\InfoBoxContract;

/**
 * Class VideoAdapter
 *
 * @package \Bonnier\Willow\Base\Adapters\Wp
 */
class InfoBoxAdapter extends AbstractContentAdapter implements InfoBoxContract
{
    public function getTitle(): ?string
    {
        return array_get($this->acfArray, 'title') ?: null;
    }

    public function getBody(): ?string
    {
        return array_get($this->acfArray, 'body') ?: null;
    }

    public function getDisplayHint(): string
    {
        return array_get($this->acfArray, 'display_hint') ?: 'yellow';
    }
}
