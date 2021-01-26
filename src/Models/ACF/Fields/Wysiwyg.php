<?php

namespace Bonnier\Willow\Base\Models\ACF\Fields;

use Bonnier\Willow\Base\Models\ACF\ACFField;

class Wysiwyg extends ACFField
{
    public const TYPE = 'wysiwyg';

    public const TABS_ALL = 'all';

    public const TABS_VISUAL = 'visual';

    public const TABS_TEXT = 'text';

    public const TOOLBAR_FULL = 'full';

    public const TOOLBAR_BASIC = 'basic';

    /**
     * @var string
     */
    private $tabs;

    /**
     * @var string
     */
    private $toolbar;

    /**
     * @var bool
     */
    private $mediaUpload = true;

    /**
     * @var bool
     */
    private $delay = false;

    /**
     * @return string
     */
    public function getTabs(): string
    {
        return $this->tabs;
    }

    /**
     * @param string $tabs
     * @return Wysiwyg
     */
    public function setTabs(string $tabs): Wysiwyg
    {
        if (! in_array($tabs, [static::TABS_ALL, static::TABS_VISUAL, static::TABS_TEXT])) {
            throw new \InvalidArgumentException(sprintf("%s is not a valid tabs option", $tabs));
        }

        $this->tabs = $tabs;

        return $this;
    }

    /**
     * @return string
     */
    public function getToolbar(): string
    {
        return $this->toolbar;
    }

    /**
     * @param string $toolbar
     * @return Wysiwyg
     */
    public function setToolbar(string $toolbar): Wysiwyg
    {
        if (! in_array($toolbar, [static::TOOLBAR_BASIC, static::TOOLBAR_FULL])) {
            throw new \InvalidArgumentException(sprintf("%s is not a valid tabs option", $toolbar));
        }

        $this->toolbar = $toolbar;

        return $this;
    }

    /**
     * @return bool
     */
    public function isMediaUpload(): bool
    {
        return $this->mediaUpload;
    }

    /**
     * @param bool $mediaUpload
     * @return Wysiwyg
     */
    public function setMediaUpload(bool $mediaUpload): Wysiwyg
    {
        $this->mediaUpload = $mediaUpload;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDelay(): bool
    {
        return $this->delay;
    }

    /**
     * @param bool $delay
     * @return Wysiwyg
     */
    public function setDelay(bool $delay): Wysiwyg
    {
        $this->delay = $delay;
        return $this;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'tabs' => $this->tabs,
            'toolbar' => $this->toolbar,
            'media_upload' => $this->mediaUpload,
            'delay' => $this->delay,
        ]);
    }
}
