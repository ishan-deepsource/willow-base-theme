<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types;

use Bonnier\Willow\Base\Adapters\Wp\Composites\CompositeAdapter;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\ContentContract;

/**
 * Class AssociatedContentAdapter
 *
 * @package \Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types
 */
class AssociatedContentAdapter extends CompositeAdapter implements ContentContract
{
    protected $acfFields;

    public function __construct($acfArray) {
        $this->acfFields = $acfArray;
        parent::__construct($acfArray['composite'][0]);
    }

    public function getType() : string
    {
        return $this->acfArray['acf_fc_layout'] ?? '';
    }

    public function isLocked() : bool
    {
        return $this->acfArray['locked_content'] ?? false;
    }

    public function getStickToNext(): bool
    {
        return $this->acfArray['stick_to_next'] ?? false;
    }
}
