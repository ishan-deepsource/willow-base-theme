<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Root;

use Bonnier\Willow\Base\Models\Contracts\Root\FileContract;
use Bonnier\Willow\MuPlugins\Helpers\LanguageProvider;
use WP_Post;

/**
 * Class FileAdapter
 *
 * @package \Bonnier\Willow\Base\Adapters\Wp
 */
class FileAdapter implements FileContract
{
    protected $file;
    protected $meta;

    public function __construct(array $file, $meta)
    {
        $this->file = $file;
        $this->meta = $meta;
    }

    public function getId(): ?int
    {
        return data_get($this->file, 'ID') ?: null;
    }

    public function getUrl(): ?string
    {
        return as3cf_get_secure_attachment_url($this->getId(), HOUR_IN_SECONDS);
    }

    public function getTitle(): ?string
    {
        if (($title = data_get($this->file, 'post_title')) && $title !== $this->getId()) {
            return $title;
        }

        return null;
    }

    public function getDescription(): ?string
    {
        return data_get($this->file, 'post_content') ?: null;
    }

    public function getCaption(): ?string
    {
        return data_get($this->file, 'post_excerpt') ?: null;
    }

    public function getLanguage(): ?string
    {
        return LanguageProvider::getPostLanguage($this->getId()) ?: null;
    }
}
