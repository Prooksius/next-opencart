<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        '//fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap',
        'js/fancybox/jquery.fancybox.css',
        'js/swiper/swiper.css',
        'js/jquery-ui/jquery-ui.min.css',
        'js/tooltipster/tooltipster.bundle.min.css',
        'js/jQueryFormStylerMaster/jquery.formstyler.css',
        'css/normalize.css',
//        'css/component.css',
        'css/site.css',
    ];
    public $js = [
        'https://unpkg.com/sweetalert/dist/sweetalert.min.js',
        'js/jquery-ui/jquery-ui.min.js',
        'js/swiper/swiper.min.js',
        'js/fancybox/jquery.fancybox.min.js',
        'js/tooltipster/tooltipster.bundle.min.js',
        'js/maskeinput.js',
        'js/jQueryFormStylerMaster/jquery.formstyler.min.js',
        'js/jquery-ui-touch-punch-master/jquery.ui.touch-punch.min.js',
        'js/site.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapPluginAsset',
        '\rmrevin\yii\fontawesome\AssetBundle',
    ];
}
