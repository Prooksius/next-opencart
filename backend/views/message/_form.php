<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\components\MyCustomerChoose;
use dosamigos\datetimepicker\DateTimePicker;
use backend\components\MyHtml;
use yii\bootstrap\Tabs;

/* @var $this yii\web\View */
/* @var $model app\models\Payout */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="payout-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-sm-6">
          <?= $form->field($model, 'customer_id')->widget(MyCustomerChoose::className(), [
            'id' => 'customer-choose',
          ]) ?>
          <?= $form->field($model, 'viewed')->dropDownList([0 => 'Нет', 1 => 'Да']) ?>
        </div>
        <div class="col-sm-6">
          <?= $form->field($model, 'created')->widget(DateTimePicker::classname(), [
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
          <?= $form->field($model, 'type')->dropDownList($model->typeslist) ?>
          <?= $form->field($model, 'icon')->dropDownList($model->iconslist) ?>
        </div>
    </div>

    <?= Tabs::widget([
        'items' => MyHtml::languageTabs($model, $languages, [
            'name' => 'text',
            'description' => 'CKEditor_medium',
        ]),
        'options' => [
            'id' => 'message-update-tabs',
        ]
    ]);?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Отмена', Yii::$app->request->referrer , ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
