<?php

return [
    'db_file' => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'database.sqlite',
    'email_setting' => [
        'from' => 'test-skylink@meta.ua',
        'is_smtp' => true,
        'host' => 'smtp.meta.ua',
        'port' => 465,
        'user' => 'test-skylink@meta.ua',
        'password' => 'test-skylink010318'
    ],
    'csrf_salt' => '87654321'

];