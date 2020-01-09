<?php

namespace Bonnier\Willow\Base\Models\Contracts\Pages\Contents\Types;

use Bonnier\Willow\Base\Models\Contracts\Pages\Contents\ContentContract;

interface NewsletterContract extends ContentContract
{
    public function getSourceCodeCheckbox(): ?bool;
    public function getSourceCode(): ?int;
    public function getPermissionText(): ?string;
}
