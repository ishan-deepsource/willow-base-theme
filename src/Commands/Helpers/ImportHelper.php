<?php

namespace Bonnier\Willow\Base\Commands\Helpers;

class ImportHelper
{
    public static function removeInsertCodeEmptyLines($rawInsertCode)
    {
        // replace empty lines
        $patterns = [
            '#<p>(&nbsp;)*</p>#',
            '#<h2></h2>#',
            '#<p class="">(<br>)*</p>#',
            '#<[h1|h2|h3|h4|p]>(<br>)*</[h1|h2|h3|h4|p]>#',
        ];
        return preg_replace($patterns, "", $rawInsertCode);
    }

    public static function insertCodeWrappingTableClass($rawInsertCode)
    {
        //replace <table…</table> with <div class=“table-container”><table…</table><div>
        return preg_replace(
            '#<table(.*)</table>#s',
            '<div class=“table-container”><table${1}</table></div>',
            $rawInsertCode);
    }

    public static function fixFloatingTextsWithoutParagraphTag($text)
    {
        //find text without any tag around and wrap it in a <p> tag.
        $text = preg_replace(
            '#(</[h2|h3|h4]>)([^<]*)(<p>)#m',
            '$1<p>$2</p>$3',
            $text);

        return preg_replace('#<p></p>#m', '', $text);
    }
}
