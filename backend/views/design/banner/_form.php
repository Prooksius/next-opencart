<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\Tabs;

/* @var $this yii\web\View */
/* @var $model common\models\Language */
/* @var $form yii\widgets\ActiveForm */

$lang_tabs = [];
foreach ($model->getLanguagesList() as $language) {
  $lang_tabs[] = [
    'label' => Html::tag('span', '', ['class' => 'flag inline flag-' . $language->code]) . ' ' . $language->name,
    'encode' => false,
    'content' => $this->render('_images', [
        'model' => $model,
        'form' => $form,
        'language' => $language,
    ]),
    'active' => Yii::$app->language == $language->locale ? true : false,
  ];
}

?>

<div class="banner-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= Tabs::widget([
        'items' => $lang_tabs,
        'options' => [
            'id' => 'banner-images-update-tabs',
        ]
    ]);?>

    <?= $form->field($model, 'status')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton(YII::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
        <?= Html::a(YII::t('app', 'Cancel'), Yii::$app->request->referrer , ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
