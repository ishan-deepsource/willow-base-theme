<?php

namespace Bonnier\Willow\Base\Models\Base\Terms;

use Bonnier\Willow\Base\Models\Contracts\Terms\VocabularyContract;
use Bonnier\Willow\Base\Models\Contracts\Root\BrandContract;
use Illuminate\Support\Collection;

/**
 * Class Vocabulary
 *
 * @package \Bonnier\Willow\Base\Models\Base\Terms
 */
class Vocabulary implements VocabularyContract
{
    protected $Vocabulary;

    public function __construct(VocabularyContract $Vocabulary)
    {
        $this->Vocabulary = $Vocabulary;
    }

    public function getName(): ?string
    {
        return $this->Vocabulary->getName();
    }

    public function getMachineName(): ?string
    {
        return $this->Vocabulary->getMachineName();
    }

    public function getContentHubId(): ?string
    {
        return $this->Vocabulary->getContentHubId();
    }

    public function getMultiSelect(): ?string
    {
        return $this->Vocabulary->getMultiSelect();
    }

    public function getBrand(): ?BrandContract
    {
        return $this->Vocabulary->getBrand();
    }

    public function getTerms(): ?Collection
    {
        return $this->Vocabulary->getTerms();
    }
}
