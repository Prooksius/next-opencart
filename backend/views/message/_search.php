<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\components\MyCustomerChoose;
use dosamigos\datetimepicker\DateTimePicker;

/* @var $this yii\web\View */
/* @var $model app\models\PaymentSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="payment-search">
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>
    <div class="row">
        <div class="col-md-4 col-sm-6">
            <?= $form->field($model, 'customer_id')->widget(MyCustomerChoose::className(), [
                'id' => 'customer-choose',
            ]) ?>
        </div>
        <div class="col-md-12 cl-sm-6">
            <div class="form-group text-right">
                <?= Html::submitButton('Искать', ['class' => 'btn btn-primary']) ?>
                <?= Html::a('Сброс', ['index'], ['class' => 'btn btn-default']) ?>
            </div>
        </div>

    <?php ActiveForm::end(); ?>

</div>
