<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use \backend\components\InputFileWithPic;
use backend\components\MyHtml;
use yii\bootstrap\Tabs;

/* @var $this yii\web\View */
/* @var $model app\models\Reviews */
/* @var $form yii\widgets\ActiveForm */

$general_active = true;
$languages_active = false;

$errors = $model->getFirstErrors();
if (isset($errors['languages'])) {
  $general_active = false;
  $languages_active = true;
}

?>

<div class="filter-group-form">

  <?php $form = ActiveForm::begin(); ?>

  <?= Tabs::widget([
    'items' => [
      [
        'label' => YII::t('app', 'General Data'),
        'content' => $this->render('_form/_general', [
          'model' => $model,
          'form' => $form,
        ]),
        'active' => $general_active,
      ],
      [
        'label' => YII::t('app', 'Language Data'),
        'content' => $this->render('_form/_language', [
          'model' => $model,
          'form' => $form,
          'languages' => $languages,
        ]),
        'active' => $languages_active,
      ],
    ]
  ]);?>

  <div class="form-group">
    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    <?= Html::a(YII::t('app', 'Cancel'), Yii::$app->request->referrer , ['class' => 'btn btn-default']) ?>
  </div>

  <?php ActiveForm::end(); ?>

</div>
