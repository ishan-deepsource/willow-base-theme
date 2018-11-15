<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types;

use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\AbstractContentAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Root\AudioAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Root\ImageAdapter;
use Bonnier\Willow\Base\Factories\DataFactory;
use Bonnier\Willow\Base\Models\Base\Root\Audio;
use Bonnier\Willow\Base\Models\Base\Root\Image;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\ContentAudioContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;

/**
 * Class ImageAdapter
 *
 * @package \Bonnier\Willow\Base\Adapters\Wp
 */
class ContentAudioAdapter extends AbstractContentAdapter implements ContentAudioContract
{
    protected $audio;
    protected $postMeta;
    protected $attachmentMeta;

    public function __construct(?array $acfArray)
    {
        parent::__construct($acfArray);
        $postArray = array_get($this->acfArray, 'file');
        if ($post = DataFactory::instance()->getPost($postArray)) {
            $this->attachmentMeta = DataFactory::instance()->getAttachmentMeta($post);
            $this->audio = new Audio(new AudioAdapter($post));
        }
    }

    public function isLead(): bool
    {
        return array_get($this->acfArray, 'lead_image', false);
    }

    public function getId(): ?int
    {
        return optional($this->audio)->getId() ?: null;
    }

    public function getUrl(): ?string
    {
        return optional($this->audio)->getUrl() ?: null;
    }

    public function getTitle(): ?string
    {
        return optional($this->audio)->getTitle() ?: null;
    }

    public function getDescription(): ?string
    {
        return optional($this->audio)->getDescription() ?: null;
    }

    public function getLanguage(): ?string
    {
        return optional($this->audio)->getLanguage() ?: null;
    }

    public function getAudioTitle(): ?string
    {
        return array_get($this->acfArray, 'title') ?: null;
    }

    public function getCaption(): ?string
    {
        return optional($this->audio)->getCaption() ?: null;
    }

    public function getImage(): ?ImageContract
    {
        if (($imageArray = array_get($this->acfArray, 'image'))) {
            $image = DataFactory::instance()->getPost($imageArray);
            return new Image(new ImageAdapter($image));
        }

        return null;
    }

    public function getDuration(): int
    {
        $length = array_get($this->attachmentMeta, 'length');
        return $length ? ceil($length / 60) : 0;
    }
}
