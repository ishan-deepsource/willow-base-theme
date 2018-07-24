<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types;

use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\AbstractContentAdapter;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\LinkContract;

/**
 * Class LinkItemAdapter
 *
 * @package \Bonnier\Willow\Base\Adapters\Wp
 */
class LinkAdapter extends AbstractContentAdapter implements LinkContract
{
    public function getTitle(): string
    {
        return $this->acfArray['title'] ?? '';
    }

    public function getUrl(): string
    {
        return $this->acfArray['url'] ?? '';
    }

    public function getTarget(): string
    {
        return $this->acfArray['target'] ?? '';
    }
}
