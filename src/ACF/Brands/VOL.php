<?php


namespace Bonnier\Willow\Base\ACF\Brands;


use Bonnier\Willow\Base\Models\ACF\ACFField;
use Bonnier\Willow\Base\Models\ACF\ACFLayout;
use Bonnier\Willow\Base\Models\ACF\Composite\CompositeFieldGroup;
use Bonnier\Willow\Base\Models\ACF\Fields\RadioField;
use Bonnier\Willow\Base\Models\ACF\Page\PageFieldGroup;

class VOL extends Brand
{

    public static function register(): void
    {
        self::init();
        self::removeVideoUrlFromImageWidget();
        self::removeVideoUrlFromGalleryItems();
        self::removeVideoUrlFromParagraphListWidget();
        self::removeVideoUrlFromTeaserImages();
        self::removeImageFromInfoboxWidget();

        self::removeInventoryWidget();
        self::removeAudioWidget();
        self::removeMultimediaWidget();
        self::removeProductWidget();

        self::removeQuotePageWidget();
        self::removeFeaturedContentPageWidget();
        self::removeRecipeWidget();

        $teaserListWidget =  PageFieldGroup::getTeaserListLayout();
        add_filter(sprintf('willow/acf/layout=%s', $teaserListWidget->getKey()), [__CLASS__, 'setTeaserListDisplayHints']);

        $galleryField = CompositeFieldGroup::getGalleryWidget();
        add_filter(sprintf('willow/acf/layout=%s', $galleryField->getKey()), [__CLASS__, 'setGalleryDisplayHints']);

        $imageWidget = CompositeFieldGroup::getImageWidget();
        add_filter(sprintf('willow/acf/layout=%s', $imageWidget->getKey()), [__CLASS__, 'setImageDisplayHints']);

        $paragraphListWidget = parent::$paragraphListWidget;
        add_filter(sprintf('willow/acf/layout=%s', $paragraphListWidget->getKey()), [__CLASS__, 'setParagraphListDisplayHints']);
        add_filter(sprintf('willow/acf/layout=%s', $paragraphListWidget->getKey()), [__CLASS__, 'removeParagraphListCollapsible']);
    }

    public static function setTeaserListDisplayHints(ACFLayout $teaserList)
    {
        $displayHint = new RadioField('field_5bb319a1ffcf1');
        $displayHint->setLabel('Display Format')
            ->setName('display_hint')
            ->setChoice('1plus2', '1 + 2')
            ->setChoice('2plus1', '2 + 1')
            ->setChoice('featured', 'Featured')
            ->setChoice('1col', '1 Col')
            ->setChoice('2col', '2 Col')
            ->setChoice('3col', '3 Col')
            ->setChoice('4col', '4 Col')
            ->setChoice('toplist', 'Top list')
            ->setChoice('slider', 'Slider')
            ->setDefaultValue('1plus2')
            ->setLayout('vertical')
            ->setReturnFormat(ACFField::RETURN_VALUE);

        return $teaserList->addSubField($displayHint);
    }

    public static function setGalleryDisplayHints(ACFLayout $gallery): ACFLayout
    {
        $displayHint = new RadioField('field_5af2a198b1028');
        $displayHint->setLabel('Display Format')
            ->setName('display_hint')
            ->setChoice('default', 'Default')
            ->setChoice('slider', 'Slider')
            ->setDefaultValue('default')
            ->setLayout('vertical')
            ->setReturnFormat(ACFField::RETURN_VALUE);

        return $gallery->addSubField($displayHint);
    }

    public static function setImageDisplayHints(ACFLayout $image)
    {
        return $image->mapSubFields(function (ACFField $field) {
            if ($field instanceof RadioField && $field->getName() === 'display_hint') {
                $field->setChoices([
                    'default' => 'Default',
                    'xl' => 'XL',
                    'sm' => 'SM',
                ]);
                $field->setDefaultValue('default');
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
                    'border' => 'Border',
                    'clean' => 'Clean',
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
}
