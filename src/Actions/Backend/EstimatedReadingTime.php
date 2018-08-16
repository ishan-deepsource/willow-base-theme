<?php

namespace Bonnier\Willow\Base\Actions\Backend;

use Bonnier\Willow\Base\Adapters\Wp\Composites\CompositeAdapter;


class EstimatedReadingTime
{
    protected $totalWordCount = 0;

    public function __construct()
    {
        add_filter('acf/save_post', [$this, 'addEstimatedReadingTime'], 20);
    }

    public function addEstimatedReadingTime($postId) {
        $wordsPerMinute = 180 / 60; // 180 words per 60 seconds
        $imageCounter = 0;
        $readingTime = 0;

        $compositeAdapter = new CompositeAdapter(get_post($postId));

        foreach ($compositeAdapter->getContents() as $item) {
            switch ($item->getType()) {
                case 'file':
                    break;
                case 'gallery':
                    $imageCounter = $imageCounter + $item->getImages()->count();
                    break;
                case 'image':
                    $imageCounter++;
                    break;
                case 'infobox':
                    $this->totalWordCount = $this->totalWordCount + str_word_count($item->getBody());
                    break;
                case 'inserted_code':
                    break;
                case 'link':
                    break;
                case 'text_item':
                    $this->totalWordCount = $this->totalWordCount + str_word_count($item->getBody());
                    break;
                case 'video':
                    break;
                default:
                    break;
            }
        }

        switch ($compositeAdapter->getLocale()) {
            case 'da':
                $wordsPerMinute = 180 / 60;
                break;
            case 'se':
                $wordsPerMinute = 180 / 60;
                break;
            case 'nb':
                $wordsPerMinute = 180 / 60;
                break;
            case 'fi':
                $wordsPerMinute = 150 / 60;
                break;
            case 'nl':
                $wordsPerMinute = 180 / 60;
                break;
            default:
                break;
        }

        $secondsForImages = $this->addTimeForImages($imageCounter);
        $readingTime = (int) round(($this->totalWordCount / $wordsPerMinute + $secondsForImages) / 60);

        update_post_meta($postId, 'reading_time', $readingTime ?? null);
    }

    public function addTimeForImages($amountOfImages)
    {
        $seconds = 0;
        $initialSecondsPerImage = 12;

        for($i=0; $i<$amountOfImages; $i++) {
            $seconds = $seconds + ($initialSecondsPerImage - $i);
        }
        return $seconds;
    }
}
