<?php

namespace Bonnier\Willow\Base\ACF\Brands;

use Bonnier\Willow\Base\Models\ACF\ACFField;
use Bonnier\Willow\Base\Models\ACF\ACFLayout;
use Bonnier\Willow\Base\Models\ACF\Composite\CompositeFieldGroup;
use Bonnier\Willow\Base\Models\ACF\Fields\RadioField;

class GDS extends Brand
{
    public static function register(): void
    {
        self::removeVideoUrlFromImageWidget();
        self::removeVideoUrlFromGalleryItems();
        self::removeVideoUrlFromParagraphListWidget();
        self::removeVideoUrlFromTeaserImages();
        self::removeInventoryWidget();

        $galleryField = CompositeFieldGroup::getGalleryWidget();
        add_filter(sprintf('willow/acf/layout=%s', $galleryField->getKey()), [__CLASS__, 'setGalleryDisplayHints']);

        $paragraphListWidget = CompositeFieldGroup::getParagraphListWidget();
        add_filter(sprintf('willow/acf/layout=%s', $paragraphListWidget->getKey()), [__CLASS__, 'setParagraphListDisplayHints']);

        $paragraphListWidget = CompositeFieldGroup::getParagraphListWidget();
        add_filter(sprintf('willow/acf/layout=%s', $paragraphListWidget->getKey()), [__CLASS__, 'removeParagraphListCollapsible']);

        $imageWidget = CompositeFieldGroup::getImageWidget();
        add_filter(sprintf('willow/acf/layout=%s', $imageWidget->getKey()), [__CLASS__, 'setImageDisplayHints']);
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


    public static function removeParagraphListCollapsible(ACFLayout $layout)
    {
        $subFields = array_filter($layout->getSubFields(), function (ACFField $field) {
            return $field->getName() !== CompositeFieldGroup::COLLAPSIBLE_FIELD_NAME;
        });
        return $layout->setSubFields($subFields);
    }

    public static function setImageDisplayHints(ACFLayout $paragraphList)
    {
        return $paragraphList->mapSubFields(function (ACFField $field) {
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
}
