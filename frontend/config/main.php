<?php

use frontend\models\Settings;

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-frontend',
    'name' => 'Next-Cart',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
//	'language' => 'en-US',
	  'language' => 'ru-RU',
    'controllerNamespace' => 'frontend\controllers',
    'modules' => [],
    'on beforeRequest' => function ($event) {
      //
    }, 
    'components' => [
        'formatter' => [
            'dateFormat' => 'dd.MM.yyyy',
            'defaultTimeZone' => 'Europe/Moscow',
            'decimalSeparator' => '.',
            'thousandSeparator' => ' ',
            'currencyCode' => 'RUB',
        ],
        'request' => [
          'csrfParam' => '_csrf-frontend',
          'baseUrl' => '',
        ],
        'user' => [
            'identityClass' => 'common\models\Customer',
            'enableAutoLogin' => true,
            'enableSession' => false,
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-frontend',
        ],
        'currency' => [
            'class' => 'common\components\AppCurrency'
        ],
        'length' => [
            'class' => 'common\components\AppLength'
        ],
        'weight' => [
            'class' => 'common\components\AppWeight'
        ],
        'shopConfig' => [
            'class' => 'common\components\AppConfig'
        ],
        'cart' => [
            'class' => 'frontend\components\AppCart'
        ],
        'delivery' => [
            'class' => 'frontend\components\AppDelivery'
        ],
        'payment' => [
            'class' => 'frontend\components\AppPayment'
        ],
        'totals' => [
            'class' => 'frontend\components\AppCartTotal'
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    //'basePath' => '@app/messages',
                    //'sourceLanguage' => 'en-US',
                    'fileMap' => [
                        'app' => 'app.php',
                        'app/error' => 'error.php',
                    ],
                ],
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                ['class' => 'frontend\components\SefRule',
                    'connectionID' => 'db',
                ],
            ],
        ],
        'assetManager' => [
            'bundles' => [
                'yii\bootstrap\BootstrapAsset' => [
                    'css' => [],
                ],
                'yii\bootstrap\BootstrapPluginAsset' => [
                    'js'=>[]
                ],
            ],
        ],
    ],
    'params' => $params,
];
