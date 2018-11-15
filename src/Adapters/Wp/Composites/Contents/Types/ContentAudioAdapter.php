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
    protected $meta;

    public function __construct(?array $acfArray)
    {
        parent::__construct($acfArray);
        $post = array_get($this->acfArray, 'file');
        $this->meta = wp_get_attachment_metadata(array_get($post, 'ID'));
        $this->audio = $post ? new Audio(new AudioAdapter($post, $this->meta)) : null;
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
        if (($image = array_get($this->acfArray, 'image'))) {
            $meta = wp_get_attachment_metadata(array_get($image, 'ID'));
            return new Image(new ImageAdapter($image, $meta));
        }

        return null;
    }

    public function getDuration(): int
    {
        $length = array_get($this->meta, 'length');
        return $length ? ceil($length / 60) : 0;
    }
}
