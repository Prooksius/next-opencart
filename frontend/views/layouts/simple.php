<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\helpers\Url;
use frontend\components\MyNav;
use frontend\components\MyMainTopNav;
use frontend\components\MySocialLinks;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use common\widgets\Alert;
use newerton\fancybox3\FancyBox;
use yii\web\View;
use evgeniyrru\yii2slick\Slick;
use yii\web\JsExpression;
use frontend\models\Settings;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <? $this->registerLinkTag(['rel' => 'icon', 'type' => 'image/png', 'href' => Url::to(['/fav.png'])]);?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>
    <body>
    <?php $this->beginBody() ?>
    <?= $this->render('sandwich.php', [
        'bg' => 'light-bg',
        'general' => Settings::readAllSettings('general'),
    ]); ?>
    <div class="wrap light-bg stretch">
        <nav id="top">
            <div class="container full sidepaddings">
                <div class="top-row">
                    <?= $this->render('logo.php') ?>
                    <div class="menu-topright-cont">
                        <div class="menu-btn-cont">
                            <div class="icon nav-icon-8 menu-btn">
                                <span></span>
                                <span></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
        <div class="wrap-content">
            <div class="container full wrap-main-container">
                <div class="site-full-pages">
                    <?= $content ?>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer light-bg bottom">
        <div class="container full sidepaddings">
            <div class="row">
                <div class="col-xs-6 text-left">
                    <? echo MySocialLinks::widget(['options' => ['class' => 'inline-ul social-links']]);?>
                </div>
                <div class="col-xs-6 text-right">
                    <span class="made-kan">Сделано в KAN Agency</span>
                </div>
            </div>
        </div>
    </footer>

    <?php $this->endBody() ?>

    </body>
    </html>
<?php $this->endPage() ?>