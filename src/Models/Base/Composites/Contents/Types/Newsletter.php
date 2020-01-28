<?php

namespace Bonnier\Willow\Base\Models\Base\Composites\Contents\Types;

use Bonnier\Willow\Base\Models\Base\Composites\Contents\AbstractContent;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\NewsletterContract;

/**
 * Class Link
 *
 * @property NewsletterContract $model
 *
 * @package Bonnier\Willow\Base\Models\Base\Composites\Contents\Types
 */
class Newsletter extends AbstractContent implements NewsletterContract
{
    /**
     * Link constructor.
     *
     * @param \Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\NewsletterContract $newsletter
     */
    public function __construct(NewsletterContract $newsletter)
    {
        parent::__construct($newsletter);
    }

    public function getTitle(): ?string
    {
        return $this->model->getTitle();
    }

    public function getDescription(): ?string
    {
        return $this->model->getDescription();
    }

    public function getSourceCode(): ?int
    {
        return $this->model->getSourceCode();
    }

    public function getPermissionText(): ?string
    {
        return $this->model->getPermissionText();
    }

    public function getStickToNext(): bool
    {
        return $this->model->getStickToNext();
    }
}
