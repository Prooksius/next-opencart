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

    <?= $form->field($model, 'filter_id')->dropDownList($allFilters) ?>
    <?= $form->field($model, 'product_id')->hiddenInput()->label(false) ?>

    <div class="form-group">
        <?= Html::submitButton(YII::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
        <?= Html::a(YII::t('app', 'Cancel'), Yii::$app->request->referrer , ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
