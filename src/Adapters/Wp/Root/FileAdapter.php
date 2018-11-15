<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Root;

use Bonnier\Willow\Base\Factories\DataFactory;
use Bonnier\Willow\Base\Models\Contracts\Root\FileContract;
use Bonnier\Willow\MuPlugins\Helpers\LanguageProvider;

/**
 * Class FileAdapter
 *
 * @package \Bonnier\Willow\Base\Adapters\Wp
 */
class FileAdapter implements FileContract
{
    protected $file;
    protected $postMeta;
    protected $attachmentMeta;

    public function __construct($file)
    {
        $this->file = $file;
        $this->postMeta = DataFactory::instance()->getPostMeta($file);
        $this->attachmentMeta = DataFactory::instance()->getAttachmentMeta($file);
    }

    public function getId(): ?int
    {
        return array_get($this->file, 'ID', data_get($this->file, 'ID')) ?: null;
    }

    public function getUrl(): ?string
    {
        return as3cf_get_secure_attachment_url($this->getId(), HOUR_IN_SECONDS);
    }

    public function getTitle(): ?string
    {
        if (($title = array_get($this->file, 'title', data_get($this->file, 'post_title'))) &&
            $title !== $this->getId()
        ) {
            return $title;
        }

        return null;
    }

    public function getDescription(): ?string
    {
        return array_get($this->file, 'description', data_get($this->file, 'post_content')) ?: null;
    }

    public function getCaption(): ?string
    {
        return array_get($this->file, 'caption', data_get($this->file, 'post_excerpt')) ?: null;
    }

    public function getLanguage(): ?string
    {
        if ($fileId = $this->getId()) {
            return LanguageProvider::getPostLanguage($fileId) ?: null;
        }

        return null;
    }
}
