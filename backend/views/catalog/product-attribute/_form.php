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
        <div class="col-md-4 col-sm-4">
          <?= $form->field($model, 'attribute_id')->dropDownList($attributesList) ?>
          <?= $form->field($model, 'alias')->textInput(['maxlength' => true]); ?>
        </div>
        <div class="col-md-8 col-sm-8">
          <?= MyHtml::formGroupLangArea($model, 'ProductAttribute', 'textsarr', $model->getAttributeLabel('text'), $model->textsarr, [], $languages) ?>
        </div>
    </div>

    <?= $form->field($model, 'language_id')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'product_id')->hiddenInput()->label(false) ?>
    <div class="form-group">
        <?= Html::submitButton(YII::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
        <?= Html::a(YII::t('app', 'Cancel'), Yii::$app->request->referrer , ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
