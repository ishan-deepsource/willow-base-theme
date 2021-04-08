<?php

namespace Bonnier\Willow\Base\Models\ACF\Fields;

use Bonnier\Willow\Base\Models\ACF\ACFField;

class TaxonomyField extends ACFField
{
    public const TYPE = 'taxonomy';

    public const TAXONOMY_CATEGORY = 'category';
    public const TAXONOMY_TAG = 'post_tag';
    public const TAXONOMY_EDITORIAL_TYPE = 'editorial_type';

    public const TYPE_SELECT = 'select';
    public const TYPE_MULTI = 'multi_select';

    /** @var string  */
    private $taxonomy = '';
    /** @var string  */
    private $fieldType = '';
    /** @var bool  */
    private $allowNull = false;
    /** @var bool  */
    private $addTerm = false;
    /** @var bool  */
    private $saveTerms = false;
    /** @var bool  */
    private $loadTerms = false;
    /** @var string  */
    private $returnFormat = self::RETURN_OBJECT;
    /** @var bool  */
    private $multiple = false;

    /**
     * @param string $taxonomy
     * @return TaxonomyField
     */
    public function setTaxonomy(string $taxonomy): TaxonomyField
    {
        $this->taxonomy = $taxonomy;
        return $this;
    }

    /**
     * @param string $fieldType
     * @return TaxonomyField
     */
    public function setFieldType(string $fieldType): TaxonomyField
    {
        if (!in_array($fieldType, [self::TYPE_SELECT, self::TYPE_MULTI])) {
            throw new \InvalidArgumentException(sprintf('\'%s\' is not a valid field type', $fieldType));
        }
        $this->fieldType = $fieldType;
        return $this;
    }

    /**
     * @param bool $allowNull
     * @return TaxonomyField
     */
    public function setAllowNull(bool $allowNull): TaxonomyField
    {
        $this->allowNull = $allowNull;
        return $this;
    }

    /**
     * @param bool $addTerm
     * @return TaxonomyField
     */
    public function setAddTerm(bool $addTerm): TaxonomyField
    {
        $this->addTerm = $addTerm;
        return $this;
    }

    /**
     * @param bool $saveTerms
     * @return TaxonomyField
     */
    public function setSaveTerms(bool $saveTerms): TaxonomyField
    {
        $this->saveTerms = $saveTerms;
        return $this;
    }

    /**
     * @param bool $loadTerms
     * @return TaxonomyField
     */
    public function setLoadTerms(bool $loadTerms): TaxonomyField
    {
        $this->loadTerms = $loadTerms;
        return $this;
    }

    /**
     * @param string $returnFormat
     * @return TaxonomyField
     */
    public function setReturnFormat(string $returnFormat): TaxonomyField
    {
        if (!in_array($returnFormat, [self::RETURN_ID, self::RETURN_OBJECT, self::RETURN_ARRAY, self::RETURN_VALUE])) {
            throw new \InvalidArgumentException(sprintf('\'%s\' is not a valid return format', $returnFormat));
        }
        $this->returnFormat = $returnFormat;
        return $this;
    }

    /**
     * @param bool $multiple
     * @return TaxonomyField
     */
    public function setMultiple(bool $multiple): TaxonomyField
    {
        $this->multiple = $multiple;
        return $this;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'taxonomy' => $this->taxonomy,
            'field_type' => $this->fieldType,
            'allow_null' => $this->allowNull ? 1 : 0,
            'add_term' => $this->addTerm ? 1 : 0,
            'save_terms' => $this->saveTerms ? 1 : 0,
            'load_terms' => $this->loadTerms ? 1 : 0,
            'return_format' => $this->returnFormat,
            'multiple' => $this->multiple ? 1 : 0,
        ]);
    }
}
