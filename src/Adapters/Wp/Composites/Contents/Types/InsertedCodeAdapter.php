<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types;

use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\AbstractContentAdapter;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\InsertedCodeContract;

/**
 * Class InsertedCodeAdapter
 *
 * @package \Bonnier\Willow\Base\Adapters\Wp
 */
class InsertedCodeAdapter extends AbstractContentAdapter implements InsertedCodeContract
{
    public function getCode(): string
    {
        return $this->acfArray['code'] ?? '';
    }
}
