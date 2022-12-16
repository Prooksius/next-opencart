<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\components\MyHtml;
use yii\bootstrap\Tabs;

/* @var $this yii\web\View */
/* @var $model app\models\Reviews */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="faq-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= Tabs::widget([
        'items' => MyHtml::languageTabs($model, $languages, [
            'name' => 'text',
            'description' => 'CKEditor_medium',
        ]),
        'options' => [
            'id' => 'country-update-tabs',
        ]
    ]);?>

    <?= $form->field($model, 'sort_order')->textInput(['type' => 'number']); ?>
    <?= $form->field($model, 'status')->checkbox(); ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
        <?= Html::a(YII::t('app', 'Cancel'), Yii::$app->request->referrer , ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
