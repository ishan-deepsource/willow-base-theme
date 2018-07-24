<?php

namespace Bonnier\Willow\Base\Traits;

use DateTime;
use DateTimeZone;

trait DateTimeZoneTrait
{
    protected $timezone;

    protected function getTimezone(): DateTimeZone
    {
        if (!$this->timezone) {
            $this->timezone = new DateTimeZone(get_option('timezone_string') ?: 'Europe/Copenhagen');
        }
        return $this->timezone;
    }

    protected function toDateTime(string $timestring): DateTime
    {
        return new DateTime($timestring, $this->getTimezone());
    }
}
