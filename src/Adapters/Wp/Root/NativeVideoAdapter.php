<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Root;

use Bonnier\Willow\Base\Models\Contracts\Root\NativeVideoContract;

/**
 * Class NativeVideoAdapter
 *
 * @package \Bonnier\Willow\Base\Adapters\Wp\Root
 */
class NativeVideoAdapter extends FileAdapter implements NativeVideoContract
{
    public function getUrl(): ?string
    {
        return wp_get_attachment_url($this->getId()) ?: null;
    }
}
