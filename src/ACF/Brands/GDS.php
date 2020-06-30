<?php

namespace Bonnier\Willow\Base\ACF\Brands;

use Bonnier\Willow\Base\Models\ACF\ACFField;
use Bonnier\Willow\Base\Models\ACF\ACFLayout;
use Bonnier\Willow\Base\Models\ACF\Composite\CompositeFieldGroup;
use Bonnier\Willow\Base\Models\ACF\Fields\FlexibleContentField;
use Bonnier\Willow\Base\Models\ACF\Fields\RadioField;

class GDS implements BrandInterface
{
    public static function register(): void
    {
        $galleryField = CompositeFieldGroup::getGalleryWidget();
        add_filter(sprintf('willow/acf/layout=%s', $galleryField->getKey()), [__CLASS__, 'setGalleryDisplayHints']);

        $contentField = CompositeFieldGroup::getContentField();
        add_filter(sprintf('willow/acf/field=%s', $contentField->getKey()), [__CLASS__, 'removeInventory']);
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

    public static function removeInventory(FlexibleContentField $contentField)
    {
        $inventoryField = CompositeFieldGroup::getInventoryWidget();
        return $contentField->removeLayout($inventoryField->getKey());
    }
}
