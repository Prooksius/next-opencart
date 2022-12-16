<?php
/**
 * Project: yii2-blog for internal using
 * Author: akiraz2
 * Copyright (c) 2018.
 */

namespace common\modules\blog\assets;

use yii\web\AssetBundle;

class AppAsset extends AssetBundle
{
    public $sourcePath = '@common/modules/blog/assets/default';
    public $baseUrl = '@web';
    public $css = [
    ];
    public $js = [
        'https://www.google.com/recaptcha/api.js?render=6LezU60UAAAAABoDznk_-QRnVkf7IqCzZNPBbj77',
        'js/reply_comment.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
