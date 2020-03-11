<?php

namespace Bonnier\Willow\Base\Models\Contracts\Pages\Contents\Types;

use Bonnier\Willow\Base\Models\Contracts\Composites\CompositeContract;
use Bonnier\Willow\Base\Models\Contracts\Pages\Contents\ContentContract;

interface QuoteTeaserContract extends ContentContract
{
    public function getQuote(): string;
    public function getAuthor(): ?string;
    public function getLinkLabel(): ?string;
    public function getLink(): ?string;
    public function getComposite(): ?CompositeContract;
}
