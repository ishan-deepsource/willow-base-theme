<?php

namespace Bonnier\Willow\Base\Factories;

use Bonnier\Willow\Base\Exceptions\Controllers\Api\MissingAdapterException;
use Bonnier\Willow\Base\Exceptions\Controllers\Api\OverrideModelMissingContractException;
use Bonnier\Willow\Base\Factories\Contracts\ModelFactoryContract;

abstract class AbstractModelFactory implements ModelFactoryContract
{
    protected $baseClass;
    protected $adapterMapping;

    /**
     * ModelFactory constructor.
     *
     * @param $baseClass
     */
    public function __construct($baseClass)
    {
        $this->baseClass = $baseClass;
    }

    /**
     * @param mixed $baseClass
     *
     * @return ModelFactoryContract
     */
    public function setBaseClass($baseClass): ModelFactoryContract
    {
        $this->baseClass = $baseClass;
        return $this;
    }

    /**
     * @param $model
     *
     * @return mixed
     * @throws OverrideModelMissingContractException
     * @throws MissingAdapterException
     */
    public function getModel($model)
    {
        $adapter = $this->getAdapter($model);
        $baseModel = new $this->baseClass($this->instantiateAdapter($adapter, $model));
        if ($overrideClass = $this->getOverrideClass()) {
            return new $overrideClass($baseModel);
        }
        return $baseModel;
    }

    /**
     * @return mixed|null
     * @throws OverrideModelMissingContractException
     */
    protected function getOverrideClass()
    {
        $overrideClass = str_replace('Models\\Base', 'Models\\' . $this->getBrandTheme(), $this->baseClass);
        if ($this->validOverride($overrideClass)) {
            return $overrideClass;
        }
        return null;
    }

    /**
     * @param $overrideClass
     *
     * @return bool
     * @throws OverrideModelMissingContractException
     */
    protected function validOverride($overrideClass)
    {
        if (! class_exists($overrideClass)) {
            return false;
        }
        // Make sure that override class implements the interface from base class
        if (! collect(class_implements($this->baseClass))->diff(class_implements($overrideClass))->isEmpty()) {
            throw new OverrideModelMissingContractException(
                sprintf(
                    'Override Model: %s must implement: %s from baseModel: %s',
                    $overrideClass,
                    array_values(class_implements($this->baseClass))[0],
                    $this->baseClass
                )
            );
        }
        return true;
    }

    /**
     * @param $adapter
     * @param $model
     *
     * @return mixed
     *
     * @throws MissingAdapterException
     */
    protected function instantiateAdapter($adapter, $model)
    {
        if (!$adapter || !class_exists($adapter)) {
            throw new MissingAdapterException(sprintf('The adapter class \'%s\' does not exist', $adapter));
        }
        return new $adapter($model);
    }

    /**
     * @return string
     */
    protected function getBrandTheme() {
        $theme = getenv('APP_CHILD_THEME') ?? '';
        return ucfirst(strtolower($theme));
    }
}
