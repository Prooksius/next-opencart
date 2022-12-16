<?php

use backend\components\MyHtml;
use backend\components\MyProductSelect;
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
    <div class="col-md-6">
      <br />
      <br />
      <?= $form->field($model, 'status')->checkbox() ?>
    </div>
  </div>
  <hr>
  <?= MyHtml::formGroupLang('Module', 'settingsArr[title]', 'Заголовок', $model->settingsArr['title'], [], $model->languagesList)?>
  <?= MyHtml::formGroupLang('Module', 'settingsArr[subtitle]', 'Подзаголовок', $model->settingsArr['subtitle'], [], $model->languagesList)?>
  <?= MyHtml::formGroup('Module', 'settingsArr[visible]', 'Видимые слайды', $model->settingsArr['visible'])?>

  <div class="form-group">
    <?= Html::submitButton(YII::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    <?= Html::a(YII::t('app', 'Cancel'), Yii::$app->request->referrer , ['class' => 'btn btn-default']) ?>
  </div>

  <?php ActiveForm::end(); ?>

</div>
