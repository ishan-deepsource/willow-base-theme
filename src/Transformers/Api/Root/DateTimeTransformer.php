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
            'timezone_type' => $this->getTimezoneType($publishedAt),
            'timezone' => $publishedAt->getTimezone()->getName(),
        ];
    }

    protected function getTimezoneType(DateTime $date)
    {
        $json = json_encode($date);
        $object = json_decode($json);
        return $object->timezone_type;
    }
}
