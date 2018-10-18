<?php

namespace Bonnier\Willow\Base\Actions\Backend;

use Bonnier\Willow\Base\Adapters\Wp\Composites\CompositeAdapter;


class EstimatedReadingTime
{
    protected $charList = 'âÂéÉèÈêÊøØóÓòÒôÔäÄåÅöÖæÆ:/.';

    public function __construct()
    {
        add_filter('acf/save_post', [$this, 'addEstimatedReadingTime'], 20);
        add_filter('acf/save_post', [$this, 'countNumberOfWords'], 20);
    }

    public function countNumberOfWords($postId)
    {
        $compositeAdapter = new CompositeAdapter(get_post($postId));
        $totalWordCount = 0;

        foreach ($compositeAdapter->getContents() as $item) {
            switch ($item->getType()) {
                case 'infobox':
                case 'text_item':
                    $totalWordCount = $totalWordCount + str_word_count($item->getBody(), 0, $this->charList);
                    break;
                default:
                    break;
            }
        }

        update_post_meta($postId, 'word_count', $totalWordCount);
    }

    public function addEstimatedReadingTime($postId)
    {
        $wordsPerMinute = 180; // 180 words per 60 seconds
        $totalWordCount = 0;
        $imageCounter = 0;
        $readingTime = 0;

        $compositeAdapter = new CompositeAdapter(get_post($postId));

        foreach ($compositeAdapter->getContents() as $item) {
            switch ($item->getType()) {
                case 'gallery':
                    $imageCounter = $imageCounter + $item->getImages()->count();
                    break;
                case 'image':
                    $imageCounter++;
                    break;
                case 'text_item':
                case 'infobox':
                    $totalWordCount = $totalWordCount + str_word_count($item->getBody(), 0, $this->charList);
                    break;
                case 'inserted_code':
                case 'link':
                case 'video':
                case 'file':
                default:
                    break;
            }
        }

        switch ($compositeAdapter->getLocale()) {
            case 'da':
                $wordsPerMinute = 180;
                break;
            case 'se':
                $wordsPerMinute = 180;
                break;
            case 'nb':
                $wordsPerMinute = 180;
                break;
            case 'fi':
                $wordsPerMinute = 150;
                break;
            case 'nl':
                $wordsPerMinute = 180;
                break;
            default:
                $wordsPerMinute = 180;
                break;
        }

        $secondsForImages = $this->addTimeForImages($imageCounter);

        $readingTime = round((($totalWordCount / $wordsPerMinute * 60) + $secondsForImages) / 60);

        if ($readingTime < 1) {
            $readingTime = 1;
        }

        update_post_meta($postId, 'reading_time', $readingTime ?? null);
    }

    public function addTimeForImages($amountOfImages)
    {
        $seconds = 0;
        $initialSecondsPerImage = 12;

        for ($i = 0; $i < $amountOfImages; $i++) {
            if ($i < 10) {
                $seconds = $seconds + ($initialSecondsPerImage - $i);
            } else {
                $seconds = $seconds + 3; // After 10 images the average time pr images is estimated to 3 sec. (According to research made by Medium).
            }

        }
        return $seconds;
    }
}
