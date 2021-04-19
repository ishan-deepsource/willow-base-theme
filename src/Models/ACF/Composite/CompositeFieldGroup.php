<?php

namespace Bonnier\Willow\Base\Models\ACF\Composite;

use Bonnier\Willow\Base\Models\ACF\ACFField;
use Bonnier\Willow\Base\Models\ACF\ACFGroup;
use Bonnier\Willow\Base\Models\ACF\ACFLayout;
use Bonnier\Willow\Base\Models\ACF\Fields\FileField;
use Bonnier\Willow\Base\Models\ACF\Fields\FlexibleContentField;
use Bonnier\Willow\Base\Models\ACF\Fields\GroupField;
use Bonnier\Willow\Base\Models\ACF\Fields\ImageField;
use Bonnier\Willow\Base\Models\ACF\Fields\ImageHotspotCoordinatesField;
use Bonnier\Willow\Base\Models\ACF\Fields\MarkdownField;
use Bonnier\Willow\Base\Models\ACF\Fields\MessageField;
use Bonnier\Willow\Base\Models\ACF\Fields\NumberField;
use Bonnier\Willow\Base\Models\ACF\Fields\RadioField;
use Bonnier\Willow\Base\Models\ACF\Fields\RelationshipField;
use Bonnier\Willow\Base\Models\ACF\Fields\RepeaterField;
use Bonnier\Willow\Base\Models\ACF\Fields\SelectField;
use Bonnier\Willow\Base\Models\ACF\Fields\TaxonomyField;
use Bonnier\Willow\Base\Models\ACF\Fields\TextAreaField;
use Bonnier\Willow\Base\Models\ACF\Fields\TextField;
use Bonnier\Willow\Base\Models\ACF\Fields\TimePickerField;
use Bonnier\Willow\Base\Models\ACF\Fields\TrueFalseField;
use Bonnier\Willow\Base\Models\ACF\Fields\UrlField;
use Bonnier\Willow\Base\Models\ACF\Fields\UserField;
use Bonnier\Willow\Base\Models\ACF\Properties\ACFConditionalLogic;
use Bonnier\Willow\Base\Models\ACF\Properties\ACFLocation;
use Bonnier\Willow\Base\Models\ACF\Properties\ACFWrapper;
use Bonnier\Willow\Base\Models\WpComposite;

class CompositeFieldGroup
{
    public const CONTENT_FIELD = 'field_58aae476809c6';
    public const VIDEO_TEASER_IMAGE_FIELD = 'field_5a8d7ae021e44';
    public const SHELL_LINK_FIELD = 'field_5d66623efb36e';
    public const KIND_FIELD = 'field_58e388862daa8';
    public const COMPOSITE_FIELD_GROUP = 'group_58abfd3931f2f';
    public const OTHER_AUTHERS_FIELD_NAME = 'other_authors';
    public const SHELL_VALUE = 'Shell';
    public const VIDEO_URL_FIELD_NAME = 'video_url';
    public const VIDEO_CHAPTER_ITEMS_FIELD = 'chapter_items';
    public const VIDEO_INCLUDE_INTRO_VIDEO_FIELD = 'include_intro_video';
    public const COLLAPSIBLE_FIELD_NAME = 'collapsible';
    public const SHOW_NUMBERS_FIELD_NAME = 'show_numbers';
    public const IMAGE_FIELD = 'image';
    public const TITLE_FIELD = 'title';
    public const DISPLAY_HINT_FIELD = 'display_hint';

    private const AUTHOR_FIELD = 'field_5af9888b4b7a1';
    private const LOCKED_CONTENT_FIELD = 'field_5921f0c676974';
    private const IMAGE_LINK_FIELD = 'field_5ba0c550e9e5f';
    private const SOURCE_CODE_FIELD = 'field_5e2ebd197a759';
    private const MULTIMEDIA_DISPLAY_HINT = 'field_5fa10ca4c5576';
    private const MULTIMEDIA_DISPLAY_HINT_3D = '3d';

    public static function register(): void
    {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }

        $group = new ACFGroup(self::COMPOSITE_FIELD_GROUP);
        $group->setTitle('Composite Fields')
            ->setLocation(new ACFLocation('post_type', ACFLocation::OPERATOR_EQUALS, WpComposite::POST_TYPE))
            ->setMenuOrder(6)
            ->setPosition(ACFGroup::POSITION_AFTER_TITLE)
            ->setStyle(ACFGroup::STYLE_SEAMLESS)
            ->setHideOnScreen([
                'slug',
                'author',
                'categories',
            ]);

        $group->addField(self::getKindField());
        $group->addField(self::getDescriptionField());
        $group->addField(self::getAuthorField());
        $group->addField(self::getAuthorDescriptionField());
        $group->addField(self::getOtherAuthorField());
        $group->addField(self::getCategoryField());
        $group->addField(self::getTagField());
        $group->addField(self::getArticleContentField());
        $group->addField(self::getLockedContentField());
        $group->addField(self::getUserRoleField());
        $group->addField(self::getContentField());
        $group->addField(self::getShellLinkField());

        $filteredGroup = apply_filters(sprintf('willow/acf/group=%s', $group->getKey()), $group);

        acf_add_local_field_group($filteredGroup->toArray());

        self::registerHooks();
    }

    public static function getKindField(): ACFField
    {
        $kind = new RadioField(self::KIND_FIELD);
        $kind->setLabel('Kind')
            ->setName('kind')
            ->setRequired(true)
            ->setChoices([
                'Article' => 'Article',
                self::SHELL_VALUE => self::SHELL_VALUE,
                'Story' => 'Story',
            ])
            ->setDefaultValue('Article')
            ->setLayout('horizontal')
            ->setReturnFormat(ACFField::RETURN_VALUE);
        return apply_filters(sprintf('willow/acf/field=%s', $kind->getKey()), $kind);
    }

    public static function getDescriptionField(): ACFField
    {
        $description = new TextAreaField('field_58abfebd21b82');
        $description->setLabel('Description')
            ->setName('description');

        return apply_filters(sprintf('willow/acf/field=%s', $description->getKey()), $description);
    }

    public static function getAuthorField(): ACFField
    {
        $author = new UserField(self::AUTHOR_FIELD);
        $author->setLabel('Author')
            ->setName('author')
            ->setReturnFormat(ACFField::RETURN_ARRAY);
        return apply_filters(sprintf('willow/acf/field=%s', $author->getKey()), $author);
    }

    public static function getAuthorDescriptionField(): ACFField
    {
        $authorDescription = new TextField('field_5a8d44d026528');
        $authorDescription->setLabel('Author Description')
            ->setName('author_description')
            ->setInstructions('Extra information about the authors ie. who took the photos or did the styling');
        return apply_filters(sprintf('willow/acf/field=%s', $authorDescription->getKey()), $authorDescription);
    }

    public static function getOtherAuthorField(): ACFField
    {
        $otherAuthors = new UserField('field_602cce2886a59');
        $otherAuthors->setLabel('Other Authors')
            ->setName(self::OTHER_AUTHERS_FIELD_NAME)
            ->setMultiple(true)
            ->setReturnFormat(ACFField::RETURN_ARRAY);
        return apply_filters(sprintf('willow/acf/field=%s', $otherAuthors->getKey()), $otherAuthors);
    }

    public static function getCategoryField(): ACFField
    {
        $category = new TaxonomyField('field_58e39a7118284');
        $category->setLabel('Category')
            ->setName('category')
            ->setRequired(true)
            ->setTaxonomy(TaxonomyField::TAXONOMY_CATEGORY)
            ->setFieldType(TaxonomyField::TYPE_SELECT)
            ->setSaveTerms(true)
            ->setReturnFormat(ACFField::RETURN_OBJECT);
        return apply_filters(sprintf('willow/acf/field=%s', $category->getKey()), $category);
    }

    public static function getTagField(): ACFField
    {
        $tags = new TaxonomyField('field_58f606b6e1fb0');
        $tags->setLabel('Tags')
            ->setName('tags')
            ->setTaxonomy(TaxonomyField::TAXONOMY_TAG)
            ->setFieldType(TaxonomyField::TYPE_MULTI)
            ->setSaveTerms(true)
            ->setReturnFormat(ACFField::RETURN_OBJECT);
        return apply_filters(sprintf('willow/acf/field=%s', $tags->getKey()), $tags);
    }

    public static function getArticleContentField(): ACFField
    {
        $articleContent = new MessageField('field_5afa811fbf221');
        $articleContent->setLabel('Article Content')
            ->setInstructions('Click the add widget button to add content')
            ->setConditionalLogic(new ACFConditionalLogic(
                self::KIND_FIELD,
                ACFConditionalLogic::OPERATOR_NOT_EQUALS,
                self::SHELL_VALUE
            ));
        return apply_filters(sprintf('willow/acf/field=%s', $articleContent->getKey()), $articleContent);
    }

    public static function getLockedContentField(): ACFField
    {
        $lockedContent = new TrueFalseField(self::LOCKED_CONTENT_FIELD);
        $lockedContent->setLabel('Locked Content')
            ->setName('locked_content')
            ->setInstructions(
                'Check this box if you want parts of the content to be locked. ' .
                'Please note that you should mark each content item that you want to be locked, ' .
                'by checking the "Locked Content" checkbox.'
            )
            ->setConditionalLogic(new ACFConditionalLogic(
                self::KIND_FIELD,
                ACFConditionalLogic::OPERATOR_NOT_EQUALS,
                self::SHELL_VALUE
            ));

        return apply_filters(sprintf('willow/acf/field=%s', $lockedContent->getKey()), $lockedContent);
    }

    public static function getUserRoleField(): ACFField
    {
        $userRoleConditional = new ACFConditionalLogic();
        $userRoleConditional->add(self::LOCKED_CONTENT_FIELD, ACFConditionalLogic::OPERATOR_EQUALS, '1')
            ->add(self::KIND_FIELD, ACFConditionalLogic::OPERATOR_NOT_EQUALS, self::SHELL_VALUE);
        $userRole = new SelectField('field_5921f0e576975');
        $userRole->setLabel('Required User Role')
            ->setName('required_user_role')
            ->setInstructions('Select the role required to access the locked parts of the content')
            ->setConditionalLogic($userRoleConditional)
            ->addChoice('RegUser', 'Registered User')
            ->addChoice('Subscriber', 'Subscriber')
            ->setReturnFormat(ACFField::RETURN_VALUE);

        return apply_filters(sprintf('willow/acf/field=%s', $userRole->getKey()), $userRole);
    }

    public static function getContentField(): ACFField
    {
        $content = new FlexibleContentField(self::CONTENT_FIELD);
        $content->setLabel('Content (Widgets)')
            ->setName('composite_content')
            ->setRequired(true)
            ->setConditionalLogic(new ACFConditionalLogic(
                self::KIND_FIELD,
                ACFConditionalLogic::OPERATOR_NOT_EQUALS,
                self::SHELL_VALUE
            ))
            ->setButtonLabel('Add Widget');

        $content->addLayout(self::getTextItemWidget());
        $content->addLayout(self::getImageWidget());
        $content->addLayout(self::getAudioWidget());
        $content->addLayout(self::getFileWidget());
        $content->addLayout(self::getVideoWidget());
        $content->addLayout(self::getLinkWidget());
        $content->addLayout(self::getGalleryWidget());
        $content->addLayout(self::getInsertedCodeWidget());
        $content->addLayout(self::getInfoboxWidget());
        $content->addLayout(self::getLeadParagraphWidget());
        $content->addLayout(self::getParagraphListWidget());
        $content->addLayout(self::getAssociatedCompositeWidget());
        $content->addLayout(self::getInventoryWidget());
        $content->addLayout(self::getHotspotImageWidget());
        $content->addLayout(self::getQuoteWidget());
        $content->addLayout(self::getNewsletterWidget());
        $content->addLayout(self::getChaptersSummaryWidget());
        $content->addLayout(self::getMultimediaWidget());
        $content->addLayout(self::getProductWidget());
        $content->addLayout(self::getRecipeWidget());
        $content->addLayout(self::getCalculatorWidget());

        return apply_filters(sprintf('willow/acf/field=%s', $content->getKey()), $content);
    }

    public static function getShellLinkField(): ACFField
    {
        $shellLink = new UrlField(self::SHELL_LINK_FIELD);
        $shellLink->setLabel('Shell Link')
            ->setName('shell_link')
            ->setInstructions('Enter the URL that the shell article should direct users to')
            ->setRequired(true)
            ->setConditionalLogic(new ACFConditionalLogic(
                self::KIND_FIELD,
                ACFConditionalLogic::OPERATOR_EQUALS,
                self::SHELL_VALUE
            ))
            ->setPlaceholder('Example of valid URL: https://google.com');

        return apply_filters(sprintf('willow/acf/field=%s', $shellLink->getKey()), $shellLink);
    }

    public static function getTextItemWidget(): ACFLayout
    {
        $textItemWidget = new ACFLayout('58aae53c26608');
        $textItemWidget->setName('text_item')
            ->setLabel('Text');
        $body = new MarkdownField('field_58aae55326609');
        $body->setLabel('Body')
            ->setName('body')
            ->setRequired(true)
            ->setMdeConfig(MarkdownField::CONFIG_STANDARD);
        $textItemWidget->addSubField($body);

        return apply_filters(sprintf('willow/acf/layout=%s', $textItemWidget->getKey()), $textItemWidget);
    }

    public static function getImageWidget(): ACFLayout
    {
        $imageWidget = new ACFLayout('58aaef9fb02bc');
        $imageWidget->setName('image')
            ->setLabel('Image');

        $leadImage = new TrueFalseField('field_5908407c246cb');
        $leadImage->setLabel('Lead Image')
            ->setName('lead_image');

        $imageWidget->addSubField($leadImage);

        $file = new ImageField('field_58aaf042b02c1');
        $file->setLabel('File')
            ->setName('file')
            ->setRequired(true)
            ->setReturnFormat(ACFField::RETURN_ARRAY)
            ->setPreviewSize(ImageField::PREVIEW_MEDIUM);

        $imageWidget->addSubField($file);

        $videoUrl = new UrlField('field_5f1ece99714b3');
        $videoUrl->setLabel('Video url')
            ->setName(self::VIDEO_URL_FIELD_NAME)
            ->setInstructions('The embed url for the Vimeo video.');

        $imageWidget->addSubField($videoUrl);

        $lockedContent = new TrueFalseField('field_5922bd8e5cd9e');
        $lockedContent->setLabel('Locked Content')
            ->setName('locked_content')
            ->setConditionalLogic(new ACFConditionalLogic(
                self::LOCKED_CONTENT_FIELD,
                ACFConditionalLogic::OPERATOR_EQUALS,
                '1'
            ));

        $imageWidget->addSubField($lockedContent);

        $link = new UrlField(self::IMAGE_LINK_FIELD);
        $link->setLabel('Link')
            ->setName('link');

        $imageWidget->addSubField($link);

        $openIn = new RadioField('field_5ba0c558e9e60');
        $openIn->setLabel('Open in')
            ->setName('target')
            ->setConditionalLogic(new ACFConditionalLogic(
                self::IMAGE_LINK_FIELD,
                ACFConditionalLogic::OPERATOR_NOT_EMPTY
            ))
            ->setChoice('_self', 'Same window')
            ->setChoice('_blank', 'New window')
            ->setDefaultValue('_self')
            ->setLayout('vertical')
            ->setReturnFormat(ACFField::RETURN_VALUE);

        $imageWidget->addSubField($openIn);

        $rel = new RadioField('field_5ba0c574e9e61');
        $rel->setLabel('Relationship (Follow / No Follow)')
            ->setName('rel')
            ->setConditionalLogic(new ACFConditionalLogic(
                self::IMAGE_LINK_FIELD,
                ACFConditionalLogic::OPERATOR_NOT_EMPTY
            ))
            ->setChoice('follow', 'Follow')
            ->setChoice('nofollow', 'No Follow')
            ->setDefaultValue('follow')
            ->setLayout('vertical')
            ->setReturnFormat(ACFField::RETURN_VALUE);

        $imageWidget->addSubField($rel);

        $displayFormat = new RadioField('field_5bb4a00b2aa05');
        $displayFormat->setLayout('Display Format')
            ->setName('display_hint')
            ->setChoice('inline', 'Inline')
            ->setChoice('wide', 'Full Width')
            ->setDefaultValue('inline')
            ->setLayout('vertical')
            ->setReturnFormat(ACFField::RETURN_VALUE);

        $imageWidget->addSubField($displayFormat);

        return apply_filters(sprintf('willow/acf/layout=%s', $imageWidget->getKey()), $imageWidget);
    }

    public static function getAudioWidget(): ACFLayout
    {
        $audioWidget = new ACFLayout('layout_5b6aee597180e');
        $audioWidget->setName('audio')
            ->setLabel('Audio');

        $file = new FileField('field_5b6aee6a7180f');
        $file->setLabel('File')
            ->setName('file')
            ->setRequired(true)
            ->setReturnFormat(ACFField::RETURN_ARRAY);

        $audioWidget->addSubField($file);

        $title = new TextField('field_5b6bf0163e57d');
        $title->setLabel('Title')
            ->setName('title');

        $audioWidget->addSubField($title);

        $image = new ImageField('field_5b716358c2e60');
        $image->setLabel('Image')
            ->setName('image')
            ->setInstructions('picture shown on audio of the audio file. If not set, it\'ll default to the lead image.')
            ->setReturnFormat(ACFField::RETURN_ARRAY)
            ->setPreviewSize(ImageField::PREVIEW_MEDIUM);

        $audioWidget->addSubField($image);

        return apply_filters(sprintf('willow/acf/layout=%s', $audioWidget->getKey()), $audioWidget);
    }

    public static function getFileWidget(): ACFLayout
    {
        $fileWidget = new ACFLayout('590aef9de4a5e');
        $fileWidget->setName('file')
            ->setLabel('File');

        $title = new TextField('field_6038e027bbf88');
        $title->setLabel('Title')
            ->setName('title');

        $fileWidget->addSubField($title);

        $description = new TextAreaField('field_590aefe3e4a5f');
        $description->setLabel('Description')
            ->setName('description');

        $fileWidget->addSubField($description);

        $fileField = new FileField('field_590af026e4a61');
        $fileField->setLabel('File')
            ->setName('file')
            ->setReturnFormat(ACFField::RETURN_ARRAY);

        $fileWidget->addSubField($fileField);

        $images = new RepeaterField('field_5921e5a83f4ea');
        $images->setLabel('Images')
            ->setName('images')
            ->setRequired(true)
            ->setLayout('table')
            ->setButtonLabel('Add Image');

        $image = new ImageField('field_5921e94c3f4eb');
        $image->setLabel('File')
            ->setName('file')
            ->setRequired(true)
            ->setReturnFormat(ACFField::RETURN_ARRAY)
            ->setPreviewSize(ImageField::PREVIEW_MEDIUM);

        $images->addSubField($image);

        $fileWidget->addSubField($images);

        $lockedContent = new TrueFalseField('field_590af0eee4a62');
        $lockedContent->setLabel('Locked Content')
            ->setName('locked_content')
            ->setConditionalLogic(new ACFConditionalLogic(
                self::LOCKED_CONTENT_FIELD,
                ACFConditionalLogic::OPERATOR_EQUALS,
                '1'
            ));

        $fileWidget->addSubField($lockedContent);

        $button = new TextField('field_59e49490911cf');
        $button->setLabel('Download Button Text (Optional)')
            ->setName('download_button_text')
            ->setInstructions('This will override the default button text.');

        $fileWidget->addSubField($button);

        return apply_filters(sprintf('willow/acf/layout=%s', $fileWidget->getKey()), $fileWidget);
    }

    public static function getVideoWidget(): ACFLayout
    {
        $videoWidget = new ACFLayout('58aaea63b12d2');
        $videoWidget->setName('video')
            ->setLabel('Video');

        $teaserImage = new TrueFalseField(self::VIDEO_TEASER_IMAGE_FIELD);
        $teaserImage->setLabel('Teaser Image')
            ->setName('video_teaser_image')
            ->setInstructions(
                'This will generate an image from the video and set it as a <b>teaser image</b> for the article.'
            );

        $videoWidget->addSubField($teaserImage);

        $includeIntroVideo = new TrueFalseField('field_6061945f12bd9');
        $includeIntroVideo->setLabel('Include intro video')
            ->setName(self::VIDEO_INCLUDE_INTRO_VIDEO_FIELD);

        $videoWidget->addSubField($includeIntroVideo);

        $url = new TextField('field_5938fe71ed0bb');
        $url->setLabel('Embed Url')
            ->setName('embed_url')
            ->setInstructions(
                'Paste the embed url from your video provider, supported providers are: Vimeo, YouTube, 23Video'
            )->setRequired(true);

        $videoWidget->addSubField($url);

        $caption = new TextAreaField('field_58aaeb26b12d4');
        $caption->setLabel('Caption')
            ->setName('caption');

        $videoWidget->addSubField($caption);

        $chapterItems = new RepeaterField('field_6026694040f43');
        $chapterItems->setLabel('Chapters')
            ->setName(self::VIDEO_CHAPTER_ITEMS_FIELD)
            ->setLayout('row')
            ->setButtonLabel('Add chapter');

        $chapterItemImage = new ImageField('field_6026696e40f46');
        $chapterItemImage->setLabel('Thumbnail')
            ->setName('thumbnail')
            ->setReturnFormat(ACFField::RETURN_ARRAY)
            ->setPreviewSize(ImageField::PREVIEW_MEDIUM);

        $chapterItems->addSubField($chapterItemImage);

        $chapterItemUrl = new TextField('field_602669b940f48');
        $chapterItemUrl->setLabel('Url')
            ->setName('url');

        $chapterItems->addSubField($chapterItemUrl);

        $chapterItemTitle = new TextField('field_6026695840f44');
        $chapterItemTitle->setLabel('Title')
            ->setName('title');

        $chapterItems->addSubField($chapterItemTitle);

        $chapterItemDescription = new TextField('field_6026696440f45');
        $chapterItemDescription->setLabel('Description')
            ->setName('description');

        $chapterItems->addSubField($chapterItemDescription);

        $chapterItemSeconds = new TimePickerField('field_602669a740f47');
        $chapterItemSeconds->setLabel('Time')
            ->setName('time');

        $chapterItems->addSubField($chapterItemSeconds);

        $chapterItemTitle = new TrueFalseField('field_602669c340f49');
        $chapterItemTitle->setLabel('Show in list overview')
            ->setName('show_in_list_overview');

        $chapterItems->addSubField($chapterItemTitle);

        $videoWidget->addSubField($chapterItems);

        $lockedContent = new TrueFalseField('field_5922be0e5cda4');
        $lockedContent->setLabel('Locked Content')
            ->setName('locked_content')
            ->setConditionalLogic(new ACFConditionalLogic(
                self::LOCKED_CONTENT_FIELD,
                ACFConditionalLogic::OPERATOR_EQUALS,
                '1'
            ));

        $videoWidget->addSubField($lockedContent);

        return apply_filters(sprintf('willow/acf/layout=%s', $videoWidget->getKey()), $videoWidget);
    }

    public static function getLinkWidget()
    {
        $urlWidget = new ACFLayout('590b1798c8768');
        $urlWidget->setName('link')
            ->setLabel('Link');

        $url = new TextField('field_590b17c4c876a');
        $url->setLabel('URL')
            ->setName('url')
            ->setRequired(true);

        $urlWidget->addSubField($url);

        $button = new TextField('field_590b179fc8769');
        $button->setLabel('Title text')
            ->setName('title');

        $urlWidget->addSubField($button);

        $target = new SelectField('field_590b17d4c876b');
        $target->setLabel('Target')
            ->setName('target')
            ->addChoice('Default', 'Default For the Site')
            ->addChoice('Self', 'Open in same window/tab')
            ->addChoice('Blank', 'Open in a new tab')
            ->addChoice('Download', 'Force download a file')
            ->addDefaultValue('Default')
            ->setReturnFormat(ACFField::RETURN_VALUE);

        $urlWidget->addSubField($target);

        $lockedContent = new TrueFalseField('field_5922be3e5cda5');
        $lockedContent->setLabel('Locked Content')
            ->setName('locked_content')
            ->setConditionalLogic(new ACFConditionalLogic(
                self::LOCKED_CONTENT_FIELD,
                ACFConditionalLogic::OPERATOR_EQUALS,
                '1'
            ));

        $urlWidget->addSubField($lockedContent);

        return apply_filters(sprintf('willow/acf/layout=%s', $urlWidget->getKey()), $urlWidget);
    }

    public static function getGalleryWidget(): ACFLayout
    {
        $galleryWidget = new ACFLayout('5a4f4dea1745f');
        $galleryWidget->setName('gallery')
            ->setLabel('Gallery');

        $title = new TextField('field_5a952a1a811d2');
        $title->setLabel('Title')
            ->setName('title');

        $galleryWidget->addSubField($title);

        $description = new MarkdownField('field_5bbb153867a6f');
        $description->setLabel('Description')
            ->setName('description')
            ->setMdeConfig(MarkdownField::CONFIG_STANDARD);

        $galleryWidget->addSubField($description);

        $images = new RepeaterField('field_5a4f4dfd17460');
        $images->setLabel('Images')
            ->setName('images')
            ->setRequired(true)
            ->setLayout('block')
            ->setButtonLabel('Add Image to Gallery');

        $image = new ImageField('field_5a4f4e0f17461');
        $image->setLabel('Image')
            ->setName('image')
            ->setRequired(true)
            ->setReturnFormat(ACFField::RETURN_ARRAY)
            ->setPreviewSize(ImageField::PREVIEW_MEDIUM);

        $images->addSubField($image);

        $imageTitle = new TextField('field_5bbb151067a6e');
        $imageTitle->setLabel('Title')
            ->setName('title');

        $images->addSubField($imageTitle);

        $imageDescription = new MarkdownField('field_5af2a0fcb1027');
        $imageDescription->setLabel('Description')
            ->setName('description')
            ->setMdeConfig(MarkdownField::CONFIG_STANDARD);

        $images->addSubField($imageDescription);

        $imageVideoUrl = new UrlField('field_5f33c474363f4');
        $imageVideoUrl->setLabel('Video url')
            ->setName(self::VIDEO_URL_FIELD_NAME)
            ->setInstructions('The embed url for the Vimeo video.');

        $images->addSubField($imageVideoUrl);

        $galleryWidget->addSubField($images);

        $lockedContent = new TrueFalseField('field_5a4f4e5f17462');
        $lockedContent->setLabel('Locked Content')
            ->setName('locked_content')
            ->setConditionalLogic(new ACFConditionalLogic(
                self::LOCKED_CONTENT_FIELD,
                ACFConditionalLogic::OPERATOR_EQUALS,
                '1'
            ));

        $galleryWidget->addSubField($lockedContent);

        $displayHint = new RadioField('field_5af2a198b1028');
        $displayHint->setLabel('Display Format')
            ->setName('display_hint')
            ->setChoice('default', 'Default')
            ->setChoice('inline', 'Inline')
            ->setChoice('parallax', 'Parallax')
            ->setDefaultValue('default')
            ->setLayout('vertical')
            ->setReturnFormat(ACFField::RETURN_VALUE);

        $galleryWidget->addSubField($displayHint);

        return apply_filters(sprintf('willow/acf/layout=%s', $galleryWidget->getKey()), $galleryWidget);
    }

    public static function getInsertedCodeWidget(): ACFLayout
    {
        $insertedCode = new ACFLayout('58aae89d0f005');
        $insertedCode->setName('inserted_code')
            ->setLabel('Inserted Code');

        $code = new TextAreaField('field_58aae8b00f006');
        $code->setLabel('Code')
            ->setName('code');

        $insertedCode->addSubField($code);

        $lockedContent = new TrueFalseField('field_5922bdbd5cda1');
        $lockedContent->setLabel('Locked Content')
            ->setName('locked_content')
            ->setConditionalLogic(new ACFConditionalLogic(
                self::LOCKED_CONTENT_FIELD,
                ACFConditionalLogic::OPERATOR_EQUALS,
                '1'
            ));

        $insertedCode->addSubField($lockedContent);

        return apply_filters(sprintf('willow/acf/layout=%s', $insertedCode->getKey()), $insertedCode);
    }

    public static function getInfoboxWidget(): ACFLayout
    {
        $infobox = new ACFLayout('58aae479d3958');
        $infobox->setName('infobox')
            ->setLabel('Infobox');

        $title = new TextField('field_58aae4b6809c7');
        $title->setLabel('Title')
            ->setName('title')
            ->setRequired(false);

        $infobox->addSubField($title);

        $body = new MarkdownField('field_58aae4d4809c8');
        $body->setLabel('Body')
            ->setName('body')
            ->setRequired(true)
            ->setMdeConfig(MarkdownField::CONFIG_SIMPLE);

        $infobox->addSubField($body);

        $image = new ImageField('field_5fa3fdc25406d');
        $image->setLabel('Image')
            ->setName('image')
            ->setRequired(false)
            ->setReturnFormat(ACFField::RETURN_ARRAY)
            ->setPreviewSize(ImageField::PREVIEW_MEDIUM);

        $infobox->addSubField($image);

        $lockedContent = new TrueFalseField('field_5922bdd55cda2');
        $lockedContent->setLabel('Locked Content')
            ->setName('locked_content')
            ->setConditionalLogic(new ACFConditionalLogic(
                self::LOCKED_CONTENT_FIELD,
                ACFConditionalLogic::OPERATOR_EQUALS,
                '1'
            ));

        $infobox->addSubField($lockedContent);

        return apply_filters(sprintf('willow/acf/layout=%s', $infobox->getKey()), $infobox);
    }

    public static function getLeadParagraphWidget(): ACFLayout
    {
        $leadParagraphWidget = new ACFLayout('layout_5bbb614643179');
        $leadParagraphWidget->setName('lead_paragraph')
            ->setLabel('Lead Paragraph');

        $title = new TextField('field_5bbb61464317a');
        $title->setLabel('Title')
            ->setName('title')
            ->setRequired(true);

        $leadParagraphWidget->addSubField($title);

        $description = new MarkdownField('field_5bbb61464317b');
        $description->setLabel('Description')
            ->setName('description')
            ->setMdeConfig(MarkdownField::CONFIG_SIMPLE);

        $leadParagraphWidget->addSubField($description);

        $displayFormat = new RadioField('field_5bbb61464317d');
        $displayFormat->setLabel('Display Format')
            ->setName('display_hint')
            ->setChoice('default', 'Default')
            ->setChoice('chapter', 'Chapter')
            ->setDefaultValue('default')
            ->setLayout('vertical')
            ->setReturnFormat(ACFField::RETURN_VALUE);

        $leadParagraphWidget->addSubField($displayFormat);

        return apply_filters(sprintf('willow/acf/layout=%s', $leadParagraphWidget->getKey()), $leadParagraphWidget);
    }

    public static function getParagraphListWidget(): ACFLayout
    {
        $paragraphListWidget = new ACFLayout('layout_5bb4bd1afd048');
        $paragraphListWidget->setName('paragraph_list')
            ->setLabel('Paragraph List');

        $title = new TextField('field_5bb4bd2ffd049');
        $title->setLabel('Title')
            ->setName('title');

        $paragraphListWidget->addSubField($title);

        $description = new MarkdownField('field_5bb4bd38fd04a');
        $description->setLabel('Description')
            ->setName('description')
            ->setMdeConfig(MarkdownField::CONFIG_SIMPLE);

        $paragraphListWidget->addSubField($description);

        $image = new ImageField('field_5bb4bd65fd04b');
        $image->setLabel('Image')
            ->setName('image')
            ->setReturnFormat(ACFField::RETURN_ARRAY)
            ->setPreviewSize(ImageField::PREVIEW_MEDIUM);

        $paragraphListWidget->addSubField($image);

	    $videoUrl = new UrlField('field_5f214904627a6');
	    $videoUrl->setLabel('Video Url')
	             ->setName(self::VIDEO_URL_FIELD_NAME)
	             ->setInstructions('The embed url for the video.');

	    $paragraphListWidget->addSubField($videoUrl);

        $collapsible = new TrueFalseField('field_5bd30f723cdcc');
        $collapsible->setLabel('Collapsible')
            ->setName(self::COLLAPSIBLE_FIELD_NAME)
            ->setInstructions('Should this paragraph list be collapsed in the view?');

        $paragraphListWidget->addSubField($collapsible);

        $showNumbers = new TrueFalseField('field_5f9a7d5a67430');
        $showNumbers->setLabel('Show numbers')
            ->setName(self::SHOW_NUMBERS_FIELD_NAME)
            ->setInstructions('Should this paragraph show numbers on the items?')
            ->setDefaultValue(true);

        $paragraphListWidget->addSubField($showNumbers);

        $displayHint = new RadioField('field_5bb4bd75fd04c');
        $displayHint->setLabel('Display Format')
            ->setName('display_hint')
            ->setChoice('ordered', 'Number List')
            ->setChoice('unordered', 'Bullet List')
            ->setChoice('image', 'Image List')
            ->setChoice('custom', 'Custom List')
            ->setDefaultValue('ordered')
            ->setLayout('vertical')
            ->setReturnFormat(ACFField::RETURN_VALUE);

        $paragraphListWidget->addSubField($displayHint);

        $items = new RepeaterField('field_5bb4be68fd04d');
        $items->setLabel('Items')
            ->setName('items')
            ->setLayout('row')
            ->setButtonLabel('Add item');

        $itemTitle = new TextField('field_5bb4be86fd04e');
        $itemTitle->setLabel('Title')
            ->setName('title')
            ->setRequired(false);

        $items->addSubField($itemTitle);

        $itemDescription = new MarkdownField('field_5bb4be91fd04f');
        $itemDescription->setLabel('Description')
            ->setName('description')
            ->setRequired(true)
            ->setMdeConfig(MarkdownField::CONFIG_SIMPLE);

        $items->addSubField($itemDescription);

        $itemImage = new ImageField('field_5bb4bea5fd050');
        $itemImage->setLabel('Image')
            ->setName('image')
            ->setReturnFormat(ACFField::RETURN_ARRAY)
            ->setPreviewSize(ImageField::PREVIEW_MEDIUM);

        $items->addSubField($itemImage);

        $itemVideoUrl = new UrlField('field_5f3ba1fad26a3');
        $itemVideoUrl->setLabel('Video url')
            ->setName(self::VIDEO_URL_FIELD_NAME)
            ->setInstructions('The embed url for the Vimeo video.');

        $items->addSubField($itemVideoUrl);

        $paragraphListWidget->addSubField($items);

        return apply_filters(sprintf('willow/acf/layout=%s', $paragraphListWidget->getKey()), $paragraphListWidget);
    }

    public static function getAssociatedCompositeWidget(): ACFLayout
    {
        $associatedCompositeWidget = new ACFLayout('58e393a7128b3');
        $associatedCompositeWidget->setName('associated_composites')
            ->setLabel('Associated Composites');

        $title = new TextField('field_6078023029282');
        $title->setLabel('Title')
            ->setName('title');

        $associatedCompositeWidget->addSubField($title);

        $content = new RelationshipField('field_58e393e0128b4');
        $content->setLabel('Content')
            ->setName('composites')
            ->addPostType(WpComposite::POST_TYPE)
            ->addFilter(RelationshipField::FILTER_SEARCH)
            ->addFilter(RelationshipField::FILTER_TAXONOMY)
            ->setReturnFormat(ACFField::RETURN_OBJECT);

        $associatedCompositeWidget->addSubField($content);

        $lockedContent = new TrueFalseField('field_5922be585cda6');
        $lockedContent->setLabel('Locked Content')
            ->setName('locked_content')
            ->setConditionalLogic(new ACFConditionalLogic(
                self::LOCKED_CONTENT_FIELD,
                ACFConditionalLogic::OPERATOR_EQUALS,
                '1'
            ));

        $associatedCompositeWidget->addSubField($lockedContent);

        $displayHint = new RadioField('field_603f7f06ddaac');
        $displayHint->setLabel('Display Format')
            ->setName('display_hint')
            ->setChoice('default', 'Default')
            ->setChoice('food-plan', 'Food plan')
            ->setChoice('story-list', 'Story list')
            ->setDefaultValue('default')
            ->setLayout('vertical')
            ->setReturnFormat(ACFField::RETURN_VALUE);

        $associatedCompositeWidget->addSubField($displayHint);

        return apply_filters(
            sprintf('willow/acf/layout=%s', $associatedCompositeWidget->getKey()),
            $associatedCompositeWidget
        );
    }

    public static function getInventoryWidget(): ACFLayout
    {
        $inventoryWidget = new ACFLayout('layout_58aeadaacbe5c');
        $inventoryWidget->setName('inventory')
            ->setLabel('Inventory');

        $title = new TextField('field_58e3971e4d277');
        $title->setLabel('Title')
            ->setName('title')
            ->setRequired(false);

        $inventoryWidget->addSubField($title);

        $description = new MarkdownField('field_6017cd4793c46');
        $description->setLabel('Description')
            ->setName('description')
            ->setMdeConfig(MarkdownField::CONFIG_STANDARD);

        $inventoryWidget->addSubField($description);

        $items = new RepeaterField('field_58aeadcdcbe5d');
        $items->setLabel('Inventory Items')
            ->setName('items')
            ->setLayout('table')
            ->setRequired(true)
            ->setButtonLabel('Add Row');

        $displayHint = new RadioField('field_6017cd8b93c47');
        $displayHint->setLabel('Display Format')
            ->setName('display_hint')
            ->setChoice('default', 'Default')
            ->setChoice('heading', 'Heading')
            ->setChoice('summary', 'Summary')
            ->setDefaultValue('default')
            ->setLayout('horizontal')
            ->setReturnFormat(ACFField::RETURN_VALUE);

        $items->addSubField($displayHint);

        $key = new TextField('field_58aeae3fcbe61');
        $key->setLabel('Key')
            ->setName('key')
            ->setRequired(true);

        $items->addSubField($key);

        $value = new TextField('field_58aeae4ccbe62');
        $value->setLabel('Value')
            ->setName('value')
            ->setRequired(false);

        $items->addSubField($value);

        $inventoryWidget->addSubField($items);

        $lockedContent = new TrueFalseField('field_5922be6d5cda7');
        $lockedContent->setLabel('Locked Content')
            ->setName('locked_content')
            ->setConditionalLogic(new ACFConditionalLogic(
                self::LOCKED_CONTENT_FIELD,
                ACFConditionalLogic::OPERATOR_EQUALS,
                '1'
            ));

        $inventoryWidget->addSubField($lockedContent);

        return apply_filters(sprintf('willow/acf/layout=%s', $inventoryWidget->getKey()), $inventoryWidget);
    }

    public static function getProductWidget(): ACFLayout
    {
        $productWidget = new ACFLayout('layout_601a57813410a');
        $productWidget->setName('product')
            ->setLabel('Product');

        $title = new TextField('field_601a57813410b');
        $title->setLabel('Title')
            ->setName('title')
            ->setRequired(true);

        $productWidget->addSubField($title);

        $description = new MarkdownField('field_601a57813410c');
        $description->setLabel('Description')
            ->setName('description')
            ->setRequired(false)
            ->setMdeConfig(MarkdownField::CONFIG_STANDARD);

        $productWidget->addSubField($description);

        $image = new ImageField('field_601a578d34112');
        $image->setLabel('Image')
            ->setName('image')
            ->setRequired(true)
            ->setReturnFormat(ACFField::RETURN_ARRAY)
            ->setPreviewSize(ImageField::PREVIEW_MEDIUM);

        $productWidget->addSubField($image);

        $price = new TextField('field_601a57a534113');
        $price->setLabel('Price')
            ->setName('price')
            ->setRequired(true);

        $productWidget->addSubField($price);

        $winner = new TrueFalseField('field_601a57d634114');
        $winner->setLabel('Winner')
            ->setName('winner')
            ->setMessage('Mark the product as winner.');

        $productWidget->addSubField($winner);

        $bestBuy = new TrueFalseField('field_601a580934115');
        $bestBuy->setLabel('Best buy')
            ->setName('best_buy')
            ->setMessage('Mark the product as best buy.');

        $productWidget->addSubField($bestBuy);

        $maxPoints = new SelectField('field_601a5910575dd');
        $maxPoints->setLabel('Max points')
            ->setName('max_points')
            ->setInstructions('Max points. Default is 10.')
            ->addChoice('1', '1')
            ->addChoice('2', '2')
            ->addChoice('3', '3')
            ->addChoice('4', '4')
            ->addChoice('5', '5')
            ->addChoice('6', '6')
            ->addChoice('7', '7')
            ->addChoice('8', '8')
            ->addChoice('9', '9')
            ->addChoice('10', '10')
            ->setDefaultValue(['10'])
            ->setReturnFormat(ACFField::RETURN_VALUE);

        $productWidget->addSubField($maxPoints);

        $items = new RepeaterField('field_601a57813410d');
        $items->setLabel('Scores')
            ->setName('items')
            ->setLayout('table')
            ->setRequired(true)
            ->setButtonLabel('Add Row');

        $parameter = new TextField('field_601a57813410f');
        $parameter->setLabel('Parameter')
            ->setName('parameter')
            ->setRequired(true);

        $items->addSubField($parameter);

        $score = new TextField('field_601a578134110');
        $score->setLabel('Score')
            ->setName('score')
            ->setRequired(true);

        $items->addSubField($score);

        $productWidget->addSubField($items);

        $detailsTitle = new TextField('field_6025148bd81a0');
        $detailsTitle->setLabel('Details Title')
            ->setName('details_title')
            ->setRequired(false);

        $productWidget->addSubField($detailsTitle);

        $detailsDescription = new MarkdownField('field_6022832a2442e');
        $detailsDescription->setLabel('Details Description')
            ->setName('details_description')
            ->setRequired(false)
            ->setMdeConfig(MarkdownField::CONFIG_STANDARD);

        $productWidget->addSubField($detailsDescription);

        $detailsItems = new RepeaterField('field_6022837e2442f');
        $detailsItems->setLabel('Details Items')
            ->setName('details_items')
            ->setLayout('table')
            ->setRequired(false)
            ->setButtonLabel('Add Row');

        $detailsItemDisplayHint = new RadioField('field_6022839324430');
        $detailsItemDisplayHint->setLabel('Display Format')
            ->setName('display_hint')
            ->setChoice('default', 'Default')
            ->setChoice('heading', 'Heading')
            ->setChoice('summary', 'Summary')
            ->setDefaultValue('default')
            ->setLayout('horizontal')
            ->setReturnFormat(ACFField::RETURN_VALUE);

        $detailsItems->addSubField($detailsItemDisplayHint);

        $detailsItemKey = new TextField('field_602283fc24431');
        $detailsItemKey->setLabel('Key')
            ->setName('key')
            ->setRequired(true);

        $detailsItems->addSubField($detailsItemKey);

        $detailsItemValue = new TextField('field_6022844b24432');
        $detailsItemValue->setLabel('Value')
            ->setName('value')
            ->setRequired(false);

        $detailsItems->addSubField($detailsItemValue);

        $productWidget->addSubField($detailsItems);

        $lockedContent = new TrueFalseField('field_601a578134111');
        $lockedContent->setLabel('Locked Content')
            ->setName('locked_content')
            ->setConditionalLogic(new ACFConditionalLogic(
                self::LOCKED_CONTENT_FIELD,
                ACFConditionalLogic::OPERATOR_EQUALS,
                '1'
            ));

        $productWidget->addSubField($lockedContent);

        return apply_filters(sprintf('willow/acf/layout=%s', $productWidget->getKey()), $productWidget);
    }

    public static function getHotspotImageWidget(): ACFLayout
    {
        $hotspotImageWidget = new ACFLayout('layout_5bb21d074132f');
        $hotspotImageWidget->setName('hotspot_image')
            ->setLabel('Hotspot Image');

        $title = new TextField('field_5bb21d1841330');
        $title->setLabel('Title')
            ->setName('title');

        $hotspotImageWidget->addSubField($title);

        $description = new MarkdownField('field_5bb21d2a2c2c4');
        $description->setLabel('Description')
            ->setName('description')
            ->setMdeConfig(MarkdownField::CONFIG_STANDARD);

        $hotspotImageWidget->addSubField($description);

        $image = new ImageField('field_5bb21d3b2c2c5');
        $image->setLabel('Image')
            ->setName('image')
            ->setRequired(true)
            ->setReturnFormat(ACFField::RETURN_ARRAY)
            ->setPreviewSize(ImageField::PREVIEW_MEDIUM);

        $hotspotImageWidget->addSubField($image);

        $displayHint = new RadioField('field_5bb36df662c91');
        $displayHint->setLabel('Display Format')
            ->setName('display_hint')
            ->setChoice('ordered', 'Ordered')
            ->setChoice('unordered', 'Unordered')
            ->setDefaultValue('ordered')
            ->setLayout('vertical')
            ->setReturnFormat(ACFField::RETURN_VALUE);

        $hotspotImageWidget->addSubField($displayHint);

        $hotspots = new RepeaterField('field_5bb21d902c2c6');
        $hotspots->setLayout('Hotspots')
            ->setName('hotspots')
            ->setRequired(true)
            ->setMin(1)
            ->setLayout('block')
            ->setButtonLabel('Add Hotspot');

        $hotspotTitle = new TextField('field_5bb21db12c2c7');
        $hotspotTitle->setLabel('Title')
            ->setName('title');

        $hotspots->addSubField($hotspotTitle);

        $hotspotDescription = new MarkdownField('field_5bb21db72c2c8');
        $hotspotDescription->setLabel('Description')
            ->setName('description')
            ->setRequired(true)
            ->setMdeConfig(MarkdownField::CONFIG_SIMPLE);

        $hotspots->addSubField($hotspotDescription);

        $hotspotCoordinate = new ImageHotspotCoordinatesField('field_5bb21dc52c2c9');
        $hotspotCoordinate->setLabel('Coordinates')
            ->setName('coordinates')
            ->setRequired(true);

        $hotspots->addSubField($hotspotCoordinate);

        $hotspotImageWidget->addSubField($hotspots);

        return apply_filters(sprintf('willow/acf/layout=%s', $hotspotImageWidget->getKey()), $hotspotImageWidget);
    }

    public static function getQuoteWidget(): ACFLayout
    {
        $quoteWidget = new ACFLayout('layout_5bb315118c73b');
        $quoteWidget->setName('quote')
            ->setLabel('Quote');

        $quote = new TextAreaField('field_5bb315248c73c');
        $quote->setLabel('Quote')
            ->setName('quote')
            ->setRequired(true)
            ->setRows(2);

        $quoteWidget->addSubField($quote);

        $author = new TextField('field_5bb315e38c73d');
        $author->setLabel('Author')
            ->setName('author');

        $quoteWidget->addSubField($author);

        return apply_filters(sprintf('willow/acf/layout=%s', $quoteWidget->getKey()), $quoteWidget);
    }

    public static function getNewsletterWidget(): ACFLayout
    {
        $newsletterWidget = new ACFLayout('layout_5e2eb60fe1ce9');
        $newsletterWidget->setName('newsletter')
            ->setLabel('Newsletter');

        $title = new TextField('field_5e2eb61be1cea');
        $title->setLabel('Title')
            ->setName('title')
            ->setPlaceholder('Title');

        $newsletterWidget->addSubField($title);

        $description = new MarkdownField('field_5e2eb62ee1ceb');
        $description->setLabel('Description')
            ->setName('description')
            ->setMdeConfig(MarkdownField::CONFIG_SIMPLE);

        $newsletterWidget->addSubField($description);

        $sourceCode = new NumberField(self::SOURCE_CODE_FIELD);
        $sourceCode->setLabel('Source Code')
            ->setName('source_code')
            ->setInstructions('If no source code is provided, the default for the brand will be used')
            ->setPlaceholder('Source Code')
            ->setMin(100000)
            ->setMax(999999);

        $newsletterWidget->addSubField($sourceCode);

        $permissionText = new MarkdownField('field_5e2ebd3b7a75a');
        $permissionText->setLabel('Permission Text')
            ->setName('permission_text')
            ->setInstructions('If no permission text is provided, the default for the brand will be used')
            ->setMdeConfig(MarkdownField::CONFIG_SIMPLE);

        $newsletterWidget->addSubField($permissionText);

        return apply_filters(sprintf('willow/acf/layout=%s', $newsletterWidget->getKey()), $newsletterWidget);
    }

    public static function getChaptersSummaryWidget(): ACFLayout
    {
        $chaptersSummaryWidget = new ACFLayout('layout_5e4be48e39b18');
        $chaptersSummaryWidget->setName('chapters_summary')
            ->setLabel('Chapters summary');

        return apply_filters(sprintf('willow/acf/layout=%s', $chaptersSummaryWidget->getKey()), $chaptersSummaryWidget);
    }

    public static function getMultimediaWidget()
    {
        $multimediaWidget = new ACFLayout('5fa10a410e4db');
        $multimediaWidget->setName('multimedia')
            ->setLabel('Multimedia');

        $title = new TextField('field_5fa10a570e4dc');
        $title->setLabel('Title')
            ->setName('title')
            ->setPlaceholder('Title');

        $multimediaWidget->addSubField($title);

        $description = new MarkdownField('field_5fa10a650e4dd');
        $description->setLabel('Description')
            ->setName('description')
            ->setMdeConfig(MarkdownField::CONFIG_SIMPLE);;

        $multimediaWidget->addSubField($description);

        $image = new ImageField('field_5fa10adc0e4de');
        $image->setLabel('Image')
            ->setName('image')
            ->setRequired(true)
            ->setReturnFormat(ACFField::RETURN_ARRAY)
            ->setPreviewSize(ImageField::PREVIEW_MEDIUM);

        $multimediaWidget->addSubField($image);

        $displayHint = new RadioField('field_5fa10ca4c5576');
        $displayHint->setLabel('Display Format')
            ->setName('display_hint')
            ->setChoice('blueprint', 'Blueprint')
            ->setChoice('3d', '3D')
            ->setDefaultValue('blueprint')
            ->setLayout('vertical')
            ->setReturnFormat(ACFField::RETURN_VALUE);

        $multimediaWidget->addSubField($displayHint);

        $id = new TextField('field_5fa10b770e4e0');
        $id->setLabel('Vectary ID')
            ->setName('vectary_id')
            ->setInstructions('The ID to the 3D model in Vectary.')
            ->setRequired(true)
            ->setConditionalLogic(new ACFConditionalLogic(
                self::MULTIMEDIA_DISPLAY_HINT,
                ACFConditionalLogic::OPERATOR_EQUALS,
                self::MULTIMEDIA_DISPLAY_HINT_3D
            ));

        $multimediaWidget->addSubField($id);

        $url = new TextField('field_5fa10b230e4df');
        $url->setLabel('URL')
            ->setName('vectary_url')
            ->setInstructions('The Vectary 3D model url.')
            ->setRequired(true)
            ->setConditionalLogic(new ACFConditionalLogic(
                self::MULTIMEDIA_DISPLAY_HINT,
                ACFConditionalLogic::OPERATOR_EQUALS,
                self::MULTIMEDIA_DISPLAY_HINT_3D
            ));

        $multimediaWidget->addSubField($url);

        return apply_filters(sprintf('willow/acf/layout=%s', $multimediaWidget->getKey()), $multimediaWidget);
    }

    public static function getRecipeWidget()
    {
        $timeUnits = [
            'm' => 'minutes',
            'h' => 'hours',
            'd' => 'days',
        ];

        $recipeWidget = new ACFLayout('6017fc21f57e4');
        $recipeWidget->setName('recipe')
            ->setLabel('Recipe');

        // General data
        $title = new TextField('field_6017fc8af57e5');
        $title->setLabel('Title')
            ->setName('title');
        $recipeWidget->addSubField($title);

        $description = new MarkdownField('field_6017fc9cf57e6');
        $description->setLabel('Description')
            ->setName('description');
        $recipeWidget->addSubField($description);

        $image = new ImageField('field_6017fcaef57e7');
        $image->setLabel('Image')
            ->setName('image')
            ->setReturnFormat(ACFField::RETURN_ARRAY)
            ->setPreviewSize(ImageField::PREVIEW_MEDIUM);
        $recipeWidget->addSubField($image);

        $useAdArticleLeadImage = new TrueFalseField('field_601a977ef88d4');
        $useAdArticleLeadImage->setLabel('Use as article lead-image')
            ->setName('use_as_article_lead_image');
        $recipeWidget->addSubField($useAdArticleLeadImage);

        //Duration data
        $durationGroup = new GroupField('field_6017fd2d456b0');
        $durationGroup->setLabel('Duration')
            ->setName('duration_group');
        $recipeWidget->addSubField($durationGroup);

        //Duration data - preparation time
        $preparationTime = new TextField('field_6017fd4f456b1');
        $preparationTime->setLabel('Preparation time')
            ->setName('preparation_time')
            ->setPlaceholder('preparation time')
            ->setWrapper((new ACFWrapper())->setWidth('60'));
        $recipeWidget->addSubField($preparationTime);

        $preparationTimeMin = new TextField('field_6017fd6a456b2');
        $preparationTimeMin->setLabel('Time')
            ->setName('preparation_time_min')
            ->setWrapper((new ACFWrapper())->setWidth('20'));
        $recipeWidget->addSubField($preparationTimeMin);

        $preparationTimeMinUnit = new SelectField('field_6017fd79456b3');
        $preparationTimeMinUnit->setLabel('Units')
            ->setName('preparation_time_unit')
            ->setWrapper((new ACFWrapper())->setWidth('20'))
            ->setChoices($timeUnits)
            ->setMultiple(false)
            ->setDefaultValue(['m'])
            ->setReturnFormat(ACFField::RETURN_VALUE);
        $recipeWidget->addSubField($preparationTimeMinUnit);

        //Duration data - cooking time
        $cookingTime = new TextField('field_6017febc9b6cc');
        $cookingTime->setLabel('Cooking time')
            ->setName('cooking_time')
            ->setPlaceholder('cooking time')
            ->setWrapper((new ACFWrapper())->setWidth('60'));
        $recipeWidget->addSubField($cookingTime);

        $cookingTimeMin = new TextField('field_6017feaf9b6cb');
        $cookingTimeMin->setLabel('Time')
            ->setName('cooking_time_min')
            ->setWrapper((new ACFWrapper())->setWidth('20'));
        $recipeWidget->addSubField($cookingTimeMin);

        $cookingTimeMinUnit = new SelectField('field_6017fee69b6cd');
        $cookingTimeMinUnit->setLabel('Units')
            ->setName('cooking_time_unit')
            ->setWrapper((new ACFWrapper())->setWidth('20'))
            ->setChoices($timeUnits)
            ->setDefaultValue(['m'])
            ->setReturnFormat(ACFField::RETURN_VALUE);
        $recipeWidget->addSubField($cookingTimeMinUnit);

        //Duration data - total time
        $totalTime = new TextField('field_601a902438441');
        $totalTime->setLabel('Total time')
            ->setName('total_time')
            ->setPlaceholder('total time')
            ->setWrapper((new ACFWrapper())->setWidth('60'));
        $recipeWidget->addSubField($totalTime);

        $totalTimeMin = new TextField('field_601a902738442');
        $totalTimeMin->setLabel('Time')
            ->setName('total_time_min')
            ->setWrapper((new ACFWrapper())->setWidth('20'));
        $recipeWidget->addSubField($totalTimeMin);

        $totalTimeMinUnit = new SelectField('field_601a902d38443');
        $totalTimeMinUnit->setLabel('Units')
            ->setName('total_time_unit')
            ->setWrapper((new ACFWrapper())->setWidth('20'))
            ->setChoices($timeUnits)
            ->setDefaultValue(['m'])
            ->setReturnFormat(ACFField::RETURN_VALUE);
        $recipeWidget->addSubField($totalTimeMinUnit);

        $totalTimeExtraInfo = new TextField('field_601a909c38444');
        $totalTimeExtraInfo->setLabel('Extra info')
            ->setName('total_time_extra_info')
            ->setPlaceholder('total time (extra info)');
        $recipeWidget->addSubField($totalTimeExtraInfo);

        $showMetaInfoInArticleHeaderAndTeaser = new TrueFalseField('field_601a97c1f88d5');
        $showMetaInfoInArticleHeaderAndTeaser->setLabel('Show meta info in article header and teaser')
            ->setName('show_meta_info_in_header_and_teaser')
            ->setDefaultValue(true);
        $recipeWidget->addSubField($showMetaInfoInArticleHeaderAndTeaser);

        $quantity = new TextField('field_601800c2a3761');
        $quantity->setLabel('Quantity')
            ->setName('quantity')
            ->setWrapper((new ACFWrapper())->setWidth('25'));
        $recipeWidget->addSubField($quantity);

        $quantity = new TextField('field_601800f5a3762');
        $quantity->setLabel('Quantity type')
            ->setName('quantity_type')
            ->setPlaceholder('eg. cookies')
            ->setWrapper((new ACFWrapper())->setWidth('75'));
        $recipeWidget->addSubField($quantity);

        $ingredientBlockItems = new RepeaterField('field_601a92095acd4');
        $ingredientBlockItems->setLabel('Ingredients')
            ->setName('ingredient_block_items')
            ->setLayout('block')
            ->setButtonLabel('Add Ingredient block')
            ->setMin(1);
        // adds subfields to repeater here !!
        self::setRecipeIngredientBlockItemsSubFields($ingredientBlockItems);
        $recipeWidget->addSubField($ingredientBlockItems);

        $instructionsHeadline = new TextField('field_601a940a4bdd9');
        $instructionsHeadline->setLabel('Instructions headline')
            ->setName('instructions_headline');
        $recipeWidget->addSubField($instructionsHeadline);

        $instructionsMarkdown = new MarkdownField('field_601a944c4bdda');
        $instructionsMarkdown->setLabel('Instructions')
            ->setName('instructions');
        $recipeWidget->addSubField($instructionsMarkdown);

        $instructionsTipMarkdown = new MarkdownField('field_601a96a123029');
        $instructionsTipMarkdown->setLabel('Instructions tip')
            ->setName('instructions_tip');
        $recipeWidget->addSubField($instructionsTipMarkdown);

        $nutrientsHeadline = new TextField('field_601a95744bddc');
        $nutrientsHeadline->setLabel('Nutrients headline')
            ->setName('nutrients_headline');
        $recipeWidget->addSubField($nutrientsHeadline);

        $nutrientItems = new RepeaterField('field_601a95a40796a');
        $nutrientItems->setLabel('Nutrients list')
            ->setName('nutrient_items')
            ->setLayout('table')
            ->setButtonLabel('Add Nutrient')
            ->setMin(5);
        // adds subfields to repeater here !!
        self::setRecipeNutrientItemsSubFields($nutrientItems);
        $recipeWidget->addSubField($nutrientItems);

        $tags = new TextField('field_601beae919f6d');
        $tags->setLabel('Tags')
            ->setName('recipe_tags');
        $recipeWidget->addSubField($tags);

        return apply_filters(sprintf('willow/acf/layout=%s', $recipeWidget->getKey()), $recipeWidget);
    }

    private static function setRecipeIngredientBlockItemsSubFields(&$ingredientBlockItems)
    {
        $headLine = new TextField('field_601a92925acd5');
        $headLine->setLabel('Headline')
            ->setName('headline')
            ->setPlaceholder('Headline text (leave empty for no headline)');
        $ingredientBlockItems->addSubField($headLine);

        $ingredientItems = new RepeaterField('field_601a92b65acd6');
        $ingredientItems->setLabel('')
            ->setName('ingredient_items')
            ->setLayout('table')
            ->setButtonLabel('Add ingredient')
            ->setMin(5);
        // adds subfields to repeater here !!
        self::setRecipeIngredientItemsSubFields($ingredientItems);
        $ingredientBlockItems->addSubField($ingredientItems);
    }

    /**
     * Recipe ingredient choices, please don't change items order, it used in migration script
     * @return string[]
     */
    public static function getRecipeIngredientChoices(){
       return [
           '-' => '-',
           'gram' => 'gram',
           'dl' => 'dl',
           'teaspoon' => 'teaspoon',
           'tablespoon' => 'tablespoon',
           'ml' => 'ml',
           'cl' => 'cl',
           'liter' => 'liter',
           'kg' => 'kg',
           'piece' => 'piece',
           'pinch' => 'pinch',
           'nip' => 'nip',
           'sprinkle' => 'sprinkle',
           'bundle' => 'bundle',
           'cloves' => 'cloves',
           'slice' => 'slice',
           'handful' => 'handful',
           'can' => 'can',
           'packet' => 'packet',
       ];
    }

    private static function setRecipeIngredientItemsSubFields(&$ingredientItems)
    {
        $amount = new TextField('field_601a92f25acd7');
        $amount->setLabel('Amount')
            ->setName('amount');
        $ingredientItems->addSubField($amount);

        $unit = new SelectField('field_601a930d5acd8');
        $unit->setLabel('Unit')
            ->setName('unit')
            ->setChoices(self::getRecipeIngredientChoices())
            ->setDefaultValue(['-'])
            ->setReturnFormat(ACFField::RETURN_VALUE);
        $ingredientItems->addSubField($unit);

        $ingredient = new TextField('field_601a93305acd9');
        $ingredient->setLabel('Ingredient')
            ->setName('ingredient');
        $ingredientItems->addSubField($ingredient);
    }

    /**
     * please don't change items order, it used in migration script
     * @return string[]
     */
    public static function getRecipeNutrientItemsChoices()
    {
       return [
           'Energy' => 'Energy',
           'Protein' => 'Protein',
           'Fat' => 'Fat',
           'Carbohydrate' => 'Carbohydrate',
           'Fiber' => 'Fiber',
       ];
    }

    /**
     * please don't change items order, it used in migration script
     * @return string[]
     */
    public static function getRecipeNutrientItemsUnitChoices()
    {
       return [
           '-' => '-',
           'kcal' => 'kcal',
           'gram' => 'gram',
       ];
    }

    private static function setRecipeNutrientItemsSubFields(&$nutrientItems)
    {
        $nutrient = new SelectField('field_601a95c40796b');
        $nutrient->setLabel('Nutrient')
            ->setName('nutrient')
            ->setChoices(self::getRecipeNutrientItemsChoices())
            ->setDefaultValue(['Energy'])
            ->setReturnFormat(ACFField::RETURN_VALUE);
        $nutrientItems->addSubField($nutrient);

        $nutrientAmount = new TextField('field_601a95ed0796c');
        $nutrientAmount->setLabel('Amount')
            ->setName('amount');
        $nutrientItems->addSubField($nutrientAmount);

        $nutrientAmountUnit = new SelectField('field_601a95f60796d');
        $nutrientAmountUnit->setLabel('Unit')
            ->setName('unit')
            ->setChoices(self::getRecipeNutrientItemsUnitChoices())
            ->setDefaultValue(['gram'])
            ->setReturnFormat(ACFField::RETURN_VALUE);
        $nutrientItems->addSubField($nutrientAmountUnit);
    }

    public static function getCalculatorWidget()
    {
        $calculatorWidget = new ACFLayout('layout_603fbef1f5e40');
        $calculatorWidget->setLabel('Calculator')
            ->setName('calculator');

        $calculatorField = new SelectField('field_603fbefcf5e41');
        $calculatorField->setName('calculator')
            ->setChoices([
                // TODO: Out comment elements exits in White Album and should be enabled later.
                '' => 'Select a calculator...',
                'bmi' => 'BMI calculator',
                // 'reward' => 'Calculates how much you can reward yourself',
                // 'calories_burn' => 'Calories burn time based on activity',
                // 'christmas_sin_burner' => 'Calories burn time based on christmas intake and activity',
                // 'sin_burn' => 'Calories burn time based on intake and activity',
                // 'kcal_burn' => 'Calories burning based on activity',
                'kcal_burn_v2' => 'Calories burning based on activity (new version)',
                // 'christmas_kcal' => 'Calories burning based on christmas activity',
                'cooper_test' => 'Cooper test',
                // 'calories_needed' => 'Daily calories need',
                'calories_needed_with_weight_loss' => 'Daily calories need with weight loss',
                'protein' => 'Daily protein need',
                // 'due_date' => 'Due date',
                // 'due_date_reverse' => 'Due date (reverse)',
                // 'fertility' => 'Fertility days',
                // 'weight_gain' => 'Intake-based weight gain calculator',
                'fitness_value' => 'Physical fitness value',
                // 'end_time' => 'Predicts 10k and half marathon run times based on 5k run time',
                // 'pulse_rate' => 'Recommended training pulse rate',
                // 'calories_while_resting' => 'Resting metabolic rate',
                // 'speed_with_different_weight' => 'Run time after weight gain or loss',
                // 'speed_with_different_age' => 'Run time prediction when getting older',
                // 'speed' => 'Suggests running paces based on 5k run time',
                // 'mars_bar' => 'Time it will take to burn a Mars bar',
                'fat_percentage' => 'Waist-to-hip ratio',
            ])
            ->setDefaultValue([''])
            ->setReturnFormat(ACFField::RETURN_VALUE);

        $calculatorWidget->addSubField($calculatorField);

        return apply_filters(sprintf('willow/acf/layout=%s', $calculatorWidget->getKey()), $calculatorWidget);
    }

    private static function registerHooks()
    {
        add_filter(sprintf('acf/load_value/key=%s', self::AUTHOR_FIELD), function ($value) {
            return get_post()->post_author ?: wp_get_current_user()->ID;
        }, 10, 1);
        add_filter(sprintf('acf/update_value/key=%s', self::AUTHOR_FIELD), function ($newAuthor) {
            $post = get_post();
            $oldAuthor = $post->post_author;
            if (intval($newAuthor) !== intval($oldAuthor)) {
                $post->post_author = $newAuthor;
                wp_update_post($post);
            }
            return null;
        }, 10, 1);
        add_filter(sprintf('acf/validate_value/key=%s', self::SOURCE_CODE_FIELD), function ($valid, $value) {
            if ($valid && !empty($value) && strlen($value) !== 6) {
                $valid = 'Please make sure your source code is in the right format';
            }

            return $valid;
        }, 10, 4);
        add_filter(sprintf('acf/validate_value/name=%s', self::VIDEO_URL_FIELD_NAME), function ($valid, $value) {
            if( $valid !== true ) {
                return $valid;
            }
            if (isset($value) && !empty($value) && strpos($value, 'vimeo') === false) {
                $valid = 'Url must be a Vimeo url';
            }
            return $valid;
        }, 10, 4);
    }
}
