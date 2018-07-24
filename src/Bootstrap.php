<?php

namespace Bonnier\Willow\Base;

use Bonnier\Willow\Base\Actions\ActionsBootstrap;
use Bonnier\Willow\Base\Commands\CommandBootstrap;
use Bonnier\Willow\Base\Controllers\App\AppControllerBootstrap;
use Bonnier\Willow\Base\Controllers\Formatters\ControllerBootstrap;

/**
 * Class Bootstrap
 *
 * @package \Bonnier\Willow\Base
 */
class Bootstrap
{

    /**
     * Boostrap constructor.
     */
    public function __construct()
    {
        new ControllerBootstrap();
        new CommandBootstrap();
        new ActionsBootstrap();
        new AppControllerBootstrap();
    }
}
