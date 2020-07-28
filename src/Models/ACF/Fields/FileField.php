<?php

namespace Bonnier\Willow\Base\Models\ACF\Fields;

use Bonnier\Willow\Base\Models\ACF\ACFField;

class FileField extends ACFField
{
    public const TYPE = 'file';

    private $returnFormat = self::RETURN_ARRAY;
    private $library = 'all';
    private $minSize = '';
    private $maxSize = '';
    private $mimeTypes = '';

    /**
     * @param string $returnFormat
     * @return FileField
     */
    public function setReturnFormat(string $returnFormat): FileField
    {
        if (!in_array($returnFormat, [self::RETURN_ID, self::RETURN_OBJECT, self::RETURN_ARRAY, self::RETURN_VALUE])) {
            throw new \InvalidArgumentException(sprintf('\'%s\' is not a valid return format', $returnFormat));
        }
        $this->returnFormat = $returnFormat;
        return $this;
    }

    /**
     * @param string $library
     * @return FileField
     */
    public function setLibrary(string $library): FileField
    {
        $this->library = $library;
        return $this;
    }

    /**
     * @param string $minSize
     * @return FileField
     */
    public function setMinSize(string $minSize): FileField
    {
        $this->minSize = $minSize;
        return $this;
    }

    /**
     * @param string $maxSize
     * @return FileField
     */
    public function setMaxSize(string $maxSize): FileField
    {
        $this->maxSize = $maxSize;
        return $this;
    }

    /**
     * @param string $mimeTypes
     * @return FileField
     */
    public function setMimeTypes(string $mimeTypes): FileField
    {
        $this->mimeTypes = $mimeTypes;
        return $this;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'return_format' => $this->returnFormat,
            'library' => $this->library,
            'min_size' => $this->minSize,
            'max_size' => $this->maxSize,
            'mime_types' => $this->mimeTypes,
        ]);
    }
}
