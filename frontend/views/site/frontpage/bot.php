<?php
/**
 * Created by PhpStorm.
 * User: prook
 * Date: 26.04.2020
 * Time: 21:28
 */

use yii\helpers\Html;

?>
<section class="price scroll" id="price">
    <div class="price-bg"></div>
    <div class="container">
        <div class="price-container">
            <div class="price-wrap1">
                <h2>Цены</h2>
                <h3>Выберите подходящего Вашим возможностям робота</h3>
                <?= $this->render('consult', [
                    'model' => $model,
                ]); ?>
                <p><?//= Yii::$app->session->get('partner-reflink')?></p>
            </div>
            <div class="price-wrap2">
                <p>Полный комплект установочный файл робота, полное описание, видеоинструкции по установке и запуску. доступы к обучающим материалам, любительский чат, чат поддержки.</p>
                <div class="price-list">
                    <? $i2 = 1; ?>
                    <? foreach ($query->all() as $bot) { ?>
                    <div class="price-item">
                        <div class="price-item-wrap0">
                            <span>Бот</span>
                            <b><?= $bot->name ?></b>
                        </div>
                        <div class="price-item-wrap1">
                            <span>Депозит до</span>
                            <b>$&nbsp;<?= number_format($bot->deposit, 0, '', ' '); ?></b>
                        </div>
                        <div class="price-item-wrap2">
                            <span>Стоимость</span>
                            <b><?= number_format($bot->price, 0, '', ' '); ?>&nbsp;₽</b>
                        </div>
                        <div class="price-item-wrap3">
                            <?= Html::button('Купить Бот', ['class' => 'btn2 form-popup', 'data-target' => '/site/paymentpopup?bot_id='.$bot->id.'&init=1', 'data-goal' => 'clickBuyBot'])?>
                        </div>
                    </div>
                    <? $i2++; ?>
                    <? } ?>
                 </div>
            </div>
        </div>
    </div>
</section>