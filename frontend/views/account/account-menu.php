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
use yii\helpers\Url;

$cur_url = Yii::$app->request->url;

?>
<div class="privateOffice-title">
    <h2>Личный кабинет <span class="device-show"><a href="/site/logout"><svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14.2083 16.5H13.2917C13.0386 16.5 12.8333 16.7052 12.8333 16.9583V20.1667H1.83335V1.83335H12.8333V5.0417C12.8333 5.29482 13.0386 5.50004 13.2917 5.50004H14.2083C14.4615 5.50004 14.6667 5.29482 14.6667 5.0417V1.83335C14.6667 0.820832 13.8459 0 12.8333 0H1.83335C0.820832 0 0 0.820832 0 1.83335V20.1667C0 21.1792 0.820832 22 1.83335 22H12.8333C13.8459 22 14.6667 21.1792 14.6667 20.1667V16.9583C14.6667 16.7052 14.4615 16.5 14.2083 16.5Z" fill="white"/><path d="M21.8514 10.6621L16.3514 5.62043C16.2171 5.49823 16.0224 5.46467 15.8573 5.53896C15.6908 5.61192 15.5833 5.77662 15.5833 5.95834V6.87503C15.5833 7.00484 15.6384 7.12881 15.7351 7.21564L18.9212 10.0834H5.95835C5.705 10.0834 5.5 10.2884 5.5 10.5417V11.4584C5.5 11.7117 5.705 11.9167 5.95835 11.9167H18.9212L15.7351 14.7845C15.6384 14.8713 15.5833 14.9953 15.5833 15.1251V16.0417C15.5833 16.2234 15.6908 16.3882 15.8573 16.4611C15.9164 16.4875 15.9795 16.5 16.0417 16.5C16.1541 16.5 16.265 16.4584 16.3514 16.3796L21.8514 11.338C21.9463 11.2511 22 11.1285 22 11C22 10.8716 21.9463 10.7489 21.8514 10.6621Z" fill="white"/></svg></a></span></h2>
    <nav class="privateOffice-nav">
        <menu class="privateOffice-menu">
            <li>
                <? if ($cur_url == Url::to(['/account/main'])) { ?>
                    <?= Html::a('Главная', false, ['class' => 'active']); ?>
                <? } else { ?>
                    <?= Html::a('Главная', ['/account/main']); ?>
                <? } ?>
            </li>
            <li>
                <? if ($cur_url == Url::to(['/account/referals'])) { ?>
                    <?= Html::a('Структура', false, ['class' => 'active']); ?>
                <? } else { ?>
                    <?= Html::a('Структура', ['/account/referals']); ?>
                <? } ?>
            </li>
            <li>
                <? if ($cur_url == Url::to(['/account/payments'])) { ?>
                    <?= Html::a('Начисления', false, ['class' => 'active']); ?>
                <? } else { ?>
                    <?= Html::a('Начисления', ['/account/payments']); ?>
                <? } ?>
            </li>
            <li>
                <? if ($cur_url == Url::to(['/account/payouts'])) { ?>
                    <?= Html::a('Баланс', false, ['class' => 'active']); ?>
                <? } else { ?>
                    <?= Html::a('Баланс', ['/account/payouts']); ?>
                <? } ?>
            </li>
            <li>
                <? if ($cur_url == Url::to(['/account/profile'])) { ?>
                    <?= Html::a('Профиль', false, ['class' => 'active']); ?>
                <? } else { ?>
                    <?= Html::a('Профиль', ['/account/profile']); ?>
                <? } ?>
            </li>
            <li>
                <? if ($cur_url == Url::to(['/account/marketing'])) { ?>
                    <?= Html::a('Маркетинг', false, ['class' => 'active']); ?>
                <? } else { ?>
                    <?= Html::a('Маркетинг', ['/account/marketing']); ?>
                <? } ?>
            </li>

        </menu>
    </nav>
</div>
