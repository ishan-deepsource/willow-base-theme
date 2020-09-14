<?php

namespace Bonnier\Willow\Base\Models\Base\Root;

use Bonnier\Willow\Base\Adapters\Wp\Root\GuideMetaAdapter;
use Bonnier\Willow\Base\Models\Contracts\Root\GuideMetaContract;

class GuideMeta implements GuideMetaContract {
    protected $guideAdapter;

    /**
     * GuideMeta constructor.
     *
     * @param GuideMetaAdapter $guideAdapter
     */
    public function __construct(GuideMetaContract $guideAdapter)
    {
        $this->guideAdapter = $guideAdapter;
    }

    public function getDifficulty(): ?int
    {
        return $this->guideAdapter->getDifficulty();
    }

    public function getTimeRequired(): ?string
    {
        return $this->guideAdapter->getTimeRequired();
    }

    public function getPrice(): ?string
    {
        return $this->guideAdapter->getPrice();
    }
}
