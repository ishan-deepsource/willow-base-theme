<?php

namespace Bonnier\Willow\Base\Exceptions\Database;

class UnknownDatabaseException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Unknown database error');
    }
}
