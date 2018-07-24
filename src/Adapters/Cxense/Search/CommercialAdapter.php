<?php

namespace Bonnier\Willow\Base\Adapters\Cxense\Search;

use Bonnier\WP\Cxense\Parsers\Document;
use Bonnier\Willow\Base\Models\Contracts\Root\CommercialContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;

/**
 * Class DocumentAdapter
 *
 * @package \\${NAMESPACE}
 */
class CommercialAdapter implements CommercialContract
{
    protected $label;
    protected $type;

    public function __construct(Document $document)
    {
        $this->type = $document->getField('bod-commercial-format');
        $this->label = $document->getField('bod-commercial-label');
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function getLogo(): ?ImageContract
    {
        return null;
    }
}
