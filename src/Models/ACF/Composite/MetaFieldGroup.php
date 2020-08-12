<?php

namespace Bonnier\Willow\Base\Models\ACF\Composite;

use Bonnier\Willow\Base\Models\ACF\ACFField;
use Bonnier\Willow\Base\Models\ACF\ACFGroup;
use Bonnier\Willow\Base\Models\ACF\Fields\CheckboxField;
use Bonnier\Willow\Base\Models\ACF\Fields\FileField;
use Bonnier\Willow\Base\Models\ACF\Fields\GroupField;
use Bonnier\Willow\Base\Models\ACF\Fields\ImageField;
use Bonnier\Willow\Base\Models\ACF\Fields\SelectField;
use Bonnier\Willow\Base\Models\ACF\Fields\TextAreaField;
use Bonnier\Willow\Base\Models\ACF\Fields\TextField;
use Bonnier\Willow\Base\Models\ACF\Fields\TrueFalseField;
use Bonnier\Willow\Base\Models\ACF\Fields\UrlField;
use Bonnier\Willow\Base\Models\ACF\Properties\ACFConditionalLogic;
use Bonnier\Willow\Base\Models\ACF\Properties\ACFLocation;
use Bonnier\Willow\Base\Models\WpComposite;
use Bonnier\Willow\MuPlugins\Helpers\LanguageProvider;

class MetaFieldGroup
{
    public const SITEMAP_FIELD = 'field_5bfe50afe902e';

    private const COMMERCIAL_FIELD = 'field_58fde84d034e4';
    private const COMMERCIAL_TYPES = [
        'Advertorial',
        'AffiliatePartner',
        'CommercialContent',
        'Offer',
    ];

    public static function register()
    {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }
        $group = new ACFGroup('group_58fde819ea0f1');
        $group->setTitle('Meta')
            ->setLocation(new ACFLocation('post_type', ACFLocation::OPERATOR_EQUALS, WpComposite::POST_TYPE))
            ->setMenuOrder(4)
            ->setPosition(ACFGroup::POSITION_SIDE)
            ->setLabelPlacement(ACFGroup::LABEL_PLACEMENT_TOP)
            ->setInstructionPlacement(ACFGroup::INSTRUCTION_PLACEMENT_LABEL)
            ->setActive(true);

        $group->addField(self::getCanonicalUrlField());
        $group->addField(self::getCommercialField());
        $group->addField(self::getCommercialTypeField());
        $group->addField(self::getCommercialLogoField());
        $group->addField(self::getCommercialLabelField());
        $group->addField(self::getCommercialLinkField());
        $group->addField(self::getInternalCommentField());
        $group->addField(self::getMagazineYearField());
        $group->addField(self::getMagazineIssueField());
        $group->addField(self::getAudioField());
        $group->addField(self::getExcludePlatformsField());
        $group->addField(self::getDisableCTMField());
        $group->addField(self::getSitemapField());

        $filteredGroup = apply_filters(sprintf('willow/acf/group=%s', $group->getKey()), $group);

        acf_add_local_field_group($filteredGroup->toArray());

        self::registerTranslations();
        self::registerSitemapHooks();
    }

    public static function getCanonicalUrlField(): ACFField
    {
        $canonical = new UrlField('field_5af188bbb5b45');
        $canonical->setLabel('Canonical URL')
            ->setName('canonical_url');

        return apply_filters(sprintf('willow/acf/field=%s', $canonical->getKey()), $canonical);
    }

    public static function getCommercialField(): ACFField
    {
        $commercial = new TrueFalseField(self::COMMERCIAL_FIELD);
        $commercial->setLabel('Commercial')
            ->setName('commercial');

        return apply_filters(sprintf('willow/acf/field=%s', $commercial->getKey()), $commercial);
    }

    public static function getCommercialTypeField(): ACFField
    {
        $commercialType = new SelectField('field_58fde876034e5');
        $commercialType->setLabel('Commercial Type')
            ->setName('commercial_type')
            ->setConditionalLogic(new ACFConditionalLogic(
                self::COMMERCIAL_FIELD,
                ACFConditionalLogic::OPERATOR_EQUALS,
                '1'
            ))
            ->setAllowNull(true)
            ->setReturnFormat(ACFField::RETURN_VALUE);

        foreach (self::COMMERCIAL_TYPES as $type) {
            $commercialType->addChoice($type);
        }

        return apply_filters(sprintf('willow/acf/field=%s', $commercialType->getKey()), $commercialType);
    }

    public static function getCommercialLogoField(): ACFField
    {
        $logo = new ImageField('field_5a8d72d39ee48');
        $logo->setLabel('Commercial Logo')
            ->setName('commercial_logo')
            ->setConditionalLogic(new ACFConditionalLogic(
                self::COMMERCIAL_FIELD,
                ACFConditionalLogic::OPERATOR_EQUALS,
                '1'
            ))
            ->setReturnFormat(ACFField::RETURN_ARRAY)
            ->setPreviewSize(ImageField::PREVIEW_THUMB);

        return apply_filters(sprintf('willow/acf/field=%s', $logo->getKey()), $logo);
    }

    public static function getCommercialLabelField(): ACFField
    {
        $label = new TextField('field_5e68cf051f4ea');
        $label->setLabel('Commercial Label')
            ->setName('commercial_label')
            ->setConditionalLogic(new ACFConditionalLogic(
                self::COMMERCIAL_FIELD,
                ACFConditionalLogic::OPERATOR_EQUALS,
                '1'
            ));

        return apply_filters(sprintf('willow/acf/field=%s', $label->getKey()), $label);
    }

    public static function getCommercialLinkField(): ACFField
    {
        $link = new UrlField('field_5e68cf2b1f4eb');
        $link->setLabel('Commercial Link')
            ->setName('commercial_link')
            ->setConditionalLogic(new ACFConditionalLogic(
                self::COMMERCIAL_FIELD,
                ACFConditionalLogic::OPERATOR_EQUALS,
                '1'
            ));

        return apply_filters(sprintf('willow/acf/field=%s', $link->getKey()), $link);
    }

    public static function getInternalCommentField(): ACFField
    {
        $comment = new TextAreaField('field_58fde876034e6');
        $comment->setLabel('Internal Comment')
            ->setName('internal_comment')
            ->setRows(3);

        return apply_filters(sprintf('willow/acf/field=%s', $comment->getKey()), $comment);
    }

    public static function getMagazineYearField(): ACFField
    {
        $years = array_reverse(range(1980, date("Y") + 1));
        $magazineYear = new SelectField('field_58f5febf3cb9c');
        $magazineYear->setLabel('Magazine Year')
            ->setName('magazine_year')
            ->setInstructions('The magazine year ie. 2017 if the article was published in 2017')
            ->setChoices(array_combine($years, $years))
            ->setAllowNull(true)
            ->setReturnFormat(ACFField::RETURN_VALUE);

        return apply_filters(sprintf('willow/acf/field=%s', $magazineYear->getKey()), $magazineYear);
    }

    public static function getMagazineIssueField(): ACFField
    {
        $issues = array_map(function ($issue) {
            if ($issue <= 9) {
                return '0' . $issue;
            }
            return $issue;
        }, range(1, 19));
        $magazineIssue = new SelectField('field_58e3878b2dc76');
        $magazineIssue->setLabel('Magazine Issue')
            ->setName('magazine_issue')
            ->setInstructions('The magazine issue ie. 01 for the first issue of a given year')
            ->setChoices(array_combine($issues, $issues))
            ->setAllowNull(true)
            ->setReturnFormat(ACFField::RETURN_VALUE);

        return apply_filters(sprintf('willow/acf/field=%s', $magazineIssue->getKey()), $magazineIssue);
    }

    public static function getAudioField(): ACFField
    {
        $audio = new GroupField('field_5bb4abe52f3d7');
        $audio->setLabel('Audio')
            ->setName('audio');

        $file = new FileField('field_5bb4ac792f3d8');
        $file->setLabel('File')
            ->setName('file')
            ->setReturnFormat(ACFField::RETURN_ARRAY);

        $audio->addSubField($file);

        $title = new TextField('field_5bb4aca02f3d9');
        $title->setLabel('Title')
            ->setName('title');

        $audio->addSubField($title);

        $thumbnail = new ImageField('field_5bb4acb12f3da');
        $thumbnail->setLabel('Audio Thumbnail')
            ->setName('audio_thumbnail')
            ->setReturnFormat(ACFField::RETURN_ARRAY)
            ->setPreviewSize(ImageField::PREVIEW_THUMB);

        $audio->addSubField($thumbnail);

        return apply_filters(sprintf('willow/acf/field=%s', $audio->getKey()), $audio);
    }

    public static function getExcludePlatformsField(): ACFField
    {
        $exclude = new CheckboxField('field_5bd2fc21bad06');
        $exclude->setLabel('Exclude from platforms')
            ->setName('exclude_platforms')
            ->setInstructions('Do not show this content on:')
            ->addChoice('app', 'App')
            ->addChoice('web', 'Web');

        return apply_filters(sprintf('willow/acf/field=%s', $exclude->getKey()), $exclude);
    }

    public static function getDisableCTMField(): ACFField
    {
        $ctm = new TrueFalseField('field_5c177e092b1ec');
        $ctm->setLabel('Disable CTM')
            ->setName('disable_ctm');

        return apply_filters(sprintf('willow/acf/field=%s', $ctm->getKey()), $ctm);
    }

    public static function getSitemapField(): ACFField
    {
        $sitemap = new TrueFalseField(self::SITEMAP_FIELD);
        $sitemap->setLabel('Hide from Sitemaps?')
            ->setName('sitemap')
            ->setMessage('Should this page be hidden from sitemaps (no-follow)?')
            ->setDefaultValue(0);

        return apply_filters(sprintf('willow/acf/field=%s', $sitemap->getKey()), $sitemap);
    }

    private static function registerTranslations()
    {
        collect(static::COMMERCIAL_TYPES)->each(function ($commercialType) {
            LanguageProvider::registerStringTranslation($commercialType, $commercialType, 'content-hub-editor');
        });
    }

    private static function registerSitemapHooks()
    {
        add_filter('acf/update_value/key=' . static::SITEMAP_FIELD, function ($hideFromSiteMap, $postId) {
            if (CompositeFieldGroup::SHELL_VALUE === get_field(CompositeFieldGroup::KIND_FIELD, $postId)) {
                // Force hide from sitemap allways
                $hideFromSiteMap = 1;
            }
            return $hideFromSiteMap;
        }, 10, 2);
    }
}
