<?php

namespace Bonnier\Willow\Base\Transformers\Api\Root;

use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;
use Bonnier\Willow\Base\Traits\UrlTrait;
use League\Fractal\TransformerAbstract;

class ImageTransformer extends TransformerAbstract
{
    use UrlTrait;

    public function transform(ImageContract $image)
    {
        return [
            'id' => $image->getId(),
            'url' => $this->getPath($image->getUrl()),
            'title' => $image->getTitle(),
            'description' => $image->getDescription(),
            'caption' => $image->getCaption(),
            'alt' => $image->getAlt(),
            'copyright' => $image->getCopyright(),
            'language' => $image->getLanguage(),
            'focalpoint' => $image->getFocalPoint(),
            'aspectratio' => $image->getAspectRatio(),
            'link' => $this->transformLink($image),
            'imgix_palette' => $this->transformImgixPalette($image),
        ];
    }

    private function transformLink(ImageContract $image)
    {
        if (($hyperlink = $image->getLink()) && !empty($hyperlink->getUrl())) {
            return with(new HyperlinkTransformer)->transform($hyperlink);
        }

        return null;
    }

    private function transformImgixPalette($image)
    {
        $meta = wp_get_attachment_metadata($image->getId());

        if (!isset($meta['imgix_palette'])) {
            return '';
        }

        $data = json_decode($meta['imgix_palette']);

        // Only output the hex values of the colors
        $data->colors = collect($data->colors)->pluck('hex');
        $data->dominant_colors = collect($data->dominant_colors)->map(function ($var) {
            return $var->hex;
        });

        return $data;
    }
}
