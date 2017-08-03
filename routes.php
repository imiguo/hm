<?php

/*
 * This file is part of the entimm/hm.
 *
 * (c) entimm <entimm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

$klein = app('klein');

$klein->respond(['GET', 'POST'], '/', function () {
    include APP_PATH.'/http/index.php';
});

$klein->respond(['GET', 'POST'], env('ADMIN_ROUTE', '/admin'), function () {
    include APP_PATH.'/http/admin.php';
});

$klein->respond('GET', '/test', function () {
    include APP_PATH.'/http/test.php';
});

$klein->respond('GET', '/wap', function () {
    include APP_PATH.'/http/wap.php';
});

$klein->respond(['GET', 'POST'], '/payments/[:payment]', function ($request) {
    $payments = [
        'payeer',
        'perfectmoney',
    ];
    if (in_array($request->payment, $payments)) {
        include APP_PATH.'/http/payments/'.$request->payment.'.php';
    } else {
        throw Klein\Exceptions\HttpException::createFromCode(404);
    }
});

$klein->onHttpError(function ($code, $router) {
    if ($code >= 400 && $code < 500) {
        $router->response()->body(
            'Oh no, a bad error happened that caused a '.$code
        );
    } elseif ($code >= 500 && $code <= 599) {
        error_log('uhhh, something bad happened');
    }
});
