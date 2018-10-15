<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types;

use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\AbstractContentAdapter;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\VideoContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;

/**
 * Class VideoAdapter
 *
 * @package \Bonnier\Willow\Base\Adapters\Wp
 */
class VideoAdapter extends AbstractContentAdapter implements VideoContract
{
    public function getEmbedUrl(): ?string
    {
        if ($embedUrl = array_get($this->acfArray, 'embed_url')) {
            if (preg_match('/src=["\']([^\'"]+)/', $embedUrl, $matches)) {
                return $matches[1];
            }
            return $embedUrl;
        }

        return null;
    }

    public function getCaption(): ?string
    {
        return array_get($this->acfArray, 'caption') ?: null;
    }
}
