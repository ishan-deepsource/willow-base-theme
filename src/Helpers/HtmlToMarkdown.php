<?php

namespace Bonnier\Willow\Base\Helpers;

use Exception;
use League\HTMLToMarkdown\HtmlConverter;

class HtmlToMarkdown
{
    protected static $instance = null;

    /**
     * @return HtmlConverter
     */
    public static function getInstance(): HtmlConverter
    {
        if (is_null(self::$instance)) {
            self::$instance = new HtmlConverter([
                'header_style' => 'atx'
            ]);
        }
        return self::$instance;
    }

    /**
     * @param      $html
     *
     * @param bool $fixAnchors
     *
     * @return null|string
     */
    public static function parseHtml($html, $fixAnchors = true)
    {
        if ($fixAnchors) {
            $html = static::fixAnchorTags($html);
        }
        $html = static::fixOrderedListsInHeaders($html);
        try {
            $markdown = static::getInstance()->convert($html);
            // Strip tags to avoid unwanted HTML in markdown
            return strip_tags($markdown);
        } catch (Exception $exception) {
            return null;
        }
    }

    private static function fixOrderedListsInHeaders($html)
    {
        // Get all headings with markdown lists in them
        preg_match_all('/<h\d[^>]*>\d\. (?:.|\n)*?<\/h\d>/i', $html, $matches);

        collect($matches)->flatten()->each(function ($headerHtml) use (&$html) {

            // convert encoding to special chars are read correctly
            $utfEncodedHtml = mb_convert_encoding($headerHtml, 'HTML-ENTITIES', "UTF-8");

            // Escape the . after the digit to prevent markdown parsing as a ordered list
            $fixedHtml = preg_replace('/(\d)\./', '$1\.', $utfEncodedHtml);

            // Replace with the fixed html
            $html = str_replace($headerHtml, $fixedHtml, $html);
        });
        return $html;
    }

    private static function fixAnchorTags($html)
    {
        // Get all anchor tags as html strings
        preg_match_all('/<a[^>]*>(?:.|\n)*?<\/a>/i', $html, $matches);

        collect($matches)->flatten()->each(function ($anchorHtml) use (&$html) {
            
            // convert encoding to special chars are read correctly
            $utfEncodedHtml = mb_convert_encoding($anchorHtml, 'HTML-ENTITIES', "UTF-8");
            // Parse the anchor so we may use objects to access the attributes

            $domDocument = new \DOMDocument();
            $domDocument->loadHTML($utfEncodedHtml);
            $anchors = $domDocument->getElementsByTagName('a');
            /* @var $anchor \DOMElement */
            if ($anchor = $anchors->item(0)) {
                $attributes = collect($anchor->attributes)
                    ->only(['target', 'title', 'rel']) // Get only the attributes we are interested in
                    ->reduce(function ($attributes, \DOMAttr $attribute) {
                        $attributes[$attribute->name] = $attribute->textContent;
                        return $attributes;
                    }, []);


                $markdown = sprintf(
                    '[%s](%s%s)',
                    static::parseHtml($anchor->textContent, false), // Fix any html that might be inside
                    $anchor->getAttribute('href'),
                    empty($attributes) ? '' : sprintf(' %s', json_encode($attributes))
                );
                $html = str_replace($anchorHtml, $markdown, $html);
            }
        });
        return $html;
    }
}
