<?php

namespace Bonnier\Willow\Base\Transformers\Api\Composites;

use Bonnier\Willow\Base\Helpers\RecipeHelper;
use Bonnier\Willow\Base\Models\Contracts\Composites\CompositeContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;
use Bonnier\Willow\Base\Traits\UrlTrait;
use Bonnier\Willow\Base\Transformers\Api\Root\CommercialTransformer;
use Bonnier\Willow\Base\Transformers\Api\Root\ImageTransformer;
use Bonnier\Willow\Base\Transformers\Api\Terms\Vocabulary\VocabularyTransformer;
use League\Fractal\TransformerAbstract;

class CompositeTeaserTransformer extends TransformerAbstract
{
    use UrlTrait;

    protected $originalResponseData;

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'vocabularies',
    ];

    /**
     * CategoryTransformer constructor.
     *
     * @param $originalResponseData
     */
    public function __construct(array $originalResponseData = [])
    {
        $this->originalResponseData = $originalResponseData;
    }

    public function transform(CompositeContract $composite)
    {
        $out = [
            'id'            => $composite->getId(),
            'title'         => $this->getTitle($composite),
            'kind'          => $composite->getKind(),
            'status'        => $composite->getStatus(),
            'image'         => $this->getImage($composite),
            'video_url'     => $this->getVideoUrl($composite),
            'description'   => $this->getDescription($composite),
            'link'          => $this->getPath($composite->getLink()),
            'published_at'  => $composite->getPublishedAt(),
            'commercial'    => $this->getCommercial($composite),
            'label'         => [
                'title' => $composite->getLabel(),
                'url'   => $this->getPath($composite->getLabelLink()),
            ],
            'estimated_reading_time'  => $composite->getEstimatedReadingTime(),
            'word_count'              => $composite->getWordCount(),
            'contenthub_id'           => $composite->getContenthubId(),
        ];

        (new RecipeHelper())->addRecipeMetaToOutput($composite, $out);

        return $out;
    }

    public function includeVocabularies(CompositeContract $composite)
    {
        return $this->collection($composite->getVocabularies(), new VocabularyTransformer());
    }

    private function getTitle(CompositeContract $composite)
    {
        return $composite->getTeaser('default')->getTitle();
    }

    private function getImage(CompositeContract $composite)
    {
        return $this->transformTeaserImage($composite->getTeaser('default')->getImage());
    }

    private function getVideoUrl(CompositeContract $composite)
    {
        return $composite->getTeaser('default')->getVideoUrl();
    }

    private function getDescription(CompositeContract $composite)
    {
        return $composite->getTeaser('default')->getDescription();
    }

    private function transformTeaserImage(?ImageContract $image)
    {
        if (!$image) {
            return null;
        }
        return with(new ImageTransformer())->transform($image);
    }

    private function getCommercial(CompositeContract $composite)
    {
        $commercial = $composite->getCommercial();
        return $commercial ? with(new CommercialTransformer())->transform($commercial) : null;
    }
}
