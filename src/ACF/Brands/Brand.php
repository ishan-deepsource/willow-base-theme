<?php


namespace Bonnier\Willow\Base\ACF\Brands;


use Bonnier\Willow\Base\Models\ACF\ACFField;
use Bonnier\Willow\Base\Models\ACF\ACFLayout;
use Bonnier\Willow\Base\Models\ACF\Composite\CompositeFieldGroup;
use Bonnier\Willow\Base\Models\ACF\Composite\TeaserFieldGroup;
use Bonnier\Willow\Base\Models\ACF\Fields\FlexibleContentField;
use Bonnier\Willow\Base\Models\ACF\Fields\RepeaterField;

abstract class Brand implements BrandInterface
{
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

    protected static function removeVideoUrlFromImageWidget()
    {
        $imageWidget = CompositeFieldGroup::getImageWidget();
        add_filter(sprintf('willow/acf/layout=%s', $imageWidget->getKey()), [__CLASS__, 'removeVideoUrlField']);
    }

    protected static function removeVideoUrlFromParagraphListWidget(): void
    {
	   $paragraphListWidget= CompositeFieldGroup::getParagraphListWidget();
	    add_filter(sprintf('willow/acf/layout=%s', $paragraphListWidget->getKey()), [__CLASS__, 'removeVideoUrlField']);
    }

    protected static function removeVideoUrlFromParagraphListItems(): void
    {
        $paragraphListWidget= CompositeFieldGroup::getParagraphListWidget();
        add_filter(sprintf('willow/acf/layout=%s', $paragraphListWidget->getKey()), [__CLASS__, 'removeVideoUrlField']);
    }

    protected static function removeVideoUrlFromGalleryItems(): void
    {
        $galleryWidget= CompositeFieldGroup::getGalleryWidget();
        add_filter(sprintf('willow/acf/layout=%s', $galleryWidget->getKey()), [__CLASS__, 'removeVideoUrlField']);
    }

    protected static function removeVideoUrlFromTeaserImages()
    {
        $teaserFieldGroup = TeaserFieldGroup::getTeaserVideoUrlField();
        add_filter(sprintf('willow/acf/layout=%s', $teaserFieldGroup->getKey()), [__CLASS__, 'removeVideoUrlField']);
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
