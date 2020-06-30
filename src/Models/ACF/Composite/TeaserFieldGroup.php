<?php

namespace Bonnier\Willow\Base\Models\ACF\Composite;

use Bonnier\Willow\Base\Models\ACF\ACFField;
use Bonnier\Willow\Base\Models\ACF\ACFGroup;
use Bonnier\Willow\Base\Models\ACF\Fields\ImageField;
use Bonnier\Willow\Base\Models\ACF\Fields\TabField;
use Bonnier\Willow\Base\Models\ACF\Fields\TextAreaField;
use Bonnier\Willow\Base\Models\ACF\Fields\TextField;
use Bonnier\Willow\Base\Models\ACF\Properties\ACFLocation;
use Bonnier\Willow\Base\Models\WpComposite;

class TeaserFieldGroup
{
    private const TEASER_IMAGE_FIELD = 'field_58e38da2194e3';

    public static function register()
    {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }
        $group = new ACFGroup('group_58e38d7eca92e');
        $location = new ACFLocation();
        $location->addLocation('post_type', ACFLocation::OPERATOR_EQUALS, WpComposite::POST_TYPE)
            ->addLocation('post_type', ACFLocation::OPERATOR_EQUALS, 'page');
        $group->setTitle('Teasers')
            ->setLocation($location)
            ->setMenuOrder(0)
            ->setPosition(ACFGroup::POSITION_AFTER_TITLE)
            ->setStyle(ACFGroup::STYLE_SEAMLESS)
            ->setLabelPlacement(ACFGroup::LABEL_PLACEMENT_TOP)
            ->setInstructionPlacement(ACFGroup::INSTRUCTION_PLACEMENT_LABEL)
            ->setActive(true);

        $group->addField(self::getSiteTeaserField());
        $group->addField(self::getTeaserTitleField());
        $group->addField(self::getTeaserImageField());
        $group->addField(self::getTeaserDescription());
        $group->addField(self::getSEOTeaser());
        $group->addField(self::getSEOTeaserTitle());
        $group->addField(self::getSEOTeaserImage());
        $group->addField(self::getSEOTeaserDescription());
        $group->addField(self::getFacebookTeaser());
        $group->addField(self::getFacebookTeaserTitle());
        $group->addField(self::getFacebookTeaserImage());
        $group->addField(self::getFacebookTeaserDescription());
        $group->addField(self::getTwitterTeaser());
        $group->addField(self::getTwitterTeaserTitle());
        $group->addField(self::getTwitterTeaserImage());
        $group->addField(self::getTwitterTeaserDescription());

        $filteredGroup = apply_filters(sprintf('willow/acf/group=%s', $group->getKey()), $group);

        acf_add_local_field_group($filteredGroup->toArray());

        self::registerValidationHooks();
    }

    public static function getSiteTeaserField(): ACFField
    {
        $siteTeaser = new TabField('field_5aeac69f22931');
        $siteTeaser->setLabel('Site Teaser (eg. Article teasers, Search results, Cxense, etc.)');

        return apply_filters(sprintf('willow/acf/field=%s', $siteTeaser->getKey()), $siteTeaser);
    }

    public static function getTeaserTitleField(): ACFField
    {
        $title = new TextField('field_58e38d86194e2');
        $title->setLabel('Teaser Title')
            ->setName('teaser_title')
            ->setRequired(true);

        return apply_filters(sprintf('willow/acf/field=%s', $title->getKey()), $title);
    }

    public static function getTeaserImageField(): ACFField
    {
        $image = new ImageField(self::TEASER_IMAGE_FIELD);
        $image->setLabel('Teaser Image')
            ->setName('teaser_image')
            ->setRequired(true)
            ->setReturnFormat(ACFField::RETURN_ARRAY)
            ->setPreviewSize(ImageField::PREVIEW_MEDIUM);

        return apply_filters(sprintf('willow/acf/field=%s', $image->getKey()), $image);
    }

    public static function getTeaserDescription(): ACFField
    {
        $description = new TextAreaField('field_58e38dd0194e4');
        $description->setLabel('Teaser Description')
            ->setName('teaser_description')
            ->setRequired(true);

        return apply_filters(sprintf('willow/acf/field=%s', $description->getKey()), $description);
    }

    public static function getSEOTeaser(): ACFField
    {
        $teaser = new TabField('field_5aeac72bfaaf1');
        $teaser->setLabel('SEO Teaser (Google)');

        return apply_filters(sprintf('willow/acf/field=%s', $teaser->getKey()), $teaser);
    }

    public static function getSEOTeaserTitle(): ACFField
    {
        $title = new TextField('field_5aeac749faaf2');
        $title->setLabel('Teaser Title')
            ->setName('seo_teaser_title');

        return apply_filters(sprintf('willow/acf/field=%s', $title->getKey()), $title);
    }

    public static function getSEOTeaserImage(): ACFField
    {
        $image = new ImageField('field_5aeac77c8d9cf');
        $image->setLabel('Teaser Image')
            ->setName('seo_teaser_image')
            ->setReturnFormat(ACFField::RETURN_ARRAY)
            ->setPreviewSize(ImageField::PREVIEW_MEDIUM);

        return apply_filters(sprintf('willow/acf/field=%s', $image->getKey()), $image);
    }

    public static function getSEOTeaserDescription(): ACFField
    {
        $description = new TextAreaField('field_5aeac79e8d9d0');
        $description->setLabel('Teaser Description')
            ->setName('seo_teaser_description');

        return apply_filters(sprintf('willow/acf/field=%s', $description->getKey()), $description);
    }

    public static function getFacebookTeaser(): ACFField
    {
        $teaser = new TabField('field_5aeac7e96eb57');
        $teaser->setLabel('Facebook Teaser');

        return apply_filters(sprintf('willow/acf/field=%s', $teaser->getKey()), $teaser);
    }

    public static function getFacebookTeaserTitle(): ACFField
    {
        $title = new TextField('field_5aeac8356eb58');
        $title->setLabel('Teaser Title')
            ->setName('fb_teaser_title');

        return apply_filters(sprintf('willow/acf/field=%s', $title->getKey()), $title);
    }

    public static function getFacebookTeaserImage(): ACFField
    {
        $image = new ImageField('field_5aeac8476eb59');
        $image->setLabel('Teaser Image')
            ->setName('fb_teaser_image')
            ->setReturnFormat(ACFField::RETURN_ARRAY)
            ->setPreviewSize(ImageField::PREVIEW_MEDIUM);

        return apply_filters(sprintf('willow/acf/field=%s', $image->getKey()), $image);
    }

    public static function getFacebookTeaserDescription(): ACFField
    {
        $description = new TextAreaField('field_5aeac8546eb5a');
        $description->setLabel('Teaser Description')
            ->setName('fb_teaser_description');

        return apply_filters(sprintf('willow/acf/field=%s', $description->getKey()), $description);
    }

    public static function getTwitterTeaser(): ACFField
    {
        $teaser = new TabField('field_5aeac86e39537');
        $teaser->setLabel('Twitter Teaser');

        return apply_filters(sprintf('willow/acf/field=%s', $teaser->getKey()), $teaser);
    }

    public static function getTwitterTeaserTitle(): ACFField
    {
        $title = new TextField('field_5aeac87839538');
        $title->setLabel('Teaser Title')
            ->setName('tw_teaser_title');

        return apply_filters(sprintf('willow/acf/field=%s', $title->getKey()), $title);
    }

    public static function getTwitterTeaserImage(): ACFField
    {
        $image = new ImageField('field_5aeac88039539');
        $image->setLabel('Teaser Image')
            ->setName('tw_teaser_image')
            ->setReturnFormat(ACFField::RETURN_ARRAY)
            ->setPreviewSize(ImageField::PREVIEW_MEDIUM);

        return apply_filters(sprintf('willow/acf/field=%s', $image->getKey()), $image);
    }

    public static function getTwitterTeaserDescription(): ACFField
    {
        $description = new TextAreaField('field_5aeac88c3953a');
        $description->setLabel('Teaser Description')
            ->setName('tw_teaser_description');

        return apply_filters(sprintf('willow/acf/field=%s', $description->getKey()), $description);
    }

    private static function registerValidationHooks()
    {
        add_filter(sprintf('acf/validate_value/key=%s', self::TEASER_IMAGE_FIELD), function ($valid) {
            // If not valid and a Video-Widget with Teaser-Image checked exists
            return $valid ?:
                collect($_POST['acf'][CompositeFieldGroup::CONTENT_FIELD])
                    ->contains(CompositeFieldGroup::VIDEO_TEASER_IMAGE_FIELD, '1');
        });
    }
}
