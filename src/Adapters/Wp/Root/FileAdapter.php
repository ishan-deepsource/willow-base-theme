<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Root;

use Bonnier\Willow\Base\Repositories\WpModelRepository;
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
    protected $acfData;

    public function __construct($file)
    {
        $this->file = $file;
        $this->postMeta = WpModelRepository::instance()->getPostMeta($file);
        $this->attachmentMeta = WpModelRepository::instance()->getAttachmentMeta($file);
        $this->acfData = WpModelRepository::instance()->getAcfData($file);
    }

    public function getId(): ?int
    {
        return array_get($this->file, 'ID', data_get($this->file, 'ID')) ?: null;
    }

    public function getUrl(): ?string
    {
        return as3cf_get_secure_attachment_url($this->getId(), 6 * HOUR_IN_SECONDS);
    }

    public function getTitle(): ?string
    {
        if (is_array($this->file)) {
            if (($title = array_get($this->file, 'title', array_get($this->file, 'post_title'))) &&
                $title !== $this->getId()
            ) {
                return $title;
            }
        } else {
            if (($title = data_get($this->file, 'title', data_get($this->file, 'post_title'))) &&
                $title !== $this->getId()
            ) {
                return $title;
            }
        }
        return null;
    }

    public function getDescription(): ?string
    {
        if (is_array($this->file)) {
            return array_get($this->file, 'description', array_get($this->file, 'post_content')) ?: null;
        }

        return data_get($this->file, 'description', data_get($this->file, 'post_content')) ?: null;
    }

    public function getCaption(): ?string
    {
        return array_get($this->acfData, 'caption') ?: null;
    }

    public function getLanguage(): ?string
    {
        if ($fileId = $this->getId()) {
            return LanguageProvider::getPostLanguage($fileId) ?: null;
        }

        return null;
    }
}
