<?php

use yii\helpers\Html;
use backend\components\MyHtml;
use yii\widgets\ActiveForm;
use yii\bootstrap\Tabs;

/* @var $this yii\web\View */
/* @var $model app\models\MenuMain */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="menu-about-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= Tabs::widget([
        'items' => MyHtml::languageTabs($model, $languages, [
            'name' => 'text',
        ]),
        'options' => [
            'id' => 'OurTeam-update-tabs',
        ]
    ]);?>
    <?= $form->field($model, 'link')->textInput() ?>
    <?= $form->field($model, 'sort_order')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'top_status')->checkbox() ?>
    <?= $form->field($model, 'bottom_status')->checkbox() ?>
    <?= $form->field($model, 'status')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton(YII::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
        <?= Html::a(YII::t('app', 'Cancel'), Yii::$app->request->referrer , ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
