<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\bootstrap\Tabs;
use yii\helpers\Url;
use yii\web\View;

$jscript = <<< JS
	fbq('track', 'CompleteRegistration');
JS;

$this->registerJs( $jscript, View::POS_READY);

?>
<div class="container">
    <br />
    <br />
    <h1>Спасибо за вашу заявку!</h1>
    <br />
    <p>В ближайшее время мы с вам свяжемся.</p>
    <br />
    <br />
    <br />
    <a href="/" class="btn2">На главную</span></a>
    <br />
    <br />
    <br />
    <br />
</div>
