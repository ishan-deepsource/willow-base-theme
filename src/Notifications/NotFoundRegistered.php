<?php

namespace Bonnier\Willow\Base\Notifications;

use Bonnier\Willow\Base\Models\Admin\NotFound;
use Bonnier\Willow\Base\Repositories\NotFoundRepository;
use Bonnier\Willow\MuPlugins\Helpers\LanguageProvider;

class NotFoundRegistered
{
    public static function notify(NotFound $notFound)
    {
        if ($notFound->isNotificationSent()) {
            return;
        }
        
        $email = env('NOTFOUND_EMAIL');
        $threshold = env('NOTFOUND_THRESHOLD');

        if (!$email || !$threshold) {
            return;
        }

        if ($notFound->getHits() >= $threshold) {
            $url = LanguageProvider::getHomeUrl($notFound->getUrl(), $notFound->getLocale());
            $hits = $notFound->getHits();
            $body = <<<EOF
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>404 URL over threshold</title>
    </head>
    <body>
        <h1>A URL was not found!</h1>
        <p>The URL <strong><a href="$url">$url</a></strong> has been hit <strong>$hits</strong> times!</p>
    </body>
</html>
EOF;
            if (Mailer::send($email, '404 URL over threshold', $body)) {
                $notFound->setNotificationSent(true);
                NotFoundRepository::instance()->save($notFound);
            }
        }
    }
}
