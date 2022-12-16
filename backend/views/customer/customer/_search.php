<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use dosamigos\datetimepicker\DateTimePicker;

/* @var $this yii\web\View */
/* @var $model app\models\CustomerSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="customer-search">

    <?php $form = ActiveForm::begin([
        'action' => [($mode == 'partners' ? 'partner' : 'usual')],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <div class="row">
        <div class="col-md-4 col-sm-12">
            <?= $form->field($model, 'username')->label('Логин/телефон') ?>
            <?= $form->field($model, 'phone')->label(false) ?>
        </div>
        <div class="col-md-4 col-sm-12">
            <?= $form->field($model, 'datefrom')->label('Дата регистрации (от/до)')->widget(DateTimePicker::classname(), [
                'language' => 'ru',
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
                'language' => 'ru',
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
        <div class="col-md-4 col-sm-12">
            <?= $form->field($model, 'email') ?>
        </div>
        <div class="col-md-12 col-sm-12 text-right">
            <div class="form-group">
                <?= Html::submitButton('Поиск', ['class' => 'btn btn-primary']) ?>
                <?= Html::a('Сброс', ['customer'], ['class' => 'btn btn-default']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
