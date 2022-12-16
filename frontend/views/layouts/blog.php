<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use frontend\components\MyNav;
use frontend\components\MyBlogNav;
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

$loader = 1;
$session = Yii::$app->session;
$current_ip = Yii::$app->request->userIP;
if (isset($session[$current_ip])) {
    $loader = 0;
} else {
    $session[$current_ip] = 1;
}

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
<body class="no-scroll">
<?php $this->beginBody() ?>
<?= $this->render('sandwich.php', [
    'bg' => '',
    'general' => Settings::readAllSettings('general'),
]); ?>
<?php
FancyBox::widget([
    'target' => 'a[rel=1212]',
]);
?>
<?= $this->render('first_screen.php', ['active' => $loader]) ?>
<div class="wrap usual">
    <div class="loader is-showing">
        <div class="loader__overlay"></div>
        <div class="loader__wiper"></div>
        <div class="loader__logo">
            <svg class="num3" width="89" height="82" viewBox="0 0 89 82" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M62.4589 82H0L44.3411 0L89 82H62.4589ZM34.8054 62.856H53.4L44.1821 43.0739L34.8054 62.856Z" fill="#aaa"/>
                <path d="M33.3763 20.1012L44.3411 0L89 82H62.4589L33.3763 20.1012Z" fill="#aaa"/>
            </svg>
        </div>
    </div>
    <nav id="top" class="at-top">
        <div class="container wide">
            <div class="top-row">
                <?= $this->render('logo.php') ?>
                <div class="menu-topright-cont">
                    <div class="menu-topright">
                        <? echo MyBlogNav::widget();?>
                    </div>
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
    <div class="top-divider"></div>
    <div class="wrap-content">
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

        <div class="container hidden">
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]) ?>
            <?= Alert::widget() ?>
        </div>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container wide">
        <div class="row">
            <div class="col-sm-6 sm-text-left">
                <? echo MySocialLinks::widget(['options' => ['class' => 'inline-ul social-links']]);?>

            </div>
            <div class="col-sm-6 sm-text-right">
                <span class="made-kan">Сделано в KAN Agency</span>
            </div>
        </div>
    </div>
</footer>
<div class="busy-loader">
    <svg class="num3" width="89" height="82" viewBox="0 0 89 82" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M62.4589 82H0L44.3411 0L89 82H62.4589ZM34.8054 62.856H53.4L44.1821 43.0739L34.8054 62.856Z" fill="#000"/>
        <path d="M33.3763 20.1012L44.3411 0L89 82H62.4589L33.3763 20.1012Z" fill="#000"/>
    </svg>
</div>
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
    new WOW(
        {
            mobile: false,
        }
    ).init();

    $('#login-link').on('click', function() {
       $('#modal-content').load($(this).data('target'));
       $.fancybox.open({
            src  : '#login_modal',
       });   
    });
JS;

$this->registerJs( $jscript, View::POS_READY);

$jscript = <<< JS
    $('.loader').removeClass('is-showing').addClass('is-hiding');
    setTimeout(function(){
        $('.loader').removeClass('is-hiding').addClass('is-loaded');
    }, 300);
JS;

$this->registerJs( $jscript, View::POS_LOAD);

?>
</body>
</html>
<?php $this->endPage() ?>
