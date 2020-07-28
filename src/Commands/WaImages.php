<?php

namespace Bonnier\Willow\Base\Commands;

use Bonnier\Willow\Base\Models\WpAttachment;
use Bonnier\Willow\Base\Repositories\WhiteAlbum\ImageRepository;
use WP_CLI;
use WP_Post;

/**
 * Class AdvancedCustomFields
 */
class WaImages extends BaseCmd
{
    private const CMD_NAMESPACE = 'wa images';

    /* @var ImageRepository|null $repository */
    private $repository = null;

    public static function register()
    {
        WP_CLI::add_command(CmdManager::CORE_CMD_NAMESPACE . ' ' . static::CMD_NAMESPACE, __CLASS__);
    }


    /**
     * Updates images from WhiteAlbum
     *
     *
     * ## EXAMPLES
     * wp contenthub editor wa images import
     *
     * @param $args
     * @param $assocArgs
     *
     * @throws \Exception
     */
    public function update($args, $assocArgs)
    {
        $this->repository = new ImageRepository();

        WpAttachment::mapAll(function (Wp_Post $attachment) {
            if ($waId = WpAttachment::contenthub_id($attachment->ID)) {
                if ($waImage = $this->repository->findById($waId)) {
                    WP_CLI::line(sprintf('Updating image with post_id: %d and whitealbum_id: %d', $attachment->ID, $waId));
                    WpAttachment::updateAttachment($attachment->ID, $waImage);
                }
            }
            if (getenv('WP_ENV') === 'production') {
                usleep(250000); // Wait for 250 ms between each update to avoid DDOS'ing WhiteAlbum
            }
        });

        WP_CLI::success('Done');
    }
}
