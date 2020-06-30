<?php

namespace Bonnier\Willow\Base\ACF\Brands;

use Bonnier\Willow\Base\Models\ACF\Composite\CompositeFieldGroup;
use Bonnier\Willow\Base\Models\ACF\Fields\FlexibleContentField;

class HIS implements BrandInterface
{

    public static function register(): void
    {
        $contentField = CompositeFieldGroup::getContentField();
        add_filter(sprintf('willow/acf/field=%s', $contentField->getKey()), [__CLASS__, 'removeInventory']);
    }

    public static function removeInventory(FlexibleContentField $contentField)
    {
        $inventoryField = CompositeFieldGroup::getInventoryWidget();
        return $contentField->removeLayout($inventoryField->getKey());
    }
}
