<?php

namespace Bonnier\Willow\Base\Models\ACF\Fields;

use Bonnier\Willow\Base\Models\ACF\ACFField;

class CustomRelationshipField extends ACFField
{
    public const TYPE = 'custom_relationship';

    private $postTypes = [];
    private $taxonomy = [];
    private $tags = [];
    private $filters = [];
    private $elements = '';
    private $min = 0;
    private $max = 0;
    private $returnFormat = self::RETURN_OBJECT;

    /**
     * @param array $postTypes
     * @return CustomRelationshipField
     */
    public function setPostTypes(array $postTypes): CustomRelationshipField
    {
        $this->postTypes = $postTypes;
        return $this;
    }

    public function addPostType(string $postType): CustomRelationshipField
    {
        array_push($this->postTypes, $postType);
        return $this;
    }

    /**
     * @param array $taxonomy
     * @return CustomRelationshipField
     */
    public function setTaxonomy(array $taxonomy): CustomRelationshipField
    {
        $this->taxonomy = $taxonomy;
        return $this;
    }

    /**
     * @param array $tags
     * @return CustomRelationshipField
     */
    public function setTags(array $tags): CustomRelationshipField
    {
        $this->tags = $tags;
        return $this;
    }

    /**
     * @param array $filters
     * @return CustomRelationshipField
     */
    public function setFilters(array $filters): CustomRelationshipField
    {
        $this->filters = $filters;
        return $this;
    }

    public function addFilter(string $filter): CustomRelationshipField
    {
        array_push($this->filters, $filter);
        return $this;
    }

    /**
     * @param string $elements
     * @return CustomRelationshipField
     */
    public function setElements(string $elements): CustomRelationshipField
    {
        $this->elements = $elements;
        return $this;
    }

    /**
     * @param int $min
     * @return CustomRelationshipField
     */
    public function setMin(int $min): CustomRelationshipField
    {
        $this->min = $min;
        return $this;
    }

    /**
     * @param int $max
     * @return CustomRelationshipField
     */
    public function setMax(int $max): CustomRelationshipField
    {
        $this->max = $max;
        return $this;
    }

    /**
     * @param string $returnFormat
     * @return CustomRelationshipField
     */
    public function setReturnFormat(string $returnFormat): CustomRelationshipField
    {
        if (!in_array($returnFormat, [self::RETURN_OBJECT, self::RETURN_VALUE, self::RETURN_ARRAY, self::RETURN_ID])) {
            throw new \InvalidArgumentException(sprintf('\'%s\' is not a valid return format.', $returnFormat));
        }
        $this->returnFormat = $returnFormat;
        return $this;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'post_type' => empty($this->postTypes) ? '' : $this->postTypes,
            'taxonomy' => empty($this->taxonomy) ? '' : $this->taxonomy,
            'post_tag' => empty($this->tags) ? '' : $this->tags,
            'filters' => $this->filters,
            'elements' => $this->elements,
            'min' => $this->min,
            'max' => $this->max,
            'return_format' => $this->returnFormat,
        ]);
    }
}
