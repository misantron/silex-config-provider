<?php

return [
    'db.options' => [
        'db_name' => 'db_app',
        'user' => 'app',
        'password' => 'root',
    ],
    'logger' => [
        'monolog.logfile' => '%ROOT_PATH%/logs/app.log',
        'monolog.name' => 'app'
    ]
];
