<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\bootstrap\Tabs;
use yii\helpers\Url;
use yii\web\View;

$jscript = <<< JS
	fbq('track', 'SubmitApplication');
JS;

$this->registerJs( $jscript, View::POS_READY);

?>
<div class="container">
    <br />
    <br />
    <h1>Поздравляем с успешной оплатой!</h1>
    <br />
    <p>Мы получили вашу оплату. В ближайшее время мы с вами свяжемся.</p>
    <br />
    <br />
    <? if (Yii::$app->user->isGuest) { ?>
    <p>Вы можете зарегистрироваться и подключится к партнерской программе</p>
    <br />
    <?= Html::button('Регистрация', ['class' => 'btn2 form-popup', 'data-target' => '/site/signuppopup'])?>
    <? } ?>
    <a href="/account" class="btn2">В аккаунт</span></a>
    <br />
    <br />
    <br />
    <br />
</div>