<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Pages\Contents\Types;

use Bonnier\Willow\Base\Adapters\Wp\Composites\CompositeAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Pages\Contents\AbstractContentAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Pages\Contents\Types\Partials\TeaserListHyperlink;
use Bonnier\Willow\Base\Adapters\Wp\Root\ImageAdapter;
use Bonnier\Willow\Base\Factories\DataFactory;
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

    public function getTitle(): ?string
    {
        return array_get($this->acfArray, 'title') ?: null;
    }

    public function getLabel(): ?string
    {
        return array_get($this->acfArray, 'label') ?: null;
    }

    public function getDescription(): ?string
    {
        return array_get($this->acfArray, 'description') ?: null;
    }

    public function getImage(): ?ImageContract
    {
        if ($imageArray = array_get($this->acfArray, 'image')) {
            $image = DataFactory::instance()->getPost($imageArray);
            return new Image(new ImageAdapter($image));
        }

        return null;
    }

    public function getLink(): ?HyperlinkContract
    {
        if ($link = array_get($this->acfArray, 'link')) {
            return new Hyperlink(new TeaserListHyperlink($this, $link));
        }

        return null;
    }

    public function getLinkLabel(): ?string
    {
        return array_get($this->acfArray, 'link_label') ?: null;
    }

    public function getDisplayHint(): ?string
    {
        return array_get($this->acfArray, 'display_hint') ?: null;
    }

    public function getTeasers(): ?Collection
    {
        if (!$this->teasers) {
            if ($composites = SortBy::getComposites($this->acfArray)) {
                $this->teasers = $composites->map(function (\WP_Post $post) {
                    $composite = DataFactory::instance()->getPost($post);
                    return new Composite(new CompositeAdapter($composite));
                });
            }
        }

        return $this->teasers;
    }
}
