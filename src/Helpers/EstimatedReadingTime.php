<?php

namespace Bonnier\Willow\Base\Helpers;

use Bonnier\Willow\MuPlugins\Helpers\LanguageProvider;

class EstimatedReadingTime
{
    private const EXTENDED_CHARLIST = 'âÂéÉèÈêÊøØóÓòÒôÔäÄåÅöÖæÆ:/.';

    public static function addEstimatedReadingTime($postId)
    {
        if (!is_int($postId)) {
            // A term was saved and ACF triggered this hook.
            return;
        }
        list($totalWordCount, $imageCounter) = static::getWordAndImageCount($postId);

        $locale = LanguageProvider::getPostLanguage($postId);

        $readingTimeInSecconds = static::readingTime($locale, $totalWordCount) +
            self::imageConsumationTime($imageCounter);

        $readingTimeInMinutes = ceil( // Round to nearest whole minute using ceil to avoid hitting 0
            $readingTimeInSecconds / 60
        );

        update_post_meta($postId, 'word_count', $totalWordCount);
        update_post_meta($postId, 'reading_time', $readingTimeInMinutes);
    }

    private static function imageConsumationTime($amountOfImages)
    {
        $defaultConsumptionTime = 12;
        if ($amountOfImages <= 10) {
            $seconds = $defaultConsumptionTime * $amountOfImages;
        } else {
            $seconds = ($defaultConsumptionTime * 10) + (($amountOfImages - 10) * 3);
        }
        return $seconds;
    }

    private static function getWordAndImageCount($postId)
    {
        $totalWordCount = 0;
        $imageCounter = 0;

        foreach (get_field('composite_content', $postId) ?: [] as $contentWidget) {
            switch (data_get($contentWidget, 'acf_fc_layout')) {
                case 'gallery':
                    $imageCounter += count(data_get($contentWidget, 'images', []));
                    break;
                case 'image':
                    $imageCounter++;
                    break;
                case 'text_item':
                case 'infobox':
                    $totalWordCount += str_word_count(data_get($contentWidget, 'body', ''), 0, self::EXTENDED_CHARLIST);
                    break;
                case 'lead_paragraph':
                    $totalWordCount += str_word_count(data_get($contentWidget, 'title', '') .
                        data_get($contentWidget, 'description', ''), 0, self::EXTENDED_CHARLIST);
                    break;
                case 'paragraph_list':
                    $totalWordCount += self::getParagraphListWordCount($contentWidget, $imageCounter);
                    break;
                case 'hotspot_image':
                    $totalWordCount += self::getHostspotImageWordCount($contentWidget, $imageCounter);
                    break;
                case 'inserted_code':
                case 'link':
                case 'video':
                case 'file':
                default:
                    break;
            }
        }

        return [$totalWordCount, $imageCounter];
    }

    private static function readingTime($locale, $wordCount)
    {
        $wordsPerMinute = 180;
        if ($locale === 'fi') {
            $wordsPerMinute = 150;
        }
        // calculcate number of minutes required to read number of words and convert to secconds
        return $wordCount / $wordsPerMinute * 60;
    }

    private static function getParagraphListWordCount($contentWidget, int &$imageCounter)
    {
        if (data_get($contentWidget, 'image')) {
            $imageCounter++;
        }
        $widgetWords = data_get($contentWidget, 'title', '') . data_get($contentWidget, 'description', '');

        $items = data_get($contentWidget, 'items', []) ?: [];

        $widgetWords .= array_reduce($items, function ($words, $paragraphItem) use (&$imageCounter) {
            $words .= data_get($paragraphItem, 'title', '') . data_get($paragraphItem, 'description', '');
            if (data_get($paragraphItem, 'image')) {
                $imageCounter++;
            }
            return $words;
        }, '');
        return str_word_count($widgetWords, 0, self::EXTENDED_CHARLIST);
    }

    private static function getHostspotImageWordCount($contentWidget, int &$imageCounter)
    {
        if (data_get($contentWidget, 'image')) {
            $imageCounter++;
        }
        $widgetWords = data_get($contentWidget, 'title', '') . data_get($contentWidget, 'description', '');
        $hotspotItems = data_get($contentWidget, 'hotspots', []) ?: [];
        $widgetWords .= array_reduce($hotspotItems, function ($words, $hotspotItem) use (&$imageCounter) {
            $words .= data_get($hotspotItem, 'title', '') . data_get($hotspotItem, 'description', '');
            return $words;
        }, '');
        return str_word_count($widgetWords, 0, self::EXTENDED_CHARLIST);
    }
}
