<?php

namespace Bonnier\Willow\Base\Models\ACF\Page;

use Bonnier\Willow\Base\Helpers\AcfName;
use Bonnier\Willow\Base\Models\ACF\ACFField;
use Bonnier\Willow\Base\Models\ACF\ACFGroup;
use Bonnier\Willow\Base\Models\ACF\ACFLayout;
use Bonnier\Willow\Base\Models\ACF\Fields\CustomRelationshipField;
use Bonnier\Willow\Base\Models\ACF\Fields\FileField;
use Bonnier\Willow\Base\Models\ACF\Fields\FlexibleContentField;
use Bonnier\Willow\Base\Models\ACF\Fields\ImageField;
use Bonnier\Willow\Base\Models\ACF\Fields\MarkdownField;
use Bonnier\Willow\Base\Models\ACF\Fields\NumberField;
use Bonnier\Willow\Base\Models\ACF\Fields\RadioField;
use Bonnier\Willow\Base\Models\ACF\Fields\SelectField;
use Bonnier\Willow\Base\Models\ACF\Fields\TabField;
use Bonnier\Willow\Base\Models\ACF\Fields\TaxonomyField;
use Bonnier\Willow\Base\Models\ACF\Fields\TextAreaField;
use Bonnier\Willow\Base\Models\ACF\Fields\TextField;
use Bonnier\Willow\Base\Models\ACF\Fields\TrueFalseField;
use Bonnier\Willow\Base\Models\ACF\Fields\UrlField;
use Bonnier\Willow\Base\Models\ACF\Fields\UserField;
use Bonnier\Willow\Base\Models\ACF\Fields\Wysiwyg;
use Bonnier\Willow\Base\Models\ACF\Properties\ACFConditionalLogic;
use Bonnier\Willow\Base\Models\ACF\Properties\ACFLocation;
use Bonnier\Willow\Base\Models\WpComposite;
use Bonnier\Willow\Base\Models\WpTaxonomy;

class PageFieldGroup
{
    private const TEASER_LIST_URL_FIELD = 'field_5bb31a9c1d392';
    private const TAXONOMY_FIELD = 'field_5bc055aad2019';
    private const MANUAL_SOURCE_CODE_FIELD = 'field_5e15a823c1913';
    private const SOURCE_CODE_FIELD = 'field_5e144f59a2ac8';
    private const COMMERCIAL_SPOT_URL_FIELD = 'field_5c0fa2fcea1a6';
    private const LINK_TYPE_FIELD = 'field_5e68a31bbe22d';

    public static function register()
    {
        if (function_exists('acf_add_local_field_group')) {
            $location = new ACFLocation();
            $location->addLocation('post_type', ACFLocation::OPERATOR_EQUALS, 'page')
                ->addLocation('taxonomy', ACFLocation::OPERATOR_EQUALS, 'category')
                ->addLocation('taxonomy', ACFLocation::OPERATOR_EQUALS, 'post_tag');

            $pageGroup = new ACFGroup('group_5bb31817b40e4');
            $pageGroup->setTitle('Page')
                ->setLocation($location)
                ->setMenuOrder(0)
                ->setPosition(ACFGroup::POSITION_NORMAL)
                ->setStyle(ACFGroup::STYLE_SEAMLESS)
                ->setLabelPlacement(ACFGroup::LABEL_PLACEMENT_TOP)
                ->setInstructionPlacement(ACFGroup::INSTRUCTION_PLACEMENT_LABEL)
                ->setHideOnScreen([
                    'discussion',
                    'comments',
                    'revisions',
                    'featured_image',
                    'send-trackbacks',
                ])
                ->setActive(true);

            $pageGroup->addField(self::getPageWidgetsField());

            $filteredPageGroup = apply_filters(sprintf('willow/acf/group=%s', $pageGroup->getKey()), $pageGroup);

            acf_add_local_field_group($filteredPageGroup->toArray());

            $metaGroup = new ACFGroup('group_5bfe529145a7e');
            $metaGroup->setTitle('Page Meta')
                ->setLocation(new ACFLocation('post_type', ACFLocation::OPERATOR_EQUALS, 'page'))
                ->setMenuOrder(0)
                ->setPosition(ACFGroup::POSITION_SIDE)
                ->setStyle(ACFGroup::STYLE_DEFAULT)
                ->setLabelPlacement(ACFGroup::LABEL_PLACEMENT_TOP)
                ->setInstructionPlacement(ACFGroup::INSTRUCTION_PLACEMENT_LABEL)
                ->setActive(true)
                ->addField(self::getSitemapField());

            $filteredMetaGroup = apply_filters(sprintf('willow/acf/group=%s', $metaGroup->getKey()), $metaGroup);

            acf_add_local_field_group($filteredMetaGroup->toArray());

            self::registerHooks();
        }
    }

    public static function getPageWidgetsField(): ACFField
    {
        $widgets = new FlexibleContentField('field_5bb318f2ffcef');
        $widgets->setLabel('Page Widgets')
            ->setName('page_widgets')
            ->setButtonLabel('Add Widget');

        $widgets->addLayout(self::getFeaturedContentLayout());
        $widgets->addLayout(self::getTeaserListLayout());
        $widgets->addLayout(self::getAuthorOverviewLayout());
        $widgets->addLayout(self::getSeoTextLayout());
        $widgets->addLayout(self::getTaxonomyListLayout());
        $widgets->addLayout(self::getNewsletterLayout());
        $widgets->addLayout(self::getBannerPlacementLayout());
        $widgets->addLayout(self::getCommercialSpotLayout());
        $widgets->addLayout(self::getQuoteTeaserLayout());

        return apply_filters(sprintf('willow/acf/field=%s', $widgets->getKey()), $widgets);
    }

    public static function getFeaturedContentLayout(): ACFLayout
    {
        $layout = new ACFLayout('5bbb23424c064');
        $layout->setName(AcfName::WIDGET_FEATURED_CONTENT)
            ->setLabel('Featured Content');

        $settings = new TabField('field_5bbb4363c6cc8');
        $settings->setLabel('Settings');

        $layout->addSubField($settings);

        $image = new ImageField('field_5bbb41245b291');
        $image->setLabel('Image')
            ->setName('image')
            ->setInstructions('Image is used as fallback if you video is selected. That\'s why it is required.')
            ->setReturnFormat(ACFField::RETURN_ARRAY)
            ->setPreviewSize(ImageField::PREVIEW_THUMB);

        $layout->addSubField($image);

        $video = new FileField('field_5bbb41625b292');
        $video->setLabel('Video')
            ->setName('video')
            ->setReturnFormat(ACFField::RETURN_ARRAY)
            ->setMimeTypes('mp4');

        $layout->addSubField($video);

        $hint = new RadioField('field_5bbb417a5b293');
        $hint->setLabel('Display Format')
            ->setName('display_hint')
            ->setChoice('default', 'Default')
            ->setChoice('lead', 'Lead')
            ->setChoice('compact', 'Compact')
            ->setDefaultValue('default')
            ->setLayout('vertical')
            ->setReturnFormat(ACFField::RETURN_VALUE);

        $layout->addSubField($hint);

        $label = new TextField('field_5c98afd2a5e6e');
        $label->setLabel('Label')
            ->setName('label');

        $layout->addSubField($label);

        $layout->addSubFields(SortByFields::getFields(AcfName::WIDGET_FEATURED_CONTENT, [
            'minTeasers' => 1,
            'maxTeasers' => 1,
            'teaserCountDefault' => 1
        ]));

        return apply_filters(sprintf('willow/acf/layout=%s', $layout->getKey()), $layout);
    }

    public static function getTeaserListLayout(): ACFLayout
    {
        $layout = new ACFLayout('5bb3190811fdf');
        $layout->setName(AcfName::WIDGET_TEASER_LIST)
            ->setLabel('Teaser List');

        $settings = new TabField('field_5bb31940ffcf0');
        $settings->setLabel('Settings');

        $layout->addSubField($settings);

        $title = new TextField('field_5bb31a3bffcf2');
        $title->setLabel('Title')
            ->setName('title');

        $layout->addSubField($title);

        $label = new TextField('field_5bb759ce606a7');
        $label->setLabel('Label')
            ->setName('label');

        $layout->addSubField($label);

        $description = new TextAreaField('field_5bb31a6c1d390');
        $description->setLabel('Description')
            ->setName('description');

        $layout->addSubField($description);

        $image = new ImageField('field_5bb31a7d1d391');
        $image->setLabel('Image')
            ->setName('image')
            ->setReturnFormat(ACFField::RETURN_ARRAY)
            ->setPreviewSize(ImageField::PREVIEW_THUMB);

        $layout->addSubField($image);

        $link = new UrlField(self::TEASER_LIST_URL_FIELD);
        $link->setLabel('Link')
            ->setName('link');

        $layout->addSubField($link);

        $linkLabel = new TextField('field_5bb759f880c92');
        $linkLabel->setLabel('Link Label')
            ->setName('link_label')
            ->setConditionalLogic(new ACFConditionalLogic(
                self::TEASER_LIST_URL_FIELD,
                ACFConditionalLogic::OPERATOR_NOT_EMPTY
            ));

        $layout->addSubField($linkLabel);

        $hint = new RadioField('field_5bb319a1ffcf1');
        $hint->setLabel('Display Format')
            ->setName('display_hint')
            ->setChoice('default', 'Standard')
            ->setChoice('presentation', 'Presentation')
            ->setChoice('ordered_list', 'Ordered List')
            ->setChoice('magazine_issue', 'Magazine Issue')
            ->setChoice('slider', 'Slider')
            ->setChoice('featured_with_related', 'Featured With Related')
            ->setDefaultValue('default');

        $layout->addSubField($hint);

        $pagination = new TrueFalseField('field_5c090fa6c5e0d');
        $pagination->setLabel('Pagination')
            ->setName('pagination')
            ->setInstructions('Should this Teaser List paginate? Only one Teaser List per page can paginate.');

        $layout->addSubField($pagination);

        $layout->addSubFields(SortByFields::getFields(AcfName::WIDGET_TEASER_LIST));

        return apply_filters(sprintf('willow/acf/layout=%s', $layout->getKey()), $layout);
    }

    public static function getAuthorOverviewLayout(): ACFLayout
    {
        $layout = new ACFLayout('layout_5ffd8e7e5dd86');
        $layout->setName('author_overview')
            ->setLabel('Author Overview');

        $editorDescriptionTitle = new TextField('field_60100c8f854a5');
        $editorDescriptionTitle->setLabel('Editors description title')
            ->setName('editors_description_title');

        $layout->addSubField($editorDescriptionTitle);

        $editorDescription = new Wysiwyg('field_6010095022dc9');
        $editorDescription->setLabel('Editors description')
            ->setName('editors_description')
            ->setTabs(Wysiwyg::TABS_ALL)
            ->setToolbar(Wysiwyg::TOOLBAR_FULL)
            ->setMediaUpload(false);

        $layout->addSubField($editorDescription);

        $userField = (new UserField('field_5ffd94425dd8c'))
            ->setLabel('Authors')
            ->setName('authors')
            ->setRole('author')
            ->setRequired(true)
            ->setMultiple(true)
            ->setInstructions('Author overview will only display public authors, even thou you can select non public ones. The order of the authors will determine the order on the overview page.');

        $layout->addSubField($userField);

        return apply_filters(sprintf('willow/acf/layout=%s', $layout->getKey()), $layout);
    }

    public static function getSeoTextLayout(): ACFLayout
    {
        $layout = new ACFLayout('5bbc5200c758b');
        $layout->setName('seo_text')
            ->setLabel('SEO Text');

        $title = new TextField('field_5bbc5050c8158');
        $title->setLabel('Title')
            ->setName('title');

        $layout->addSubField($title);

        $description = new MarkdownField('field_5bbc5058c8159');
        $description->setLabel('Description')
            ->setName('description')
            ->setMdeConfig(MarkdownField::CONFIG_STANDARD);

        $layout->addSubField($description);

        $image = new ImageField('field_5bbc505fc815a');
        $image->setLabel('Image')
            ->setName('image')
            ->setReturnFormat(ACFField::RETURN_ARRAY)
            ->setPreviewSize(ImageField::PREVIEW_THUMB);

        $layout->addSubField($image);

        $position = new RadioField('field_5be56c5ca42ef');
        $position->setLabel('Image Position')
            ->setName('image_position')
            ->setChoice('before', 'Before Text')
            ->setChoice('after', 'After Text')
            ->setDefaultValue('before')
            ->setLayout('horizontal')
            ->setReturnFormat(ACFField::RETURN_VALUE);

        $layout->addSubField($position);

        return apply_filters(sprintf('willow/acf/layout=%s', $layout->getKey()), $layout);
    }

    public static function getTaxonomyListLayout(): ACFLayout
    {
        $layout = new ACFLayout('layout_5bc0556fd2014');
        $layout->setName('taxonomy_teaser_list')
            ->setLabel('Taxonomy Teaser List');

        $title = new TextField('field_5bc0557ad2015');
        $title->setLabel('Title')
            ->setName(AcfName::FIELD_TITLE)
            ->setRequired(true);

        $layout->addSubField($title);

        $description = new MarkdownField('field_5bc05582d2016');
        $description->setLabel('Description')
            ->setName(AcfName::FIELD_DESCRIPTION)
            ->setMdeConfig(MarkdownField::CONFIG_SIMPLE);

        $layout->addSubField($description);

        $image = new ImageField('field_5bc05591d2017');
        $image->setLabel('Image')
            ->setName(AcfName::FIELD_IMAGE)
            ->setReturnFormat(ACFField::RETURN_ARRAY)
            ->setPreviewSize(ImageField::PREVIEW_THUMB);

        $layout->addSubField($image);

        $label = new TextField('field_5bc055a2d2018');
        $label->setLabel('Label')
            ->setName(AcfName::FIELD_LABEL);

        $layout->addSubField($label);

        $hint = new RadioField('field_5bc0566fff85d');
        $hint->setLabel('Display Format')
            ->setName(AcfName::FIELD_DISPLAY_HINT)
            ->setChoice(AcfName::DISPLAY_HINT_DEFAULT, 'Default')
            ->setChoice(AcfName::DISPLAY_HINT_PRESENTATION, 'Presentation')
            ->setDefaultValue(AcfName::DISPLAY_HINT_DEFAULT)
            ->setLayout('vertical')
            ->setReturnFormat(ACFField::RETURN_VALUE);

        $layout->addSubField($hint);

        $taxonomy = new SelectField(self::TAXONOMY_FIELD);
        $taxonomy->setLabel('Taxonomy')
            ->setName(AcfName::FIELD_TAXONOMY)
            ->addChoice(AcfName::FIELD_CATEGORY, 'Category')
            ->addChoice(AcfName::FIELD_TAG, 'Tag')
            ->setDefaultValue([
                AcfName::FIELD_CATEGORY
            ])
            ->setAllowNull(false)
            ->setMultiple(false)
            ->setReturnFormat(ACFField::RETURN_VALUE)
            ->setAjax(false);

        WpTaxonomy::get_custom_taxonomies()->each(function ($customTaxonomy) use (&$taxonomy) {
            $taxonomy->addChoice($customTaxonomy->machine_name, $customTaxonomy->name);
        });

        $layout->addSubField($taxonomy);

        $layout->addSubField(self::getTaxonomyField('category', 'Categories', 'category'));

        $layout->addSubField(self::getTaxonomyField('tag', 'Tags', 'post_tag'));

        WpTaxonomy::get_custom_taxonomies()->each(function ($taxonomy) use ($layout) {
            $layout->addSubField(
                self::getTaxonomyField($taxonomy->machine_name, ucfirst($taxonomy->name), $taxonomy->machine_name)
            );
        });

        return apply_filters(sprintf('willow/acf/layout=%s', $layout->getKey()), $layout);
    }

    public static function getNewsletterLayout(): ACFLayout
    {
        $layout = new ACFLayout('layout_5bbc54bacaf10');
        $layout->setName('newsletter')
            ->setLabel('Newsletter');

        $checkbox = new TrueFalseField(self::MANUAL_SOURCE_CODE_FIELD);
        $checkbox->setLabel('Manual Source Code and Permission Text')
            ->setName('manual_source_code')
            ->setMessage('Enable manual input of source code and permission text');

        $layout->addSubField($checkbox);

        $code = new NumberField(self::SOURCE_CODE_FIELD);
        $code->setLabel('Source Code')
            ->setName('source_code')
            ->setRequired(true)
            ->setConditionalLogic(new ACFConditionalLogic(
                self::MANUAL_SOURCE_CODE_FIELD,
                ACFConditionalLogic::OPERATOR_EQUALS,
                '1'
            ))
            ->setPlaceholder('Source Code')
            ->setMin(100000)
            ->setMax(999999);

        $layout->addSubField($code);

        $text = new MarkdownField('field_5e144fbda2ac9');
        $text->setLabel('Permission Text')
            ->setName('permission_text')
            ->setRequired(true)
            ->setConditionalLogic(new ACFConditionalLogic(
                self::MANUAL_SOURCE_CODE_FIELD,
                ACFConditionalLogic::OPERATOR_EQUALS,
                '1'
            ))
            ->setMdeConfig(MarkdownField::CONFIG_SIMPLE);

        $layout->addSubField($text);

        return apply_filters(sprintf('willow/acf/layout=%s', $layout->getKey()), $layout);
    }

    public static function getBannerPlacementLayout(): ACFLayout
    {
        $layout = new ACFLayout('layout_5bbc54cbcaf11');
        $layout->setName('banner_placement')
            ->setLabel('Banner Placement');

        return apply_filters(sprintf('willow/acf/layout=%s', $layout->getKey()), $layout);
    }

    public static function getCommercialSpotLayout(): ACFLayout
    {
        $layout = new ACFLayout('layout_5c0fa2f4ea1a5');
        $layout->setName('commercial_spot')
            ->setLabel('Commercial Spot');

        $title = new TextField('field_5c0fa33fea1a8');
        $title->setLabel('Title')
            ->setName('title')
            ->setRequired(true);

        $layout->addSubField($title);

        $description = new MarkdownField('field_5c0fa350ea1a9');
        $description->setLabel('Description')
            ->setName('description')
            ->setMdeConfig(MarkdownField::CONFIG_SIMPLE);

        $layout->addSubField($description);

        $image = new ImageField('field_5c0fa313ea1a7');
        $image->setLabel('Image')
            ->setName('image')
            ->setReturnFormat(ACFField::RETURN_ARRAY)
            ->setPreviewSize(ImageField::PREVIEW_THUMB);

        $layout->addSubField($image);

        $link = new UrlField(self::COMMERCIAL_SPOT_URL_FIELD);
        $link->setLabel('Link')
            ->setName('link')
            ->setRequired(true);

        $layout->addSubField($link);

        $linkLabel = new TextField('field_5c0fa375ea1aa');
        $linkLabel->setLabel('Link Label')
            ->setName('link_label')
            ->setRequired(true)
            ->setConditionalLogic(new ACFConditionalLogic(
                self::COMMERCIAL_SPOT_URL_FIELD,
                ACFConditionalLogic::OPERATOR_NOT_EMPTY
            ));

        $layout->addSubField($linkLabel);

        $label = new TextField('field_5c121e24c912f');
        $label->setLabel('Label')
            ->setName('label');

        $layout->addSubField($label);

        $hint = new RadioField('field_5c98e5cce3b8c');
        $hint->setLabel('Display Format')
            ->setName('display_hint')
            ->setChoice('default', 'Default')
            ->setChoice('wide', 'Wide')
            ->setDefaultValue('default')
            ->setLayout('vertical')
            ->setReturnFormat(ACFField::RETURN_VALUE);

        $layout->addSubField($hint);

        return apply_filters(sprintf('willow/acf/layout=%s', $layout->getKey()), $layout);
    }

    public static function getQuoteTeaserLayout(): ACFLayout
    {
        $layout = new ACFLayout('layout_5e68a042a0db7');
        $layout->setName('quote_teaser')
            ->setLabel('Quote Teaser');

        $quote = new TextAreaField('field_5e68a04ca0db8');
        $quote->setLabel('Quote')
            ->setName('quote')
            ->setRequired(true);

        $layout->addSubField($quote);

        $author = new TextField('field_5e68a068a0db9');
        $author->setLabel('Author')
            ->setName('author');

        $layout->addSubField($author);

        $type = new SelectField(self::LINK_TYPE_FIELD);
        $type->setLabel('Link Type')
            ->setName('link_type')
            ->addChoice('external', 'External URL')
            ->addChoice('composite', 'Composite Content')
            ->setDefaultValue(['external'])
            ->setReturnFormat(ACFField::RETURN_VALUE);

        $layout->addSubField($type);

        $label = new TextField('field_5e68a41bba6be');
        $label->setLabel('Link Label')
            ->setName('link_label')
            ->setConditionalLogic(new ACFConditionalLogic(
                self::LINK_TYPE_FIELD,
                ACFConditionalLogic::OPERATOR_EQUALS,
                'external'
            ));

        $layout->addSubField($label);

        $link = new UrlField('field_5e68a075a0dba');
        $link->setLabel('Link')
            ->setName('link')
            ->setConditionalLogic(new ACFConditionalLogic(
                self::LINK_TYPE_FIELD,
                ACFConditionalLogic::OPERATOR_EQUALS,
                'external'
            ));

        $layout->addSubField($link);

        $composites = new CustomRelationshipField('field_5e68a391be22e');
        $composites
            ->setLabel('Composite Content')
            ->setName('composite_content')
            ->setConditionalLogic(new ACFConditionalLogic(
                self::LINK_TYPE_FIELD,
                ACFConditionalLogic::OPERATOR_EQUALS,
                'composite'
            ))
            ->addPostType(WpComposite::POST_TYPE)
            ->addFilter('search')
            ->addFilter('taxonomy')
            ->addFilter('post_tag')
            ->setMin(1)
            ->setMax(1)
            ->setReturnFormat(ACFField::RETURN_OBJECT);

        $layout->addSubField($composites);

        return apply_filters(sprintf('willow/acf/layout=%s', $layout->getKey()), $layout);
    }

    public static function getSitemapField(): ACFField
    {
        $field = new TrueFalseField('field_5bfe50afe902e');
        $field->setLabel('Display in Sitemaps')
            ->setName('sitemap')
            ->setMessage('Should this page be displayed on Sitemaps?')
            ->setDefaultValue(1);

        return apply_filters(sprintf('willow/acf/field=%s', $field->getKey()), $field);
    }

    public static function getTaxonomyField(string $name, string $label, string $taxonomy)
    {
        $taxonomyField = new TaxonomyField(sprintf(
            'field_%s',
            hash('md5', sprintf('%s-%s-%s', $name, $label, $taxonomy))
        ));
        $taxonomyField->setLabel($label)
            ->setName($name)
            ->setRequired(true)
            ->setConditionalLogic(new ACFConditionalLogic(
                self::TAXONOMY_FIELD,
                ACFConditionalLogic::OPERATOR_EQUALS,
                $name
            ))
            ->setTaxonomy($taxonomy)
            ->setFieldType('multi_select')
            ->setReturnFormat(ACFField::RETURN_ID);

        return apply_filters(sprintf('willow/acf/field=%s', $taxonomyField->getKey()), $taxonomyField);
    }

    private static function registerHooks()
    {
        add_filter(sprintf('acf/validate_value/key=%s', self::SOURCE_CODE_FIELD), function ($valid, $value) {
            if (!$valid) {
                return $valid;
            }

            if (!empty($value) && strlen($value) !== 6) {
                $valid = 'Please make sure your source code is in the right format';
            }

            return $valid;
        }, 10, 4);
    }
}
