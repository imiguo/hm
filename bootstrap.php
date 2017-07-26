<?php

/*
 * This file is part of the entimm/hm.
 *
 * (c) entimm <entimm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

require __DIR__.'/vendor/autoload.php';
require __DIR__.'/helpers.php';

use Illuminate\Database\Capsule\Manager as Capsule;

define('APP_PATH', __DIR__);

$environmentFile = env('APP_ENV') ? '.env.'.env('APP_ENV') : '.env';
$dotenv = new Dotenv\Dotenv(APP_PATH, $environmentFile);
$dotenv->load();

$capsule = new Capsule();
$capsule->addConnection([
    'driver'    => getenv('DB_CONNECTION'),
    'host'      => getenv('DB_HOST'),
    'database'  => getenv('DB_DATABASE'),
    'username'  => getenv('DB_USERNAME'),
    'password'  => getenv('DB_PASSWORD'),
    'port'      => getenv('DB_PORT'),
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => 'hm2_',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

app()->singleton('klein', Klein\Klein::class);
app()->singleton('mysql', function () {
    return new mysqli(getenv('DB_HOST'), getenv('DB_USERNAME'), getenv('DB_PASSWORD'), getenv('DB_DATABASE'));
});

require __DIR__.'/routes.php';
