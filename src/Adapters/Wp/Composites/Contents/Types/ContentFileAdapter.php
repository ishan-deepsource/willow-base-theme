<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types;

use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\AbstractContentAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Root\FileAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Root\ImageAdapter;
use Bonnier\Willow\Base\Models\Contracts\Root\FileContract;
use Bonnier\Willow\Base\Repositories\WpModelRepository;
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
        if ($fileArray = array_get($acfArray, 'file')) {
            $file = WpModelRepository::instance()->getPost($fileArray);
            $this->file = new File(new FileAdapter($file));
        }
        if (!$this->file) {
            throw new \InvalidArgumentException('Missing file');
        }
    }

    public function getTitle(): ?string
    {
        return array_get($this->acfArray, 'title') ?: null;
    }

    public function getDescription(): ?string
    {
        return array_get($this->acfArray, 'description') ?: null;
    }

    public function getFile(): ?FileContract
    {
        return $this->file;
    }

    public function getImages(): ?Collection
    {
        return collect(array_get($this->acfArray, 'images', []))->map(function ($acfImage) {
            if ($fileArray = array_get($acfImage, 'file')) {
                $file = WpModelRepository::instance()->getPost($fileArray);
                return new Image(new ImageAdapter($file));
            }
            return null;
        })->reject(function ($image) {
            return is_null($image);
        });
    }

    public function getDownloadButtonText(): ?string
    {
        return array_get($this->acfArray, 'download_button_text') ?: null;
    }

    /*
    public function getCaption(): ?string
    {
        return optional($this->file)->getCaption() ?: null;
    }

    public function getUrl() : ?string
    {
        return optional($this->file)->getUrl() ?: null;
    }

    public function getLanguage(): ?string
    {
        return optional($this->file)->getLanguage() ?: null;
    }

    public function getId(): ?int
    {
        return optional($this->file)->getId() ?: null;
    }
    */
}
