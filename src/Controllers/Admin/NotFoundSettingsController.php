<?php

namespace Bonnier\Willow\Base\Controllers\Admin;

use Symfony\Component\HttpFoundation\Request;

class NotFoundSettingsController
{
    const IGNORED_EXTENSIONS_KEY = 'not_found_ignored_extensions';
    /** @var Request */
    private $request;
    /** @var array */
    private $ignoredExtensions = [];
    /** @var array */
    private $notices = [];

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->ignoredExtensions = get_option(self::IGNORED_EXTENSIONS_KEY, []);
    }

    public function handlePost()
    {
        if ($this->request->isMethod(Request::METHOD_POST)) {
            if (current_user_can('manage_options')) {
                if ($this->request->request->has('add-ignore-extension-input')) {
                    $this->handleIgnoreExtension();
                }
                if ($this->request->request->has('remove-ignore-extension-submit')) {
                    $this->handleRemoveExtension();
                }
            } else {
                wp_die('Unauthorized', 'Error', [
                    'response' => 403,
                    'back_link' => admin_url('admin.php'),
                ]);
            }
        }
    }

    public function registerScripts()
    {

    }

    public function displaySettingsPage()
    {
        $view = sprintf('%s/Views/notFoundSettings.php', rtrim(dirname(dirname(__DIR__)), '/'));

        include_once($view);
    }

    private function getNotices()
    {
        return $this->notices;
    }

    private function handleIgnoreExtension()
    {
        $extension = mb_strtolower(ltrim(trim($this->request->get('add-ignore-extension-input')), '.'));
        $ignoredExtensions = $this->ignoredExtensions;
        if (!in_array($extension, $ignoredExtensions)) {
            array_push($ignoredExtensions, $extension);
        }
        if (update_option(self::IGNORED_EXTENSIONS_KEY, $ignoredExtensions)) {
            $this->ignoredExtensions = $ignoredExtensions;
            $this->addNotice('Extension saved!', 'success');
        } else {
            $this->addNotice('Error saving extension');
        }
    }

    private function handleRemoveExtension()
    {
        $extension = $this->request->get('remove-ignore-extension-submit');
        $ignoredExtensions = $this->ignoredExtensions;
        $index = array_search($extension, $ignoredExtensions);
        if ($index >= 0) {
            array_splice($ignoredExtensions, $index, 1);
        }
        if (update_option(self::IGNORED_EXTENSIONS_KEY, $ignoredExtensions)) {
            $this->ignoredExtensions = $ignoredExtensions;
            $this->addNotice('Extension removed!', 'success');
        } else {
            $this->addNotice('Error removing extension');
        }
    }

    /**
     * @param string $message
     * @param string $type
     */
    protected function addNotice(string $message, string $type = 'error')
    {
        $this->notices[] = [$type => $message];
    }
}
