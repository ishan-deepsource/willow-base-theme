<?php

namespace Bonnier\Willow\Base\Models\ACF\Fields;

use Bonnier\Willow\Base\Models\ACF\ACFField;

class RelationshipField extends ACFField
{
    public const TYPE = 'relationship';

    public const FILTER_SEARCH = 'search';
    public const FILTER_TAXONOMY = 'taxonomy';

    /** @var array|string[] */
    private $postTypes = [];
    /** @var array|string[] */
    private $taxonomy = [];
    /** @var array|string[] */
    private $filters = [];
    private $elements = '';
    private $min = '';
    private $max = '';
    private $returnFormat = self::RETURN_OBJECT;

    /**
     * @param array|string[] $postTypes
     * @return RelationshipField
     */
    public function setPostTypes(array $postTypes): RelationshipField
    {
        $this->postTypes = $postTypes;
        return $this;
    }

    public function addPostType(string $postType): RelationshipField
    {
        array_push($this->postTypes, $postType);
        return $this;
    }

    /**
     * @param array|string[] $taxonomy
     * @return RelationshipField
     */
    public function setTaxonomy(array $taxonomy): RelationshipField
    {
        $this->taxonomy = $taxonomy;
        return $this;
    }

    public function addTaxonomy(string $taxonomy): RelationshipField
    {
        array_push($this->taxonomy, $taxonomy);
        return $this;
    }

    /**
     * @param array|string[] $filters
     * @return RelationshipField
     */
    public function setFilters(array $filters): RelationshipField
    {
        $this->filters = $filters;
        return $this;
    }

    public function addFilter(string $filter): RelationshipField
    {
        array_push($this->filters, $filter);
        return $this;
    }

    /**
     * @param string $elements
     * @return RelationshipField
     */
    public function setElements(string $elements): RelationshipField
    {
        $this->elements = $elements;
        return $this;
    }

    /**
     * @param string $min
     * @return RelationshipField
     */
    public function setMin(string $min): RelationshipField
    {
        $this->min = $min;
        return $this;
    }

    /**
     * @param string $max
     * @return RelationshipField
     */
    public function setMax(string $max): RelationshipField
    {
        $this->max = $max;
        return $this;
    }

    /**
     * @param string $returnFormat
     * @return RelationshipField
     */
    public function setReturnFormat(string $returnFormat): RelationshipField
    {
        if (!in_array($returnFormat, [self::RETURN_ID, self::RETURN_OBJECT, self::RETURN_ARRAY, self::RETURN_VALUE])) {
            throw new \InvalidArgumentException(sprintf('\'%s\' is not a valid return format', $returnFormat));
        }
        $this->returnFormat = $returnFormat;
        return $this;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'post_type' => $this->postTypes,
            'taxonomy' => $this->taxonomy,
            'filters' => $this->filters,
            'elements' => $this->elements,
            'min' => $this->min,
            'max' => $this->max,
            'return_format' => $this->returnFormat,
        ]);
    }
}
