<?php

namespace Bonnier\Willow\Base\Repositories;

interface TranslationRepositoryContract
{
    public function getTranslations(string $locale = null) : ?array ;
}
