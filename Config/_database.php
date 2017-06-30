<?php
return [
    'default' => [
        'type' => 'MySQLi',
        'connection' => [
            /**
             * The following options are available for MySQL:
             *
             * string   hostname     server hostname, or socket
             * string   database     database name
             * string   username     database username
             * string   password     database password
             * boolean  persistent   use persistent connections?
             * array    variables    system variables as "key => value" pairs
             *
             * Ports and sockets may be appended to the hostname.
             */
            'hostname' => '91.200.60.68',
            'database' => 'cms_db',
            'username' => 'cms_db',
            'password' => "6X8d3D4y",
            'persistent' => FALSE,
        ],
        'table_prefix' => '',
        'charset' => 'utf8',
        'caching' => FALSE,
    ],
];

