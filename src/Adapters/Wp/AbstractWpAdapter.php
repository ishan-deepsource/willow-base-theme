<?php

namespace Bonnier\Willow\Base\Adapters\Wp;

/**
 * Class AbstractWpAdapter
 *
 * @package \Bonnier\Willow\Base\Adapters\Wp
 */
abstract class AbstractWpAdapter
{
    protected $wpModel;

    /**
     * AbstractWpAdapter constructor.
     *
     * @param $wpModel
     */
    public function __construct($wpModel)
    {
        $this->wpModel = $wpModel;
    }

    /**
     * @return mixed
     */
    public function getWpModel()
    {
        return $this->wpModel;
    }
}
