<?php

/*
 * This file is part of the entimm/hm.
 *
 * (c) entimm <entimm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

require '../common/function.php';

if (!validate()) {
    header('HTTP/1.1 403 Forbidden');
    echo 'Invalid request.';
    exit;
}

include 'lib/config.inc.php';

if ('init_db' == $frm['a']) {
    $sql = file_get_contents('database/db.sql');
    Mysql::instance()->multi_query($sql) or die(1);
}

echo 'ok';
