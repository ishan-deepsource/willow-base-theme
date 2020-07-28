<?php


namespace Bonnier\Willow\Base\Models;

use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Eloquent\Model;

class EloquentModel extends Model
{
    private $capsuleConnection;

    public function __construct(array $attributes = [])
    {
        /** @var \wpdb $wpdb */
        global $wpdb;
        $capsule = new Manager();
        $capsule->addConnection([
            'driver' => 'mysql',
            'host' => env('DB_HOST', 'localhost'),
            'database' => env('DB_NAME'),
            'username' => env('DB_USER'),
            'password' => env('DB_PASSWORD'),
            'charset' => $wpdb->charset,
            'collation' => $wpdb->collate,
            'prefix' => $wpdb->prefix
        ]);
        $capsule->bootEloquent();
        $this->capsuleConnection = $capsule->getConnection();
        $this->connection = $this->capsuleConnection->getName();
        parent::__construct($attributes);
    }
}
