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
            'id'            => $image->getId(),
            'url'           => $this->getPath($image->getUrl()),
            'title'         => $image->getTitle(),
            'description'   => $image->getDescription(),
            'caption'       => $image->getCaption(),
            'alt'           => $image->getAlt(),
            'copyright'     => $image->getCopyright(),
            'language'      => $image->getLanguage(),
            'focalpoint'    => $image->getFocalPoint(),
            'aspectratio'   => $image->getAspectRatio(),
            'link'          => $this->transformLink($image),
            'color_palette' => $this->transformColorPalette($image),
        ];
    }

    private function transformLink(ImageContract $image)
    {
        if (($hyperlink = $image->getLink()) && !empty($hyperlink->getUrl())) {
            return with(new HyperlinkTransformer)->transform($hyperlink);
        }

        return null;
    }

    private function transformColorPalette(ImageContract $image)
    {
        if (($colorPalette = $image->getColorPalette()) && $colorPalette->getColors()) {
            return with(new ColorPaletteTransformer())->transform($colorPalette);
        }

        return null;
    }
}
