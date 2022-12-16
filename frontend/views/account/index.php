<?php

/**
 * Created by PhpStorm.
 * User: prook
 * Date: 27.04.2020
 * Time: 9:40
 */

/* @var $this \yii\web\View */
/* @var $customer */

use yii\helpers\Html;

$this->title = 'Личный кабинет | ' . $seo['meta_title'];

?>
<section class="privateOffice<?= ($is_partner ? ' privateOffice1' : '') ?>">
    <div class="container">
        <?= $this->render('account-menu'); ?>
        <div class="privateOfficePromotionalCodeSetting" style="padding-left: 27px">
            <h3>Здесь вы можете просматривать свою статистику и управлять вашим аккаунтом</h3>
            <br />
            <br />
            <p><b>Промокоды: </b>просмотр и изменение ваших промокодов</p>
            <br />
            <p><b>Начисления: </b>просмотр списка начислений с покупки по вашим промокодам</p>
            <br />
            <p><b>Баланс: </b>просмотр ваших выводов и создание заявки на вывод</p>
            <br />
            <p><b>Профиль: </b>управление личными данными</p>
        </div>
    </div>
</section>