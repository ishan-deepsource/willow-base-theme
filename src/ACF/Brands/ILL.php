<?php

namespace Bonnier\Willow\Base\ACF\Brands;

use Bonnier\Willow\Base\Models\ACF\ACFLayout;
use Bonnier\Willow\Base\Models\ACF\Composite\CompositeFieldGroup;
use Bonnier\Willow\Base\Models\ACF\Fields\FlexibleContentField;

class ILL implements BrandInterface
{

    public static function register(): void
    {
        $contentField = CompositeFieldGroup::getContentField();
        add_filter(sprintf('willow/acf/field=%s', $contentField->getKey()), [__CLASS__, 'removeInventory']);
        $imageWidget = CompositeFieldGroup::getImageWidget();
        add_filter(sprintf('willow/acf/layout=%s', $imageWidget->getKey()), [__CLASS__, 'addVideoUrlField']);
    }

    public static function removeInventory(FlexibleContentField $contentField)
    {
        $inventoryField = CompositeFieldGroup::getInventoryWidget();
        return $contentField->removeLayout($inventoryField->getKey());
    }

    public static function addVideoUrlField(ACFLayout $imageWidget) {
        $videoUrl = CompositeFieldGroup::getVideoUrlField();
        return $imageWidget->addSubField($videoUrl);
    }
}
