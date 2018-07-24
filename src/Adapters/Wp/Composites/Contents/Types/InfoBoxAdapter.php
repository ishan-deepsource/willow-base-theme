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
    public function getTitle(): string
    {
        return $this->acfArray['title'] ?? '';
    }

    public function getBody(): string
    {
        return $this->acfArray['body'] ?? '';
    }
}
