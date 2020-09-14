<?php

namespace Bonnier\Willow\Base\Commands\GDS;

use SimpleXMLElement;
use WP_CLI;

class XmlParser
{
    private static $countryLanguages = [
        'DK' => 'da',
        'SE' => 'sv',
        'NO' => 'nb',
        'FI' => 'fi',
        'NL' => 'nl',
    ];

    private $dir;
    private $postId;
    private $country;
    private $type;
    private $issue;
    private $year;

    private $blocks;
    private $time;
    private $price;
    private $materials;
    //private $authors;
    private $firstAuthor;

    //private $leadImageSet;
    private $success;

    public function __construct($basedir, $dir, $encodeUtf8)
    {
        $this->success = false;

        if (preg_match("/^(\d+)_(\w{2})_(\w)_(\d{1,2})_(\d{4})$/", $dir, $res)) {
            $this->dir = $basedir . '/' . $dir;
            $this->postId = $res[1];
            $this->country = $res[2];
            $this->type = $res[3];
            $this->issue = $res[4];
            $this->year = $res[5];

            /*
            if ($this->country === 'DK') {
                setlocale(LC_ALL, 'da_DK');
            }
            */

            $this->firstAuthor = null;

            //$this->leadImageSet = false;

            $this->parseDir();
            $this->success = true;
        }
        else {
            WP_CLI::error('Illegal name format for directory: ' . $dir);
        }
    }

    public function parseDir()
    {
        WP_CLI::line('Parsing dir: ' . $this->dir);

        $files = scandir($this->dir . '/');
        $markUpfiles = array_values(preg_grep("/\.(html|xml)$/", $files));
        if (sizeof($markUpfiles) !==1 ) {
            WP_CLI::error('There should be exactly 1 markup-file in each dir.');
        }
        $markupFile = $markUpfiles[0];
        WP_CLI::line('Parsing file: ' . $this->dir . '/' . $markupFile);
        $this->parseFile($this->dir . '/' . $markupFile);
    }

    private function fixInvalidXml($xml)
    {
        // Fix invalid XML
        $xml = preg_replace("/itemscope /", "itemscope=\"\" ", $xml);
        $xml = preg_replace("/(<img.*?[^\/])>/", "$1 />", $xml);

        // ->xpath does not work if <html> has xmlns set
        $xml = preg_replace("#<html xmlns=\"http://www.w3.org/1999/xhtml\">#", "<html>", $xml);

        return $xml;
    }

    private function parseHeadline($xml)
    {
        if ($headline = $xml->xpath("//h1[@itemprop='headline']")) {
            return strval($headline[0]);
        }
        return null;
    }

    private function parseDescription($xml)
    {
        if ($description = $xml->xpath("//p[@itemprop='description']")) {
            return self::getText($description[0]);
        }
        return null;
    }

    private function parseAuthor($xml)
    {
        if ($author = $xml->xpath("//p[@itemprop='author']")) {
            return self::getText($author[0]);
        }
    }

    private function setFirstAuthor($xml)
    {
        if ($author = $xml->xpath("//p[@itemprop='author']")) {
            $this->firstAuthor = self::getText($author[0]->span[0]);
        }
    }

    public function getFirstAuthor()
    {
        return $this->firstAuthor;
    }

    private function parseFile($filename)
    {
        $data = file_get_contents($filename);

        //$data = utf8_encode($data);

        $data = $this->fixInvalidXml($data);

        $xml = new SimpleXMLElement($data);

        $blocks = [];

        if ($headLine = $this->parseHeadline($xml)) {
            //WP_CLI::line('Title: ' . $headLine);
            $blocks[] = ['type' => 'title', 'content' => $headLine];
        }

        if ($description = $this->parseDescription($xml)) {
            $blocks[] = ['type' => 'description', 'content' => $description];
        }

        if ($author = $this->parseAuthor($xml)) {
            //$this->authors = $author;
            $blocks[] = ['type' => 'author', 'content' => $author];
        }

        $this->setFirstAuthor($xml);

        $body = $xml->xpath("//div[@itemprop='articleBody']")[0];
        $blocks = array_merge($blocks, $this->processNodes($body));
        $blocks = self::prepareBlocks($blocks);
        $this->blocks = $blocks;

        $this->parseMeta($blocks);
    }

    private static function prepareBlocks($blocks)
    {
        $blocks = self::mergeText($blocks); // Merge h2 and text elements in the root
        $blocks = self::prepareSecondLevelText($blocks); // Merge h2 and text elements in the next level
        return $blocks;
    }

    private static function prepareSecondLevelText($blocks)
    {
        $i = 0;
        while ($i < sizeof($blocks)) {
            if (in_array($blocks[$i]['type'], ['how-to', 'boxout'])) {
                $blocks[$i]['content'] = self::mergeText($blocks[$i]['content']);
            }
            $i++;
        }
        return $blocks;
    }

    private static function mergeText($blocks)
    {
        $i = 0;
        while ($i < sizeof($blocks) - 1) {
            $changesMade = false;

            // Change h2 to text and merge with following text
            if (!$changesMade && $blocks[$i]['type'] === 'h2') {
                $blocks[$i]['type'] = 'text'; // Change type to text
                $blocks[$i]['content'] = '<h2>' . $blocks[$i]['content'] . '</h2>'; // Add h2 around content
                if ($blocks[$i + 1]['type'] === 'text') {
                    $blocks[$i]['content'] .= "<p>" . $blocks[$i + 1]['content'] . "</p>";  // TODO
                    array_splice($blocks, $i + 1, 1);
                    $changesMade = true;
                }
            }
            // Merge text with following text
            if (!$changesMade && $blocks[$i]['type'] === 'text') {
                if ($blocks[$i + 1]['type'] === 'text') {
                    $blocks[$i]['content'] .= "<p>" . $blocks[$i + 1]['content'] . "</p>";  // TODO
                    array_splice($blocks, $i + 1, 1);
                    $changesMade = true;
                }
            }
            if (!$changesMade) {
                $i++;
            }
        }
        return $blocks;
    }

    private function getText($ele)
    {
        return trim(strip_tags($ele->asXml(), '<b><i><br>'));
    }

    private function processNodes($nodes)
    {
        $blocks = [];
        foreach ($nodes->children() as $child) {
            if ($data = $this->processNode($child)) {
                $blocks[] = $data;
            }
        }
        return $blocks;
    }

    private function processNode($ele)
    {
        $type = $ele->getName();
        if ($type === 'div' && $ele->attributes()->class == 'article-boxout') {
            return $this->processBoxout($ele);
        }
        else if ($type === 'div' && $ele->attributes()->itemtype == 'http://schema.org/HowTo') {
            return $this->processHowTo($ele);
        }
        else if ($type === 'div' && $ele->attributes()->itemtype == 'http://schema.org/Offer') {
            return $this->processOffer($ele);
        }
        else if ($type == 'div' && $ele->attributes()->itemtype == 'http://schema.org/HowToSection') {
            return self::processHowToSection($ele);
        }
        else if ($type == 'div' && $ele->attributes()->itemtype == 'http://schema.org/HowToStep') {
            return $this->processHowToStep($ele);
        }
        else if ($type == 'div' && $ele->attributes()->itemtype == 'http://schema.org/HowToDirection') {
            return $this->processHowToDirection($ele);
        }
        else if ($type === 'figure') {
            return $this->processFigure($ele);
        }
        else if ($type === 'h2') {
            return $this->processH2($ele);
        }
        else if ($type === 'h3') {
            return $this->processH3($ele);
        }
        else if ($type === 'img') {
            return $this->processImg($ele);
        }
        else if ($type === 'link') {
            return $this->processLink($ele);
        }
        else if ($type === 'meta') {
            return $this->processMeta($ele);
        }
        else if ($type === 'p' && $ele->attributes()->itemprop == 'text') {
            return $this->processP($ele);
        }
        else if ($type === 'p' && $ele->attributes()->itemprop == 'timeRequired') {
            return $this->processP($ele);
        } else if ($type === 'ul') {
            return $this->processUl($ele);
        } else {
            WP_CLI::error('Type not handled processNode: ' . $type);
        }
    }

    private function processBoxout($data)
    {
        //WP_CLI::line("\n*** Process Boxout");

        $blocks = $this->processNodes($data);

        $difficulty = false;
        foreach ($blocks as $block) {
            if ($block['type'] === 'difficulty') {
                $difficulty = true;
            }
        }

        if ($difficulty) {
            return ['type' => 'metabox', 'content' => $blocks];
        }
        return ['type' => 'boxout', 'content' => $blocks];
    }

    private function processFigure($data)
    {
        //WP_CLI::line("\n*** Process Figure");

        $block = ['type' => 'image'];

        if ($data->img) {
            if (isset($data->img->attributes()->alt)) {
                $block['alt'] = strval($data->img->attributes()->alt);
            }
            if (isset($data->img->attributes()->src)) {
                $block['src'] = strval($data->img->attributes()->src);
                // Use file that ends with a.jpg instead of b.jpg if possible
                if (preg_match('/^(.+)b\.jpg$/', $block['src'], $res)) {
                    $baseFileName = $res[1];
                    if (file_exists($this->dir . "/" . $baseFileName . "a.jpg")) {
                        $block['src'] = $baseFileName . "a.jpg";
                    }
                }
            }

            //WP_CLI::line('Src: ' . $block['src']);

            if (!file_exists($this->dir . "/" . $block['src'])) {
                WP_CLI::error('ERROR: image not found : ' . $this->dir . '/' . $block['src']);
                exit;
            }
        }

        if (isset($data->figcaption)) {
            $block['figcaption'] = $this->getText($data->figcaption);
            //WP_CLI::line('Fig Caption: ' . $block['figcaption']);
        }
        return $block;
    }

    private function processImg($data)
    {
        //WP_CLI::line("\n*** Process Img");

        $block = ['type' => 'image'];

        if (isset($data->attributes()->alt)) {
            $block['alt'] = strval($data->attributes()->alt);
        }
        if (isset($data->attributes()->src)) {
            $block['src'] = strval($data->attributes()->src);
            // Use file that ends with a.jpg instead of b.jpg if possible
            if (preg_match('/^(.+)b\.jpg$/', $block['src'], $res)) {
                $baseFileName = $res[1];
                if (file_exists($this->dir . "/" . $baseFileName . "a.jpg")) {
                    $block['src'] = $baseFileName . "a.jpg";
                }
            }
        }

        //WP_CLI::line('Src: ' . $block['src']);

        if (!file_exists($this->dir . "/" . $block['src'])) {
            WP_CLI::error('Image not found : ' . $this->dir . '/' . $block['src']);
            exit;
        }

        return $block;
    }

    private function processH2($data)
    {
        //WP_CLI::line("\n*** Process H2");
        //WP_CLI::line(ucfirst(self::toLower($data)));
        return ['type' => 'h2', 'content' => ucfirst(self::toLower((strval($data))))];
    }

    private function processH3($data)
    {
        if (in_array(strval($data), ['SVÆRHEDSGRAD', 'VAIKEUSASTE', 'VANSKELIGHETSGRAD', 'SVÅRIGHETSGRAD'])) {
            //WP_CLI::line("\n*** Process H3 - DIFFICULTY");
            //WP_CLI::line(ucfirst(self::toLower($data)));
            return ['type' => 'difficulty', 'content' => ucfirst(self::toLower((strval($data))))];
        }

        //WP_CLI::line("\n*** Process H3");
        //WP_CLI::line(ucfirst(self::toLower($data)));
        return ['type' => 'h3', 'content' => ucfirst(self::toLower((strval($data))))];
    }

    private function processHowTo($ele)
    {
        //WP_CLI::line("\n*** Process How To");
        return ['type' => 'how-to', 'content' => $this->processNodes($ele)];
    }

    private function processHowToSection($ele)
    {
        //WP_CLI::line("\n*** Process How To Section");
        return ['type' => 'how-to-section', 'content' => $this->processNodes($ele)];
    }

    private function processHowToStep($ele)
    {
        $step = strval($ele->meta->attributes()->content);
        //WP_CLI::line("\n*** Process Step " . $step);
        return ['type' => 'how-to-step', 'step' => $step, 'content' => $this->processNodes($ele)];
    }

    private function processHowToDirection($data)
    {
        //WP_CLI::line("\n*** Process Direction");
        //WP_CLI::line($this->getText($data->div));

        return ['type' => 'direction', 'content' => $this->getText($data->div)];
    }

    private function processLink($data)
    {
        //WP_CLI::line("\n*** Process Link");
        //WP_CLI::line($data->attributes()->href);

        return ['type' => 'link', 'href' => strval($data->attributes()->href)];
    }

    private function processMeta($data)
    {
        //WP_CLI::line("\n*** Process Meta");
        //WP_CLI::line($data->attributes()->content);

        return ['type' => 'meta', 'content' => strval($data->attributes()->content)];
    }

    private function processOffer($data)
    {
        //WP_CLI::line("\n*** Process Offer");
        //WP_CLI::line($this->getText($data));

        return ['type' => 'offer', 'content' => $this->getText($data)];
    }

    private function processP($data)
    {
        //WP_CLI::line("\n*** Process P");
        //WP_CLI::line($this->getText($data));

        return ['type' => 'text', 'content' => $this->getText($data)];
    }

    private function processUl($data)
    {
        //WP_CLI::line("\n*** Process Ul");
        $block = [];
        foreach ($data->children() as $child) {
            //WP_CLI::line($this->getText($child));
            $block[] = $this->getText($child);
        }
        return ['type' => 'list', 'content' => $block];
    }

    private function parseMeta($blocks) {
        //WP_CLI::line("\n*** Parsing metabox");

        if ($metaBox = self::getMetaBox($blocks)) {
            // Iterate metabox lines
            for ($i = 0; $i < sizeof($metaBox['content']) - 1; $i++) {
                if (isset($metaBox['content'][$i]['content'])) {

                    // Check for time required
                    if (in_array($metaBox['content'][$i]['content'], ['Tidsforbrug', 'Vie aikaa', 'Tidsforbruk', 'Tidsförbrukning'])) {
                        // The time required is in the next element
                        $this->time = $metaBox['content'][$i + 1]['content'];
                        //WP_CLI::line('Time: ' . $this->time);
                    }

                    // Check for price
                    if (in_array($metaBox['content'][$i]['content'], ['Pris', 'Hinta'])) {
                        // The price is in the next element
                        $this->price = $metaBox['content'][$i + 1]['content'];
                        //WP_CLI::line('Price: ' . $this->price);
                    }

                    // Check for materials
                    if (in_array($metaBox['content'][$i]['content'], ['Materialer', 'Materiaalit', 'Materialer', 'Material'])) {
                        //WP_CLI::line('MATERIALER');
                        $materials = '';
                        // The materials are in the following elements
                        for ($j = $i; $j < sizeof($metaBox['content']); $j++) {
                            if (isset($metaBox['content'][$j]['content'])) {
                                // If list then iterate the list items
                                if ($metaBox['content'][$j]['type'] === 'list') {
                                    $materials .= '<ul>';
                                    foreach ($metaBox['content'][$j]['content'] as $listItem) {
                                        $materials .= "<li>" . $listItem . "</li>";
                                    }
                                    $materials .= '</ul>';
                                } else if ($metaBox['content'][$j]['type'] === 'h3') {
                                    //WP_CLI::line($metaBox['content'][$j]['content']);
                                    $materials .= "<h3>" . $metaBox['content'][$j]['content'] . "</h3>";
                                } else {
                                    //print "HER!!!!";exit; // TODO
                                    //WP_CLI::line($metaBox['content'][$j]['content']);
                                    $materials .= $metaBox['content'][$j]['content'] . "<br>";  // TODO ?
                                }
                            }
                        }
                        $this->materials = $materials;
                    }
                }
            }
        }
    }

    private static function getMetaBox($blocks)
    {
        foreach ($blocks as $block) {
            if ($block['type'] === 'metabox') {
                return $block;
            }
        }

        return null;
    }

    private static function toLower($txt)
    {
        return str_replace(['Æ', 'Ø', 'Å', 'Ä', 'Ö'], ['æ', 'ø', 'å', 'ä', 'ö'], strtolower($txt));
    }

    public function getPostId()
    {
        return $this->postId;
    }

    public function getType()
    {
        return $this->type;
    }

    public function isGuide()
    {
        return $this->type === 'A';
    }

    public function getBlocks()
    {
        return $this->blocks;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function getTime()
    {
        return $this->time;
    }

    public function getMaterials()
    {
        return $this->materials;
    }

    public function getCountry()
    {
        return $this->country;
    }

    public function getLanguage()
    {
        return self::$countryLanguages[strtoupper($this->country)];
    }

    public function success()
    {
        return $this->success;
    }
}