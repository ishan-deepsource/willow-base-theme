<?php

namespace Bonnier\Willow\Base\ACF\Brands;

use Bonnier\Willow\Base\Models\ACF\ACFLayout;
use Bonnier\Willow\Base\Models\ACF\Composite\CompositeFieldGroup;
use Bonnier\Willow\Base\Models\ACF\Fields\FlexibleContentField;

class HIS implements BrandInterface
{

    public static function register(): void
    {
        $contentField = CompositeFieldGroup::getContentField();
        add_filter(sprintf('willow/acf/field=%s', $contentField->getKey()), [__CLASS__, 'removeInventory']);
        $imageWidget = CompositeFieldGroup::getImageWidget();
        add_filter(sprintf('willow/acf/field=%s', $imageWidget->getKey()), [__CLASS__, 'removeVideUrlField']);
    }

    public static function removeInventory(FlexibleContentField $contentField)
    {
        $inventoryField = CompositeFieldGroup::getInventoryWidget();
        return $contentField->removeLayout($inventoryField->getKey());
    }

    public static function removeVideoUrlField(ACFLayout $imageWidget)
    {
        $videoUrlField = CompositeFieldGroup::getVideoUrlField();
        return $imageWidget->removeSubField($videoUrlField->getKey());
    }
}
