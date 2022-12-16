<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.6/dist/jquery.fancybox.min.css',
        '//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css',
        'css/site.css',
        'css/msklad.css',
    ];
    public $js = [
        'https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js',
        'https://code.jquery.com/ui/1.12.1/jquery-ui.js',
        'js/jscolor.js',
        'js/site.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
