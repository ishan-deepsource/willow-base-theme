<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types;

use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\AbstractContentAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Root\FileAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Root\ImageAdapter;
use Bonnier\Willow\Base\Models\Base\Root\File;
use Bonnier\Willow\Base\Models\Base\Root\Image;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\ContentFileContract;
use Illuminate\Support\Collection;

/**
 * Class ImageAdapter
 *
 * @package \Bonnier\Willow\Base\Adapters\Wp
 */
class ContentFileAdapter extends AbstractContentAdapter implements ContentFileContract
{
    protected $file;

    public function __construct(array $acfArray)
    {
        parent::__construct($acfArray);
        if ($file = array_get($acfArray, 'file')) {
            $postMeta = get_post_meta(array_get($file, 'ID'));
            $attachmentMeta = wp_get_attachment_metadata(array_get($file, 'ID'));
            $this->file = new File(new FileAdapter($file, $postMeta, $attachmentMeta));
        }
        if (!$this->file) {
            throw new \InvalidArgumentException('Missing file');
        }
    }

    public function getId(): ?int
    {
        return optional($this->file)->getId() ?: null;
    }

    public function getImages(): ?Collection
    {
        return collect(array_get($this->acfArray, 'images', []))->map(function ($acfImage) {
            if ($file = array_get($acfImage, 'file')) {
                $postMeta = get_post_meta(data_get($file, 'ID'));
                $attachmentMeta = wp_get_attachment_metadata(data_get($file, 'ID'));
                return new Image(new ImageAdapter($file, $postMeta, $attachmentMeta));
            }
            return null;
        })->reject(function ($image) {
            return is_null($image);
        });
    }

    public function getCaption(): ?string
    {
        return optional($this->file)->getCaption() ?: null;
    }

    public function getUrl() : ?string
    {
        return optional($this->file)->getUrl() ?: null;
    }

    public function getTitle(): ?string
    {
        return optional($this->file)->getTitle() ?: null;
    }

    public function getDescription(): ?string
    {
        return optional($this->file)->getDescription() ?: null;
    }

    public function getLanguage(): ?string
    {
        return optional($this->file)->getLanguage() ?: null;
    }
}
