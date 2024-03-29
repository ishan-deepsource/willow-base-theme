<?php

namespace Bonnier\Willow\Base\ACF\Brands;

use Bonnier\Willow\Base\Models\ACF\ACFField;
use Bonnier\Willow\Base\Models\ACF\ACFLayout;
use Bonnier\Willow\Base\Models\ACF\Composite\CompositeFieldGroup;

class HIS extends Brand
{

    public static function register(?string $brandCode = null): void
    {
        self::init();
        self::removeImageFromInfoboxWidget();
        self::removeIncludeIntroVideoFromVideoWidget();
        self::removeChapterItemsFromVideoWidget();
        self::removeThemeFromTeaserListPageWidget();
        self::removeSortByEditorialTypeFromTeaserListPageWidget();
        self::removeAdvancedCustomSortByFieldsFromTeaserListPageWidget();
        self::removeTitleFromAssociatedCompositesWidget();
        self::removeDisplayHintFromAssociatedCompositesWidget();
        self::removeUseAsArticleLeadImageFromRecipeWidget();

        self::removeInventoryWidget();
        self::removeMultimediaWidget();
        self::removeProductWidget();
        self::removeRecipeWidget();
        self::removeCalculatorWidget();

        $paragraphListWidget = parent::$paragraphListWidget;
        add_filter(sprintf('willow/acf/layout=%s', $paragraphListWidget->getKey()), [__CLASS__, 'removeParagraphListShowNumbers']);
    }

    public static function removeParagraphListShowNumbers(ACFLayout $layout)
    {
        $subFields = array_filter($layout->getSubFields(), function (ACFField $field) {
            return $field->getName() !== CompositeFieldGroup::SHOW_NUMBERS_FIELD_NAME;
        });
        return $layout->setSubFields($subFields);
    }
}
