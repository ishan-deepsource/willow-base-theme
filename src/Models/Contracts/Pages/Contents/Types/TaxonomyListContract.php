<?php

namespace Bonnier\Willow\Base\Models\Contracts\Pages\Contents\Types;

use Bonnier\Willow\Base\Models\Contracts\Pages\Contents\ContentContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;
use Illuminate\Support\Collection;

interface TaxonomyListContract extends ContentContract
{
    public function getTitle(): ?string;
    public function getDescription(): ?string;
    public function getImage(): ?ImageContract;
    public function getLabel(): ?string;
    public function getDisplayHint(): ?string;
    public function getTaxonomy(): ?string;
    public function getTaxonomyList(): ?Collection;
}
