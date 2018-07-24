<?php

namespace Bonnier\Willow\Base\Adapters\Cxense\Search\Partials;

use Bonnier\Willow\Base\Adapters\Cxense\Search\DocumentAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Root\AbstractTeaserAdapter;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;

class DocumentTeaserAdapter extends AbstractTeaserAdapter
{
    protected $document;

    /**
     * DocumentTeaserAdapter constructor.
     * @param $document
     * @param $type
     */
    public function __construct(DocumentAdapter $document, $type)
    {
        parent::__construct($type);
        $this->document = $document;
    }


    public function getTitle(): string
    {
        return $this->document->getTitle() ?? '';
    }

    public function getImage(): ?ImageContract
    {
        return $this->document->getLeadImage();
    }

    public function getDescription(): string
    {
        return $this->document->getDescription() ?? '';
    }
}
