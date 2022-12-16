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

    <?= $form->field($model, 'color')->textInput(['class' => 'form-control jscolor']); ?>

    <?= Tabs::widget([
        'items' => MyHtml::languageTabs($model, $model->languagesList, [
            'name' => 'text',
        ]),
        'options' => [
            'id' => 'country-update-tabs',
        ]
    ]);?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
        <?= Html::a(YII::t('app', 'Cancel'), Yii::$app->request->referrer , ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
