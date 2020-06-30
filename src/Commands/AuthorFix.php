<?php

namespace Bonnier\Willow\Base\Commands;

use Bonnier\Willow\Base\Models\WpAuthor;
use Bonnier\Willow\Base\Models\WpComposite;
use Bonnier\Willow\MuPlugins\Helpers\LanguageProvider;
use WP_CLI;
use WP_CLI_Command;

class AuthorFix extends WP_CLI_Command
{
    private const CMD_NAMESPACE = 'author';

    public static function register()
    {
        WP_CLI::add_command(CommandBootstrap::CORE_CMD_NAMESPACE  . ' ' . static::CMD_NAMESPACE, __CLASS__);
    }

    private function fixLanguageAuthor($language, $authorId)
    {
        $posts = get_posts([
            'lang' => $language,
            'post_type' => WpComposite::POST_TYPE,
            'numberposts' => -1,
            'author' => 1,
        ]);

        $count = count($posts);
        WP_CLI::line(sprintf('Found %s composites with language: %s', $count, $language));

        foreach ($posts as $post) {
            WP_CLI::line(sprintf('Updating post %s, set author_id %s', $post->ID, $authorId));
            wp_update_post([
                'ID' => $post->ID,
                'post_author' => $authorId,
            ]);
        }
    }

    /**
     * Sets all posts by default user to be by the user of your choice
     *
     * ## OPTIONS
     *
     * <language>
     * : The language you want to use to find composites.
     * <author>
     * : The id of the user you want to set all composites to.
     * ---
     *
     * ## EXAMPLES
     *
     *     wp contenteditor author fix da 3
     *
     */
    public function fix($args)
    {
        if (count($args) < 2) {
            WP_CLI::error('Please provide both language and author example:');
            WP_CLI::error(sprintf('%s author fix da 3', CommandBootstrap::CORE_CMD_NAMESPACE));
        }

        $language = $args[0];
        $authorId = $args[1];

        self::fixLanguageAuthor($language, $authorId);
    }

    /**
     * Sets all posts by default user to be by the editor user for each language
     * ---
     *
     * ## EXAMPLES
     *
     *     wp contenteditor author fixAll
     *
     */

    public function fixAll()
    {
        collect(LanguageProvider::getLanguageList())->each(function ($language) {
            $author = WpAuthor::getDefaultAuthor($language->slug);
            if (empty($author)) {
                WP_CLI::error('Could not get default user');
                return;
            }

            WP_CLI::line();
            WP_CLI::line(sprintf('Language:    %s', strtoupper($language->slug)));
            WP_CLI::line(sprintf('Login:       %s', $author->user_login));
            WP_CLI::line(sprintf('DisplayName: %s', $author->display_name));
            WP_CLI::line(sprintf('ID:          %s', $author->id));

            self::fixLanguageAuthor($language->slug, $author->id);
        });
    }
}
