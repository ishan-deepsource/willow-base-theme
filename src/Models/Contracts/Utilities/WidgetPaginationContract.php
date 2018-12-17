<?php

namespace Bonnier\Willow\Base\Models\Contracts\Utilities;

interface WidgetPaginationContract
{
    public function getCurrentPage(): int;
    public function setCurrentPage(int $page): WidgetPaginationContract;
    public function getTotalItems(): ?int;
    public function setTotalItems(int $items): WidgetPaginationContract;
    public function getTotalPages(): ?int;
    public function setTotalPages(int $pages): WidgetPaginationContract;
    public function getItemsPerPage(): ?int;
    public function setItemsPerPage(int $items): WidgetPaginationContract;
    public function getItemCount(): ?int;
    public function setItemCount(int $items): WidgetPaginationContract;
    public function getNextCursor(): ?string;
    public function getPreviousCursor(): ?string;
    public function getCurrentCursor(): ?string;
    public function getParentId(): ?int;
    public function setParentId(int $parentId): WidgetPaginationContract;
    public function getParentType(): ?string;
    public function setParentType(string $type): WidgetPaginationContract;
}
