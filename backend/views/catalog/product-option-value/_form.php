<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\components\MyHtml;
use yii\bootstrap\Tabs;

/* @var $this yii\web\View */
/* @var $model app\models\Reviews */
/* @var $form yii\widgets\ActiveForm */

?>


<div class="attribute-form">

  <?php $form = ActiveForm::begin(); ?>

  <div class="row">
    <div class="col-md-6 col-sm-12">
      <?= $form->field($model, 'option_value_id')->dropDownList($model->allOptionValues) ?>
    </div>
    <div class="col-md-6 col-sm-12">
      <?= $form->field($model, 'quantity')->textInput(['maxlength' => true]) ?>
    </div>
  </div>
  <div class="row">
    <div class="col-md-6 col-sm-12">
      <div class="row">
        <div class="col-md-3 col-sm-12">
          <?= $form->field($model, 'price_prefix')->dropDownList($model->actionsList) ?>
        </div>
        <div class="col-md-9 col-sm-12">
          <?= $form->field($model, 'price')->textInput(['maxlength' => true]) ?>
        </div>
      </div>
    </div>
    <div class="col-md-6 col-sm-12">
      <div class="row">
        <div class="col-md-3 col-sm-12">
          <?= $form->field($model, 'points_prefix')->dropDownList($model->actionsList) ?>
        </div>
        <div class="col-md-9 col-sm-12">
          <?= $form->field($model, 'points')->textInput(['maxlength' => true]) ?>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-6 col-sm-12">
      <div class="row">
        <div class="col-md-3 col-sm-12">
          <?= $form->field($model, 'weight_prefix')->dropDownList($model->actionsList) ?>
        </div>
        <div class="col-md-9 col-sm-12">
          <?= $form->field($model, 'weight')->textInput(['maxlength' => true]) ?>
        </div>
      </div>
    </div>
    <div class="col-md-6 col-sm-12">
      <br />
      <?= $form->field($model, 'subtract')->checkbox() ?>
    </div>
  </div>
  <?= $form->field($model, 'product_id')->hiddenInput()->label(false) ?>
  <?= $form->field($model, 'option_id')->hiddenInput()->label(false) ?>
  <?= $form->field($model, 'product_option_id')->hiddenInput()->label(false) ?>

  <div class="form-group">
      <?= Html::submitButton(YII::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
      <?= Html::a(YII::t('app', 'Cancel'), Yii::$app->request->referrer , ['class' => 'btn btn-default']) ?>
  </div>

  <?php ActiveForm::end(); ?>

</div>
