<?php

namespace Bonnier\Willow\Base\Models\Base\Pages\Contents\Types;

use Bonnier\Willow\Base\Models\Base\Pages\Contents\AbstractContent;
use Bonnier\Willow\Base\Models\Contracts\Pages\Contents\Types\TeaserListContract;
use Bonnier\Willow\Base\Models\Contracts\Root\HyperlinkContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;
use Bonnier\Willow\Base\Models\Contracts\Utilities\WidgetPaginationContract;
use Illuminate\Support\Collection;

/**
 * Class TeaserList
 * @package Bonnier\Willow\Base\Models\Base\Pages\Contents\Types
 * @property TeaserListContract $model
 */
class TeaserList extends AbstractContent implements TeaserListContract
{
    public function getTitle(): ?string
    {
        return $this->model->getTitle();
    }

    public function getLabel(): ?string
    {
        return $this->model->getLabel();
    }

    public function getDescription(): ?string
    {
        return $this->model->getDescription();
    }

    public function getImage(): ?ImageContract
    {
        return $this->model->getImage();
    }

    public function getLink(): ?HyperlinkContract
    {
        return $this->model->getLink();
    }

    public function getLinkLabel(): ?string
    {
        return $this->model->getLinkLabel();
    }

    public function getDisplayHint(): ?string
    {
        return $this->model->getDisplayHint();
    }

    public function canPaginate(): bool
    {
        return $this->model->canPaginate();
    }

    public function getTeasers(): ?Collection
    {
        return $this->model->getTeasers();
    }

    public function getCurrentPage(): int
    {
        return $this->model->getCurrentPage();
    }

    public function setCurrentPage(int $page): WidgetPaginationContract
    {
        return $this->model->setCurrentPage($page);
    }

    public function getTotalItems(): ?int
    {
        return $this->model->getTotalItems();
    }

    public function setTotalItems(int $items): WidgetPaginationContract
    {
        return $this->model->setTotalItems($items);
    }

    public function getTotalPages(): ?int
    {
        return $this->model->getTotalPages();
    }

    public function setTotalPages(int $pages): WidgetPaginationContract
    {
        return $this->model->setTotalPages($pages);
    }

    public function getItemsPerPage(): ?int
    {
        return $this->model->getItemsPerPage();
    }

    public function setItemsPerPage(int $items): WidgetPaginationContract
    {
        return $this->model->setItemsPerPage($items);
    }

    public function getItemCount(): ?int
    {
        return $this->model->getItemCount();
    }

    public function setItemCount(int $items): WidgetPaginationContract
    {
        return $this->model->setItemCount($items);
    }

    public function getNextCursor(): ?string
    {
        return $this->model->getNextCursor();
    }

    public function getPreviousCursor(): ?string
    {
        return $this->model->getPreviousCursor();
    }

    public function getCurrentCursor(): ?string
    {
        return $this->model->getCurrentCursor();
    }

    public function getParentId(): ?int
    {
        return $this->model->getParentId();
    }

    public function setParentId(int $parentId): WidgetPaginationContract
    {
        return $this->model->setParentId($parentId);
    }

    public function getParentType(): ?string
    {
        return $this->model->getParentType();
    }

    public function setParentType(string $type): WidgetPaginationContract
    {
        return $this->model->setParentType($type);
    }
}
