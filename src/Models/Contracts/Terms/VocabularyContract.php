<?php

namespace Bonnier\Willow\Base\Models\Contracts\Terms;

use Bonnier\Willow\Base\Models\Contracts\Root\BrandContract;
use Illuminate\Support\Collection;

/**
 * Class VocabularyContract
 *
 * @package \Bonnier\Willow\Base\Models\Contracts\Terms
 */
interface VocabularyContract
{
    public function getName(): ?string;

    public function getMachineName(): ?string;

    public function getContentHubId(): ?string;

    public function getMultiSelect(): ?string;

    public function getTerms(): ?Collection;
}
