<?php

namespace Bonnier\Willow\Base\Commands;

use Bonnier\WP\ContentHub\Editor\Models\WpComposite;
use WP_CLI;
use WP_CLI_Command;

class AuthorFix extends WP_CLI_Command
{
    const CMD_NAMESPACE = 'author';

    public static function register()
    {
        WP_CLI::add_command(CommandBootstrap::CORE_CMD_NAMESPACE  . ' ' . static::CMD_NAMESPACE, __CLASS__);
    }

    /**
     * Sets all posts by default user to be by the user of your choice
     *
     * ## OPTIONS
     *
     * <locale>
     * : The locale you want to use to find composites.
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
            WP_CLI::error('please provide both locale and author example:');
            WP_CLI::error(sprintf('%s author fix da 3', CommandBootstrap::CORE_CMD_NAMESPACE));
        }

        $locale = $args[0];
        $author = $args[1];

        $posts = get_posts([
            'lang' => $locale,
            'post_type' => WpComposite::POST_TYPE,
            'numberposts' => -1,
            'author' => 1,
        ]);

        $amountOfPosts = count($posts);

        WP_CLI::line(sprintf('found %s composites with locale: %s', $amountOfPosts, $locale));

        if ($amountOfPosts > 0) {
            foreach ($posts as $post) {
                WP_CLI::runcommand(sprintf('post update %d --post_author=%d', $post->ID, $author));
            }
        }
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
        $users = [
            'da' => 'Redaktionen',
            'sv' => 'Redaktionen',
            'nb' => 'Redaksjonen',
            'fi' => 'Toimitus',
            'nl' => 'de redactie',

        ];
        foreach ($users as $lang => $username) {
            $user = get_user_by('login', $username);
            if (empty($user)) {
                return;
            }
            WP_CLI::line(sprintf('starting author fix for %s (%s)', $user->display_name, strtoupper($lang)));
            WP_CLI::runcommand(sprintf(
                '%s fix %s %d',
                CommandBootstrap::CORE_CMD_NAMESPACE  . ' ' . static::CMD_NAMESPACE,
                $lang,
                $user->ID
            ));
        }
    }

}
