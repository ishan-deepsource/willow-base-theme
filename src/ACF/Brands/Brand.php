<?php


namespace Bonnier\Willow\Base\ACF\Brands;


use Bonnier\Willow\Base\Models\ACF\ACFLayout;
use Bonnier\Willow\Base\Models\ACF\Composite\CompositeFieldGroup;
use Bonnier\Willow\Base\Models\ACF\Fields\FlexibleContentField;

abstract class Brand implements BrandInterface
{
    protected static function removeVideoUrlFromImageWidget()
    {
        $imageWidget = CompositeFieldGroup::getImageWidget();
        add_filter(sprintf('willow/acf/field=%s', $imageWidget->getKey()), function (ACFLayout $imageWidget) {
            $videoUrlField = CompositeFieldGroup::getVideoUrlField();
            return $imageWidget->removeSubField($videoUrlField->getKey());
        });
    }

    protected static function removeInventoryWidget()
    {
        $contentField = CompositeFieldGroup::getContentField();
        add_filter(sprintf('willow/acf/field=%s', $contentField->getKey()), function (FlexibleContentField $contentField) {
            $inventoryField = CompositeFieldGroup::getInventoryWidget();
            return $contentField->removeLayout($inventoryField->getKey());
        });
    }
}
