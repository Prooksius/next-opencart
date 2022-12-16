<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\Tabs;

/* @var $this yii\web\View */
/* @var $model common\models\Language */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="banner-form">

  <?php $form = ActiveForm::begin(); ?>

  <div class="row">
    <div class="col-md-6">
      <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-md-6 hidden">
      <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>
    </div>
  </div>
  
  <hr>

  <?= $this->render('_modules', [
    'model' => $model,
    'form' => $form,
    'allModules' => $allModules,
  ]) ?>

  <div class="form-group">
    <?= Html::submitButton(YII::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    <?= Html::a(YII::t('app', 'Cancel'), Yii::$app->request->referrer , ['class' => 'btn btn-default']) ?>
  </div>

  <?php ActiveForm::end(); ?>

</div>
