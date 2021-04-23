<?php

namespace Bonnier\Willow\Base\Commands\Helpers;

class ImportHelper
{
    public static function removeEmptyLines($rawInsertCode)
    {
        // replace empty lines
        $patterns = [
            '#<p\s*(class="")?\s*>(\s|<br>|&nbsp;)*</p>#',
            '#<h([1-6])>(<br>)*</h\1>#',
            '#<h([1-6])><span.*></span></h\1>#',
            '#<h([1-6])><strong.*></strong></h\1>#',
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
            '#(</(h2|h3|h4)>)(.*)(<(h2|h3|h4|p)>)#mU',
            '$1<p>$3</p>$4',
            $text);

        return preg_replace('#<p></p>#m', '', $text);
    }
}
