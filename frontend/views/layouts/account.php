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
//use yii2mod\alert\AlertAsset;
use newerton\fancybox3\FancyBox;
use yii\web\View;
use evgeniyrru\yii2slick\Slick;
use yii\web\JsExpression;
use frontend\models\Settings;
use common\models\Customer;
use himiklab\thumbnail\EasyThumbnailImage;
use frontend\models\Payment;
use frontend\models\PaymentSearch;
use frontend\models\PartnerPromocode;

AppAsset::register($this);

//AlertAsset::register($this);

$site_info = Settings::readAllSettings('general');
$cur_cust = Customer::findOne(['id' => Yii::$app->user->id, 'status' => Customer::STATUS_ACTIVE]);

$user_boughts = Payment::find()
    ->alias('p')
    ->select(['p.created_at', 'b.name AS botname', 'b.deposit as botdeposit'])
    ->leftJoin('bot b', 'p.bot_id = b.id')
    ->where(['or', ['p.customer_id' => Yii::$app->user->id], ['p.cust_email' => $cur_cust->email]])
    ->andWhere(['p.status' => 1])
    ->orderBy(['p.created_at' => SORT_DESC])
    ->all();

$funcs_aval = (is_array($user_boughts_arr) && count($user_boughts_arr) > 0) || $cur_cust->lk_enabled;

$searchModel = new PaymentSearch();
$totals = $searchModel->customerTotals(Yii::$app->user->id);

$seo = Settings::readAllSettings('mainpage');
$this->registerMetaTag([
    'name' => 'description',
    'content' => $seo['meta_desc'],
]);
$this->registerMetaTag([
    'name' => 'keywords',
    'content' => $seo['meta_keywords'],
]);

$jscript = <<< JS
yii.confirm = function (message, okCallback, cancelCallback) {
    swal({
            title: message,
            buttons: {
                confirm: {
                    text: 'Да',
                    visible: true,
                    value: true,
                },
                cancel: {
                    text: 'Нет',
                    visible: true,
                    value: null,
                }, 
            },
            dangerMode: true,
        })
        .then((res_action) => {
        if (res_action) {
            okCallback();
        }
    });
};
JS;

$this->registerJs( $jscript);

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
    <? $this->registerJsFile('https://www.google.com/recaptcha/api.js?render=6Ld8b-kaAAAAAKKyKxG3w3RW0hCxqBelwko_jTFZ', ['position' => View::POS_HEAD,], 'recaptcha-v3-script'); ?>

</head>
<body class="privateOffice-body">
<?php $this->beginBody() ?>
<?//= FancyBox::widget(); ?>
<div class="site-wrap">
    <header class="main-header main-header-privateOffice">
        <div class="container">
            <div class="main-header-container main-header-container1">
                <div class="main-header-wrap">
                    <a href="/" class="main-header-logo">
                        <img src="/img/accout_logo.png" alt="">
                    </a>
                    <? echo MySocialLinks::widget(['options' => ['class' => 'main-header-soc-list']]);?>
                    <a href="mailto:<?= $site_info['email'] ?>" id="clickPhone" class="main-header-tel"><?= $site_info['email'] ?></a>
                </div>
                <div class="main-header-wrap1">
                    <a href="/" class="banner-logo">
                        <div class="banner-logo-img">
                            <img src="/img/logo_new.png" alt="">
                        </div>
                        <b>COPY-TRADE</b>
                        <span>TRADING ROBOT</span>
                    </a>
                    <a href="/account/profile" class="main-header-ava">
                        <div class="main-header-ava-img">
                            <? if ($cur_cust->picture) { ?>
                            <?= EasyThumbnailImage::thumbnailImg(
                                '@root' . $cur_cust->picture,
                                150,
                                150,
                                EasyThumbnailImage::THUMBNAIL_OUTBOUND,
                                ['style' => ['border-radius' => '50%', 'width' => '40px']]
                            ) ?>
                            <? } else { ?>
                            <?= EasyThumbnailImage::thumbnailImg(
                                '@root/frontend/web/img/no-avatar.jpg',
                                150,
                                150,
                                EasyThumbnailImage::THUMBNAIL_OUTBOUND,
                                ['style' => ['border-radius' => '50%', 'width' => '40px']]
                            ) ?>
                            <? } ?>
                        </div>
                        <div class="main-header-ava-text"><?= $cur_cust->first_name; ?></div>
                    </a>
                    <ul class="main-header-list">
                        <li>
                            <div class="main-header-list-balans">
                                <span>Баланс</span>
                                <b><?= number_format($totals['paybacksum']- $totals['payoutsum'], 0, '.', ' ') ?> USD</b>
                            </div>
                        </li>
                        <li>
                            <div class="main-header-list-info<?= ($funcs_aval ? '' : ' disabled'); ?><?= ($cur_cust->user_type == Customer::CUSTOMER_PARTNER ? ' main-header-list-info-hidden' : ''); ?>">
								<span>
									Реф<i>еральная</i> <br> ссылка
								</span>
                                <b><?= $cur_cust->ref_link ?></b>
                            </div>
                        </li>
                    </ul>
                    <?= Html::a('<img src="/img/exit-icon.svg" alt="">', ['/site/logout'], ['class' => 'main-header-exit'])?>
                    <div class="main-header-list-balans">
                        <span>Баланс</span>
                        <b><?= number_format($totals['paybacksum']- $totals['payoutsum'], 0, '.', ' ') ?> USD</b>
                    </div>
                    <div class="main-header-btn"></div>
                </div>
            </div>
        </div>
    </header>

    <?= Alert::widget() ?>
    <main>
        <?= $content ?>
    </main>

    <footer class="main-footer" id="main-footer">
        <div class="container">
            <div class="main-footer-wrap1">
				<div class="main-footer-icon">
                    <img src="/img/logo_new.png" alt="">
                </div>
                <nav class="main-footer-nav">
                    <img src="/img/accout_logo_b.png" alt="">
                </nav>
                <? echo MySocialLinks::widget(['options' => ['class' => 'main-footer-soc-list']]);?>
                <a href="mailto:<?= $site_info['email'] ?>" class="main-footer-tel"><?= $site_info['email'] ?></a>
            </div>
            <div class="main-footer-wrap2">
				<div class="left-part">
					<p>© 2015-<?= date('Y'); ?> <?= Yii::$app->name ?>. Все права защищены</p>
					<p style="color:#828282"><?= nl2br($site_info['footer_text']) ?></p>
				</div>
				<div class="pics">
					<div class="payments">
						<?php foreach ($site_info['footer_icons'] as $item) { ?>
						<img src="<?= $item['image'] ?>" alt="payments" />
						<?php } ?>
					</div>
					<a class="main-footer-dev">
						<img src="/img/developer-logo.svg" alt="" />
					</a>
				</div>
            </div>
        </div>
    </footer>

</div>

<div class="hidden">
    <div id="form-modal" class="popup1">
        <div id="modal-content">
            Загружаю...
        </div>
    </div>
</div>
<?php
$jscript = <<< JS
	$(document).delegate('.form-popup', 'click', function() {
		let modal = $('#modal-content');
        modal.html('<img src="/img/ajax-loader.gif" />');
        let size = $(this).attr('data-size');
        if (size) { 
            modal.parent().addClass(size);
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
JS;

$this->registerJs( $jscript, View::POS_READY);

if (!$funcs_aval) {
    $is_disabled_informed = Yii::$app->session->get('is_disabled_informed', '');
    $jscript2 = <<< JS
        swal('Функция заблокирована!', 'Система промокодов позволяет делиться скидками и зарабатывать. Промокоды будут доступны после первой покупки.');
JS;
    if (!$is_disabled_informed) {
        $this->registerJs($jscript2, View::POS_READY);
        Yii::$app->session->set('is_disabled_informed', '1');
    }
}

?>
<?php $this->endBody() ?>

</body>
</html>
<?php $this->endPage() ?>
