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
    protected $wpMeta;

    /**
     * AbstractWpAdapter constructor.
     *
     * @param $wpModel
     * @param $wpMeta
     */
    public function __construct($wpModel, $wpMeta)
    {
        $this->wpModel = $wpModel;
        $this->wpMeta = $wpMeta;
    }

    /**
     * @return mixed
     */
    public function getWpModel()
    {
        return $this->wpModel;
    }

    public function getWpMeta()
    {
        return $this->wpMeta;
    }
}
