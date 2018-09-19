<?php

namespace Bonnier\Willow\Base\Actions\Backend;

use Bonnier\Willow\Base\Adapters\Wp\Composites\CompositeAdapter;


class EstimatedListeningTIme
{
    public function __construct()
    {
        add_filter('acf/save_post', [$this, 'addEstimatedListeningTime'], 20);
    }

    public function addEstimatedListeningTime($postId)
    {
        $duration = 0;

        $compositeAdapter = new CompositeAdapter(get_post($postId));

        foreach ($compositeAdapter->getContents() as $item) {
            if ($item->getType() == 'audio') {
                $duration = $duration + wp_get_attachment_metadata($item->getId())['length'];
            }
        }

        $listeningTime = $this->formatEstimatedListeningTime($duration);

        if ($listeningTime < 1) {
            $listeningTime = 1;
        }

        update_post_meta($postId, 'listening_time', $listeningTime ?? null);
    }

    protected function formatEstimatedListeningTime($duration) {

        return ceil($duration / 60); // Format as minutes and rounding up.
//        return sprintf("%d:%02d", ($duration /60), $duration %60 ); // Optional format as time string.
    }
}
