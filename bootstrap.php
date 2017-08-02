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

$environmentFile = env('APP_ENV') ? '.env.'.env('APP_ENV') : '.env';
$dotenv = new Dotenv\Dotenv(__DIR__, $environmentFile);
$dotenv->load();

require __DIR__.'/constants.php';

$cacheThemeFile = CACHE_PATH.'theme';
if (!is_file($cacheThemeFile) || THEME != file_get_contents($cacheThemeFile)) {
    foreach (glob(APP_PATH.'/public/*') as $file) {
        if (strpos($file, 'index.php') !== false) {
            continue;
        }
        unlink($file);
    }
    foreach (glob(dirname(TMPL_PATH).'/public/*') as $file) {
        $target = APP_PATH.'/public/'.basename($file);
        symlink($file, $target);
    }
}

$capsule = new Capsule();
$capsule->addConnection([
    'driver'    => env('DB_CONNECTION'),
    'host'      => env('DB_HOST'),
    'database'  => env('DB_DATABASE'),
    'username'  => env('DB_USERNAME'),
    'password'  => env('DB_PASSWORD'),
    'port'      => env('DB_PORT'),
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => 'hm2_',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

app()->singleton('klein', Klein\Klein::class);
app()->singleton('mysql', function () {
    return new mysqli(env('DB_HOST'), env('DB_USERNAME'), env('DB_PASSWORD'), env('DB_DATABASE'));
});
app()->singleton('smarty', function () {
    $smarty = new Smarty();
    $smarty->template_dir = TMPL_PATH;
    $smarty->compile_dir = CACHE_PATH;

    return $smarty;
});

require __DIR__.'/routes.php';
