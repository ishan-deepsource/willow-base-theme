<?php

namespace Bonnier\Willow\Base\Actions\Backend;

use Bonnier\Willow\Base\Adapters\Wp\Composites\CompositeAdapter;


class EstimatedReadingTime
{
    protected $total_word_count = 0;

    public function __construct()
    {
        add_filter('acf/save_post', [$this, 'addEstimatedReadingTime'], 20);
    }

    public function addEstimatedReadingTime($post_id) {
        $words_per_minute = 180 / 60; // 180 words per 60 seconds
        $image_counter = 0;
        $reading_time = 0;

        $ca = new CompositeAdapter(get_post($post_id));

        foreach ($ca->getContents() as $item) {
            switch ($item->getType()) {
                case 'file':
                    break;
                case 'gallery':
                    $image_counter = $image_counter + $item->getImages()->count();
                    break;
                case 'image':
                    $image_counter++;
                    break;
                case 'infobox':
                    $this->total_word_count = $this->total_word_count + str_word_count($item->getBody());
                    break;
                case 'inserted_code':
                    break;
                case 'link':
                    break;
                case 'text_item':
                    $this->total_word_count = $this->total_word_count + str_word_count($item->getBody());
                    break;
                case 'video':
                    break;
            }
        }

        switch ($ca->getLocale()) {
            case 'da':
                $words_per_minute = 180 / 60;
                break;
            case 'se':
                $words_per_minute = 180 / 60;
                break;
            case 'nb':
                $words_per_minute = 180 / 60;
                break;
            case 'fi':
                $words_per_minute = 150 / 60;
                break;
            case 'nl':
                $words_per_minute = 180 / 60;
                break;
            default:
        }

        $seconds_for_images = $this->addTimeForImages($image_counter);
        $reading_time = (int) round(($this->total_word_count / $words_per_minute + $seconds_for_images) / 60);

        update_post_meta($post_id, 'reading_time', $reading_time ?? null);
    }

    public function addTimeForImages($amount_of_images) {
        $seconds = 0;
        $initial_seconds_per_image = 12;

        for($i=0;$i<$amount_of_images;$i++) {
            $seconds = $seconds + ($initial_seconds_per_image - $i);
        }
        return $seconds;
    }
}
