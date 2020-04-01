<?php

namespace Bonnier\Willow\Base\Transformers\Api\Root;

use DateTime;
use League\Fractal\TransformerAbstract;

class DateTimeTransformer extends TransformerAbstract
{
    public function transform(DateTime $publishedAt)
    {
        return [
            'date' => $publishedAt->format(DATE_ATOM),
            'timezone_type' => 3,
            'timezone' => $publishedAt->getTimezone()->getName(),
        ];
    }
}
