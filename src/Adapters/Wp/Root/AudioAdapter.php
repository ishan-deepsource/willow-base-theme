<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Root;

use Bonnier\Willow\Base\Models\Contracts\Root\AudioContract;

/**
 * Class AudioAdapter
 *
 * @package \Bonnier\Willow\Base\Adapters\Wp\Root
 */
class AudioAdapter extends FileAdapter implements AudioContract
{
    public function getUrl(): ?string
    {
        if (defined('AWS_S3_DOMAIN') &&
            ($s3Meta = array_get($this->postMeta, 'amazonS3_info.0', data_get($this->postMeta, 'amazonS3_info.0'))) &&
            $s3Data = unserialize($s3Meta)
        ) {
            return sprintf('https://%s/%s', AWS_S3_DOMAIN, array_get($s3Data, 'key'));
        }

        if ($this->file instanceof \WP_Post) {
            if ($url = wp_get_attachment_image_url($this->getId(), 'original')) {
                return $url;
            }
            return wp_get_attachment_url($this->getId()) ?: null;
        } else {
            return array_get($this->file, 'url', data_get($this->file, 'url')) ?: null;
        }
    }
}
