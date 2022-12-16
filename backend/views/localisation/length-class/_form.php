<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\components\MyHtml;
use yii\bootstrap\Tabs;

/* @var $this yii\web\View */
/* @var $model app\models\Reviews */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="attribute-group-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= Tabs::widget([
        'items' => MyHtml::languageTabs($model, $languages, [
            'title' => 'text',
            'unit' => 'text',
        ]),
        'options' => [
            'id' => 'length-class-update-tabs',
        ]
    ]);?>

    <?= $form->field($model, 'value')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
        <?= Html::a(YII::t('app', 'Cancel'), Yii::$app->request->referrer , ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
