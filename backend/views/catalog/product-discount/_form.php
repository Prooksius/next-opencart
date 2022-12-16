<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\components\MyHtml;
use dosamigos\datetimepicker\DateTimePicker;
use yii\bootstrap\Tabs;

/* @var $this yii\web\View */
/* @var $model app\models\Reviews */
/* @var $form yii\widgets\ActiveForm */

?>


<div class="attribute-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
      <div class="col-sm-6">
        <?= $form->field($model, 'customer_group_id')->dropDownList($allGroups) ?>
        <?= $form->field($model, 'price')->textInput(['maxlength' => true]); ?>
        <?= $form->field($model, 'priority')->textInput(['maxlength' => true]); ?>
      </div>
      <div class="col-sm-6">
        <?= $form->field($model, 'datestart')->widget(DateTimePicker::classname(), [
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
        <?= $form->field($model, 'dateend')->widget(DateTimePicker::classname(), [
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
        <?= $form->field($model, 'quantity')->textInput(['maxlength' => true]); ?>
      </div>
    </div>

    <?= $form->field($model, 'product_id')->hiddenInput()->label(false) ?>

    <div class="form-group">
        <?= Html::submitButton(YII::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
        <?= Html::a(YII::t('app', 'Cancel'), Yii::$app->request->referrer , ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
