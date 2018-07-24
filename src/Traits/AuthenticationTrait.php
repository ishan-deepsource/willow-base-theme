<?php

namespace Bonnier\Willow\Base\Traits;

trait AuthenticationTrait
{
    protected $requiredCapability = 'administrator';

    public function authenticate()
    {
        $username = $_SERVER['PHP_AUTH_USER'] ?? '';
        $password = $_SERVER['PHP_AUTH_PW'] ?? '';
        $user = wp_authenticate($username, $password);
        if (is_wp_error($user) || !user_can($user, $this->requiredCapability)) {
            return new \WP_Error('rest_forbidden', 'Forbidden', ['status' => 401]);
        }

        return true;
    }
}
