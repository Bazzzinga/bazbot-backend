<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app\commands',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@tests' => '@app/tests',
    ],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
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
                'yt-sub/<domain:[\w-_]+>' => 'callback/youtube-subscription',
            ],
        ],
    ],
    'params' => $params,
    /*
    'controllerMap' => [
        'fixture' => [ // Fixture generation command line.
            'class' => 'yii\faker\FixtureController',
        ],
    ],
    */
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
