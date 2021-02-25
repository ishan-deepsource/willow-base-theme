<?php

namespace Bonnier\Willow\Base\ACF\Brands;

use Bonnier\Willow\Base\Models\ACF\ACFField;
use Bonnier\Willow\Base\Models\ACF\ACFLayout;
use Bonnier\Willow\Base\Models\ACF\Composite\CompositeFieldGroup;

class ILL extends Brand
{

    public static function register(): void
    {
        self::init();
        self::removeOtherAuthors();
        self::removeImageFromInfoboxWidget();
        self::removeChapterItemsFromVideoWidget();
        self::removeTextBlockFromLeadParagraphWidget();
        self::removeDisplayHintFromInfoBoxWidget();

        self::removeInventoryWidget();
        self::removeMultimediaWidget();
        self::removeProductWidget();
        self::removeRecipeWidget();

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
