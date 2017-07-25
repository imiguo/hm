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

use Illuminate\Database\Capsule\Manager as Capsule;

$dotenv = new Dotenv\Dotenv(__DIR__);
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

// Make this Capsule instance available globally via static methods... (optional)
$capsule->setAsGlobal();

// Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
$capsule->bootEloquent();

if (extension_loaded('mysql')) {
    mysql_connect(getenv('DB_HOST'), getenv('DB_USERNAME'), getenv('DB_PASSWORD'));
    mysql_select_db(getenv('DB_DATABASE'));
} else {
    function mysql_query($query)
    {
        return Mysql::instance()->query($query);
    }

    function mysql_fetch_array($result)
    {
        return $result->fetch_array();
    }

    function mysql_fetch_assoc($result)
    {
        return $result->fetch_assoc();
    }

    function mysql_insert_id()
    {
        return Mysql::instance()->insert_id;
    }

    function mysql_real_escape_string($escapestr)
    {
        return Mysql::instance()->real_escape_string($escapestr);
    }
}