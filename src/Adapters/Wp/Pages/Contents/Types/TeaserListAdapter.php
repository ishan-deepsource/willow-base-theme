<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Pages\Contents\Types;

use Bonnier\Willow\Base\Adapters\Wp\Composites\CompositeAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Pages\Contents\AbstractContentAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Pages\Contents\Types\Partials\TeaserListHyperlink;
use Bonnier\Willow\Base\Adapters\Wp\Root\ImageAdapter;
use Bonnier\Willow\Base\Repositories\WpModelRepository;
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
    protected $page;
    protected $totalPages;
    protected $totalTeasers;
    protected $perPage;
    protected $parentId;

    public function __construct(array $acfArray)
    {
        parent::__construct($acfArray);
        $this->page = 1;
    }

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
            $image = WpModelRepository::instance()->getPost($imageArray);
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

    public function canPaginate(): bool
    {
        return array_get($this->acfArray, 'pagination') ?: false;
    }

    public function getTeasers(): ?Collection
    {
        if (!$this->teasers) {
            if ($result = SortBy::getComposites($this->acfArray, $this->page)) {
                $this->totalPages = $result['pages'] ?? 0;
                $this->totalTeasers = $result['total'] ?? 0;
                $this->perPage = $result['per_page'] ?? 0;
                $this->teasers = $result['composites']->map(function (\WP_Post $post) {
                    $composite = WpModelRepository::instance()->getPost($post);
                    return new Composite(new CompositeAdapter($composite));
                });
            }
        }

        return $this->teasers;
    }

    public function setPage(int $page): TeaserListContract
    {
        if ($this->canPaginate()) {
            $this->page = $page;
        }
        return $this;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getTotalTeasers(): ?int
    {
        return $this->totalTeasers;
    }

    public function getTotalPages(): ?int
    {
        return $this->totalPages;
    }

    public function getTeasersPerPage(): ?int
    {
        return $this->perPage;
    }

    public function getNextCursor(): ?string
    {
        if ($this->page >= $this->totalPages) {
            return null;
        }

        return base64_encode(json_encode([
            'parent_id' => $this->parentId,
            'page' => $this->page + 1,
        ]));
    }

    public function getPreviousCursor(): ?string
    {
        if ($this->page <= 1) {
            return null;
        }

        return base64_encode(json_encode([
            'parent_id' => $this->parentId,
            'page' => $this->page - 1,
        ]));
    }

    public function getCurrentCursor(): ?string
    {
        return base64_encode(json_encode([
            'parent_id' => $this->parentId,
            'page' => $this->page,
        ]));
    }

    public function setParentId(int $parentId): ?TeaserListContract
    {
        $this->parentId = $parentId;

        return $this;
    }
}
