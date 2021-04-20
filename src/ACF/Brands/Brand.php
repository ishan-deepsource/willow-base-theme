<?php


namespace Bonnier\Willow\Base\ACF\Brands;


use Bonnier\Willow\Base\Models\ACF\ACFField;
use Bonnier\Willow\Base\Models\ACF\ACFGroup;
use Bonnier\Willow\Base\Models\ACF\ACFLayout;
use Bonnier\Willow\Base\Models\ACF\Composite\CompositeFieldGroup;
use Bonnier\Willow\Base\Models\ACF\Composite\TeaserFieldGroup;
use Bonnier\Willow\Base\Models\ACF\Fields\FlexibleContentField;
use Bonnier\Willow\Base\Models\ACF\Fields\GroupField;
use Bonnier\Willow\Base\Models\ACF\Fields\RepeaterField;
use Bonnier\Willow\Base\Models\ACF\Fields\UrlField;
use Bonnier\Willow\Base\Models\ACF\Page\PageFieldGroup;
use Bonnier\Willow\Base\Models\ACF\Page\SortByFields;

abstract class Brand implements BrandInterface
{
    public static $pageWidgetsField;
    public static $compositeContentsField;
    public static $paragraphListWidget;
    public static $infoboxWidget;

    public static function init() {
        PageFieldGroup::setBrand(class_basename(static::class));
        self::$pageWidgetsField = PageFieldGroup::getPageWidgetsField();
        self::$compositeContentsField = CompositeFieldGroup::getContentField();
        self::$paragraphListWidget = CompositeFieldGroup::getParagraphListWidget();
        self::$infoboxWidget = CompositeFieldGroup::getInfoboxWidget();
    }

    public static function removeTeaserVideoUrlField(ACFGroup $group)
    {
        $fields = array_filter($group->getFields(), function (ACFField $field) {
            return $field->getName() !== TeaserFieldGroup::VIDEO_URL_FIELD_NAME;
        });
        return $group->setFields($fields);
    }

    public static function removeOtherAuthorsField(ACFGroup $group)
    {
        $fields = array_filter($group->getFields(), function (ACFField $field) {
            return $field->getName() !== CompositeFieldGroup::OTHER_AUTHERS_FIELD_NAME;
        });
        return $group->setFields($fields);
    }

    public static function removeVideoUrlField(ACFLayout $layout)
    {
        $subFields = array_filter($layout->getSubFields(), function (ACFField $field) {
            if ($field instanceof RepeaterField) {
                $field->setSubFields($field->removeVideoUrlFromSubFields());
            }
            return $field->getName() !== CompositeFieldGroup::VIDEO_URL_FIELD_NAME;
        });
        return $layout->setSubFields($subFields);
    }

    public static function removeImageField(ACFLayout $layout)
    {
        $fields = array_filter($layout->getSubFields(), function (ACFField $field) {
            return $field->getName() !== CompositeFieldGroup::IMAGE_FIELD;
        });
        return $layout->setSubFields($fields);
    }

    public static function removeIncludeIntroVideoField(ACFLayout $layout)
    {
        $fields = array_filter($layout->getSubFields(), function (ACFField $field) {
            return $field->getName() !== CompositeFieldGroup::VIDEO_INCLUDE_INTRO_VIDEO_FIELD;
        });
        return $layout->setSubFields($fields);
    }

    public static function removeChapterItemsField(ACFLayout $layout)
    {
        $fields = array_filter($layout->getSubFields(), function (ACFField $field) {
            return $field->getName() !== CompositeFieldGroup::VIDEO_CHAPTER_ITEMS_FIELD;
        });
        return $layout->setSubFields($fields);
    }

    public static function removeThemeField(ACFLayout $layout)
    {
        $fields = array_filter($layout->getSubFields(), function (ACFField $field) {
            return $field->getName() !== PageFieldGroup::THEME_FIELD_NAME;
        });
        return $layout->setSubFields($fields);
    }

    public static function removeSortByEditorialTypeField(ACFLayout $layout)
    {
        $fields = array_filter($layout->getSubFields(), function (ACFField $field) {
            return $field->getName() !== SortByFields::SORT_BY_EDITORIAL_TYPE;
        });
        return $layout->setSubFields($fields);
    }

    public static function removeTitleField(ACFLayout $layout)
    {
        $fields = array_filter($layout->getSubFields(), function (ACFField $field) {
            return $field->getName() !== CompositeFieldGroup::TITLE_FIELD;
        });
        return $layout->setSubFields($fields);
    }

    public static function removeDisplayHintField(ACFLayout $layout)
    {
        $fields = array_filter($layout->getSubFields(), function (ACFField $field) {
            return $field->getName() !== CompositeFieldGroup::DISPLAY_HINT_FIELD;
        });
        return $layout->setSubFields($fields);
    }

    protected static function removeVideoUrlFromImageWidget()
    {
        $imageWidget = CompositeFieldGroup::getImageWidget();
        add_filter(sprintf('willow/acf/layout=%s', $imageWidget->getKey()), [__CLASS__, 'removeVideoUrlField']);
    }

    protected static function removeIncludeIntroVideoFromVideoWidget(): void
    {
        $videoWidget = CompositeFieldGroup::getVideoWidget();
        add_filter(sprintf('willow/acf/layout=%s', $videoWidget->getKey()), [__CLASS__, 'removeIncludeIntroVideoField']);
    }

    protected static function removeChapterItemsFromVideoWidget(): void
    {
        $videoWidget = CompositeFieldGroup::getVideoWidget();
        add_filter(sprintf('willow/acf/layout=%s', $videoWidget->getKey()), [__CLASS__, 'removeChapterItemsField']);
    }

    protected static function removeVideoUrlFromParagraphListWidget(): void
    {
        $paragraphListWidget = self::$paragraphListWidget;
	    add_filter(sprintf('willow/acf/layout=%s', $paragraphListWidget->getKey()), [__CLASS__, 'removeVideoUrlField']);
    }

    protected static function removeVideoUrlFromParagraphListItems(): void
    {
        $paragraphListWidget = self::$paragraphListWidget;
        add_filter(sprintf('willow/acf/layout=%s', $paragraphListWidget->getKey()), [__CLASS__, 'removeVideoUrlField']);
    }

    protected static function removeVideoUrlFromGalleryItems(): void
    {
        $galleryWidget = CompositeFieldGroup::getGalleryWidget();
        add_filter(sprintf('willow/acf/layout=%s', $galleryWidget->getKey()), [__CLASS__, 'removeVideoUrlField']);
    }

    protected static function removeVideoUrlFromTeaserImages()
    {
        $teaserFieldGroupId = TeaserFieldGroup::TEASER_FIELD_GROUP_ID;
        add_filter(sprintf('willow/acf/group=%s', $teaserFieldGroupId), [__CLASS__, 'removeTeaserVideoUrlField']);
    }

    protected static function removeOtherAuthors()
    {
        $compositeFieldGroupId = CompositeFieldGroup::COMPOSITE_FIELD_GROUP;
        add_filter(sprintf('willow/acf/group=%s', $compositeFieldGroupId), [__CLASS__, 'removeOtherAuthorsField']);
    }

    protected static function removeImageFromInfoboxWidget()
    {
        $infoboxWidget = self::$infoboxWidget;
        add_filter(sprintf('willow/acf/layout=%s', $infoboxWidget->getKey()), [__CLASS__, 'removeImageField']);
    }

    protected static function removeThemeFromTeaserListPageWidget()
    {
        $teaserListLayout = PageFieldGroup::getTeaserListLayout();
        add_filter(sprintf('willow/acf/layout=%s', $teaserListLayout->getKey()), [__CLASS__, 'removeThemeField']);
    }

    protected static function removeSortByEditorialTypeFromTeaserListPageWidget()
    {
        $teaserListLayout = PageFieldGroup::getTeaserListLayout();
        add_filter(sprintf('willow/acf/layout=%s', $teaserListLayout->getKey()), [__CLASS__, 'removeSortByEditorialTypeField']);
    }

    protected static function removeTitleFromAssociatedCompositesWidget()
    {
        $associatedComposites = CompositeFieldGroup::getAssociatedCompositeWidget();
        add_filter(sprintf('willow/acf/layout=%s', $associatedComposites->getKey()), [__CLASS__, 'removeTitleField']);
    }

    protected static function removeDisplayHintFromAssociatedCompositesWidget()
    {
        $associatedComposites = CompositeFieldGroup::getAssociatedCompositeWidget();
        add_filter(sprintf('willow/acf/layout=%s', $associatedComposites->getKey()), [__CLASS__, 'removeDisplayHintField']);
    }

	protected static function removeInventoryWidget()
    {
        $contentField = self::$compositeContentsField;
        add_filter(sprintf('willow/acf/field=%s', $contentField->getKey()), function (FlexibleContentField $contentField) {
            $inventoryField = CompositeFieldGroup::getInventoryWidget();
            return $contentField->removeLayout($inventoryField->getKey());
        });
    }

    protected static function removeAudioWidget()
    {
        $contentField = self::$compositeContentsField;
        add_filter(sprintf('willow/acf/field=%s', $contentField->getKey()), function (FlexibleContentField $contentField) {
            $audioField = CompositeFieldGroup::getAudioWidget();
            return $contentField->removeLayout($audioField->getKey());
        });
    }

    protected static function removeMultimediaWidget()
    {
        $contentField = self::$compositeContentsField;
        add_filter(sprintf('willow/acf/field=%s', $contentField->getKey()), function (FlexibleContentField $contentField) {
            $multimediaField = CompositeFieldGroup::getAudioWidget();
            return $contentField->removeLayout($multimediaField->getKey());
        });
    }

    protected static function removeChaptersSummaryWidget()
    {
        $contentField = self::$compositeContentsField;
        add_filter(sprintf('willow/acf/field=%s', $contentField->getKey()), function (FlexibleContentField $contentField) {
            $chaptersSummaryField = CompositeFieldGroup::getChaptersSummaryWidget();
            return $contentField->removeLayout($chaptersSummaryField->getKey());
        });
    }

    protected static function removeProductWidget()
    {
        $contentField = self::$compositeContentsField;
        add_filter(sprintf('willow/acf/field=%s', $contentField->getKey()), function (FlexibleContentField $contentField) {
            $productField = CompositeFieldGroup::getProductWidget();
            return $contentField->removeLayout($productField->getKey());
        });
    }

    protected static function removeRecipeWidget()
    {
        $contentField = self::$compositeContentsField;
        add_filter(sprintf('willow/acf/field=%s', $contentField->getKey()), function (FlexibleContentField $contentField) {
            $recipeField = CompositeFieldGroup::getRecipeWidget();
            return $contentField->removeLayout($recipeField->getKey());
        });
    }

    protected static function removeCalculatorWidget()
    {
        $contentField = self::$compositeContentsField;
        add_filter(sprintf('willow/acf/field=%s', $contentField->getKey()), function (FlexibleContentField $contentField) {
            $calculatorField = CompositeFieldGroup::getCalculatorWidget();
            return $contentField->removeLayout($calculatorField->getKey());
        });
    }

    protected static function removeQuotePageWidget()
    {
        $pageWidgetsField = self::$pageWidgetsField;
        add_filter(sprintf('willow/acf/field=%s', $pageWidgetsField->getKey()), function (FlexibleContentField $contentField) {
            $quoteTeaserLayout = PageFieldGroup::getQuoteTeaserLayout();
            return $contentField->removeLayout($quoteTeaserLayout->getKey());
        });
    }

    protected static function removeFeaturedContentPageWidget()
    {
        $pageWidgetsField = self::$pageWidgetsField;
        add_filter(sprintf('willow/acf/field=%s', $pageWidgetsField->getKey()), function (FlexibleContentField $contentField) {
            $chaptersSummaryField = PageFieldGroup::getFeaturedContentLayout();
            return $contentField->removeLayout($chaptersSummaryField->getKey());
        });
    }
}
