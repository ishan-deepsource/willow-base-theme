<?php

namespace Bonnier\Willow\Base\Tests\Unit\Commands\Helpers;

use Bonnier\Willow\Base\Commands\Helpers\ImportHelper;
use Bonnier\Willow\Base\Tests\Unit\ClassTestCase;

class ImportHelperTest extends ClassTestCase
{
    public function testFixFloatingTextsWithoutParagraphTag()
    {
        $text = "</h3>Alliér dig med en veninde, kollega eller din partner, så vedkommende kan hjælpe dig igennem fx <a href="" class="">ved at hoppe med vognen </a>, <br></br>give dig venlig konkurrence eller bare støtte dig og holde dig op på dit mål.&nbsp; <h3> sssskkkks </h3>ssssssss<p></p>";

        $fixedText = ImportHelper::fixFloatingTextsWithoutParagraphTag($text);

        $expectedText = "</h3><p>Alliér dig med en veninde, kollega eller din partner, så vedkommende kan hjælpe dig igennem fx <a href="" class="">ved at hoppe med vognen </a>, <br></br>give dig venlig konkurrence eller bare støtte dig og holde dig op på dit mål.&nbsp; </p><h3> sssskkkks </h3><p>ssssssss</p>";

        $this->assertSame($fixedText, $expectedText);
    }
}