<?php

namespace Bonnier\Willow\Base\Models\ACF\Properties;

use Illuminate\Contracts\Support\Arrayable;

class ACFWrapper implements Arrayable
{
    /** @var string */
    private $width = '';
    /** @var string */
    private $class = '';
    /** @var string */
    private $id = '';

    /**
     * @param string $width
     * @return ACFWrapper
     */
    public function setWidth(string $width): ACFWrapper
    {
        $this->width = $width;
        return $this;
    }
    /**
     * @param string $class
     * @return ACFWrapper
     */
    public function setClass(string $class): ACFWrapper
    {
        $this->class = $class;
        return $this;
    }
    /**
     * @param string $id
     * @return ACFWrapper
     */
    public function setId(string $id): ACFWrapper
    {
        $this->id = $id;
        return $this;
    }



    public function toArray()
    {
        return [
            'width' => $this->width,
            'class' => $this->class,
            'id' => $this->id
        ];
    }
}
