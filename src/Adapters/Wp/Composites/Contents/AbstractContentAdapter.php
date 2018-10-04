<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Composites\Contents;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\ContentContract;

/**
 * Class AbstractContentAdapter
 *
 * @package \Bonnier\Willow\Base\Adapters\Wp
 */
abstract class AbstractContentAdapter implements ContentContract
{
    protected $acfArray;
    protected $post;

    /**
     * AbstractWpAdapter constructor.
     *
     * @param array    $acfArray
     * @param \WP_Post $post
     */
    public function __construct(array $acfArray, \WP_Post $post = null)
    {
        $this->acfArray = $acfArray;
        $this->post = $post;
    }

    public function getType() : ?string
    {
        return array_get($this->acfArray, 'acf_fc_layout');
    }

    public function isLocked() : bool
    {
        return array_get($this->acfArray, 'locked_content', false);
    }

    public function getStickToNext(): bool
    {
        return array_get($this->acfArray, 'stick_to_next', false);
    }
}
