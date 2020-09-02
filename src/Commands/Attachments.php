<?php

namespace Bonnier\Willow\Base\Commands;

use function WP_CLI\Utils\make_progress_bar;

class Attachments extends \WP_CLI_Command
{
    private const CMD_NAMESPACE = 'attachments';

    public static function register()
    {
        try {
            \WP_CLI::add_command(CmdManager::CORE_CMD_NAMESPACE . ' ' . static::CMD_NAMESPACE, __CLASS__);
        } catch (\Exception $exception) {
            var_dump($exception->getMessage());
        }
    }

    /**
     * Copies captions from `post_excerpt` to ACF field `caption`
     * @see AttachmentFieldGroup
     *
     * ## EXAMPLES
     *
     * wp contenthub editor attachments captions
     *
     */
    public function captions()
    {
        \WP_CLI::line('Fetching all attachments...');
        $attachments = collect(get_posts([
            'post_type' => 'attachment',
            'posts_per_page' => -1,
            'post_mime_type' => 'image/jpeg'
        ]));
        \WP_CLI::line(sprintf('Found %s attachments', number_format($attachments->count())));
        $bar = make_progress_bar('Copying attachment captions', $attachments->count());
        $attachments->each(function (\WP_Post $attachment) use (&$bar) {
            if ( ! empty($attachment->post_excerpt)) {
                update_field('caption', $attachment->post_excerpt, $attachment->ID);
            }
            $bar->tick();
        });
        $bar->finish();
    }
}
