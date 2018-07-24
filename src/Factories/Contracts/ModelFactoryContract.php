<?php

namespace Bonnier\Willow\Base\Factories\Contracts;

interface ModelFactoryContract
{
    public function getModel($model);
    
    public function getAdapter($model);
}
