<?php

namespace Bonnier\Willow\Base\ACF\Brands;

use Bonnier\Willow\Base\Models\ACF\ACFField;
use Bonnier\Willow\Base\Models\ACF\ACFLayout;
use Bonnier\Willow\Base\Models\ACF\Composite\CompositeFieldGroup;
use Bonnier\Willow\Base\Models\ACF\Fields\ImageField;
use Bonnier\Willow\Base\Models\ACF\Fields\RadioField;
use Bonnier\Willow\Base\Models\ACF\Page\PageFieldGroup;

class GDS extends Brand
{
    public static function register(): void
    {
        self::init();
        self::removeOtherAuthors();
        self::removeVideoUrlFromImageWidget();
        self::removeVideoUrlFromGalleryItems();
        self::removeVideoUrlFromParagraphListWidget();
        self::removeIncludeIntroVideoFromVideoWidget();
        self::removeChapterItemsFromVideoWidget();
        self::removeThemeFromTeaserListPageWidget();
        self::removeAdvancedSortByFieldsFromTeaserListPageWidget();
        self::removeTitleFromAssociatedCompositesWidget();
        self::removeDisplayHintFromAssociatedCompositesWidget();
        self::removeDurationFromVideoWidget();

        self::removeRecipeWidget();
        self::removeInventoryWidget();
        self::removeCalculatorWidget();

        self::removeLanguageTitlesFromUserFieldGroup();

        $galleryField = CompositeFieldGroup::getGalleryWidget();
        add_filter(sprintf('willow/acf/layout=%s', $galleryField->getKey()), [__CLASS__, 'setGalleryDisplayHints']);

        $paragraphListWidget = parent::$paragraphListWidget;
        add_filter(sprintf('willow/acf/layout=%s', $paragraphListWidget->getKey()), [__CLASS__, 'setParagraphListDisplayHints']);

        $imageWidget = CompositeFieldGroup::getImageWidget();
        add_filter(sprintf('willow/acf/layout=%s', $imageWidget->getKey()), [__CLASS__, 'setImageDisplayHints']);

        $infoBoxWidget = parent::$infoboxWidget;
        add_filter(sprintf('willow/acf/layout=%s', $infoBoxWidget->getKey()), [__CLASS__, 'setInfoBoxDisplayHints']);

        $linkWidget = CompositeFieldGroup::getLinkWidget();
        add_filter(sprintf('willow/acf/layout=%s', $linkWidget->getKey()), [__CLASS__, 'addLinkWidgetDisplayHints']);

        $teaserListWidget =  PageFieldGroup::getTeaserListLayout();
        add_filter(sprintf('willow/acf/layout=%s', $teaserListWidget->getKey()), [__CLASS__, 'setTeaserListDisplayHints']);
    }

    public static function setGalleryDisplayHints(ACFLayout $gallery): ACFLayout
    {
        return $gallery->mapSubFields(function (ACFField $field) {
            if ($field instanceof RadioField && $field->getName() === 'display_hint') {
                $field->removeChoice('parallax');
            }
            return $field;
        });
    }

    public static function setParagraphListDisplayHints(ACFLayout $paragraphList)
    {
        return $paragraphList->mapSubFields(function (ACFField $field) {
            if ($field instanceof RadioField && $field->getName() === 'display_hint') {
                $field->setChoices([
                    'box' => 'Box',
                    'text-full' => 'Text Full',
                    'text-half' => 'Text Half',
                    'border' => 'Border',
                    'material-list' => 'Material List',
                    'slider-full-width' => 'Slider Full Width',
                    'slider-cards' => 'Slider Cards',
                ]);
                $field->setDefaultValue('box');
            }
            return $field;
        });
    }

    public static function setImageDisplayHints(ACFLayout $image)
    {
        return $image->mapSubFields(function (ACFField $field) {
            if ($field instanceof RadioField && $field->getName() === 'display_hint') {
                $field->setChoices([
                    'full-width' => 'Full Width',
                    'half-width' => 'Half Width',
                ]);
                $field->setDefaultValue('full-width');
            }
            return $field;
        });
    }

    public static function setInfoBoxDisplayHints(ACFLayout $infoBox)
    {
        $displayHint = new RadioField('field_5f60afb647c6e');
        $displayHint->setLabel('Display Format')
            ->setName('display_hint')
            ->setChoice('yellow', 'Yellow')
            ->setChoice('blue', 'Blue')
            ->setChoice('green', 'Green')
            ->setChoice('red', 'Red')
            ->setDefaultValue('yellow')
            ->setLayout('vertical')
            ->setReturnFormat(ACFField::RETURN_VALUE);

        return $infoBox->addSubField($displayHint);
    }

    public static function addLinkWidgetDisplayHints(ACFLayout $link)
    {
        $displayHint = new RadioField('field_5f916f115010d');
        $displayHint->setLabel('Display Format')
            ->setName('display_hint')
            ->setChoice('default', 'Default')
            ->setChoice('button', 'Button')
            ->setChoice('promo_sales_button', 'Promo sales button (for collections/packages)')
            ->setDefaultValue('default')
            ->setLayout('vertical')
            ->setReturnFormat(ACFField::RETURN_VALUE);

        return $link->addSubField($displayHint);
    }

    public static function setTeaserListDisplayHints(ACFLayout $teaserList)
    {
        $displayHint = new RadioField('field_5bb319a1ffcf1');
        $displayHint->setLabel('Display Format')
            ->setName('display_hint')
            ->setChoice('default', 'Default')
            ->setChoice('1plus2plus4', '1 + 2 + 4')
            ->setChoice('1col', '1 Col')
            ->setChoice('1plus5', '1 + 5')
            ->setDefaultValue('default')
            ->setLayout('vertical')
            ->setReturnFormat(ACFField::RETURN_VALUE);

        return $teaserList->addSubField($displayHint);
    }
}
