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
        if ($fileId = array_get($acfArray, 'file.id')) {
            $post = get_post($fileId);
            $this->file = $post ? new File(new FileAdapter($post)) : null;
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
            $file = array_get($acfImage, 'file');
            return $file ? new Image(new ImageAdapter(get_post($file))) : null;
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
