<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\helpers\Url;
use frontend\components\MyNav;
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
    'bg' => 'black-bg',
    'general' => Settings::readAllSettings('general'),
]); ?>
<?php
FancyBox::widget([
    'target' => 'a[rel=1212]',
]);
?>
<div class="wrap black-bg stretch">
    <nav id="top">
        <div class="container full sidepaddings">
            <div class="top-row">
                <?= $this->render('logo.php') ?>
                <div class="menu-btn-cont">
                    <div class="icon nav-icon-8 menu-btn">
                        <span></span>
                        <span></span>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    <?php

    /*
        $menuItems = [
            ['label' => 'Home', 'url' => ['/site/index']],
            ['label' => 'About', 'url' => ['/site/about']],
            ['label' => 'Contact', 'url' => ['/site/contact']],
        ];
        if (Yii::$app->user->isGuest) {
            $menuItems[] = ['label' => 'Signup', 'url' => ['/site/signup']];
            $menuItems[] = ['label' => 'Login', 'url' => 'javascript:void(0);', 'linkOptions' => ['id' => 'login-link', 'data-target' => '/site/login']];
        } else {
            $menuItems[] = '<li>'
                . Html::beginForm(['/site/logout'], 'post')
                . Html::submitButton(
                    'Logout (' . Yii::$app->user->identity->username . ')',
                    ['class' => 'btn btn-link logout']
                )
                . Html::endForm()
                . '</li>';
        }
    */
    ?>

    <?/*=Slick::widget([

        // HTML tag for container. Div is default.
        'itemContainer' => 'div',

        // HTML attributes for widget container
        'containerOptions' => ['class' => 'container'],

        // Items for carousel. Empty array not allowed, exception will be throw, if empty
        'items' => [
            '<div class="img-slide" style="width:1500px; height: 680px; background-image: url(/img/main_slide1_lg.jpg);"></div>',
            '<div class="img-slide" style="width:1500px; height: 680px; background-image: url(/img/main_slide2_lg.jpg);"></div>',
            '<div class="img-slide" style="width:1500px; height: 680px; background-image: url(/img/main_slide3_lg.jpg);"></div>',
        ],

        // HTML attribute for every carousel item
        'itemOptions' => ['class' => 'img-slide'],

        // settings for js plugin
        // @see http://kenwheeler.github.io/slick/#settings
        'clientOptions' => [
            'autoplay' => true,
            'dots'     => true,
            // note, that for params passing function you should use JsExpression object
            'onAfterChange' => new JsExpression('function() {console.log("The cat has shown")}'),
        ],

    ]); */?>

    <div class="container full wrap-main-container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer black-bg bottom">
    <div class="container full sidepaddings">
        <div class="row">
            <div class="col-xs-6">
                <? echo MySocialLinks::widget(['options' => ['class' => 'inline-ul social-links']]);?>
            </div>
            <div class="col-xs-6 text-right">
                <span class="made-kan">Сделано в KAN Agency</span>
            </div>
        </div>
    </div>
</footer>

<?php $this->endBody() ?>

<div style="display:none" class="fancybox-hidden">
    <div id="login_modal">
        <div id="modal-content">
            Загружаю...
        </div>
        </h2>
    </div>

    <?php
    $jscript = <<< JS
    $('#login-link').on('click', function() {
       $('#modal-content').load($(this).data('target'));
       $.fancybox.open({
            src  : '#login_modal',
       });   
    });
JS;

    $this->registerJs( $jscript, View::POS_READY);

    ?>
</body>
</html>
<?php $this->endPage() ?>