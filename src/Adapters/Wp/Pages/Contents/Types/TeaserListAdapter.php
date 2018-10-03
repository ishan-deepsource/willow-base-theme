<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Pages\Contents\Types;

use Bonnier\Willow\Base\Adapters\Wp\Composites\CompositeAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Pages\Contents\AbstractContentAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Pages\Contents\Types\Partials\TeaserListHyperlink;
use Bonnier\Willow\Base\Adapters\Wp\Root\ImageAdapter;
use Bonnier\Willow\Base\Models\Base\Composites\Composite;
use Bonnier\Willow\Base\Models\Base\Root\Hyperlink;
use Bonnier\Willow\Base\Models\Base\Root\Image;
use Bonnier\Willow\Base\Models\Contracts\Pages\Contents\Types\TeaserListContract;
use Bonnier\Willow\Base\Models\Contracts\Root\HyperlinkContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;
use Bonnier\WP\ContentHub\Editor\Helpers\SortBy;
use Illuminate\Support\Collection;

class TeaserListAdapter extends AbstractContentAdapter implements TeaserListContract
{
    protected $teasers;

    public function __construct($acfWidget)
    {
        parent::__construct($acfWidget);
    }

    public function getTitle(): ?string
    {
        return $this->acfArray['title'] ?? null;
    }

    public function getDescription(): ?string
    {
        return $this->acfArray['description'] ?? null;
    }

    public function getBackgroundImage(): ?ImageContract
    {
        if (($imageId = $this->acfArray['background_image'] ?? null) && $image = get_post($imageId)) {
            return new Image(new ImageAdapter($image));
        }

        return null;
    }

    public function getLink(): ?HyperlinkContract
    {
        if ($link = $this->acfArray['link'] ?? null) {
            return new Hyperlink(new TeaserListHyperlink($this, $link));
        }

        return null;
    }

    public function getDisplayHint(): ?string
    {
        return $this->acfArray['display_hint'] ?? null;
    }

    public function getTeasers(): ?Collection
    {
        if (!$this->teasers) {
            $this->teasers = SortBy::getComposites($this->acfArray);
        }

        return $this->teasers;
    }
}
