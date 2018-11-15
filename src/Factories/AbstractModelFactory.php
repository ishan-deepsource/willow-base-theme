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
        $meta = $this->getMeta($model);
        $baseModel = new $this->baseClass($this->instantiateAdapter($adapter, $model, $meta));
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
        $theme = getenv('APP_CHILD_THEME') ?? '';
        $overrideClass = str_replace('Models\\Base', 'Models\\' . $theme, $this->baseClass);
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
     * @param $meta
     *
     * @return mixed
     *
     * @throws MissingAdapterException
     */
    protected function instantiateAdapter($adapter, $model, $meta)
    {
        if (!$adapter || !class_exists($adapter)) {
            throw new MissingAdapterException(sprintf('The adapter class \'%s\' does not exist', $adapter));
        }
        return new $adapter($model, $meta);
    }

    private function getMeta($model)
    {
        if ($model instanceof \WP_Post) {
            $this->meta = get_post_meta($model->ID);
        } elseif ($model instanceof \WP_Term) {
            $this->meta = get_term_meta($model->term_id);
        }
    }
}
