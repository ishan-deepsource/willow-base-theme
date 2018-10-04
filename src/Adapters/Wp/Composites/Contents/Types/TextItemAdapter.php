<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types;

use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\AbstractContentAdapter;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\TextItemContract;

/**
 * Class ImageAdapter
 *
 * @package \Bonnier\Willow\Base\Adapters\Wp
 */
class TextItemAdapter extends AbstractContentAdapter implements TextItemContract
{
    public function getBody(): ?string
    {
        return array_get($this->acfArray, 'body') ?: null;
    }
}
