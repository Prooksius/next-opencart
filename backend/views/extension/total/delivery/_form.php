<?php

use backend\components\MyHtml;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Language */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="total-form">

  <?php $form = ActiveForm::begin(); ?>

  <div class="row">
    <div class="col-md-4">
      <?= MyHtml::formGroup('ModuleGroup', 'name', Yii::t('app', 'Name'), $model->name)?>
    </div>
    <div class="col-md-4">
      <?= MyHtml::formGroup('ModuleGroup', 'sort_order', Yii::t('app', 'Sort order'), $model->sort_order)?>
    </div>
    <div class="col-md-4">
      <br />
      <br />
      <?= MyHtml::formGroupCheckbox('ModuleGroup', 'status', Yii::t('app', 'Active?'), 1, $model->status) ?>
    </div>
  </div>
  <hr>

  <?= MyHtml::formGroupLang('ModuleGroup', 'settingsArr[title]', 'Заголовок', $model->settingsArr['title'], [], $model->languagesList)?>

  <div class="form-group">
    <?= Html::submitButton(YII::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    <?= Html::a(YII::t('app', 'Cancel'), Yii::$app->request->referrer , ['class' => 'btn btn-default']) ?>
  </div>

  <?php ActiveForm::end(); ?>

</div>
