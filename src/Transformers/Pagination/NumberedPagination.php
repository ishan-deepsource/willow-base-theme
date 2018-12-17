<?php

namespace Bonnier\Willow\Base\Transformers\Pagination;

use Bonnier\Willow\Base\Models\Contracts\Utilities\WidgetPaginationContract;
use Illuminate\Contracts\Support\Arrayable;
use League\Fractal\Pagination\PaginatorInterface;

class NumberedPagination implements PaginatorInterface, Arrayable
{
    protected $currentPage;
    protected $perPage;
    protected $total;
    protected $count;
    protected $lastPage;

    /**
     * NumberedPagination constructor.
     *
     * @param $currentPage
     * @param $perPage
     * @param $total
     * @param $count
     * @param $lastPage
     */
    public function __construct($currentPage = null, $perPage = null, $total = null, $count = null, $lastPage = null)
    {
        $this->currentPage = $currentPage;
        $this->perPage = $perPage;
        $this->total = $total;
        $this->count = $count;
        $this->lastPage = $lastPage;
    }

    public static function createFromWidget(WidgetPaginationContract $widget): NumberedPagination
    {
        return new self(
            $widget->getCurrentPage(),
            $widget->getItemsPerPage(),
            $widget->getTotalItems(),
            $widget->getItemCount(),
            $widget->getTotalPages()
        );
    }

    /**
     * @param mixed $currentPage
     *
     * @return NumberedPagination
     */
    public function setCurrentPage($currentPage)
    {
        $this->currentPage = $currentPage;
        return $this;
    }

    /**
     * @param mixed $lastPage
     *
     * @return NumberedPagination
     */
    public function setLastPage($lastPage)
    {
        $this->lastPage = $lastPage;
        return $this;
    }

    /**
     * @param mixed $total
     *
     * @return NumberedPagination
     */
    public function setTotal($total)
    {
        $this->total = $total;
        return $this;
    }

    /**
     * @param mixed $count
     *
     * @return NumberedPagination
     */
    public function setCount($count)
    {
        $this->count = $count;
        return $this;
    }

    /**
     * @param mixed $perPage
     *
     * @return NumberedPagination
     */
    public function setPerPage($perPage)
    {
        $this->perPage = $perPage;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * @return mixed
     */
    public function getLastPage()
    {
        return $this->lastPage;
    }

    /**
     * @return mixed
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @return mixed
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @return mixed
     */
    public function getPerPage()
    {
        return $this->perPage;
    }


    /**
     * Get the url for the given page.
     *
     * @param int $page
     *
     * @return string
     */
    public function getUrl($page)
    {
        return '';
    }

    public function toArray()
    {
        return [
            'total' => $this->getTotal(),
            'count' => $this->getCount(),
            'per_page' => $this->getPerPage(),
            'current_page' => $this->getCurrentPage(),
            'total_pages' => $this->getLastPage(),
            'links' => [
                'previous' => '',
                'next' => '',
            ],
        ];
    }
}
