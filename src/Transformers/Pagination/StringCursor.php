<?php

namespace Bonnier\Willow\Base\Transformers\Pagination;

use Bonnier\Willow\Base\Models\Contracts\Utilities\WidgetPaginationContract;
use Illuminate\Contracts\Support\Arrayable;
use League\Fractal\Pagination\CursorInterface;

class StringCursor implements CursorInterface, Arrayable
{
    protected $current;
    protected $next;
    protected $prev;
    protected $count;

    public static function createFromWidget(WidgetPaginationContract $widget)
    {
        $cursor = new self();
        $cursor->setCount($widget->getItemCount());
        $cursor->setNext($widget->getNextCursor());
        $cursor->setPrev($widget->getPreviousCursor());
        $cursor->setCurrent($widget->getCurrentCursor());
        return $cursor;
    }

    /**
     * @param mixed $current
     *
     * @return StringCursor
     */
    public function setCurrent($current)
    {
        $this->current = $current;
        return $this;
    }

    /**
     * @param mixed $next
     *
     * @return StringCursor
     */
    public function setNext($next)
    {
        $this->next = $next;
        return $this;
    }

    /**
     * @param mixed $prev
     *
     * @return StringCursor
     */
    public function setPrev($prev)
    {
        $this->prev = $prev;
        return $this;
    }

    /**
     * @param mixed $count
     *
     * @return StringCursor
     */
    public function setCount($count)
    {
        $this->count = $count;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCurrent()
    {
        return $this->current;
    }

    /**
     * @return mixed
     */
    public function getNext()
    {
        return $this->next;
    }

    /**
     * @return mixed
     */
    public function getPrev()
    {
        return $this->prev;
    }

    /**
     * @return mixed
     */
    public function getCount()
    {
        return $this->count;
    }

    public function toArray()
    {
        return [
            'current' => $this->getCurrent(),
            'prev' => $this->getPrev(),
            'next' => $this->getNext(),
            'count' => $this->getCount(),
        ];
    }
}
