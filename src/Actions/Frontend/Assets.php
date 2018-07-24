<?php

namespace Bonnier\Willow\Base\Actions\Frontend;

/**
 * Class Assets
 *
 * @package \Bonnier\Willow\Base\ActionsFilters
 */
class Assets
{
    protected $assetManifest;

    /**
     * Init constructor.
     */
    public function __construct()
    {
        add_action('wp_enqueue_scripts', [$this, 'printAssetTags'], 100);
        add_action('wp_enqueue_scripts', [$this, 'removeJquery'], 100);
    }

    public function printAssetTags()
    {
        wp_enqueue_script('blankslate/manifest.js', $this->getAssetUri('/scripts/manifest.js'), null, null, true);
        wp_enqueue_script('blankslate/vendor.js', $this->getAssetUri('/scripts/vendor.js'), null, null, true);
        wp_enqueue_script('blankslate/app.js', $this->getAssetUri('/scripts/app.js'), null, null, true);
    }
    public function removeJquery()
    {
        wp_deregister_script('jquery');
    }

    private function getAssetUri($assetPath)
    {
        return get_template_directory_uri() . '/dist' . $this->getAssetManifest()->get($assetPath, $assetPath);
    }

    private function getAssetManifest()
    {
        if ($this->assetManifest) {
            return $this->assetManifest;
        }
        if (!$assetManifest = @file_get_contents(get_template_directory() . '/dist/mix-manifest.json')) {
            throw new \Exception(
                sprintf('Did you remember to run the yarn "dev|production" from: %s', get_template_directory())
            );
        }
        $this->assetManifest = collect(json_decode($assetManifest, $associativeArray = true));
        return $this->assetManifest;
    }
}
