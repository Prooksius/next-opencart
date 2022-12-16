<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = 'Вам необходимо авторизоваться | ' . \Yii::$app->name;
?>
<div class="container">

    <br />
    <br />
    <br />
    <h1>Вам необходимо авторизоваться</h1>
    <br />
    <p> Пожалуйста <button type="button" data-target="/site/loginpopup" class="btn2 form-popup">авторизуйтесь</button>.</p>
    <br />
    <br />
    <br />

</div>
