<?php

namespace Bonnier\Willow\Base\Transformers\Api\Root;

use Bonnier\Willow\Base\Models\Contracts\Root\CommercialContract;
use League\Fractal\TransformerAbstract;

class CommercialTransformer extends TransformerAbstract
{
    public function transform(CommercialContract $commercial)
    {
        return [
            'type' => $commercial->getType(),
            'label' => $commercial->getLabel(),
            'logo' => $this->getLogo($commercial),
        ];
    }

    protected function getLogo(CommercialContract $commercial)
    {
        if ($logo = $commercial->getLogo()) {
            return with(new ImageTransformer())->transform($logo);
        }

        return null;
    }
}
