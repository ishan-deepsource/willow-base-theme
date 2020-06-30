<?php

namespace Bonnier\Willow\Base\Models\ACF\Fields;

use Bonnier\Willow\Base\Models\ACF\ACFField;

class ImageField extends ACFField
{
    public const TYPE = 'image';

    public const PREVIEW_THUMB = 'thumbnail';
    public const PREVIEW_MEDIUM = 'medium';

    private $returnFormat = self::RETURN_ARRAY;
    private $previewSize = self::PREVIEW_MEDIUM;
    private $library = 'all';
    private $minWidth = '';
    private $minHeight = '';
    private $minSize = '';
    private $maxWidth = '';
    private $maxHeight = '';
    private $maxSize = '';
    private $mimeTypes = '';

    /**
     * @param string $returnFormat
     * @return ImageField
     */
    public function setReturnFormat(string $returnFormat): ImageField
    {
        if (!in_array($returnFormat, [self::RETURN_ID, self::RETURN_OBJECT, self::RETURN_ARRAY, self::RETURN_VALUE])) {
            throw new \InvalidArgumentException(sprintf('\'%s\' is not a valid return format', $returnFormat));
        }
        $this->returnFormat = $returnFormat;
        return $this;
    }

    /**
     * @param string $previewSize
     * @return ImageField
     */
    public function setPreviewSize(string $previewSize): ImageField
    {
        if (!in_array($previewSize, [self::PREVIEW_THUMB, self::PREVIEW_MEDIUM])) {
            throw new \InvalidArgumentException(sprintf('\'%s\' is not a valid preview size', $previewSize));
        }
        $this->previewSize = $previewSize;
        return $this;
    }

    /**
     * @param string $library
     * @return ImageField
     */
    public function setLibrary(string $library): ImageField
    {
        $this->library = $library;
        return $this;
    }

    /**
     * @param string $minWidth
     * @return ImageField
     */
    public function setMinWidth(string $minWidth): ImageField
    {
        $this->minWidth = $minWidth;
        return $this;
    }

    /**
     * @param string $minHeight
     * @return ImageField
     */
    public function setMinHeight(string $minHeight): ImageField
    {
        $this->minHeight = $minHeight;
        return $this;
    }

    /**
     * @param string $minSize
     * @return ImageField
     */
    public function setMinSize(string $minSize): ImageField
    {
        $this->minSize = $minSize;
        return $this;
    }

    /**
     * @param string $maxWidth
     * @return ImageField
     */
    public function setMaxWidth(string $maxWidth): ImageField
    {
        $this->maxWidth = $maxWidth;
        return $this;
    }

    /**
     * @param string $maxHeight
     * @return ImageField
     */
    public function setMaxHeight(string $maxHeight): ImageField
    {
        $this->maxHeight = $maxHeight;
        return $this;
    }

    /**
     * @param string $maxSize
     * @return ImageField
     */
    public function setMaxSize(string $maxSize): ImageField
    {
        $this->maxSize = $maxSize;
        return $this;
    }

    /**
     * @param string $mimeTypes
     * @return ImageField
     */
    public function setMimeTypes(string $mimeTypes): ImageField
    {
        $this->mimeTypes = $mimeTypes;
        return $this;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'return_format' => $this->returnFormat,
            'preview_size' => $this->previewSize,
            'library' => $this->library,
            'min_width' => $this->minWidth,
            'min_height' => $this->minHeight,
            'min_size' => $this->minSize,
            'max_width' => $this->maxWidth,
            'max_height' => $this->maxHeight,
            'max_size' => $this->maxSize,
            'mime_types' => $this->mimeTypes,
        ]);
    }
}
