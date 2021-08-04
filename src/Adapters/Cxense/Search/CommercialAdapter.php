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
    protected $orgPreFix;
    protected $label;
    protected $type;

    public function __construct(Document $document)
    {
        $this->orgPreFix = WpCxense::instance()->settings->getOrganisationPrefix();
        $this->type = $document->getField($this->orgPreFix . '-commercial-format');
        $this->label = $document->getField($this->orgPreFix . '-commercial-label');
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

    public function getLinkLabel(): ?string
    {
        return null;
    }

    public function getLink(): ?string
    {
        return null;
    }
}
