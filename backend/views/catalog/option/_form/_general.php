<?php

use yii\helpers\Html;
use \backend\components\InputFileWithPic;
use backend\components\MyHtml;

/* @var $this yii\web\View */
/* @var $model app\models\Pages */
/* @var $form yii\widgets\ActiveForm */

?>
<div class="row">
  <div class="col-sm-6">
    <?= $form->field($model, 'type')->dropDownList($model->typesList) ?>
    <?= $form->field($model, 'alias')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'open_filter')->checkbox(); ?>
  </div>
  <div class="col-sm-6">
    <?= $form->field($model, 'sort_order')->textInput(['type' => 'number']); ?>
    <?= $form->field($model, 'filter_sort_order')->textInput(['type' => 'number']); ?>
  </div>
</div>
