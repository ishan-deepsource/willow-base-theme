<?php

namespace Bonnier\Willow\Base\Transformers\Api\Composites\Includes\Contents\Types;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\ChaptersSummaryContract;
use League\Fractal\TransformerAbstract;

/**
 * Class ChaptersSummaryTransformer
 *
 * @package \Bonnier\Willow\Base\Transformers\Api\Composites\Partials
 */
class ChaptersSummaryTransformer extends TransformerAbstract
{
    public function transform(ChaptersSummaryContract $chaptersSummary)
    {
        return [];
    }
}
