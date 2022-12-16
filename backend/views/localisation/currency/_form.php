<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Language */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="language-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'decimal_place')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'symbol_left')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'symbol_right')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'value')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'status')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton(YII::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
        <?= Html::a(YII::t('app', 'Cancel'), Yii::$app->request->referrer , ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
