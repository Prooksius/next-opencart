<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\components\MyCustomerChoose;

/* @var $this yii\web\View */
/* @var $model app\models\PaymentSearch */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="accruals-wrap1">
    <div class="accruals-info">
        <ul class="accruals-list">
            <li>
                <span>Моя прибыль</span>
                <b><?= number_format($paybacksum, 0, '.', ' ') ?> USD</b>
            </li>
            <li>
                <span>Сумма продаж</span>
                <b><?= number_format($sum, 0, '.', ' ') ?> USD</b>
            </li>
            <li>
                <span>Количество продаж</span>
                <b><?= $payments->getTotalCount(); ?></b>
            </li>
            <li>
                <span>Выводов</span>
                <b><?= number_format($payoutsum, 0, '.', ' ') ?> USD</b>
            </li>
        </ul>
    </div>
    <div class="accruals-select-container">
        <?php $form = ActiveForm::begin([
            'action' => ['payments'],
            'method' => 'get',
            'options' => [
                'data-pjax' => 1
            ],
        ]); ?>
        <div class="accruals-select">
            <?= $form->field($model, 'level')->dropDownList($levels, ['prompt' => 'Уровень'])->label(false) ?>
        </div>
        <div class="accruals-select">
            <?= $form->field($model, 'datefrom')->dropDownList([0 => 'За все время', (date('d.m.Y H:i', time()-30*24*3600)) => 'За последние 30 дней'])->label(false) ?>
        </div>
        <?= Html::submitButton('Искать', ['class' => 'btn btn-primary hidden']) ?>
        <?php ActiveForm::end(); ?>
    </div>
</div>