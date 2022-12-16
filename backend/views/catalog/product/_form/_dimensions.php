<?php

use yii\helpers\Html;
use \backend\components\InputFileWithPic;
use dosamigos\datetimepicker\DateTimePicker;
use backend\components\MyHtml;

/* @var $this yii\web\View */
/* @var $model app\models\Pages */
/* @var $form yii\widgets\ActiveForm */

?>
<div class="row">
    <div class="col-md-4 col-sm-6">
        <?= $form->field($model, 'length_class_id')->dropDownList($model->lengthClassesList) ?>
        <?= $form->field($model, 'length')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-md-4 col-sm-6">
        <?= $form->field($model, 'height')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'width')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-md-4 col-sm-6">
        <?= $form->field($model, 'weight_class_id')->dropDownList($model->weightClassesList) ?>
        <?= $form->field($model, 'weight')->textInput(['maxlength' => true]) ?>
    </div>
</div>
