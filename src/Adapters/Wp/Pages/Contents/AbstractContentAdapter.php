<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Pages\Contents;

use Bonnier\Willow\Base\Helpers\AcfOutput;
use Bonnier\Willow\Base\Models\Contracts\Pages\Contents\ContentContract;

abstract class AbstractContentAdapter implements ContentContract
{
    protected $acfArray;
    protected $acfOutput;

    public function __construct(array $acfArray)
    {
        $this->acfArray = $acfArray;
        $this->acfOutput = new AcfOutput($acfArray);
    }

    public function getType() : ?string
    {
        return array_get($this->acfArray, 'acf_fc_layout') ?: null;
    }
}
