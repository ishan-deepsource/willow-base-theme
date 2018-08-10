<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types;

use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\AbstractContentAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Root\AudioAdapter;
use Bonnier\Willow\Base\Models\Base\Root\Audio;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\ContentAudioContract;

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
        $post = get_post($acfArray['audio_file'] ?? null);
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
        return $this->acfArray['audio_title'] ?? null;
    }

    public function getCaption(): ?string
    {
        return optional($this->audio)->getCaption();
    }
}
