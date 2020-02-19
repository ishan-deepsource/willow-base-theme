<?php

namespace Bonnier\Willow\Base\Models\Base\Composites\Contents\Types;

use Bonnier\Willow\Base\Models\Base\Composites\Contents\AbstractContent;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\ChaptersSummaryContract;

/**
 * Class Link
 *
 * @property ChaptersSummaryContract $model
 *
 * @package Bonnier\Willow\Base\Models\Base\Composites\Contents\Types
 */
class ChaptersSummary extends AbstractContent implements ChaptersSummaryContract
{
    /**
     * Link constructor.
     *
     * @param \Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\ChaptersSummaryContract $chaptersSummary
     */
    public function __construct(ChaptersSummaryContract $chaptersSummary)
    {
        parent::__construct($chaptersSummary);
    }

    public function getStickToNext(): bool
    {
        return $this->model->getStickToNext();
    }
}
