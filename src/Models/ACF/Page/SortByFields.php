<?php

namespace Bonnier\Willow\Base\Models\ACF\Page;

use Bonnier\Willow\Base\Helpers\AcfName;
use Bonnier\Willow\Base\Helpers\SortBy;
use Bonnier\Willow\Base\Models\ACF\ACFField;
use Bonnier\Willow\Base\Models\ACF\Fields\CustomRelationshipField;
use Bonnier\Willow\Base\Models\ACF\Fields\NumberField;
use Bonnier\Willow\Base\Models\ACF\Fields\RadioField;
use Bonnier\Willow\Base\Models\ACF\Fields\SelectField;
use Bonnier\Willow\Base\Models\ACF\Fields\TabField;
use Bonnier\Willow\Base\Models\ACF\Fields\TaxonomyField;
use Bonnier\Willow\Base\Models\ACF\Fields\TrueFalseField;
use Bonnier\Willow\Base\Models\ACF\Fields\UserField;
use Bonnier\Willow\Base\Models\ACF\Properties\ACFConditionalLogic;
use Bonnier\Willow\Base\Models\ACF\Properties\ACFWrapper;
use Bonnier\Willow\Base\Models\WpComposite;

class SortByFields
{
    private static $widgetName;
    private static $config;
    private static $sortByField;

    public const SORT_BY_EDITORIAL_TYPE = 'editorial_type';

    public static function getFields($widgetName, $config = [])
    {
        self::$widgetName = $widgetName;
        self::$config = $config;
        self::$sortByField = 'field_' . hash('md5', $widgetName . AcfName::FIELD_SORT_BY);
        $fields = [
            self::getTabField(),
            self::getOptionsField(),
        ];
        if ($teaserAmount = self::getTeaserAmountField()) {
            array_push($fields, $teaserAmount);
        }
        return array_merge($fields, [
            self::getSkipTeasersAmountField(),
            self::getTeaserListField(),
            self::getIncludeCategoryChildrenField(),
            self::getCategoryTagRelationField(),
            self::getCategoryOperationField(),
            self::getTagOperationField(),
            self::getCategoryField(),
            self::getTagField(),
            self::getEditorialTypeField(),
            self::getUserField(),
        ]);
    }

    public static function getTabField(): ACFField
    {
        $field = new TabField(sprintf('field_%s', hash('md5', self::$widgetName . 'sort by tab')));
        $field->setLabel('Sort by')
            ->setPlacement('left')
            ->setEndpoint(0);

        return apply_filters(sprintf('willow/acf/field=%s', $field->getKey()), $field);
    }

    public static function getOptionsField(): ACFField
    {
        $field = new RadioField(self::$sortByField);
        $field->setLabel('Sort by')
            ->setName(AcfName::FIELD_SORT_BY)
            ->setChoice(SortBy::POPULAR, 'Popular (Cxense)')
            ->setChoice(SortBy::RECENTLY_VIEWED, 'Recently Viewed by User (Cxense)')
            ->setChoice(SortBy::CUSTOM, 'Taxonomy (WordPress)')
            ->setChoice(SortBy::MANUAL, 'Manual (WordPress)')
            ->setChoice(SortBy::SHUFFLE, 'Shuffle')
            ->setChoice(SortBy::AUTHOR, 'Author')
            ->setAllowNull(false)
            ->setDefaultValue(SortBy::POPULAR)
            ->setLayout('vertical')
            ->setReturnFormat(ACFField::RETURN_VALUE);

        return apply_filters(sprintf('willow/acf/field=%s', $field->getKey()), $field);
    }

    public static function getTeaserAmountField(): ?ACFField
    {
        if (self::getMaxTeasers() > 1) {
            $field = new NumberField(
                sprintf('field_%s', hash('md5', self::$widgetName . AcfName::FIELD_TEASER_AMOUNT))
            );
            $field->setLabel('Amount of Teasers to display')
                ->setName(AcfName::FIELD_TEASER_AMOUNT)
                ->setInstructions(
                    'How many teasers should it contain?<br><b>Note:</b> Cxense max Teasers is configured to 10.'
                )
                ->setRequired(true)
                ->setConditionalLogic(new ACFConditionalLogic(
                    self::$sortByField,
                    ACFConditionalLogic::OPERATOR_NOT_EQUALS,
                    SortBy::MANUAL
                ))
                ->setDefaultValue(self::getDefaultTeaserAmount())
                ->setMin(self::getMinTeasers())
                ->setMax(self::getMaxTeasers());

            return apply_filters(sprintf('willow/acf/field=%s', $field->getKey()), $field);
        }
        return null;
    }

    public static function getSkipTeasersAmountField(): ACFField
    {
        $field = new NumberField(
            sprintf('field_%s', hash('md5', self::$widgetName . AcfName::FIELD_SKIP_TEASERS_AMOUNT))
        );
        $field->setLabel('Amount of Teasers skip')
            ->setName(AcfName::FIELD_SKIP_TEASERS_AMOUNT)
            ->setInstructions('How many teasers should it skip?')
            ->setConditionalLogic(new ACFConditionalLogic(
                self::$sortByField,
                ACFConditionalLogic::OPERATOR_EQUALS,
                SortBy::CUSTOM
            ))
            ->setMin(0)
            ->setMax(PHP_INT_MAX);

        return apply_filters(sprintf('willow/acf/field=%s', $field->getKey()), $field);
    }

    public static function getTeaserListField(): ACFField
    {
        $field = new CustomRelationshipField(
            sprintf('field_%s', hash('md5', self::$widgetName . AcfName::FIELD_TEASER_LIST))
        );
        $field->setLabel('Teasers')
            ->setName(AcfName::FIELD_TEASER_LIST)
            ->setRequired(true)
            ->setConditionalLogic(new ACFConditionalLogic(
                self::$sortByField,
                ACFConditionalLogic::OPERATOR_EQUALS,
                SortBy::MANUAL
            ))
            ->setPostTypes([
                WpComposite::POST_TYPE
            ])
            ->setFilters([
                'search',
                'taxonomy'
            ])
            ->setMin(self::getMinTeasers())
            ->setMax(self::getMaxTeasers())
            ->setReturnFormat(ACFField::RETURN_OBJECT);

        return apply_filters(sprintf('willow/acf/field=%s', $field->getKey()), $field);
    }

    public static function getCategoryField($multiSelect = false): ACFField
    {
        $condition = new ACFConditionalLogic();
        $condition->add(self::$sortByField, ACFConditionalLogic::OPERATOR_EQUALS, SortBy::CUSTOM)
            ->add(self::$sortByField, ACFConditionalLogic::OPERATOR_EQUALS, SortBy::POPULAR);

        $field = new TaxonomyField(sprintf('field_%s', hash('md5', self::$widgetName . AcfName::FIELD_CATEGORY)));
        $field->setLabel('Category')
            ->setName(AcfName::FIELD_CATEGORY)
            ->setConditionalLogic($condition)
            ->setWrapper((new ACFWrapper())->setWidth('50'))
            ->setTaxonomy(TaxonomyField::TAXONOMY_CATEGORY)
            ->setFieldType($multiSelect ? TaxonomyField::TYPE_MULTI : TaxonomyField::TYPE_SELECT)
            ->setAllowNull(true)
            ->setAddTerm(false)
            ->setSaveTerms(false)
            ->setLoadTerms(false)
            ->setReturnFormat(ACFField::RETURN_OBJECT)
            ->setMultiple($multiSelect);

        return apply_filters(sprintf('willow/acf/field=%s', $field->getKey()), $field);
    }

    public static function getTagField($multiSelect = false): ACFField
    {
        $condition = new ACFConditionalLogic();
        $condition->add(self::$sortByField, ACFConditionalLogic::OPERATOR_EQUALS, SortBy::CUSTOM)
            ->add(self::$sortByField, ACFConditionalLogic::OPERATOR_EQUALS, SortBy::POPULAR);

        $field = new TaxonomyField(sprintf('field_%s', hash('md5', self::$widgetName . AcfName::FIELD_TAG)));
        $field->setLabel('Tag')
            ->setName(AcfName::FIELD_TAG)
            ->setConditionalLogic($condition)
            ->setWrapper((new ACFWrapper())->setWidth('50'))
            ->setTaxonomy(TaxonomyField::TAXONOMY_TAG)
            ->setFieldType($multiSelect ? TaxonomyField::TYPE_MULTI : TaxonomyField::TYPE_SELECT)
            ->setAllowNull(true)
            ->setAddTerm(false)
            ->setSaveTerms(false)
            ->setLoadTerms(false)
            ->setReturnFormat(ACFField::RETURN_OBJECT)
            ->setMultiple($multiSelect);

        return apply_filters(sprintf('willow/acf/field=%s', $field->getKey()), $field);
    }

    public static function getEditorialTypeField(): ACFField
    {
        $condition = new ACFConditionalLogic();
        $condition->add(self::$sortByField, ACFConditionalLogic::OPERATOR_EQUALS, SortBy::CUSTOM)
            ->add(self::$sortByField, ACFConditionalLogic::OPERATOR_EQUALS, SortBy::POPULAR);

        $field = new TaxonomyField(sprintf('field_%s', hash('md5', self::$widgetName . AcfName::FIELD_EDITORIAL_TYPE)));
        $field->setLabel('Editorial Type')
            ->setName(AcfName::FIELD_EDITORIAL_TYPE)
            ->setConditionalLogic($condition)
            ->setTaxonomy(TaxonomyField::TAXONOMY_EDITORIAL_TYPE)
            ->setFieldType(TaxonomyField::TYPE_SELECT)
            ->setAllowNull(true)
            ->setAddTerm(false)
            ->setSaveTerms(true)
            ->setLoadTerms(false)
            ->setReturnFormat(ACFField::RETURN_OBJECT)
            ->setMultiple(false);
        return apply_filters(sprintf('willow/acf/field=%s', $field->getKey()), $field);
    }

    public static function getUserField(): ACFField
    {
        $field = new UserField(sprintf('field_%s', hash('md5', self::$widgetName . AcfName::FIELD_USER)));
        $field->setLabel('Author')
            ->setName(AcfName::FIELD_USER)
            ->setConditionalLogic(new ACFConditionalLogic(
                self::$sortByField,
                ACFConditionalLogic::OPERATOR_EQUALS,
                SortBy::AUTHOR
            ))
            ->setRole('editor')
            ->setAllowNull(true)
            ->setMultiple(false)
            ->setReturnFormat(ACFField::RETURN_ARRAY);

        return apply_filters(sprintf('willow/acf/field=%s', $field->getKey()), $field);
    }

    private static function getIncludeCategoryChildrenField(): ACFField
    {
        $condition = new ACFConditionalLogic();
        $condition->add(self::$sortByField, ACFConditionalLogic::OPERATOR_EQUALS, SortBy::CUSTOM);

        $field = new TrueFalseField(sprintf('field_%s', hash('md5', self::$widgetName . AcfName::FIELD_INCLUDE_CATEGORY_CHILDREN)));
        $field->setLabel('Include category children')
            ->setName(AcfName::FIELD_INCLUDE_CATEGORY_CHILDREN)
            ->setConditionalLogic($condition)
            ->setWrapper((new ACFWrapper())->setWidth('50'))
            ->setDefaultValue(true);

        return apply_filters(sprintf('willow/acf/field=%s', $field->getKey()), $field);
    }

    private static function getCategoryTagRelationField(): ACFField
    {
        $condition = new ACFConditionalLogic();
        $condition->add(self::$sortByField, ACFConditionalLogic::OPERATOR_EQUALS, SortBy::CUSTOM);

        $field = new SelectField(sprintf('field_%s', hash('md5', self::$widgetName . AcfName::FIELD_CATEGORY_TAG_RELATION)));
        $field->setLabel('Category/tags relation')
            ->setName(AcfName::FIELD_CATEGORY_TAG_RELATION)
            ->setConditionalLogic($condition)
            ->setWrapper((new ACFWrapper())->setWidth('50'))
            ->addChoice(SortBy::AND, 'And')
            ->addChoice(SortBy::OR, 'Or')
            ->setDefaultValue([SortBy::AND, 'And'])
            ->setReturnFormat(ACFField::RETURN_VALUE);

        return apply_filters(sprintf('willow/acf/field=%s', $field->getKey()), $field);
    }

    private static function getCategoryOperationField(): ACFField
    {
        $condition = new ACFConditionalLogic();
        $condition->add(self::$sortByField, ACFConditionalLogic::OPERATOR_EQUALS, SortBy::CUSTOM);

        $field = new SelectField(sprintf('field_%s', hash('md5', self::$widgetName . AcfName::FIELD_CATEGORY_OPERATION)));
        $field->setLabel('Category operation')
            ->setName(AcfName::FIELD_CATEGORY_OPERATION)
            ->setConditionalLogic($condition)
            ->setWrapper((new ACFWrapper())->setWidth('50'))
            ->addChoice(SortBy::AND, 'And')
            ->addChoice(SortBy::IN, 'In')
            ->addChoice(SortBy::NOT_IN, 'Not in')
            ->setDefaultValue([SortBy::IN, 'In'])
            ->setReturnFormat(ACFField::RETURN_VALUE);

        return apply_filters(sprintf('willow/acf/field=%s', $field->getKey()), $field);
    }

    private static function getTagOperationField(): ACFField
    {
        $condition = new ACFConditionalLogic();
        $condition->add(self::$sortByField, ACFConditionalLogic::OPERATOR_EQUALS, SortBy::CUSTOM);

        $field = new SelectField(sprintf('field_%s', hash('md5', self::$widgetName . AcfName::FIELD_TAG_OPERATION)));
        $field->setLabel('Tag operation')
            ->setName(AcfName::FIELD_TAG_OPERATION)
            ->setConditionalLogic($condition)
            ->setWrapper((new ACFWrapper())->setWidth('50'))
            ->addChoice(SortBy::AND, 'And')
            ->addChoice(SortBy::IN, 'In')
            ->addChoice(SortBy::NOT_IN, 'Not in')
            ->setDefaultValue([SortBy::IN, 'In'])
            ->setReturnFormat(ACFField::RETURN_VALUE);

        return apply_filters(sprintf('willow/acf/field=%s', $field->getKey()), $field);
    }

    private static function getMinTeasers()
    {
        return array_get(self::$config, 'minTeasers', 1);
    }

    private static function getMaxTeasers()
    {
        switch (PageFieldGroup::$brand) {
            case 'IFO' :
                return 50;
        }

        return array_get(self::$config, 'minTeasers', 12);
    }

    private static function getDefaultTeaserAmount()
    {
        return array_get(self::$config, 'teaserCountDefault', 4);
    }
}
