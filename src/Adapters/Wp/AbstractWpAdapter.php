<?php

namespace Bonnier\Willow\Base\Adapters\Wp;

use Bonnier\Willow\Base\Repositories\WpModelRepository;

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
     */
    public function __construct($wpModel)
    {
        $this->wpModel = $wpModel;
        if ($this->wpModel) {
            if ($this->wpModel instanceof \WP_Post || array_key_exists('ID', $this->wpModel)) {
                $this->wpMeta = WpModelRepository::instance()->getPostMeta($this->wpModel);
            } elseif ($this->wpModel instanceof \WP_Term || array_key_exists('term_id', $this->wpModel)) {
                $this->wpMeta = WpModelRepository::instance()->getTermMeta($this->wpModel);
            }
        }
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
