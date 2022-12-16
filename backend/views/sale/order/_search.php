<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\components\MyDatePicker;
use backend\components\MyCustomerChoose;
use dosamigos\datetimepicker\DateTimePicker;

/* @var $this yii\web\View */
/* @var $model app\models\ProductSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="customer-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <div class="row">
        <div class="col-lg-10 col-md-10 col-sm-9">
            <div class="row">
                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6 col-xxs-12">
                    <?= $form->field($model, 'order_status_id')->dropDownList($model->statusesList, ['prompt' => 'Выберите статус'])->label('Статус/Покупатель') ?>
                    <?= $form->field($model, 'customer_id')->widget(MyCustomerChoose::className(), ['id' => 'customer-choose'])->label(false) ?>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6 col-xxs-12">
                    <?= $form->field($model, 'datefrom')->label('Дата создания (от/до)')->widget(DateTimePicker::classname(), [
                      'language' => explode('-', Yii::$app->language)[0],
                      'size' => 'ms',
                      'template' => '{input}{reset}{button}',
                      'pickButtonIcon' => 'glyphicon glyphicon-time',
                      'clientOptions' => [
                          'autoclose' => true,
                          'format' => 'dd.mm.yyyy hh:ii',
                          'todayBtn' => true
                      ],
                    ]); ?>
                    <?= $form->field($model, 'dateto')->label(false)->widget(DateTimePicker::classname(), [
                      'language' => explode('-', Yii::$app->language)[0],
                      'size' => 'ms',
                      'template' => '{input}{reset}{button}',
                      'pickButtonIcon' => 'glyphicon glyphicon-time',
                      'clientOptions' => [
                          'autoclose' => true,
                          'format' => 'dd.mm.yyyy hh:ii',
                          'todayBtn' => true
                      ],
                    ]); ?>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6 col-xxs-12">
                    <?= $form->field($model, 'totalfrom')->label('Сумма заказа (от/до)') ?>
                    <?= $form->field($model, 'totalto')->label(false) ?>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-3 text-right">
            <br />
            <div class="form-group">
                <?= Html::submitButton(YII::t('app', 'Filter'), ['class' => 'btn btn-primary']) ?>
                <?= Html::a(YII::t('app', 'Reset'), ['sale/order'], ['class' => 'btn btn-default']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
