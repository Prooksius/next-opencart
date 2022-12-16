<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class UsocialAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [

    ];
    public $js = [
        'https://usocial.pro/usocial/usocial.js?v=6.1.4',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];
    public $jsOptions = [
        'async' => 'async',
        'data-script' => 'usocial',
        'charset' => 'utf-8'
    ];
}
