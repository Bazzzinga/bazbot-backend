<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'name' => 'Baz Streams',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'language' => 'ru-RU',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'haskdhaklsjdlk1882uy91y',
            'baseUrl' => '',
        ],
        'cache' => [
            //'class' => 'yii\caching\FileCache',
            'class' => 'yii\caching\DbCache',
        ],
        'user' => [
            'identityClass' => 'app\models\Users',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'categories' => ['yt_cb'],
                    'logFile' => '@runtime/logs/yt_cb.log',
                    'exportInterval' => 1,
                    'logVars' => []
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'categories' => ['tw_cb'],
                    'logFile' => '@runtime/logs/tw_cb.log',
                    'exportInterval' => 1,
                    'logVars' => []
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'categories' => ['yt_ps'],
                    'logFile' => '@runtime/logs/yt_ps.log',
                    'exportInterval' => 1,
                    'logVars' => []
                ],
            ],
        ],
        'db' => $db,

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'admin' => 'admin/admin/index',

                'yt-sub/<domain:[\w-_]+>' => 'callback/youtube-subscription',

                'twitch_auth' => 'site/twitch-auth',

                '<action>' => 'site/<action>'
            ],
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
