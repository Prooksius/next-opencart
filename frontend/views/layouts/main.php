<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use frontend\components\MyNav;
use frontend\components\MyMainTopNav;
use frontend\components\MyMainBottomNav;
use frontend\components\MySocialLinks;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
//use frontend\assets\UsocialAsset;
use yii2mod\alert\Alert;
//use common\widgets\Alert;
use yii2mod\alert\AlertAsset;
use newerton\fancybox3\FancyBox;
use yii\web\View;
use evgeniyrru\yii2slick\Slick;
use yii\web\JsExpression;
use frontend\models\Settings;

AppAsset::register($this);

//AlertAsset::register($this);

$site_info = Settings::readAllSettings('general');

$seo = Settings::readAllSettings('mainpage');
$this->registerMetaTag([
    'name' => 'description',
    'content' => $seo['meta_desc'],
]);
$this->registerMetaTag([
    'name' => 'keywords',
    'content' => $seo['meta_keywords'],
]);
$this->registerMetaTag([
    'property' => 'og:image',
    'content' => Url::to(['/logo.png']),
]);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<meta name="yandex-verification" content="d0930e7decfb2924" />
    <?= Html::csrfMetaTags() ?>
    <? $this->registerLinkTag(['rel' => 'icon', 'type' => 'image/png', 'href' => Url::to(['/fav.png'])]);?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
	<link rel="stylesheet" href="css/preloader.css">
    <link rel="stylesheet" href="css/main.css">
    <? $this->registerJsFile('https://www.google.com/recaptcha/api.js?render=6Ld8b-kaAAAAAKKyKxG3w3RW0hCxqBelwko_jTFZ', ['position' => View::POS_HEAD,], 'recaptcha-v3-script'); ?>
	
</head>
<body>
<?php $this->beginBody() ?>

<div class="preloader d-flex">
    <img src="img/full-logo.png" alt="Copy trade" class="title">
    <p class="offer">Лучшие трейдеры на одной платформе</p>
    <div class="load-wrap d-flex">
        <p class="percentage">0%</p>
        <div class="bar">
            <div class="load"></div>
        </div>
    </div>
</div>

<?= Alert::widget() ?>
<?= $content ?>

<div class="hidden">
    <div id="form-modal" class="popup1">
        <div id="modal-content">
            Загружаю...
        </div>
    </div>
</div>
<?php
$jscript = <<< JS
    $('.form-popup').on('click', function() {
		let goal = $(this).attr('data-goal');
		if (goal) {
//			ym(66152347,'reachGoal',goal);
		}
        $('#modal-content').load($(this).data('target'));
        $.fancybox.open({
            src  : '#form-modal',
        });   
    });
    $(document).delegate('#form-modal .apply-promocode', 'click', function() {
        let target = $(this).closest('form').attr('action');
        let params = $(this).closest('form').serialize();
        console.log(target + '?' + params);
        $('#modal-content').load(target + '?' + params);
    });
    $(document).delegate('#form-modal .popup-list a.inactive', 'click', function() {
        let target = $(this).data('target');
        $('#modal-content').load(target);
    });
JS;

$this->registerJs( $jscript, View::POS_READY);

?>

<?php $this->endBody() ?>

</body>
</html>
<?php $this->endPage() ?>
