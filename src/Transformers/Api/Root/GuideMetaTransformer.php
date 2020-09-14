<?php

namespace Bonnier\Willow\Base\Transformers\Api\Root;

use Bonnier\Willow\Base\Models\Base\Root\GuideMeta;
use Bonnier\Willow\Base\Models\Contracts\Root\GuideMetaContract;
use League\Fractal\TransformerAbstract;

class GuideMetaTransformer extends TransformerAbstract
{
    /**
     * @param GuideMeta $guideMeta
     *
     * @return array
     */
    public function transform(GuideMetaContract $guideMeta)
    {
        return [
            'difficulty' => $guideMeta->getDifficulty(),
            'time_required' => $guideMeta->getTimeRequired(),
            'price' => $guideMeta->getPrice()
        ];
    }
}
