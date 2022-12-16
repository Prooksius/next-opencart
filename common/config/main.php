<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'timeZone' => 'Europe/Moscow', //time zone affect the formatter datetime format
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
            'cachePath' => '@common/runtime/cache' // Храним кэш в common/runtime/cache
        ],
        'thumbnail' => [
            'class' => 'himiklab\thumbnail\EasyThumbnail',
            'cacheAlias' => '/upload/thumbnails',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [               
            ],
        ],
        'formatter' => [ //for the showing of date datetime
            'dateFormat' => 'yyyy-MM-dd',
            'datetimeFormat' => 'yyyy-MM-dd HH:mm:ss',
            'decimalSeparator' => '.',
            'thousandSeparator' => ' ',
            'currencyCode' => 'EUR',
        ],
    ],
    'modules' => [],    
];
