<?php
/**
 * Created by PhpStorm.
 * User: prook
 * Date: 03.05.2020
 * Time: 20:46
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\PayoutRequestForm */
/* @var $form ActiveForm */

?>
<div class="account-payoutrequest">
    <h2>Вывод средств</h2>
    <?php $form = ActiveForm::begin([
        'id' => 'form-payoutrequest',
        'enableAjaxValidation' => false,
        'options' => [
            'class' => 'popup1-form',
        ]
    ]); ?>

        <div class="privateOffice-popup2-radio-wrap">
            <label>
                <input type="radio" value="1" checked name="PayoutRequestForm[request_type]">
                <span>Карта</span>
            </label>
            <label>
                <input type="radio" value="2" name="PayoutRequestForm[request_type]">
                <span>Криптокошелёк USDT</span>
            </label>
        </div>
        <?= $form->field($model, 'amount')->textInput(['type' => 'number', 'autofocus' => true, 'class' => 'input', 'placeholder' => 'Сумма*'])->label(false) ?>
        <?= $form->field($model, 'card_number')->textInput(['autofocus' => true, 'class' => 'input', 'placeholder' => 'Номер карты*'])->label(false) ?>
        <?= $form->field($model, 'bank')->textInput(['autofocus' => true, 'class' => 'input', 'placeholder' => 'Банк'])->label(false) ?>
        <?= $form->field($model, 'receiver')->textInput(['autofocus' => true, 'class' => 'input', 'placeholder' => 'Получатель'])->label(false) ?>

        <div class="form-group">
            <?= Html::Button('Отправить запрос', ['class' => 'btn2 sitelogin-button', 'data-action' => 'payoutrequest', 'data-link' => '/account/payout-request']) ?>
        </div>
    <?php ActiveForm::end(); ?>

</div><!-- account-payoutrequest -->