<?php

namespace Bonnier\Willow\Base\Controllers\App;

use Bonnier\Willow\MuPlugins\Helpers\LanguageProvider;

abstract class BaseController extends \WP_REST_Controller
{
    public function __construct()
    {
        if (!isset($_GET['lang'])) {
            $_GET['lang'] = LanguageProvider::getCurrentLanguage();
        }
    }
}
