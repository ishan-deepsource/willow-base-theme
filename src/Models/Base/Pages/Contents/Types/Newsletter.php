<?php

namespace Bonnier\Willow\Base\Models\Base\Pages\Contents\Types;

use Bonnier\Willow\Base\Models\Base\Pages\Contents\AbstractContent;
use Bonnier\Willow\Base\Models\Contracts\Pages\Contents\Types\NewsletterContract;

/**
 * Class TeaserList
 * @package Bonnier\Willow\Base\Models\Base\Pages\Contents\Types
 * @property Newsletter $model
 */
class Newsletter extends AbstractContent implements NewsletterContract
{
    public function getManualSourceCodeEnabled(): ?bool
    {
        return $this->model->getManualSourceCodeEnabled();
    }

    public function getSourceCode(): ?int
    {
        return $this->model->getSourceCode();
    }

    public function getPermissionText(): ?string
    {
        return $this->model->getPermissionText();
    }
}
