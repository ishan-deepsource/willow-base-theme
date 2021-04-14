<?php

namespace Bonnier\Willow\Base\ACF\Brands;

use Bonnier\Willow\Base\Models\ACF\ACFField;
use Bonnier\Willow\Base\Models\ACF\ACFLayout;
use Bonnier\Willow\Base\Models\ACF\Composite\CompositeFieldGroup;
use Bonnier\Willow\Base\Models\ACF\Fields\RadioField;
use Bonnier\Willow\Base\Models\ACF\Fields\TrueFalseField;
use Bonnier\Willow\Base\Models\ACF\Page\PageFieldGroup;

class IFO extends Brand
{
    public static function register(): void
    {
        self::init();
        self::removeVideoUrlFromImageWidget();
        self::removeVideoUrlFromGalleryItems();
        self::removeVideoUrlFromParagraphListWidget();
        self::removeVideoUrlFromTeaserImages();
        self::removeImageFromInfoboxWidget();
        self::removeSortByEditorialTypeFromTeaserListPageWidget();
        self::removeImageRepeaterFromFile();

        self::removeInventoryWidget();
        self::removeAudioWidget();
        self::removeMultimediaWidget();
        self::removeProductWidget();

        self::removeQuotePageWidget();
        self::removeFeaturedContentPageWidget();

        $teaserListWidget =  PageFieldGroup::getTeaserListLayout();
        add_filter(sprintf('willow/acf/layout=%s', $teaserListWidget->getKey()), [__CLASS__, 'setTeaserListDisplayHints']);

        $galleryField = CompositeFieldGroup::getGalleryWidget();
        add_filter(sprintf('willow/acf/layout=%s', $galleryField->getKey()), [__CLASS__, 'setGalleryDisplayHints']);

        $imageWidget = CompositeFieldGroup::getImageWidget();
        add_filter(sprintf('willow/acf/layout=%s', $imageWidget->getKey()), [__CLASS__, 'setImageDisplayHints']);

        $paragraphListWidget = parent::$paragraphListWidget;
        add_filter(sprintf('willow/acf/layout=%s', $paragraphListWidget->getKey()), [__CLASS__, 'setParagraphListDisplayHints']);
        add_filter(sprintf('willow/acf/layout=%s', $paragraphListWidget->getKey()), [__CLASS__, 'removeParagraphListCollapsible']);

        $infoBoxWidget = parent::$infoboxWidget;
        add_filter(sprintf('willow/acf/layout=%s', $infoBoxWidget->getKey()), [__CLASS__, 'setInfoBoxDisplayHints']);

        $associatedCompositeWidget = CompositeFieldGroup::getAssociatedCompositeWidget();
        add_filter(sprintf('willow/acf/layout=%s', $associatedCompositeWidget->getKey()), [__CLASS__, 'setAssociatedCompositeDisplayHints']);

        $videoWidget = CompositeFieldGroup::getVideoWidget();
        add_filter(sprintf('willow/acf/layout=%s', $videoWidget->getKey()), [__CLASS__, 'setIncludeIntroVideoDefaultTrue']);
    }

    public static function setTeaserListDisplayHints(ACFLayout $teaserList)
    {
        $displayHint = new RadioField('field_5bb319a1ffcf1');
        $displayHint->setLabel('Display Format')
            ->setName('display_hint')
            ->setChoice('1col', '1 Col')
            ->setChoice('2col', '2 Col')
            ->setChoice('3col', '3 Col')
            ->setChoice('4col', '4 Col')
            ->setChoice('2plus1', '2 + 1')
            ->setChoice('1plus2', '1 + 2')
            ->setChoice('1plus4', '1 + 4')
            ->setChoice('4plus1', '4 + 1')
            ->setChoice('slider4col', 'Slider - 4 col')
            ->setChoice('slider-netflix', 'Slider - netflix')
            ->setChoice('text+4', 'text + 4')
            ->setChoice('toplist', 'Top list')
            ->setChoice('featured', 'Featured')
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

    public static function setInfoBoxDisplayHints(ACFLayout $infoBox)
    {
        $displayHint = new RadioField('field_5f60afb647c6e');
        $displayHint->setLabel('Display Format')
            ->setName('display_hint')
            ->setChoice('default', 'Default')
            ->setChoice('border', 'Border')
            ->setChoice('solid', 'Solid')
            ->setDefaultValue('default')
            ->setLayout('vertical')
            ->setReturnFormat(ACFField::RETURN_VALUE);

        return $infoBox->addSubField($displayHint);
    }

    public static function setParagraphListDisplayHints(ACFLayout $paragraphList)
    {
        return $paragraphList->mapSubFields(function (ACFField $field) {
            if ($field instanceof RadioField && $field->getName() === 'display_hint') {
                $field->setChoices([
                    'accordion' => 'Accordion',
                    'box' => 'Box',
                    'border' => 'Border',
                    'clean' => 'Clean',
                    'fold-out-no-items' => 'Fold out - no items',
                    'img-left-no-items' => 'Img left - no items',
                ]);
                $field->setDefaultValue('box');
            }
            return $field;
        });
    }

    public static function setAssociatedCompositeDisplayHints(ACFLayout $associatedComposite)
    {
        $displayHint = new RadioField('field_603f7f06ddaac');
        $displayHint->setLabel('Display Format')
            ->setName('display_hint')
            ->setChoice('default', 'Default')
            ->setChoice('food-plan', 'Food plan')
            ->setDefaultValue('default')
            ->setLayout('vertical')
            ->setReturnFormat(ACFField::RETURN_VALUE);

        return $associatedComposite->addSubField($displayHint);
    }

    public static function removeParagraphListCollapsible(ACFLayout $layout)
    {
        $subFields = array_filter($layout->getSubFields(), function (ACFField $field) {
            return $field->getName() !== CompositeFieldGroup::COLLAPSIBLE_FIELD_NAME;
        });
        return $layout->setSubFields($subFields);
    }

    public static function setIncludeIntroVideoDefaultTrue(ACFLayout $videoWidget)
    {
        $includeIntroVideo = new TrueFalseField('field_6061945f12bd9');
        $includeIntroVideo->setLabel('Include intro video')
            ->setName(CompositeFieldGroup::VIDEO_INCLUDE_INTRO_VIDEO_FIELD)
            ->setDefaultValue(true);

        return $videoWidget->addSubField($includeIntroVideo);
    }
}
