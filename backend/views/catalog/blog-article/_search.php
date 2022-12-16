<?php

use app\models\Strategy;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\PaymentSearch */
/* @var $form yii\widgets\ActiveForm */

$catsTree = $model->categoriesTree;
unset($catsTree[0]);

?>

<div class="product-search">
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>
    <div class="row">
        <div class="col-md-4 col-sm-6">
            <?= $form->field($model, 'category_id')->dropDownList($catsTree, ['prompt' => YII::t('app', '--choose value--')]) ?>
        </div>
        <div class="col-md-4 col-sm-6">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-4 col-sm-6">
            <?= $form->field($model, 'status')->dropDownList([
              0 => Yii::t('app', 'Inactive'), 
              1 => Yii::t('app', 'Active')], 
              ['prompt' => YII::t('app', '--choose value--')]
            ) ?>
        </div>
        <div class="col-md-12 cl-sm-6">
            <div class="form-group text-right">
                <?= Html::submitButton(YII::t('app', 'Filter'), ['class' => 'btn btn-primary']) ?>
                <?= Html::a(YII::t('app', 'Reset'), ['index'], ['class' => 'btn btn-default']) ?>
            </div>
        </div>
    <?php // echo $form->field($model, 'ref_text') ?>

    <?php ActiveForm::end(); ?>

</div>
