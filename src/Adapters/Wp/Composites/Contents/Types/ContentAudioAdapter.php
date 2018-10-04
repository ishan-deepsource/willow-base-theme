<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types;

use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\AbstractContentAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Root\AudioAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Root\ImageAdapter;
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

    public function __construct(array $acfArray)
    {
        parent::__construct($acfArray);
        $post = get_post($acfArray['file'] ?? null);
        $this->audio = $post ? new Audio(new AudioAdapter($post)) : null;
    }

    public function isLead() : bool
    {
        return $this->acfArray['lead_image'] ?? false;
    }

    public function getId(): ?int
    {
        return optional($this->audio)->getId();
    }

    public function getUrl(): ?string
    {
        return optional($this->audio)->getUrl();
    }

    public function getTitle(): ?string
    {
        return optional($this->audio)->getTitle();
    }

    public function getDescription(): ?string
    {
        return optional($this->audio)->getDescription();
    }

    public function getLanguage(): ?string
    {
        return optional($this->audio)->getLanguage();
    }

    public function getAudioTitle(): ?string
    {
        return $this->acfArray['title'] ?? null;
    }

    public function getCaption(): ?string
    {
        return optional($this->audio)->getCaption();
    }

    public function getImage(): ?ImageContract
    {
        if (($imageId = $this->acfArray['image'] ?? null) && $image = get_post($imageId)) {
            return new Image(new ImageAdapter($image));
        }

        return null;
    }

    public function getDuration(): int
    {
        if ($audioId = $this->getId()) {
            $metaData = wp_get_attachment_metadata($audioId);
            return $metaData['length'] ? ceil($metaData['length'] / 60) : 0;
        }
        return 0;
    }
}
