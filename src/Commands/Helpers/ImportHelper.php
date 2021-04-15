<?php

namespace Bonnier\Willow\Base\Commands\Helpers;

class ImportHelper
{
    public static function removeInsertCodeEmptyLines($rawInsertCode)
    {
        // replace empty lines
        $patterns = ['#<p>(&nbsp;)*</p>#', '#<h2></h2>#', '#<p class="">(<br>)*</p>#'];
        return preg_replace($patterns, "", $rawInsertCode);
    }

    public static function insertCodeWrappingTableClass($rawInsertCode)
    {
        //replace <table…</table> with <div class=“table-container”><table…</table><div>
        return preg_replace('#<table(.*)</table>#s', '<div class=“table-container”><table${1}</table></div>', $rawInsertCode);
    }
}
