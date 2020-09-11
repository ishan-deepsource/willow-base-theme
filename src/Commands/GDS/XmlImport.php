<?php

namespace Bonnier\Willow\Base\Commands\GDS;

use Bonnier\Willow\Base\Helpers\HtmlToMarkdown;
use Bonnier\Willow\Base\Models\ACF\Attachment\AttachmentFieldGroup;
use Bonnier\Willow\Base\Models\WpAttachment;
use Bonnier\Willow\Base\Models\WpAuthor;
use WP_CLI;
use WP_CLI_Command;
use WP_User;

class XmlImport extends WP_CLI_Command
{
    private const CMD_NAMESPACE = 'gds';
    private $multiple;
    private $dir;
    private $log;
    private $skipUpload;
    private $exampleImageId;
    private $startContent;
    private $exportOldContent;
    private $outputNextId;
    private $outputParsedData;
    private $force;


    public static function register()
    {
        WP_CLI::add_command(static::CMD_NAMESPACE, __CLASS__);
    }

    /**
     * Imports from xml from X-Cago
     *
     * ## EXAMPLES
     *
     * wp gds xmlimport ../xcago
     *
     */
    public function xmlimport($params)
    {
        $this->multiple = false;
        $this->log = false;
        $this->skipUpload = false;
        $this->startContent = false;
        $this->exportOldContent = false;
        $this->outputNextId = false;
        $this->outputParsedData = false;
        $this->force = false;

        $this->parseArguments($params);

        $this->log('Script start');

        if (!$this->dir) {
            WP_CLI::line('XmlImport parameters');
            WP_CLI::line('');
            WP_CLI::line('dir:/path/to/file the directory to import from');
            WP_CLI::line('This directory should contain directories that contain a html-file and jpg-files.');
            WP_CLI::line('');
            WP_CLI::line('log:/path/to/file if you want to log start and end timestamps of the import.');
            WP_CLI::line('');
            WP_CLI::line('export-old-content:/path/to/file save the current composite_content of the post to a file');
            WP_CLI::line('');
            WP_CLI::line('import-start-content/path/to/file overwrite the current composite_content with the');
            WP_CLI::line('content of the file, before importing.');
            WP_CLI::line('');
            WP_CLI::line('skip-upload do not upload images to Wordpress and S3. Use a default picture instead.');
            WP_CLI::line('');
            WP_CLI::line('output-next-id show the next post ID to be imported.');
            WP_CLI::line('');
            WP_CLI::line('output-parsed-data show the data parsed in the xml file.');
            WP_CLI::line('');
            WP_CLI::line('multiple do not stop after the first file is imported.');
            WP_CLI::line('');
            WP_CLI::line('Examples:');
            WP_CLI::line('wp gds xmlimport dir:../folder-with-folders/');
            WP_CLI::line('wp gds xmlimport dir:../folder-with-folders/ log:/tmp/xmlimport');
            WP_CLI::line('wp gds xmlimport dir:../folder-with-folders/ log:/tmp/xmlimport multiple');
            WP_CLI::line('wp gds xmlimport dir:../folder-with-folders/ output-parsed-data');
            WP_CLI::line('wp gds xmlimport dir:../folder-with-folders/ skip-upload');
            WP_CLI::line('wp gds xmlimport dir:../folder-with-folders/ output-next-id');
            WP_CLI::line('');
            WP_CLI::error('Dir Argument is missing');
        }

        if (!is_dir($this->dir)) {
            WP_CLI::error('Invalid directory in argument: ' . $this->dir);
        }

        // Get dirs and iterate them
        $files = scandir($this->dir);
        $files = array_diff($files, ['..', '.']);
        foreach ($files as $file) {
            if (is_dir($this->dir . '/' . $file)) {
                $xmlParser = new XmlParser($this->dir, $file);

                $blocks = $xmlParser->getBlocks();
                $postId = $xmlParser->getPostId();
                $materials = $xmlParser->getMaterials();
                $time = $xmlParser->getTime();
                $price = $xmlParser->getPrice();

                if ($this->outputParsedData) {
                    WP_CLI::line('Outputting parsed data:');
                    var_export($xmlParser->getBlocks());
                    WP_CLI::line('Parsed data outputted');
                    WP_CLI::success('Done.');
                    exit;
                }

                if ($this->outputNextId) {
                    WP_CLI::line('');
                    WP_CLI::line('Next post ID: ' . $postId);
                    exit;
                }

                // Check if import has already been done for this post
                // Use "force" parameter to force reimport
                if (get_field('xml_import', $postId) && !$this->force) {
                    WP_CLI::error('This post has already been enriched with xml import.');
                }

                // Log start
                $this->log('Import - ' . $file . ' - start');

                // Save meta time
                //WP_CLI::line('Save meta time: ' . $time);
                //update_field('field_5f560d8c95208', $time, $postId);  // TODO

                // Save meta price
                //WP_CLI::line('Save meta price: ' . $price);
                //update_field('field_5f560ec795209', $price, $postId);  // TODO

                // Set author to the default editor
                self::setDefaultAuthor($xmlParser->getLanguage(), $postId);

                // Build composite content and save
                $compositeContent = $this->build($postId, $blocks, $this->dir . '/' . $file, $materials);
                update_field('composite_content', $compositeContent, $postId);

                // Save import timestamp in meta
                update_field('xml_import', date('Y-m-d H:i:s'), $postId);

                // Log end
                $this->log('Import - ' . $file . ' - end');

                if (!$this->multiple) {
                    WP_CLI::line('"multiple" argument not set, so stopping after one import.');
                    WP_CLI::line('Post ID: ' . $postId);
                    WP_CLI::success('Done.');
                    exit;
                }
            }
        }

        print "Done.";
        $this->log('Script end');
        WP_CLI::success('Import done');
    }

    private function parseArguments($params)
    {
        $returnParams = [];
        foreach ($params as $param) {
            if ($param === 'multiple') {
                $this->multiple = true;
                continue;
            }
            if (substr($param, 0, 4) === 'dir:') {  // The basedir that includes the dirs to be imported
                $this->dir = substr($param, 4, strlen($param) - 4);
                continue;
            }
            if (substr($param, 0, 4) === 'log:') {  // Path to log-file
                $this->log = substr($param, 4, strlen($param) - 4);
                continue;
            }
            if (substr($param, 0, 19) === 'export-old-content:') {  // Export the post's CompositeContent to this file before importing
                $this->exportOldContent = substr($param, 19, strlen($param) - 19);
            }
            if (substr($param, 0, 21) === 'import-start-content:') {
                $startContentFileName = substr($param, 21, strlen($param) - 21);
                $this->importStartContent($startContentFileName);
                $this->multiple = false;
                continue;
            }
            if ($param === 'skip-upload') { // Skip upload of images (great for testing / debugging)
                $this->skipUpload = true;
                continue;
            }
            if ($param === 'output-next-id') {
                $this->outputNextId = true;
                continue;
            }
            if ($param === 'output-parsed-data') {
                $this->outputParsedData = true;
                continue;
            }
            if ($param === 'force') {
                $this->force = true;
                continue;
            }
            $returnParams[] = $param;
        }
        return $returnParams;
    }

    private function importStartContent($startContentFileName)
    {
        if (!is_file($startContentFileName)) {
            WP_CLI::error('Not a file: ' . $startContentFileName);
        }
        $startContent = file_get_contents($startContentFileName);
        eval("\$this->startContent = " . $startContent . ";");
        if (!$this->startContent) {
            WP_CLI::error('Error in importing start-content from: ' . $startContentFileName);
        }
    }

    private function build($postId, $blocks, $dir, $materials)
    {
        WP_CLI::line('');
        WP_CLI::line('');
        WP_CLI::line('************************');
        WP_CLI::line('************************ build start');
        WP_CLI::line('************************');
        WP_CLI::line('');

        WP_CLI::line('Article ID: ' . $postId);

        $post = get_post($postId);
        if (!$post) {
            WP_CLI::error('Article not found');
        }

        $compositeContent = get_field('composite_content', $postId);
        if ($this->exportOldContent) {
            file_put_contents($this->exportOldContent, var_export($compositeContent, true));
            if (!is_file($this->exportOldContent) || filesize($this->exportOldContent) === 0) {
                WP_CLI::error('Error exporting ' . $this->exportOldContent);
            }
            WP_CLI::line('Export of postId: ' . $postId);
            WP_CLI::line('Exported to: ' . $this->exportOldContent);
            WP_CLI::line('Filesize: ' . filesize($this->exportOldContent));
            WP_CLI::success('Export done.');
            exit;
        }

        // Use start content if provided (great for testing / debugging)
        if ($this->startContent) {
            $compositeContent = $this->startContent;
        }

        // Used when skipping upload of images
        $this->setExampleImageId($compositeContent);

        $newContent = $this->buildBlocks($blocks, $dir, $postId);
        $newContent[] = $this->buildMaterials($materials);
        WP_CLI::line('');
        WP_CLI::line('************************');
        WP_CLI::line('************************ var_export(newContent) - start');
        WP_CLI::line('************************');
        var_export($newContent);
        WP_CLI::line('');
        WP_CLI::line('************************ var_export(newContent) - slut');
        WP_CLI::line('');

        return array_merge($compositeContent, $newContent);
    }

    private function buildMaterials($materials)
    {
        return [
            'acf_fc_layout' => 'paragraph_list',
            'title' => HtmlToMarkdown::parseHtml('Materialer'),
            'description' => HtmlToMarkdown::parseHtml($materials),
            'image' => false,
            'video_url' => '',
            'collapsible' => false,
            'display_hint' => 'ordered',    // TODO set to box when merging
            'items' => false,
        ];
    }

    private function buildBlocks($blocks, $dir, $postId)
    {
        $content = [];
        foreach ($blocks as $block) {
            WP_CLI::line('Type: ' . $block['type']);
            if ($newContent = $this->buildWidget($block, $dir, $postId)) {
                // If type is how-to then add result directly at end of content
                // (to avoid array inside array)
                if (in_array($block['type'], ['how-to', 'boxout'])) {
                    $content = array_merge($content, $newContent);
                }
                else {
                    $content[] = $newContent;
                }
            }
        }
        return $content;
    }

    private function buildWidget($block, $dir, $postId)
    {
        if ($block['type'] === 'author') {
            WP_CLI::line('Widget: Text');
            WP_CLI::line($block['content']);
            return [
                'body'           => HtmlToMarkdown::parseHtml($block['content']),
                'locked_content' => true,
                'acf_fc_layout'  => 'text_item'
            ];
        }
        else if ($block['type'] === 'title') {
            /*
            print "<h1>(Set title)</h1>";
            print "(" . $block['content'] . ")<br>\n";
            */
            return null;
        }
        else if ($block['type'] === 'description') {
            /*
            print "<h1>(Set description)</h1>";
            print "(" . $block['content'] . ")<br>\n";
            */
            return null;
        }
        else if ($block['type'] === 'boxout') {
            //var_dump($block);exit;
            //print "* boxout *\n";
            return $this->buildBlocks($block['content'], $dir, $postId);
        }
        else if ($block['type'] === 'h2') {
            WP_CLI::line('Widget: Text');
            WP_CLI::line('<h2>' . $block['content'] . '</h2>');
            return [
                'body'           => HtmlToMarkdown::parseHtml('<h2>' . $block['content'] . '</h2>'),
                'locked_content' => true,
                'acf_fc_layout'  => 'text_item'
            ];
        }
        else if ($block['type'] === 'how-to') {
            WP_CLI::line('************************ how-to');
            return $this->buildBlocks($block['content'], $dir, $postId);
        }
        else if ($block['type'] === 'how-to-section') {
            return $this->buildHowToSection($block['content'], $dir, $postId);
        }
        else if ($block['type'] === 'image') {
            WP_CLI::line('Widget: image');
            WP_CLI::line('Dir: ' . $dir);
            WP_CLI::line('src: ' . $block['src']);

            $figcaption = '';
            if (isset($block['figcaption'])) {
                $figcaption = HtmlToMarkdown::parseHtml($block['figcaption']);
                WP_CLI::line("figcaption: " . $figcaption);
            }

            $fileAttachmentId = $this->uploadFile($postId, $dir, $block['src'], $figcaption);
            WP_CLI::line('## Attachment ID Image ##: ' . $fileAttachmentId);
            return [
                'lead_image' => false,
                'file' => $fileAttachmentId,
                'locked_content' => true,
                'acf_fc_layout' => 'image'
            ];
        }
        else if ($block['type'] === 'meta') {
            // Do not use meta
        }
        else if ($block['type'] === 'text') {
            WP_CLI::line('Widget: Text');
            WP_CLI::line($block['content']);
            return [
                'body'           => HtmlToMarkdown::parseHtml($block['content']),
                'locked_content' => true,
                'acf_fc_layout'  => 'text_item'
            ];
        }
        else if ($block['type'] === 'list') {
            WP_CLI::line('Widget: Text');
            //var_dump($block);
            //WP_CLI::line($block['content']);
            $content = '<ul>';
            foreach ($block['content'] as $ele) {
                $content .= '<li>' . $ele . '<br>';
            }
            $content .= '</ul>';
            //var_dump($content);exit;

            return [
                'body'           => HtmlToMarkdown::parseHtml($content),
                'locked_content' => true,
                'acf_fc_layout'  => 'text_item'
            ];
        }
        else if (!in_array($block['type'], ['metabox'])) {  // Allow metabox to be skipped, otherwise error
            print "<h1>(buildWidget) ERROR, type not handled: " . $block['type'];
            exit;
        }
        print "<br>\n";
    }

    private function uploadFile($postId, $dir, $src, $figcaption)
    {
        if ($this->skipUpload) {
            return $this->exampleImageId;
        }

        WP_CLI::line('*** UPLOAD IMAGE ***');
        $file = $dir . '/' . $src;
        $fileName = $src;
        return WpAttachment::upload_file($postId, $file, $fileName, $figcaption);
    }

    private function buildHowTo($blocks)
    {
        foreach ($blocks as $block) {
            $this->buildWidget($block);
        }
    }

    private function buildBoxout($blocks)
    {
        foreach ($blocks as $block) {
            $this->buildWidget($block);
        }
    }

    private function buildHowToSection($blocks, $dir, $postId)
    {
        $title = '';
        $description = '';
        $image = false;
        foreach ($blocks as $block) {
            if ($block['type'] === 'h2') {
                $title = $block['content'];
            }
            else if ($block['type'] === 'text') {
                if ($description) {
                    $description .= '<br><br>';
                }
                $description .= $block['content'];
            }
            else if ($block['type'] === 'image') {
                $image = $block;
            }
        }

        print "<h1>Widget: Paragraph List</h1>";
        print "Title:<br>" . $title . "<br><br>";
        print "Description:<br>" . $description . "<br>";


        $fileAttachmentId = false;
        if ($image) {
            $figcaption = false;
            if (isset($image['figcaption'])) {
                $figcaption = HtmlToMarkdown::parseHtml($image['figcaption']);
                WP_CLI::line("figcaption: " . $figcaption);
            }

            $fileAttachmentId = $this->uploadFile($postId, $dir, $image['src'], $figcaption);
            WP_CLI::line('## Attachment ID Paragraph List ##: ' . $fileAttachmentId);
        }

        $items = [];
        foreach ($blocks as $block) {
            if ($block['type'] === 'how-to-step') {
                $items[] = $this->buildHowToStep($block['content'], $dir, $postId);
            }
        }

        // ACF needs items to be false if empty
        if (sizeof($items) === 0) {
            $items = false;
        }

        return [
            'acf_fc_layout' => 'paragraph_list',
            'title' => HtmlToMarkdown::parseHtml($title),
            'description' => HtmlToMarkdown::parseHtml($description),
            'image' => $fileAttachmentId,
            'video_url' => '',
            'collapsible' => false,
            'display_hint' => 'ordered',
            'items' => $items,
        ];
    }

    private function buildHowToStep($blocks, $dir, $postId)
    {
        WP_CLI::line('<h1>Paragraph List Item</h1>');

        $stepNumber = false;
        $image = false;
        $direction = false;
        foreach ($blocks as $block) {
            if ($block['type'] === 'meta') {
                $stepNumber = $block['content'];
            }
            else if ($block['type'] === 'link') {
            }
            else if ($block['type'] === 'image') {
                $image = $block;
            }
            else if ($block['type'] === 'direction') {
                $direction = $block['content'];
            }
            else {
                print "<h1>ERROR, not matched</h1>";
                exit;
            }
        }

        // Build array to be returned
        $data = [];
        $data['image'] = false;

        // Set title to step number
        if ($stepNumber) {
            $data['title'] = $stepNumber;
        }

        // Set description to direction (text under image)
        if ($direction) {
            $data['description'] = HtmlToMarkdown::parseHtml($direction);
            WP_CLI::line('direction: ' . $direction);
        }

        if ($image) {
            $figcaption = false;
            if (isset($image['figcaption'])) {
                $figcaption = HtmlToMarkdown::parseHtml($image['figcaption']);
                WP_CLI::line("figcaption: " . $figcaption);
            }

            $data['image'] = $this->uploadFile($postId, $dir, $image['src'], $figcaption);
            WP_CLI::line('## Attachment ID Paragraph List Item ##: ' . $data['image']);   // attachment id
        }

        return $data;
    }

    private function log($line)
    {
        if ($this->log) {
            $date = date('Y-m-d H:i:s');
            file_put_contents($this->log, $date . ' - ' . $line . PHP_EOL, FILE_APPEND);
        }
    }

    private function setAuthor($authorName, $postId)
    {
        if ($authorName) {
            $author = self::getAuthor($authorName);

            if ($author->ID) {
                WP_CLI::line(sprintf('Updating post %s, set author_id %s', $postId, $author->ID));
                wp_update_post([
                    'ID' => $postId,
                    'post_author' => $author->ID,
                ]);
            }
        }
    }

    private function setDefaultAuthor($language, $postId)
    {
        if (!$language) {
            WP_CLI::error('Language not set');
        }

        $author = WpAuthor::getDefaultAuthor($language);
        if (!$author) {
            WP_CLI::error('Error getting default author for language: ' . $language);
        }

        WP_CLI::line(sprintf('Updating post %s, set author_id %s', $postId, $author->ID));
        wp_update_post([
            'ID' => $postId,
            'post_author' => $author->ID,
        ]);
    }

    private static function getAuthor($authorName): ?WP_User
    {
        if (!empty($authorName)) {
            $author = WpAuthor::findOrCreate($authorName);
            var_dump($author);
            if ($author instanceof WP_User) {
                return $author;
            }
        }
        return null;
    }

    private function setExampleImageId($content)
    {
        foreach($content as $ele) {
            if ($ele['acf_fc_layout'] === 'image') {
                $this->exampleImageId = $ele['file']['ID'];
                return;
            }
        }
        $this->exampleImageId = false;
    }
}