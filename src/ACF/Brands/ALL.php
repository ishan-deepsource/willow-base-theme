<?php

namespace Bonnier\Willow\Base\ACF\Brands;

class ALL extends Brand
{
    public static function register(): void
    {
        self::init();
        self::removeOtherAuthors();
        self::removeVideoUrlFromImageWidget();
        self::removeVideoUrlFromGalleryItems();
        self::removeVideoUrlFromParagraphListWidget();
        self::removeVideoUrlFromTeaserImages();
        self::removeImageFromInfoboxWidget();
        self::removeChapterItemsFromVideoWidget();
        self::removeIncludeIntroVideoFromVideoWidget();
        self::removeThemeFromTeaserListPageWidget();
        self::removeSortByEditorialTypeFromTeaserListPageWidget();
        self::removeAdvancedCustomSortByFieldsFromTeaserListPageWidget();
        self::removeTitleFromAssociatedCompositesWidget();
        self::removeDisplayHintFromAssociatedCompositesWidget();
        self::removeUseAsArticleLeadImageFromRecipeWidget();

        self::removeInventoryWidget();
        self::removeAudioWidget();
        self::removeMultimediaWidget();
        self::removeProductWidget();
        self::removeCalculatorWidget();

        self::removeQuotePageWidget();
        self::removeFeaturedContentPageWidget();

        self::removeTitleFromUserFieldGroup();
        self::removeLanguageTitlesFromUserFieldGroup();
        self::removeRecipeWidget();
    }
}
