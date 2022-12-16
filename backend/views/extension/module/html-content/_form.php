<?php

use backend\components\MyHtml;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\Tabs;

/* @var $this yii\web\View */
/* @var $model common\models\Language */
/* @var $form yii\widgets\ActiveForm */

$lang_tabs = [];
foreach ($model->languagesList as $language) {
    $lang_tabs[] = [
        'label' => Html::tag('span', '', ['class' => 'flag inline flag-' . $language->code]) . ' ' . $language->name,
        'encode' => false,
        'active' => (\Yii::$app->language == $language->locale ? true : false),
        'content' => $this->render('html_lang', [
            'model' => $model,
            'locale' => $language->locale,
        ]),
    ];
}
?>

<div class="banner-form">

  <?php $form = ActiveForm::begin(); ?>

  <div class="row">
    <div class="col-md-6">
      <?= MyHtml::formGroup('Module', 'name', Yii::t('app', 'Name'), $model->name)?>
    </div>
    <div class="col-md-6">
      <br />
      <br />
      <?= MyHtml::formGroupCheckbox('Module', 'status', Yii::t('app', 'Active?'), 1, $model->status) ?>
    </div>
  </div>
  <hr>

  <div class="row">
    <div class="col-sm-12">
      <?= Tabs::widget([
          'items' => $lang_tabs,
      ]);?>
    </div>
  </div>

  <div class="form-group">
    <?= Html::submitButton(YII::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    <?= Html::a(YII::t('app', 'Cancel'), Yii::$app->request->referrer , ['class' => 'btn btn-default']) ?>
  </div>

  <?php ActiveForm::end(); ?>

</div>
